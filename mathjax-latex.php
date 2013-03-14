<?php
  /*
   Plugin Name: Mathjax Latex
   Description: Transform latex equations in javascript using mathjax
   Version: 1.2.1
   Author: Phillip Lord, Simon Cockell
   Author URI: http://knowledgeblog.org
   
   Copyright 2010. Phillip Lord (phillip.lord@newcastle.ac.uk)
   Simon Cockell (s.j.cockell@newcastle.ac.uk)
   Newcastle University. 
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

require_once( dirname( __FILE__ ) . "/mathjax-latex-admin.php" );

class MathJax{
  static $add_script;
  static $block_script;

  function init(){
    register_activation_hook(__FILE__, array(__CLASS__, 'mathjax_install'));
    register_deactivation_hook(__FILE__, array(__CLASS__, 'mathjax_uninstall'));
  
    if (get_option('force_load')) {
        self::$add_script = true;
    }
    
    add_shortcode('mathjax', 
                  array(__CLASS__, 'mathjax_shortcode' ));
    
    add_shortcode('nomathjax',
                  array(__CLASS__, 'nomathjax_shortcode' ));
    add_shortcode('latex', 
                  array(__CLASS__, 'latex_shortcode' ));
    add_action('wp_footer', 
               array(__CLASS__, 'add_script'));
    add_action('wp_footer', 
               array(__CLASS__, 'unconditional'));
    if (get_option('kblog_mathjax_use_wplatex_syntax')) {
        add_filter( 'the_content', array(__CLASS__, 'inline_to_shortcode' ) );
    }
    add_filter('plugin_action_links', array(__CLASS__, 'mathjax_settings_link'), 9, 2 );
  }

  function mathjax_install() {
    //registers default options
    add_option('kblog_mathjax_force_load', false);
    add_option('kblog_mathjax_latex_inline', 'inline');
    add_option('kblog_mathjax_use_wplatex_syntax', false );
    add_option('kblog_mathjax_use_cdn', true);
    add_option('kblog_mathjax_custom_location',false);
    add_option('kblog_mathjax_config',"default");
  }


  function mathjax_uninstall() {
    delete_option('kblog_mathjax_force_load', false);
    delete_option('kblog_mathjax_latex_inline', 'inline');
    delete_option('kblog_mathjax_use_wplatex_syntax', false );
    delete_option('kblog_mathjax_use_cdn', true);
    delete_option('kblog_mathjax_custom_location',false);
    delete_option('kblog_mathjax_config',"default");
  }
  
  function unconditional(){
    echo '<!-- MathJax Latex Plugin installed';
    if( !self::$add_script ) 
      echo ': Disabled as no shortcodes on this page';

    if( self::$block_script )
      echo ': Disabled by nomathjax shortcode';
    
    echo ' -->';
  }

  function mathjax_shortcode($atts,$content){
    self::$add_script = true;
  }

  function nomathjax_shortcode($atts,$content){
    self::$block_script = true;
  }
  
  function latex_shortcode($atts,$content)
  {
    self::$add_script = true;
    //this gives us an optional "syntax" attribute, which defaults to "inline", but can also be "display"
    extract(shortcode_atts(array(
                'syntax' => get_option('kblog_mathjax_latex_inline'),
            ), $atts));
    if ($syntax == 'inline') {
        return "\(" . $content . "\)";
    }
    else if ($syntax == 'display') {
        return "\[" . $content . "\]";
    }
  }

function add_script(){
    if( !self::$add_script )
      return;
    
    if( self::$block_script )
      return;
    
    //initialise option for existing MathJax-LaTeX users
    if (get_option('kblog_mathjax_use_cdn')) {
        $mathjax_location = "http://cdn.mathjax.org/mathjax/latest/MathJax.js";
    }
    else{
        $mathjax_location = get_option('kblog_mathjax_custom_location');
    }
    
    $mathjax_url = $mathjax_location . "?config=" .get_option( "kblog_mathjax_config" );

    wp_register_script( 'mathjax', 
                        $mathjax_url,
                        false, null, true );

    wp_print_scripts( 'mathjax' );
  }
  
  function inline_to_shortcode( $content ) {
    if ( false === strpos( $content, '$latex' ) )
      return $content;
    
    self::$add_script = true;
        
    return preg_replace_callback( '#\$latex[= ](.*?[^\\\\])\$#', 
                                  array(__CLASS__,'inline_to_shortcode_callback'),
                                  $content );
  }

  function inline_to_shortcode_callback( $matches ) {
    
  //   ##
  //   ## Also support wp-latex syntax. This includes the ability to set background and foreground 
  //   ## colour, which we can ignore. 
  //   ##

    if ( preg_match( '/.+((?:&#038;|&amp;)s=(-?[0-4])).*/i', 
                     $matches[1], $s_matches ) ) {
      $matches[1] = str_replace( $s_matches[1], '', $matches[1] );
    }
    
    if ( preg_match( '/.+((?:&#038;|&amp;)fg=([0-9a-f]{6})).*/i', 
                     $matches[1], $fg_matches ) ) {
      $matches[1] = str_replace( $fg_matches[1], '', $matches[1] );
    }
	
    if ( preg_match( '/.+((?:&#038;|&amp;)bg=([0-9a-f]{6})).*/i', 
                     $matches[1], $bg_matches ) ) {
      $matches[1] = str_replace( $bg_matches[1], '', $matches[1] );
    }
    
    return "[latex]{$matches[1]}[/latex]";
  }

  //add a link to settings on the plugin management page
  function mathjax_settings_link( $links, $file ) {
    if ($file == 'mathjax-latex/mathjax-latex.php' && function_exists('admin_url')) {
        $settings_link = '<a href="' .admin_url('options-general.php?page=kblog-mathjax-latex').'">'. __('Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
  }

}

MathJax::init();



?>
