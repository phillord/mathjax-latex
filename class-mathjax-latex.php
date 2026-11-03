<?php
/**
 * Math rendering functions.
 *
 * @package MathJaxLatex
 */

/**
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Math rendering class.
 */
class MathJax_Latex {

	/**
	 * Add the MathJax script to the page.
	 *
	 * @var boolean
	 */
	public static $add_script;

	/**
	 * Block the MathJax script on the page.
	 *
	 * @var boolean
	 */
	public static $block_script;

	/**
	 * MathJax 4 combined components that can be loaded. The first is the default.
	 *
	 * @var string[]
	 */
	const CONFIGS = [
		'tex-mml-chtml',
		'tex-chtml',
		'tex-mml-svg',
		'tex-svg',
	];

	/**
	 * Allow MathML tags (for use with KSES).
	 *
	 * @var boolean
	 */
	public static $mathml_tags = [
		'math'           => [ 'class', 'id', 'style', 'dir', 'href', 'mathbackground', 'mathcolor', 'display', 'overflow', 'xmlns' ],
		'maction'        => [ 'actiontype', 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'selection' ],
		'maligngroup'    => [],
		'malignmark'     => [],
		'menclose'       => [ 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'notation' ],
		'merror'         => [ 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ],
		'mfenced'        => [ 'class', 'id', 'style', 'close', 'href', 'mathbackground', 'mathcolor', 'open', 'separators' ],
		'mfrac'          => [ 'bevelled', 'class', 'id', 'style', 'denomalign', 'href', 'linethickness', 'mathbackground', 'mathcolor', 'numalign' ],
		'mglyph'         => [ 'alt', 'class', 'id', 'style', 'height', 'href', 'mathbackground', 'src', 'valign', 'width' ],
		'mi'             => [ 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant' ],
		'mlabeledtr'     => [ 'class', 'id', 'style', 'columnalign', 'groupalign', 'href', 'mathbackground', 'mathcolor', 'rowalign' ],
		'mlongdiv'       => [],
		'mmultiscripts'  => [ 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'subscriptshift', 'superscriptshift' ],
		'mn'             => [ 'class', 'id', 'style', 'dir', 'href', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant' ],
		'mo'             => [ 'accent', 'class', 'id', 'style', 'dir', 'fence', 'form', 'href', 'largeop', 'lspace', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'maxsize', 'minsize', 'moveablelimits', 'rspace', 'separator', 'stretchy', 'symmetric' ],
		'mover'          => [ 'accent', 'align', 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ],
		'mpadded'        => [ 'class', 'id', 'style', 'depth', 'height', 'href', 'lspace', 'mathbackground', 'mathcolor', 'voffset', 'width' ],
		'mphantom'       => [ 'class', 'id', 'style', 'mathbackground' ],
		'mroot'          => [ 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ],
		'mrow'           => [ 'class', 'id', 'style', 'dir', 'href', 'mathbackground', 'mathcolor' ],
		'ms'             => [ 'class', 'id', 'style', 'dir', 'lquote', 'href', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'rquote' ],
		'mscarries'      => [],
		'mscarry'        => [],
		'msgroup'        => [],
		'msline'         => [],
		'mspace'         => [ 'class', 'id', 'style', 'depth', 'height', 'linebreak', 'mathbackground', 'width' ],
		'msqrt'          => [ 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ],
		'msrow'          => [],
		'mstack'         => [],
		'mstyle'         => [ 'dir', 'decimalpoint', 'displaystyle', 'infixlinebreakstyle', 'scriptlevel', 'scriptminsize', 'scriptsizemultiplier' ],
		'msub'           => [ 'class', 'id', 'style', 'mathbackground', 'mathcolor', 'subscriptshift' ],
		'msubsup'        => [ 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'subscriptshift', 'superscriptshift' ],
		'msup'           => [ 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor', 'superscriptshift' ],
		'mtable'         => [ 'class', 'id', 'style', 'align', 'alignmentscope', 'columnalign', 'columnlines', 'columnspacing', 'columnwidth', 'displaystyle', 'equalcolumns', 'equalrows', 'frame', 'framespacing', 'groupalign', 'href', 'mathbackground', 'mathcolor', 'minlabelspacing', 'rowalign', 'rowlines', 'rowspacing', 'side', 'width' ],
		'mtd'            => [ 'class', 'id', 'style', 'columnalign', 'columnspan', 'groupalign', 'href', 'mathbackground', 'mathcolor', 'rowalign', 'rowspan' ],
		'mtext'          => [ 'class', 'id', 'style', 'dir', 'href', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant' ],
		'mtr'            => [ 'class', 'id', 'style', 'columnalign', 'groupalign', 'href', 'mathbackground', 'mathcolor', 'rowalign' ],
		'munder'         => [ 'accentunder', 'align', 'class', 'id', 'style', 'mathbackground', 'mathcolor' ],
		'munderover'     => [ 'accent', 'accentunder', 'align', 'class', 'id', 'style', 'href', 'mathbackground', 'mathcolor' ],
		'semantics'      => [ 'definitionURL', 'encoding', 'cd', 'name', 'src' ],
		'annotation'     => [ 'definitionURL', 'encoding', 'cd', 'name', 'src' ],
		'annotation-xml' => [ 'definitionURL', 'encoding', 'cd', 'name', 'src' ],
	];

	/**
	 * Register actions and filters.
	 */
	public static function init() {
		foreach ( self::defaults() as $option => $default ) {
			add_filter(
				"default_option_{$option}",
				function () use ( $default ) {
					return $default;
				}
			);
		}

		if ( get_option( 'kblog_mathjax_force_load' ) ) {
			self::$add_script = true;
		}

		add_shortcode( 'mathjax', [ __CLASS__, 'mathjax_shortcode' ] );
		add_shortcode( 'nomathjax', [ __CLASS__, 'nomathjax_shortcode' ] );
		add_shortcode( 'latex', [ __CLASS__, 'latex_shortcode' ] );
		add_action( 'wp_footer', [ __CLASS__, 'add_script' ] );

		if ( get_option( 'kblog_mathjax_use_wplatex_syntax' ) ) {
			add_filter( 'the_content', [ __CLASS__, 'inline_to_shortcode' ] );
		}

		add_filter( 'plugin_action_links', [ __CLASS__, 'mathjax_settings_link' ], 9, 2 );

		add_filter( 'the_content', [ __CLASS__, 'filter_br_tags_on_math' ] );

		add_action( 'init', [ __CLASS__, 'allow_mathml_tags' ] );
		add_filter( 'tiny_mce_before_init', [ __CLASS__, 'allow_mathml_tags_in_tinymce' ] );
	}

	/**
	 * The plugin's options and their defaults, which get_option() returns until an option is saved.
	 *
	 * @return array
	 */
	public static function defaults() {
		return [
			'kblog_mathjax_force_load'         => false,
			'kblog_mathjax_latex_inline'       => 'inline',
			'kblog_mathjax_use_wplatex_syntax' => false,
			'kblog_mathjax_use_cdn'            => true,
			'kblog_mathjax_custom_location'    => '',
			'kblog_mathjax_config'             => self::CONFIGS[0],
		];
	}

	/**
	 * Removes the plugin's options. Called from uninstall.php.
	 */
	public static function mathjax_uninstall() {
		foreach ( array_keys( self::defaults() ) as $option ) {
			delete_option( $option );
		}
	}

	/**
	 * Set flag to load [mathjax] shortcode.
	 *
	 * @param array  $atts     Shortcode attributes.
	 * @param string $content  Shortcode content.
	 */
	public static function mathjax_shortcode( $atts, $content ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		self::$add_script = true;
	}

	/**
	 * Set flag to load [nomathjax] shortcode.
	 *
	 * @param array  $atts     Shortcode attributes.
	 * @param string $content  Shortcode content.
	 */
	public static function nomathjax_shortcode( $atts, $content ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		self::$block_script = true;
	}

	/**
	 * Enable [latex] shortcode.
	 *
	 * @param array  $atts     Shortcode attributes.
	 * @param string $content  Shortcode content.
	 */
	public static function latex_shortcode( $atts, $content ) {
		self::$add_script = true;

		// This gives us an optional "syntax" attribute, which defaults to "inline", but can also be "display".
		$shortcode_atts = shortcode_atts(
			[
				'syntax' => get_option( 'kblog_mathjax_latex_inline' ),
			],
			$atts
		);

		// Anything other than "display" (e.g. a typo) renders inline rather than dropping the equation.
		if ( 'display' === strtolower( $shortcode_atts['syntax'] ) ) {
			return '\[' . $content . '\]';
		}

		return '\(' . $content . '\)';
	}

	/**
	 * Enqueue/add the JavaScript to the <head> tag.
	 */
	public static function add_script() {
		if ( ! self::$add_script ) {
			return;
		}

		if ( self::$block_script ) {
			return;
		}

		$mathjax_location = get_option( 'kblog_mathjax_custom_location' );
		$location_path    = strtolower( (string) wp_parse_url( $mathjax_location, PHP_URL_PATH ) );
		$use_cdn          = get_option( 'kblog_mathjax_use_cdn' ) || ! $mathjax_location;

		if ( $use_cdn ) {
			$mathjax_url = 'https://cdn.jsdelivr.net/npm/mathjax@' . MATHJAX_JS_VERSION . '/' . self::get_config() . '.js';
		} elseif ( self::is_legacy_location( $mathjax_location ) ) {
			$mathjax_url = dirname( strtok( $mathjax_location, '?#' ) ) . '/' . self::get_config() . '.js';
		} elseif ( str_ends_with( $location_path, '.js' ) ) {
			// A MathJax 4 component file, e.g. …/tex-svg.js, which takes precedence over the configuration setting.
			$mathjax_url = $mathjax_location;
		} else {
			$mathjax_url = untrailingslashit( $mathjax_location ) . '/' . self::get_config() . '.js';
		}

		wp_enqueue_script(
			'mathjax',
			$mathjax_url,
			[],
			MATHJAX_JS_VERSION,
			[
				'strategy'  => 'async',
				'in_footer' => true,
			]
		);

		$mathjax_config = (array) apply_filters( 'mathjax_config', [] );

		// Always load MathJax's Safe extension, so TeX in untrusted content (e.g. comments) can't create javascript: links.
		// The casts accept a filter that returns null, an object, or a single loader.load string, instead of a fatal error.
		$mathjax_config['loader']         = (array) ( $mathjax_config['loader'] ?? [] );
		$mathjax_config['loader']['load'] = array_values( (array) ( $mathjax_config['loader']['load'] ?? [] ) );

		$mathjax_config['loader']['load'][] = 'ui/safe';

		// MathJax 4 loads fonts from jsDelivr by default. For a self-hosted copy, load them from the @mathjax
		// directory that npm installs next to the mathjax one, unless the mathjax_config filter set a path.
		if ( ! $use_cdn ) {
			$mathjax_config['loader']['paths']            = (array) ( $mathjax_config['loader']['paths'] ?? [] );
			$mathjax_config['loader']['paths']['fonts'] ??= '[mathjax]/../@mathjax';
		}

		// Break displayed equations that are wider than their column onto several lines, instead of
		// overflowing the page (e.g. on phones), unless the mathjax_config filter chose another behaviour.
		$mathjax_config['output']                      = (array) ( $mathjax_config['output'] ?? [] );
		$mathjax_config['output']['displayOverflow'] ??= 'linebreak';

		$mathjax_json = wp_json_encode( $mathjax_config );

		// If the filtered config can't be encoded (e.g. it contains NAN), still load the Safe extension.
		if ( false === $mathjax_json ) {
			$mathjax_json = '{"loader":{"load":["ui/safe"]}}';
		}

		wp_add_inline_script( 'mathjax', 'window.MathJax = ' . $mathjax_json . ';', 'before' );
	}

	/**
	 * Whether a custom location is from before 1.4, when it was the URL of MathJax 2's MathJax.js
	 * (possibly with ?config=…) rather than a directory.
	 *
	 * @param string $location The custom MathJax location.
	 *
	 * @return bool
	 */
	public static function is_legacy_location( $location ) {
		return 'mathjax.js' === strtolower( wp_basename( (string) wp_parse_url( (string) $location, PHP_URL_PATH ) ) );
	}

	/**
	 * Get the MathJax component to load. MathJax 2 configurations saved before 1.4 map to the default, except TeX-AMS_HTML.
	 *
	 * @return string
	 */
	public static function get_config() {
		$config = get_option( 'kblog_mathjax_config' );

		if ( 'TeX-AMS_HTML' === $config ) {
			return 'tex-chtml';
		}

		if ( in_array( $config, self::CONFIGS, true ) ) {
			return $config;
		}

		return self::CONFIGS[0];
	}

	/**
	 * Content filter that replaces inline $latex with [latex] shortcode.
	 *
	 * @param string $content    The page content.
	 */
	public static function inline_to_shortcode( $content ) {
		if ( false === strpos( $content, '$latex' ) ) {
			return $content;
		}

		self::$add_script = true;

		return preg_replace_callback( '#\$latex[= ](.*?[^\\\\])\$#', [ __CLASS__, 'inline_to_shortcode_callback' ], $content );
	}

	/**
	 * Callback for inline_to_shortcode() regex.
	 *
	 * Also support wp-latex syntax. This includes the ability to set background and foreground
	 * colour, which we can ignore.
	 *
	 * @param array $matches    Regular expression matches.
	 */
	public static function inline_to_shortcode_callback( $matches ) {
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

	/**
	 * Add a link to settings on the plugin management page.
	 *
	 * @param string[] $actions     An array of plugin action links. By default this can include 'activate',
	 *                              'deactivate', and 'delete'. With Multisite active this can also include
	 *                              'network_active' and 'network_only' items.
	 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
	 */
	public static function mathjax_settings_link( $actions, $plugin_file ) {
		if ( 'mathjax-latex/mathjax-latex.php' === $plugin_file && function_exists( 'admin_url' ) ) {
			$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=kblog-mathjax-latex' ) ) . '">' . esc_html__( 'Settings', 'mathjax-latex' ) . '</a>';
			array_unshift( $actions, $settings_link );
		}
		return $actions;
	}

	/**
	 * Removes the <br /> tags inside math tags.
	 *
	 * @param string $content  The page content.
	 *
	 * @return string without <br /> tags
	 */
	public static function filter_br_tags_on_math( $content ) {
		$filtered_content = preg_replace_callback(
			'/(<math.*>.*<\/math>)/isU',
			function ( $matches ) {
				return str_replace( [ '<br/>', '<br />', '<br>' ], '', $matches[0] );
			},
			$content
		);
		return null === $filtered_content ? $content : $filtered_content;
	}

	/**
	 * Allow MathML tags within WordPress
	 * http://vip.wordpress.com/documentation/register-additional-html-attributes-for-tinymce-and-wp-kses/
	 * https://developer.mozilla.org/en-US/docs/Web/MathML/Element
	 */
	public static function allow_mathml_tags() {
		global $allowedposttags;

		foreach ( self::$mathml_tags as $tag => $attributes ) {
			$allowedposttags[ $tag ] = []; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

			foreach ( $attributes as $a ) {
				$allowedposttags[ $tag ][ $a ] = true; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}
	}

	/**
	 * Ensure that the MathML tags will not be removed
	 * by the TinyMCE editor
	 *
	 * @param array $options  Array of TinyMCE options.
	 *
	 * @return array of TinyMCE options.
	 */
	public static function allow_mathml_tags_in_tinymce( $options ) {

		$extended_tags = [];

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

MathJax_Latex::init();
