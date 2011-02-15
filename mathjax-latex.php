<?php
  /*
   Plugin Name: Mathjax Latex
   Description: Transform latex equations in javascript using mathjax
   Version: 0.2
   Author: Phillip Lord
   Author URI: http://knowledgeblog.org
   
   Copyright 2010. Phillip Lord (phillip.lord@newcastle.ac.uk)
   Newcastle University. 
   
  */


class MathJax{
  static $add_script;
  static $block_script;

  function init(){
    register_activation_hook(__FILE__, array(__CLASS__, 'mathjax_install'));
    
    if (get_option('force_load')) {
    
        self::$add_script = true;
    
    }
    
    else {
    
        add_shortcode('mathjax', 
                  array(__CLASS__, 'mathjax_shortcode' ));
    
    }

    add_shortcode('nomathjax',
                  array(__CLASS__, 'nomathjax_shortcode' ));
    
    add_shortcode('latex', 
                  array(__CLASS__, 'latex_shortcode' ));

    add_action('wp_footer', 
               array(__CLASS__, 'add_script'));
    
    add_action('wp_footer', 
               array(__CLASS__, 'unconditional'));

    if (get_option('wp_latex_enabled')) {
        add_filter( 'the_content', array(__CLASS__, 'inline_to_shortcode' ) );
    }

    add_action('admin_menu', array(__CLASS__, 'mathjax_menu'));

    add_filter('plugin_action_links', array(__CLASS__, 'mathjax_settings_link'), 9, 2 );
  }

  function mathjax_install() {
    //registers default options
    add_option('force_load', FALSE);
    add_option('latex_syntax', 'inline');
    add_option('mathjax_location', plugins_url("MathJax/MathJax.js",__FILE__));
    //test for wp-latex here
    if (method_exists('WP_LaTeX', 'init')) {
        add_option('wp_latex_enabled', FALSE);
    }
    else {
        add_option('wp_latex_enabled', TRUE);
    }
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
    //this gives us an optional "syntax" attribute, which defaults to "inline", but can also be "display"
    extract(shortcode_atts(array(
                'syntax' => get_option('latex_syntax'),
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
    if (!get_option('mathjax_location')) {
      add_option('mathjax_location', plugins_url("MathJax/MathJax.js",__FILE__));
    }
    $mathjax_location = get_option('mathjax_location');

    wp_register_script( 'mathjax', 
                        $mathjax_location,
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
        $settings_link = '<a href="' .admin_url('options-general.php?page=mathjax-latex.php').'">'. __('Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
  }

  function mathjax_menu() {
    add_options_page('MathJax-Latex Plugin Options', 'MathJax-Latex Plugin', 'manage_options', 'mathjax-latex', array(__CLASS__, 'mathjax_plugin_options'));
  }

  function mathjax_plugin_options() {
      if (!current_user_can('manage_options'))  {
        wp_die( __('You do not have sufficient permissions to access this page.') );
      }
      //initialise option for existing MathJax-LaTeX users
      if (!get_option('mathjax_location')) {
        add_option('mathjax_location', plugins_url("MathJax/MathJax.js",__FILE__));
      }
      echo '<div class="wrap" id="mathjax-latex-options">
<h2>MathJax-Latex Plugin Options</h2>
';
    if ($_POST['mathjax_hidden'] == 'Y') {
        //process form
        if ($_POST['force_load']) {
            update_option('force_load', TRUE);
        }
        else {
            update_option('force_load', FALSE);
        }
        if ($_POST['wp_latex_enabled']) {
            update_option('wp_latex_enabled', TRUE);
        }
        else {
            update_option('wp_latex_enabled', FALSE);
        }
        if ($_POST['latex_syntax'] != get_option('latex_syntax')) {
            update_option('latex_syntax', $_POST['latex_syntax']);
        }
        if ($_POST['default_disabled']) {
            update_option('default_disabled', true);
            if ($_POST['mathjax_location'] != get_option('mathjax_location')) {
                update_option('mathjax_location', $_POST['mathjax_location']);
            }
        }
        else {
            update_option('default_disabled', false);
            update_option('mathjax_location', plugins_url("MathJax/MathJax.js",__FILE__));
         }
            //$url = plugins_url($_POST['mathjax_location']."/MathJax.js",__FILE__);
            //$handle = @fopen($url,'r');
            //if($handle !== false){
                //update_option('mathjax_location', $_POST['mathjax_location']);
                //$exists = true;
            //}
            //else{
                //$exists = false;
            //}
        //}
        //else {
            //$exists = true;
        //}
        echo '<p><i>Options updated</i></p>';   
    }
?>   
      <form id="mathjaxlatex" name="mathjaxlatex" action="" method='POST'>
      <input type="hidden" name="mathjax_hidden" value="Y">
      <table class="form-table">
      <tr valign="middle">
      <th scope="row">Force Load<br/><font size="-2">Force MathJax javascript to be loaded on every post (Removes the need to use the &#91;mathjax&#93; shortcode).</font></th>
      <td><input type="checkbox" name="force_load" value="1"<?php 
      if (get_option('force_load')) {
        echo 'CHECKED';
      }
      ?>/></td>
      </tr>
      <tr valign="middle">
      <th scope="row">Default &#91;latex&#93; syntax attribute.<br/><font size='-2'>By default, the &#91;latex&#93; shortcode renders equations using the MathJax '<?php get_option('latex_syntax') ?>' syntax.</font></th>
      <td><select name='latex_syntax'>
            <option value='inline' <?php if (get_option('latex_syntax') == 'inline') echo 'SELECTED'; ?>>Inline</option>
            <option value='display' <?php if (get_option('latex_syntax') == 'display') echo 'SELECTED'; ?>>Display</option>
          </select>
      </td>
      </tr>
      <tr valign="middle">
      <th scope="row">Use wp-latex syntax?<br/><font size="-2">Allows use of the $latex$ wp-latex syntax. Conflicts with wp-latex.</font></th>
      <td><input type="checkbox" name="wp_latex_enabled" value="1"<?php 
      if (method_exists('WP_LaTeX', 'init')) {
        update_option('wp_latex_enabled', FALSE);
        echo 'DISABLED';
      }
      if (get_option('wp_latex_enabled')) {
        echo 'CHECKED';
      }
      //test for wp-latex
      ?>/>
      <?php
        if (method_exists('WP_LaTeX', 'init')) {
            echo '<br/>
<font size="-2">Uninstall wp-latex to be able to use this syntax</font>
';
        }
      ?>
      </td>
      </tr>
      <tr>
        <th>Override default MathJax location?</th>
        <td><input type="checkbox" name="default_disabled" value="1"<?php 
            if (get_option('default_disabled')) {
                echo 'CHECKED';
            }
      ?>/>
      </td>
      </tr>
      <tr>
        <th scope="row">MathJax Javascript location<br/><font size="-2">Changes will be ignored unless above is checked.</font></th>
        <td><input type='textbox' name='mathjax_location' class='regular-text code' value='<?php echo get_option('mathjax_location'); ?>'/></td>
      </tr>
      </table>
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
      </form>
      </div>
<?php
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
