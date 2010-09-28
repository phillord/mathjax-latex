<?php
  /*
   Plugin Name: Mathjax Latex
   Description: Transform latex equations in javascript using mathjax
   Version: 0.1
   Author: Phillip Lord
   Author URI: http://knowledgeblog.org
   
   Copyright 2010. Phillip Lord (phillip.lord@newcastle.ac.uk)
   Newcastle University. 
   
  */


class MathJax{
  static $add_script;
  static $block_script;

  function init(){
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

    
    add_filter( 'the_content', array(__CLASS__, 'inline_to_shortcode' ) );

  }
  
  function unconditional(){
    echo '<!-- MathJax Latex Plugin installed';
    if( !self::$add_script ) 
      echo ': Disabled as no shortcodes on this page';

    if( self::$block_script )
      echo ': Disabled by nomathjax shortcode';
    
    echo ' -->';
  }

  function debug(){
    echo "Phils debug statement";
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
    return "\(" . $content . "\)";
  }

  function add_script(){
    if( !self::$add_script )
      return;
    
    if( self::$block_script )
      return;

    wp_register_script( 'mathjax', 
                        plugins_url('MathJax/MathJax.js',__FILE__),
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

}

MathJax::init();

/*
 function mathjax_latex_hooks_footer()
 {
 $blogsurl = get_bloginfo('wpurl') . '/wp-content/plugins/' 
 . basename(dirname(__FILE__));
 echo '<script type="text/javascript" src="' . $blogsurl . '/MathJax/MathJax.js"></script>';

 }
*/




?>