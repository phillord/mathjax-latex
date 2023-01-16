<?php
/**
 * Admin UI functions.
 *
 * @package MathJaxLatex
 */

/**
 * The contents of this file are subject to the GPL License, Version 3.0.
 *
 * Copyright (C) 2013, Phillip Lord, Newcastle University
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Adium UI class.
 */
class MathJax_Latex_Admin {

	/**
	 * Allow HTML tags (for use with KSES).
	 *
	 * @var array
	 */
	public static $admin_tags = [
		'input'  => [
			'type'     => [],
			'name'     => [],
			'id'       => [],
			'disabled' => [],
			'value'    => [],
			'checked'  => [],
		],
		'select' => [
			'name' => [],
			'id'   => [],
		],
		'option' => [
			'value'    => [],
			'selected' => [],
		],
	];

	/**
	 * Hook for the menu item function.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_page_init' ] );
	}

	/**
	 * Add the MathJax-LaTeX menu item to the Settings menu.
	 */
	public function admin_page_init() {
		add_options_page( 'MathJax-LaTeX', 'MathJax-LaTeX', 'manage_options', 'kblog-mathjax-latex', [ $this, 'plugin_options_menu' ] );
	}

	/**
	 * Render the settings page.
	 */
	public function plugin_options_menu() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$this->table_head();

		// Save options if this is a valid post.
		if ( isset( $_POST['kblog_mathjax_latex_save_field'] ) && // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kblog_mathjax_latex_save_field'] ) ), 'kblog_mathjax_latex_save_action' ) // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		) {
			echo "<div class='updated settings-error' id='etting-error-settings_updated'><p><strong>Settings saved.</strong></p></div>\n";
			$this->admin_save();
		}

		$checked_force_load = '';

		if ( get_option( 'kblog_mathjax_force_load' ) ) {
			$checked_force_load = 'checked="true"';
		}

		$this->admin_table_row(
			'Force Load',
			'Force the MathJax JavaScript to be loaded on every post. This removes the need to use the [mathjax] shortcode.',
			"<input type='checkbox' name='kblog_mathjax_force_load' id='kblog_mathjax_force_load' value='1' $checked_force_load />",
			''
		);

		$selected_inline  = get_option( 'kblog_mathjax_latex_inline' ) === 'inline' ? 'selected="true"' : '';
		$selected_display = get_option( 'kblog_mathjax_latex_inline' ) === 'display' ? 'selected="true"' : '';

		$syntax_input = <<<EOT
<select name="kblog_mathjax_latex_inline" id="kblog_mathjax_latex_inline">
<option value="inline" $selected_inline>Inline</option>
<option value="display" $selected_display>Display</option>
</select>
EOT;

		$this->admin_table_row(
			'Default [latex] syntax attribute.',
			"By default, the [latex] shortcode renders equations using the MathJax 'inline' syntax.",
			$syntax_input,
			'kblog_mathjax_latex_inline'
		);

		$wp_latex_disabled         = method_exists( 'WP_LaTeX', 'init' ) ? "disabled='disable'" : '';
		$wp_latex_disabled_warning = method_exists( 'WP_LaTeX', 'init' ) ? 'Disable wp-latex to use this syntax.' : '';

		$use_wp_latex_syntax = get_option( 'kblog_mathjax_use_wplatex_syntax', false ) ? "checked='true'" : '';

		$this->admin_table_row(
			'Use wp-latex syntax?',
			"Allows use of the \$latex$ syntax, but conflicts with wp-latex. $wp_latex_disabled_warning",
			"<input type='checkbox' name='kblog_mathjax_use_wplatex_syntax' id='kblog_mathjax_use_wplatex_syntax' $wp_latex_disabled $use_wp_latex_syntax value='1'/>",
			'kblog_mathjax_use_wplatex_syntax'
		);

		$use_cdn = get_option( 'kblog_mathjax_use_cdn', true ) ? 'checked="true"' : '';

		$this->admin_table_row(
			'Use MathJax CDN Service?',
			'Allows use of the MathJax hosted content delivery network. By using this, you are agreeing to the  <a href="http://www.mathjax.org/download/mathjax-cdn-terms-of-service/">MathJax CDN Terms of Service</a>.',
			"<input type='checkbox' name='kblog_mathjax_use_cdn' id='use_cdn' value='1' $use_cdn/>",
			'use_cdn'
		);

		$custom_location_disabled = get_option( 'kblog_mathjax_use_cdn', true ) ? 'disabled="disabled"' : '';
		$custom_location          = "value='" . esc_attr( get_option( 'kblog_mathjax_custom_location', '' ) ) . "'";

		$this->admin_table_row(
			'Custom MathJax location?',
			'If you are not using the MathJax CDN enter the location of your MathJax script.',
			"<input type='textbox' name='kblog_mathjax_custom_location' id='kblog_mathjax_custom_location' $custom_location $custom_location_disabled>",
			'kblog_mathjax_custom_location'
		);

