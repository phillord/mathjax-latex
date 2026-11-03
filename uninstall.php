<?php
/**
 * Remove the plugin's options when it is uninstalled.
 *
 * @package MathJaxLatex
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once __DIR__ . '/class-mathjax-latex.php';

MathJax_Latex::mathjax_uninstall();
