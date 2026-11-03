<?php
/**
 * MathJax-LaTeX WordPress plugin..
 *
 * @package MathJaxLatex
 */

/**
 * Plugin Name: MathJax-LaTeX
 * Description: Render LaTeX and MathML equations with MathJax.
 * Version: 1.4
 * Author: Phillip Lord, Simon Cockell, Paul Schreiber
 * Author URI: http://knowledgeblog.org
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright 2010. Phillip Lord (phillip.lord@newcastle.ac.uk)
 * Simon Cockell (s.j.cockell@newcastle.ac.uk)
 * Newcastle University.
 * Paul Schreiber (paulschreiber@gmail.com)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MATHJAX_JS_VERSION', '4.1.3' );

require_once __DIR__ . '/class-mathjax-latex.php';
require_once __DIR__ . '/class-mathjax-latex-admin.php';

/**
 * Instantiate admin configuration class.
 */
function mathjax_latex_admin_init() {
	global $mathjax_latex_admin;
	$mathjax_latex_admin = new MathJax_Latex_Admin();
}

if ( is_admin() ) {
	mathjax_latex_admin_init();
}
