<?php
/**
 * MathJax-LaTeX WordPress pkugin..
 *
 * @package MathJaxLatex
 */

/**
 * Plugin Name: MathJax-LaTeX
 * Description: Transform latex equations in JavaScript using MathJax
 * Version: 1.3.12
 * Author: Phillip Lord, Simon Cockell, Paul Schreiber
 * Author URI: http://knowledgeblog.org
 *
 * Copyright 2010. Phillip Lord (phillip.lord@newcastle.ac.uk)
 * Simon Cockell (s.j.cockell@newcastle.ac.uk)
 * Newcastle University.
 * Paul Schreiber (paulschreiber@gmail.com)
 */

define( 'MATHJAX_PLUGIN_VERSION', '1.3.12' );
define( 'MATHJAX_JS_VERSION', '2.7.9' );

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
