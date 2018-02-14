<?php
/**
* mediaViewAdmin - Admin section for mediaView
*
* @package mediaView
* @author Dustin Scarberry
*
* @since 1.0
*/

if (!class_exists('mediaViewAdmin'))
{
  class mediaViewAdmin
  {
    private $_options, $_version, $_dirpath;

    public function __construct($version)
    {
      //get plugin options
      $this->_options = get_option('mediaview_main_opts');

      //set version
      $this->_version = $version;

      //set dirpath
      $this->_dirpath = dirname(__FILE__);

      //add hooks
      add_action('admin_init', array($this, 'processor'));
      add_action('admin_menu', array($this, 'addMenus'));
      add_action('admin_enqueue_scripts', array($this, 'addScripts'));

      //ajax hooks
      require($this->_dirpath . '/inc/ajax.php');
      $mvAdminAjax = new mvAdminAjax($this->_options);
    }

    public function addMenus()
    {
      add_menu_page('MediaView', 'MediaView', 'manage_options', 'mediaview', array($this, 'shortcodes_panel'), plugins_url('mediaview/i/mediaview_icon_16x16.png'));
      add_submenu_page('mediaview', 'MediaView Settings', __('Settings', 'mvcanvas'), 'manage_options', 'mediaview_settings', array($this, 'option_panel'));
    }

    public function addScripts()
    {
      wp_enqueue_style('mv-style', plugins_url('css/admin_style.min.css', __FILE__), false, $this->_version);
      wp_enqueue_media();
      wp_enqueue_script('jquery');
      wp_enqueue_script('jquery-ui-core');
      wp_enqueue_script('jquery-ui-sortable');
      wp_enqueue_style('jquery-ui-sortable', plugins_url('css/jquery-ui-1.10.3.custom.min.css', __FILE__), false, $this->_version);
      wp_enqueue_script('mv-admin', plugins_url('js/admin.min.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), $this->_version, true);

      $jsdata = array(
        'translations' => array(
          'confirmShortcodeDelete' =>  __('Are you sure you want to delete this shortcode?', 'mvcanvas'),
          'confirmItemDelete' => __('Are you sure you want to delete this item?', 'mvcanvas')
        ),
        'nonces' => array(
          'deleteShortcode' => wp_create_nonce('mv-delete-shortcode'),
          'deleteItem' => wp_create_nonce('mv-delete-item')
        )
      );

      wp_localize_script('mv-admin', 'mvJSData', $jsdata);
    }

    public function shortcodes_panel()
    {
      //declare globals
      global $wpdb;

      ?>

      <div class="wrap mv-admin">
        <h2 id="mv-masthead">MediaView <?php _e('Shortcodes', 'mvcanvas'); ?></h2>

        <?php

        if (isset($_GET['view']))
        {
          //display add a shortcode form//
          if ($_GET['view'] == 'addshortcode')
            require($this->_dirpath . '/inc/forms/addShortcode.inc.php');
          //display shortcode edit form//
          elseif ($_GET['view'] == 'editshortcode')
            require($this->_dirpath . '/inc/forms/editShortcode.inc.php');
          //view a shortcode//
          elseif ($_GET['view'] == 'viewshortcode')
            require($this->_dirpath . '/inc/forms/viewShortcode.inc.php');
          //display add media form//
          elseif ($_GET['view'] == 'addmedia')
            require($this->_dirpath . '/inc/forms/addMedia.inc.php');
          //display media item edit form//
          elseif ($_GET['view'] == 'edititem')
            require($this->_dirpath . '/inc/forms/editMedia.inc.php');
        }
        //display all shortcodes//
        else
          require($this->_dirpath . '/inc/forms/overviewShortcodes.inc.php');

        ?>

      </div>

      <?php
    }

    public function option_panel()
    {
      require($this->_dirpath . '/inc/forms/generalOptions.inc.php');
    }

    public function processor()
    {
      require($this->_dirpath . '/inc/processor.inc.php');
    }
  }
}
?>
