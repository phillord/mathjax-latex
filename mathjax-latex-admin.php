<?php

/*
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

class MathJax_Latex_Admin {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_page_init' ) );
	}

	function admin_page_init() {
		add_options_page( 'MathJax-Latex', 'MathJax-Latex', 'manage_options', 'kblog-mathjax-latex', array( $this, 'plugin_options_menu' ) );
	}

	function plugin_options_menu() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$nonce = wp_nonce_field( 'kblog_mathjax_latex_save_action', 'kblog_mathjax_latex_save_field', true, false );

		$this->table_head( $nonce );

		// save options if this is a valid post
		if ( isset( $_POST['kblog_mathjax_latex_save_field'] ) &&
			wp_verify_nonce( $_POST['kblog_mathjax_latex_save_field'], 'kblog_mathjax_latex_save_action' )
		) {
			echo "<i>Options Updated</i>\n";
			$this->admin_save();
		}

		$checked_force_load = '';

		if ( get_option( 'kblog_mathjax_force_load' ) ) {
			$checked_force_load = 'checked="true"';
		}

		$this->admin_table_row( 'Force Load',
			'Force MathJax javascript to be loaded on every post (Removes the need to use the [mathjax] shortcode).',
			"<input type='checkbox' name='kblog_mathjax_force_load' value='1' $checked_force_load />"
		);

		$selected_inline  = get_option( 'kblog_mathjax_latex_inline' ) == 'inline' ? 'selected="true"' : '';
		$selected_display = get_option( 'kblog_mathjax_latex_inline' ) == 'display' ? 'selected="true"' : '';

		$syntax_input = <<<EOT
<select name="kblog_mathjax_latex_inline">
<option value="inline" $selected_inline>Inline</option>
<option value="display" $selected_display>Display</option>
</select>
EOT;

		$this->admin_table_row( 'Default [latex] syntax attribute.',
			"By default, the [latex] shortcode renders equations using the MathJax 'inline' syntax.",
			$syntax_input
		);

		$wp_latex_disabled         = method_exists( 'WP_LaTeX', 'init' ) ? "disabled='disable'" : '';
		$wp_latex_disabled_warning = method_exists( 'WP_LaTeX', 'init' ) ? 'Disable wp-latex to use this syntax.' : '';

		$use_wp_latex_syntax = get_option( 'kblog_mathjax_use_wplatex_syntax', false ) ? "checked='true'" : '';

		$this->admin_table_row( 'Use wp-latex syntax?',
			'Allows use of the $latex$ syntax, but conflicts with wp-latex. $wp_latex_disabled_warning',
			"<input type='checkbox' name='kblog_mathjax_use_wplatex_syntax' $wp_latex_disabled $use_wp_latex_syntax value='1'/>"
		);

		$use_cdn = get_option( 'kblog_mathjax_use_cdn', true ) ? 'checked="true"' : '';

		$this->admin_table_row( 'Use MathJax CDN Service?',
			'Allows use of the MathJax hosted contet delivery network. ' .
			'By using this, you are agreeing to these ' .
			'<a href="http://www.mathjax.org/download/mathjax-cdn-terms-of-service/">Terms of Service</a>.',
			"<input type='checkbox' name='kblog_mathjax_use_cdn' id='use_cdn' value='1' $use_cdn/>"
		);

		$custom_location_disabled = get_option( 'kblog_mathjax_use_cdn', true ) ? 'disabled="disabled"' : '';
		$custom_location          = "value='" . esc_attr( get_option( 'kblog_mathjax_custom_location', '' ) ) . "'";

		$this->admin_table_row( 'Custom MathJax location?',
			'If you are not using the CDN',
			"<input type='textbox' name='kblog_mathjax_custom_location' $custom_location $custom_location_disabled>"
		);

		$options = $this->config_options();

		$select_string = "<select name='kblog_mathjax_config'>\n";

		foreach ( $options as $i ) {
			$selected = $i === get_option( 'kblog_mathjax_config', 'default' ) ? "selected='true'" : '';
			$select_string .= "<option value='$i' " . esc_attr( $selected ) . ">$i</option>\n";
		}

		$select_string .= '</select>';

		$this->admin_table_row( 'MathJax Configuration', 'See MathJax documentation for details', $select_string );

		$this->table_foot();
	}

	function config_options() {
		$options = array(
			'default',
			'Accessible',
			'TeX-AMS_HTML',
			'TeX-AMS-MML_HTMLorMML',
		);

		return $options;
	}

	function admin_save() {
		update_option( 'kblog_mathjax_force_load', array_key_exists( 'kblog_mathjax_force_load', $_POST ) );

		if ( array_key_exists( 'kblog_mathjax_latex_inline', $_POST ) &&
			in_array( $_POST['kblog_mathjax_latex_inline'], array( 'inline', 'display' ) )
		) {
			update_option( 'kblog_mathjax_latex_inline', $_POST['kblog_mathjax_latex_inline'] );
		}

		update_option( 'kblog_mathjax_use_wplatex_syntax', array_key_exists( 'kblog_mathjax_use_wplatex_syntax', $_POST ) );

		update_option( 'kblog_mathjax_use_cdn', array_key_exists( 'kblog_mathjax_use_cdn', $_POST ) );

		if ( array_key_exists( 'kblog_mathjax_custom_location', $_POST ) ) {
			update_option( 'kblog_mathjax_custom_location', esc_url_raw( $_POST['kblog_mathjax_custom_location'] ) );
		}

		if ( array_key_exists( 'kblog_mathjax_config', $_POST ) &&
			in_array( $_POST['kblog_mathjax_config'], $this->config_options() )
		) {
			update_option( 'kblog_mathjax_config', $_POST['kblog_mathjax_config'] );
		}
	}

	function table_head( $nonce ) {
		echo <<<EOT
<div class="wrap" id="mathjax-latex-options">
<h2>Mathjax-Latex by Kblog</h2>
<form id="mathjaxlatex" name="mathjaxlatex" action="" method="POST">
$nonce
<table class="form-table">
EOT;
	}

	function table_foot() {
		echo <<<EOT
</table>

<input type="submit" value="Save Changes"/>
</form>

</div>
EOT;
	}

	function admin_table_row( $head, $comment, $input ) {
		echo <<<EOT
				<tr>
<td style="width: 35%">$head<br/>
<font size="-2">$comment</font></td>
<td>$input</td>
</tr>
EOT;
	}

} // class

function mathjax_latex_admin_init() {
	global $mathjax_latex_admin;
	$mathjax_latex_admin = new MathJax_Latex_Admin();
}

if ( is_admin() ) {
	mathjax_latex_admin_init();
}
