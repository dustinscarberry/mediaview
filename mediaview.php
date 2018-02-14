<?php

/*
Plugin Name: MediaView
Plugin URI: http://www.codeclouds.net/mediaview/
Description: This plugin allows you to create media galleries / slideshows containing videos and photos.
Version: 1.1.2
Author: Dustin Scarberry
Author URI: http://www.codeclouds.net/
License: GPL2
*/

/*  2013 Dustin Scarberry webmaster@codeclouds.net

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('mediaView'))
{
  class mediaView
  {
    private $_mvadmin, $_mvfrontend, $_options, $_dirpath;
    const CURRENT_VERSION = '1.1.2';

    public function __construct()
    {
      //set dirpath
      $this->_dirpath = dirname(__FILE__);

      //load options
      $this->_options = get_option('mediaview_main_opts');

      //check for upgrade
      $this->upgrade_check();

      //load external files
      $this->load_dependencies();

      //activation hook
      register_activation_hook(__FILE__, array($this, 'activate'));
    }

    //activate plugin
    public function activate($network)
    {
      //multisite call
      if (function_exists('is_multisite') && is_multisite() && $network){

        global $wpdb;
        $old_blog =  $wpdb->blogid;

        //Get all blog ids
        $blogids =  $wpdb->get_col('SELECT blog_id FROM ' .  $wpdb->blogs);

        foreach ($blogids as $blog_id)
        {
          switch_to_blog($blog_id);
          $this->maintenance();
        }

        switch_to_blog($old_blog);
      }

      //regular call
      $this->maintenance();
    }

    private function maintenance()
    {
      /*//php version check - to implement later
      $requiredPHPVersion = '5.5';

      if(version_compare(PHP_VERSION, $requiredPHPVersion, '<')){

      deactivate_plugins( basename( __FILE__ ) );
      wp_die('<p><strong>MediaView</strong> requires PHP version ' . $requiredPHPVersion . ' or greater.</p>', 'Plugin Activation Error');

      }*/

      //set up globals
      global $wpdb;

      //create database tables for plugin
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      $tbname[0] = $wpdb->prefix . 'mediaview_dataset';
      $tbname[1] = $wpdb->prefix . 'mediaview_media';

      $sql = "CREATE TABLE $tbname[0] (
        DATA_ID int(11) NOT NULL AUTO_INCREMENT,
        DATA_NAME varchar(40) NOT NULL,
        DATA_DISPLAYHEIGHT int(11) NOT NULL,
        DATA_CANVASCOLOR varchar(10) NOT NULL,
        DATA_CAPTIONCOLOR varchar(10) NOT NULL,
        DATA_UPDATEDATE int(11) NOT NULL,
        DATA_MEDIACOUNT int(4) DEFAULT '0' NOT NULL,
        UNIQUE KEY DATA_ID (DATA_ID)
      );
      CREATE TABLE $tbname[1] (
        MEDIA_ID int(11) NOT NULL AUTO_INCREMENT,
        MEDIA_VALUE longtext NOT NULL,
        MEDIA_TYPE varchar(40) NOT NULL,
        MEDIA_CAPTION varchar(60),
        MEDIA_POS int(11) DEFAULT '0' NOT NULL,
        MEDIA_UPDATEDATE int(11) NOT NULL,
        DATA_ID int(11) NOT NULL,
        UNIQUE KEY MEDIA_ID (MEDIA_ID)
      );";

      dbDelta($sql);

      if (empty($this->_options))
        $this->_options = array();

      $dft['ytHideControls'] = 'yes';
      $dft['ytProgressColor'] = 'white';
      $dft['vimeoControlColor'] = '#00adef';
      $dft['version'] =  self::CURRENT_VERSION;

      $this->_options = $this->_options + $dft;

      update_option('mediaview_main_opts', $this->_options);

      //create photo cache directory if needed
      $dir = wp_upload_dir();
      $dir = $dir['basedir'];

      wp_mkdir_p($dir . '/mediaview-cache');
    }

    private function upgrade_check()
    {
      if (!isset($this->_options['version']) || $this->_options['version'] < self::CURRENT_VERSION)
      {
        $this->_options['version'] = self::CURRENT_VERSION;
        $this->maintenance();
      }
    }

    //load dependencies for plugin
    private function load_dependencies()
    {
      load_plugin_textdomain('mvcanvas', false, 'mediaview/language');

      //load backend or frontend dependencies
      if (is_admin())
      {
        require ($this->_dirpath . '/admin.php');
        $this->_mvadmin = new mediaViewAdmin(self::CURRENT_VERSION);
      }
      else
      {
        require ($this->_dirpath . '/frontend.php');
        $this->_mvfrontend = new mediaViewFrontend(self::CURRENT_VERSION);
      }
    }
  }

  $mediaview = new mediaView();

}
?>
