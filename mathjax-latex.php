<?php
/**
 * Plugin Name: MathJax-LaTeX
 * Description: Transform latex equations in JavaScript using mathjax
 * Version: 1.3.8
 * Author: Phillip Lord, Simon Cockell, Paul Schreiber
 * Author URI: http://knowledgeblog.org
 *
 * Copyright 2010. Phillip Lord (phillip.lord@newcastle.ac.uk)
 * Simon Cockell (s.j.cockell@newcastle.ac.uk)
 * Newcastle University.
 * Paul Schreiber (paulschreiber@gmail.com)
*/

/*
 * The contents of this file are subject to the LGPL License, Version 3.0.
 *
 * Copyright (C) 2010-2013, Phillip Lord, Newcastle University
 * Copyright (C) 2010-2011, Simon Cockell, Newcastle University
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 */

define( 'MATHJAX_VERSION', '1.3.8' );

require_once dirname( __FILE__ ) . '/mathjax-latex-admin.php';

class MathJax {
	public static $add_script;
	public static $block_script;
	public static $mathml_tags = array(
		'math'           => array( 'class', 'id', 'style', 'dir', 'href', 'mathbackground', 'mathcolor', 'display', 'overflow', 'xmlns' ),
		'maction'        => array( 'actiontype', 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'selection' ),
		'maligngroup'    => array(),
		'malignmark'     => array(),
		'menclose'       => array( 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'notation' ),
		'merror'         => array( 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ),
		'mfenced'        => array( 'class', 'id', 'style', 'close', 'href', 'mathbackground', 'mathcolor', 'open', 'separators' ),
		'mfrac'          => array( 'bevelled', 'class', 'id', 'style', 'denomalign', 'href', 'linethickness', 'mathbackground', 'mathcolor', 'numalign' ),
		'mglyph'         => array( 'alt', 'class', 'id', 'style', 'height', 'href', 'mathbackground', 'src', 'valign', 'width' ),
		'mi'             => array( 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant' ),
		'mlabeledtr'     => array( 'class', 'id', 'style', 'columnalign', 'groupalign', 'href', 'mathbackground', 'mathcolor', 'rowalign' ),
		'mlongdiv'       => array(),
		'mmultiscripts'  => array( 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'subscriptshift', 'superscriptshift' ),
		'mn'             => array( 'class', 'id', 'style', 'dir', 'href', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant' ),
		'mo'             => array( 'accent', 'class', 'id', 'style', 'dir', 'fence', 'form', 'href', 'largeop', 'lspace', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'maxsize', 'minsize', 'moveablelimits', 'rspace', 'separator', 'stretchy', 'symmetric' ),
		'mover'          => array( 'accent', 'align', 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ),
		'mpadded'        => array( 'class', 'id', 'style', 'depth', 'height', 'href', 'lspace', 'mathbackground', 'mathcolor', 'voffset', 'width' ),
		'mphantom'       => array( 'class', 'id', 'style', 'mathbackground' ),
		'mroot'          => array( 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ),
		'mrow'           => array( 'class', 'id', 'style', 'dir', 'href', 'mathbackground', 'mathcolor' ),
		'ms'             => array( 'class', 'id', 'style', 'dir', 'lquote', 'href', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'rquote' ),
		'mscarries'      => array(),
		'mscarry'        => array(),
		'msgroup'        => array(),
		'msline'         => array(),
		'mspace'         => array( 'class', 'id', 'style', 'depth', 'height', 'linebreak', 'mathbackground', 'width' ),
		'msqrt'          => array( 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ),
		'msrow'          => array(),
		'mstack'         => array(),
		'mstyle'         => array( 'dir', 'decimalpoint', 'displaystyle', 'infixlinebreakstyle', 'scriptlevel', 'scriptminsize', 'scriptsizemultiplier' ),
		'msub'           => array( 'class', 'id', 'style', 'mathbackground', 'mathcolor', 'subscriptshift' ),
		'msubsup'        => array( 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'subscriptshift', 'superscriptshift' ),
		'msup'           => array( 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'superscriptshift' ),
		'mtable'         => array( 'class', 'id', 'style', 'align', 'alignmentscope', 'columnalign', 'columnlines', 'columnspacing', 'columnwidth', 'displaystyle', 'equalcolumns', 'equalrows', 'frame', 'framespacing', 'groupalign', 'href', 'mathbackground', 'mathcolor', 'minlabelspacing', 'rowalign', 'rowlines', 'rowspacing', 'side', 'width' ),
		'mtd'            => array( 'class', 'id', 'style', 'columnalign', 'columnspan', 'groupalign', 'href', 'mathbackground', 'mathcolor', 'rowalign', 'rowspan' ),
		'mtext'          => array( 'class', 'id', 'style', 'dir', 'href', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant' ),
		'mtr'            => array( 'class', 'id', 'style', 'columnalign', 'groupalign', 'href', 'mathbackground', 'mathcolor', 'rowalign' ),
		'munder'         => array( 'accentunder', 'align', 'class', 'id', 'style', 'mathbackground', 'mathcolor' ),
		'munderover'     => array( 'accent', 'accentunder', 'align', 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ),
		'semantics'      => array( 'definitionURL', 'encoding', 'cd', 'name', 'src' ),
		'annotation'     => array( 'definitionURL', 'encoding', 'cd', 'name', 'src' ),
		'annotation-xml' => array( 'definitionURL', 'encoding', 'cd', 'name', 'src' ),
	);

	public static function init() {
		register_activation_hook( __FILE__, array( __CLASS__, 'mathjax_install' ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'mathjax_uninstall' ) );

		if ( get_option( 'kblog_mathjax_force_load' ) ) {
			self::$add_script = true;
		}

		add_shortcode( 'mathjax', array( __CLASS__, 'mathjax_shortcode' ) );
		add_shortcode( 'nomathjax', array( __CLASS__, 'nomathjax_shortcode' ) );
		add_shortcode( 'latex', array( __CLASS__, 'latex_shortcode' ) );
		add_action( 'wp_footer', array( __CLASS__, 'add_script' ) );
		add_filter( 'script_loader_tag', array( __CLASS__, 'script_loader_tag' ), 10, 3 );

		if ( get_option( 'kblog_mathjax_use_wplatex_syntax' ) ) {
			add_filter( 'the_content', array( __CLASS__, 'inline_to_shortcode' ) );
		}

		add_filter( 'plugin_action_links', array( __CLASS__, 'mathjax_settings_link' ), 9, 2 );

		add_filter( 'the_content', array( __CLASS__, 'filter_br_tags_on_math' ) );

		add_action( 'init', array( __CLASS__, 'allow_mathml_tags' ) );
		add_filter( 'tiny_mce_before_init', array( __CLASS__, 'allow_mathml_tags_in_tinymce' ) );
	}

	// registers default options
	public static function mathjax_install() {
		add_option( 'kblog_mathjax_force_load', false );
		add_option( 'kblog_mathjax_latex_inline', 'inline' );
		add_option( 'kblog_mathjax_use_wplatex_syntax', false );
		add_option( 'kblog_mathjax_use_cdn', true );
		add_option( 'kblog_mathjax_custom_location', false );
		add_option( 'kblog_mathjax_config', 'default' );
	}

	public static function mathjax_uninstall() {
		delete_option( 'kblog_mathjax_force_load' );
		delete_option( 'kblog_mathjax_latex_inline' );
		delete_option( 'kblog_mathjax_use_wplatex_syntax' );
		delete_option( 'kblog_mathjax_use_cdn' );
		delete_option( 'kblog_mathjax_custom_location' );
		delete_option( 'kblog_mathjax_config' );
	}

	public static function mathjax_shortcode( $atts, $content ) {
		self::$add_script = true;
	}

	public static function nomathjax_shortcode( $atts, $content ) {
		self::$block_script = true;
	}

	public static function latex_shortcode( $atts, $content ) {
		self::$add_script = true;

		// this gives us an optional "syntax" attribute, which defaults to "inline", but can also be "display"
		$shortcode_atts = shortcode_atts(
			array(
				'syntax' => get_option( 'kblog_mathjax_latex_inline' ),
			), $atts
		);

		if ( 'inline' === $shortcode_atts['syntax'] ) {
			return '\(' . $content . '\)';
		} elseif ( 'display' === $shortcode_atts['syntax'] ) {
			return '\[' . $content . '\]';
		}
	}

	public static function add_script() {
		if ( ! self::$add_script ) {
			return;
		}

		if ( self::$block_script ) {
			return;
		}

		// initialise option for existing MathJax-LaTeX users
		if ( get_option( 'kblog_mathjax_use_cdn' ) || ! get_option( 'kblog_mathjax_custom_location' ) ) {
			$mathjax_location = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.2/MathJax.js';
		} else {
			$mathjax_location = get_option( 'kblog_mathjax_custom_location' );
		}

		$mathjax_url = $mathjax_location . '?config=' . get_option( 'kblog_mathjax_config' );

		wp_enqueue_script( 'mathjax', $mathjax_url, false, MATHJAX_VERSION, false );

		$mathjax_config = apply_filters( 'mathjax_config', array() );
		if ( $mathjax_config ) {
			wp_add_inline_script( 'mathjax', 'MathJax.Hub.Config(' . wp_json_encode( $mathjax_config ) . ');' );
		}
	}


	/**
	 * Set the script tag to have type text/x-mathjax-config
	 *
	 * @param string $tag    The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 * @param string $src    The script's source URL.
	 *
	 * @return string $tag
	 */
	public static function script_loader_tag( $tag, $handle = null, $src = null ) {
		if ( 'mathjax' === $handle ) {
			// replace the <script> tag for the inline script, but not for the <script> tag with src=""
			return str_replace( "<script type='text/javascript'>", "<script type='text/x-mathjax-config'>", $tag );
		}

		return $tag;
	}

	public static function inline_to_shortcode( $content ) {
		if ( false === strpos( $content, '$latex' ) ) {
			return $content;
		}

		self::$add_script = true;

		return preg_replace_callback( '#\$latex[= ](.*?[^\\\\])\$#', array( __CLASS__, 'inline_to_shortcode_callback' ), $content );
	}

	public static function inline_to_shortcode_callback( $matches ) {

		//
		// Also support wp-latex syntax. This includes the ability to set background and foreground
		// colour, which we can ignore.
		//

		if ( preg_match( '/.+((?:&#038;|&amp;)s=(-?[0-4])).*/i', $matches[1], $s_matches ) ) {
			$matches[1] = str_replace( $s_matches[1], '', $matches[1] );
		}

		if ( preg_match( '/.+((?:&#038;|&amp;)fg=([0-9a-f]{6})).*/i', $matches[1], $fg_matches ) ) {
			$matches[1] = str_replace( $fg_matches[1], '', $matches[1] );
		}

		if ( preg_match( '/.+((?:&#038;|&amp;)bg=([0-9a-f]{6})).*/i', $matches[1], $bg_matches ) ) {
			$matches[1] = str_replace( $bg_matches[1], '', $matches[1] );
		}

		return "[latex]{$matches[1]}[/latex]";
	}

	// add a link to settings on the plugin management page
	public static function mathjax_settings_link( $links, $file ) {
		if ( 'mathjax-latex/mathjax-latex.php' === $file && function_exists( 'admin_url' ) ) {
			$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=kblog-mathjax-latex' ) ) . '">' . esc_html__( 'Settings' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Removes the <br /> tags inside math tags
	 *
	 * @param $content
	 * @return string without <br /> tags
	 */
	public static function filter_br_tags_on_math( $content ) {
		return preg_replace_callback(
			'/(<math.*>.*<\/math>)/isU',
			function( $matches ) {
				return str_replace( array( '<br/>', '<br />', '<br>' ), '', $matches[0] );
			},
			$content
		);
	}

	/**
	 * Allow MathML tags within WordPress
	 * http://vip.wordpress.com/documentation/register-additional-html-attributes-for-tinymce-and-wp-kses/
	 * https://developer.mozilla.org/en-US/docs/Web/MathML/Element
	 */
	public static function allow_mathml_tags() {
		global $allowedposttags;

		foreach ( self::$mathml_tags as $tag => $attributes ) {
			$allowedposttags[ $tag ] = array();

			foreach ( $attributes as $a ) {
				$allowedposttags[ $tag ][ $a ] = true;
			}
		}
	}

	/**
	 * Ensure that the MathML tags will not be removed
	 * by the TinyMCE editor
	 */
	public static function allow_mathml_tags_in_tinymce( $options ) {

		$extended_tags = array();

		foreach ( self::$mathml_tags as $tag => $attributes ) {
			if ( ! empty( $attributes ) ) {
				$tag = $tag . '[' . implode( '|', $attributes ) . ']';
			}

			$extended_tags[] = $tag;
		}

		if ( ! isset( $options['extended_valid_elements'] ) ) {
			$options['extended_valid_elements'] = '';
		}

		$options['extended_valid_elements'] .= ',' . implode( ',', $extended_tags );
		$options['extended_valid_elements']  = trim( $options['extended_valid_elements'], ',' );

		return $options;
	}
}

MathJax::init();