		$options = $this->config_options();

		$select_string = "<select name='kblog_mathjax_config' id='kblog_mathjax_config'>\n";

		foreach ( $options as $i ) {
			$selected       = get_option( 'kblog_mathjax_config', 'default' ) === $i ? "selected='true'" : '';
			$select_string .= "<option value='$i' " . esc_attr( $selected ) . ">$i</option>\n";
		}

		$select_string .= '</select>';

		$this->admin_table_row(
			'MathJax Configuration',
			"See the <a href='http://docs.mathjax.org/en/v1.1-latest/configuration.html#loading'>MathJax documentation</a> for more details.",
			$select_string,
			'kblog_mathjax_config'
		);

		$this->table_foot();
	}

	/**
	 * List of MathJax configuration options.
	 */
	public function config_options() {
		$options = [
			'default',
			'Accessible',
			'TeX-AMS_HTML',
			'TeX-AMS-MML_HTMLorMML',
		];

		return $options;
	}

	/**
	 * Save configuration changes to the database.
	 */
	public function admin_save() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'kblog_mathjax_latex_save_field', 'security' );
		}

		update_option( 'kblog_mathjax_force_load', array_key_exists( 'kblog_mathjax_force_load', $_POST ) ); // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected

		if ( array_key_exists( 'kblog_mathjax_latex_inline', $_POST ) && isset( $_POST['kblog_mathjax_latex_inline'] ) && // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
			in_array( sanitize_text_field( wp_unslash( $_POST['kblog_mathjax_latex_inline'] ) ), [ 'inline', 'display' ], true ) // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		) {
			update_option( 'kblog_mathjax_latex_inline', sanitize_text_field( wp_unslash( $_POST['kblog_mathjax_latex_inline'] ) ) ); // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		}

		update_option( 'kblog_mathjax_use_wplatex_syntax', array_key_exists( 'kblog_mathjax_use_wplatex_syntax', $_POST ) ); // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected

		update_option( 'kblog_mathjax_use_cdn', array_key_exists( 'kblog_mathjax_use_cdn', $_POST ) ); // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected

		if ( array_key_exists( 'kblog_mathjax_custom_location', $_POST ) && isset( $_POST['kblog_mathjax_custom_location'] ) ) { // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
			update_option( 'kblog_mathjax_custom_location', esc_url_raw( wp_unslash( $_POST['kblog_mathjax_custom_location'] ) ) ); // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		}

		if ( array_key_exists( 'kblog_mathjax_config', $_POST ) && isset( $_POST['kblog_mathjax_config'] ) && // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
			in_array( sanitize_text_field( wp_unslash( $_POST['kblog_mathjax_config'] ) ), $this->config_options(), true ) // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		) {
			update_option( 'kblog_mathjax_config', sanitize_text_field( wp_unslash( $_POST['kblog_mathjax_config'] ) ) ); // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		}
	}

	/**
	 * Render configuration page header.
	 */
	public function table_head() {
		?>
		<div class='wrap' id='mathjax-latex-options'>
			<h2>Mathjax-Latex</h2>
			<form id='mathjaxlatex' name='mathjaxlatex' action='' method='POST'>
				<?php wp_nonce_field( 'kblog_mathjax_latex_save_action', 'kblog_mathjax_latex_save_field', true ); ?>
			<table class='form-table'>
			<caption class='screen-reader-text'>The following lists configuration options for the MathJax-LaTeX plugin.</caption>
		<?php
	}

	/**
	 * Render configuration page footer.
	 */
	public function table_foot() {
		?>
		</table>

		<p class="submit"><input type="submit" class="button button-primary" value="Save Changes"/></p>
		</form>

		</div>
		<script type="text/javascript">
		jQuery(function($) {
			if (typeof($.fn.prop) !== 'function') {
				return; // ignore this for sites with jquery < 1.6
			}
			// enable or disable the cdn input field when checking/unchuecking the "use cdn" checkbox
			var cdn_check = $('#use_cdn'),
			cdn_location = $('#kblog_mathjax_custom_location');

			cdn_check.change(function() {
				var checked = cdn_check.is(':checked');
				cdn_location.prop('disabled', checked);
			});
		});
		</script>
		<?php
	}

	/**
	 * Render configuration table row.
	 *
	 * @param string $head     Header for the row.
	 * @param string $comment  Description of the row.
	 * @param string $input    The input tag (HTML).
	 * @param string $input_id The input ID (to associate label with input).
	 */
	public function admin_table_row( $head, $comment, $input, $input_id ) {
		?>
			<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $head ); ?></label>
					</th>
					<td>
						<?php echo wp_kses( $input, self::$admin_tags ); ?>
						<p class="description"><?php echo wp_kses_post( $comment ); ?></p>
					</td>
				</tr>
		<?php
	}
} // class
