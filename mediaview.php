<?php

/*
Plugin Name: MediaView
Plugin URI: https://www.dscarberry.com/mediaview/
Description: This plugin allows you to create media galleries / slideshows containing videos and photos.
Version: 1.1.3
Author: Dustin Scarberry
Author URI: https://www.dscarberry.com/
License: GPL2
*/

/*  2013 Dustin Scarberry bitsnbytes1001@gmail.com

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

namespace Dscarberry\MediaView;

use Dscarberry\MediaView\Controller\View\DashboardController;
use Dscarberry\MediaView\Controller\View\AppController;
use Dscarberry\MediaView\Service\Maintenance;

require(dirname(__FILE__) . '/config.php');

class Bootstrap
{
  private $_options;

  public function __construct()
  {
    // register autoloader
    spl_autoload_register([$this, 'autoloader']);

    // load options
    $this->_options = get_option('mediaview_main_opts');

    // check for upgrade
    if (is_admin())
      $this->upgrade_check();

    // load dependencies
    $this->load_dependencies();

    // activation hook
    register_activation_hook(__FILE__, [$this, 'activate']);
  }

  public function autoloader($className)
  {
    if (strpos($className, 'Dscarberry\MediaView') !== false) {
      $className = str_replace('\\', '/', $className);
      $className = str_replace('Dscarberry/MediaView/', '', $className);
      require_once('src/' . $className . '.php');
    }
  }

  public function activate($network)
  {
    // multisite call
    if (function_exists('is_multisite') && is_multisite() && $network){

      global $wpdb;
      $old_blog =  $wpdb->blogid;

      // get all blog ids
      $blogIds =  $wpdb->get_col('SELECT blog_id FROM ' .  $wpdb->blogs);

      foreach ($blogIds as $blogId)
      {
        switch_to_blog($blogId);
        $this->maintenance();
      }

      switch_to_blog($old_blog);
    }

    // regular call
    $this->maintenance();
  }

  private function maintenance()
  {
    new Maintenance();
  }

  private function upgrade_check()
  {
    if (!isset($this->_options['version']) || $this->_options['version'] < DS_MEDIAVIEW_VERSION) {
      $this->maintenance();
      $this->_options['version'] = DS_MEDIAVIEW_VERSION;
      update_option('mediaview_main_opts', $this->options);  
    }
  }

  private function load_dependencies()
  {
    load_plugin_textdomain('mvcanvas', false, '/mediaview/translations');

    // load app or dashboard
    if (is_admin()) 
      new DashboardController();
    else
      new AppController();
  }
}

// php version check
function showNotice()
{
  echo '<div class="error e-message"><p>MediaView - PHP version ' . DS_MEDIAVIEW_MIN_PHP_VERSION . ' or greater required. You currently have version ' . phpversion() . '</p></div>';
}

function deactivate()
{
  deactivate_plugins(plugin_basename(__FILE__));
}

if (strnatcmp(phpversion(), DS_MEDIAVIEW_MIN_PHP_VERSION) < 0) {
  add_action('admin_notices', 'Dscarberry\MediaView\showNotice');
  add_action('admin_notices', 'Dscarberry\MediaView\deactivate');
} else
  new Bootstrap();