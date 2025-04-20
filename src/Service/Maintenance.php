<?php

namespace Dscarberry\MediaView\Service;

class Maintenance
{
  public function __construct()
  {
    $this->checkRequiredPHPVersion();
    $this->setupDatabaseTables();
    $this->updateDefaultOptions();
    $this->createThumbnailCacheDirectory();
  }

  // create database tables
  private function setupDatabaseTables()
  {
    // set up globals
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

     // upgrade database tables
    foreach ($dbTables as $table)
      maybe_convert_table_to_utf8mb4($table);
  }

  // update default plugin options
  private function updateDefaultOptions()
  {
    $options = get_option('mediaview_main_opts');

    if (empty($options))
      $options = [];

    $dft['ytHideControls'] = 'yes';
    $dft['ytProgressColor'] = 'white';
    $dft['vimeoControlColor'] = '#00adef';
    $dft['version'] =  DS_MEDIAVIEW_VERSION;

    $options = $options + $dft;

    update_option('mediaview_main_opts', $options);
  }

  // create thumbnail cache directory
  private function createThumbnailCacheDirectory()
  {
    wp_mkdir_p(wp_upload_dir()['basedir'] . '/mediaview-cache');
  }

  // check for supported php version
  private function checkRequiredPHPVersion()
  {
    if (strnatcmp(phpversion(), DS_MEDIAVIEW_MIN_PHP_VERSION) < 0) {
      //exit( sprintf( 'Foo requires PHP 5.1 or higher. You’re still on %s.', PHP_VERSION ) );
      add_action('admin_notices', 'my_show_notice_disabled_plugin');
      //deactivate_plugins('mediaview/mediaview.php');

      return;
    }
     
  }

  function my_show_notice_diabled_plugin() {
    echo 'PHP version ' . DS_MEDIAVIEW_MIN_PHP_VERSION . ' or greater required. You currently have version ' . phpversion();
 }
}