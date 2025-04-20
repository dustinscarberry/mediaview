<?php

namespace Dscarberry\MediaView\Controller\View;

use Dscarberry\MediaView\View\GeneralOptionsView;
use Dscarberry\MediaView\View\AddMediaView;
use Dscarberry\MediaView\View\AddShortcodeView;
use Dscarberry\MediaView\View\EditMediaView;
use Dscarberry\MediaView\View\ShortcodeOverviewView;
use Dscarberry\MediaView\View\ShortcodeDetailsView;
use Dscarberry\MediaView\View\EditShortcodeView;
use Dscarberry\MediaView\Repository\DatasetRepository;
use Dscarberry\MediaView\Repository\MediaRepository;
use Dscarberry\MediaView\Service\Processor\AjaxProcessor;
use Dscarberry\MediaView\Service\Processor\FormProcessor;

class DashboardController
{
  private $options;

  public function __construct()
  {
    // get plugin options
    $this->options = get_option('mediaview_main_opts');

    // add hooks
    add_action('admin_init', [$this, 'processor']);
    add_action('admin_menu', [$this, 'addMenus']);

    // add scripts
    add_action('admin_enqueue_scripts', [$this, 'loadJS']);
    add_action('admin_enqueue_scripts', [$this, 'loadCSS']);

    // ajax hooks
    add_action('wp_ajax_mv_orderupdate', [AjaxProcessor::class, 'updateOrder']);
    add_action('wp_ajax_mv_deleteitem', [AjaxProcessor::class, 'deleteItem']);
    add_action('wp_ajax_mv_deleteshortcode', [AjaxProcessor::class, 'deleteShortcode']);
  }

  public function addMenus()
  {
    add_menu_page('MediaView', 'MediaView', 'manage_options', 'mediaview', [$this, 'shortcodesPanel'], plugins_url('mediaview/public/static/mediaview_icon_16x16.png'));
    add_submenu_page('mediaview', 'MediaView Settings', __('Settings', 'mvcanvas'), 'manage_options', 'mediaview_settings', [$this, 'optionPanel']);
  }

  public function loadJS()
  {
    wp_enqueue_media();
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('mv-admin', plugins_url('../../../public/build/admin.min.js', __FILE__), ['jquery', 'jquery-ui-core', 'jquery-ui-sortable'], DS_MEDIAVIEW_VERSION, true);

    $jsdata = [
      'translations' => [
        'confirmShortcodeDelete' =>  __('Are you sure you want to delete this shortcode?', 'mvcanvas'),
        'confirmItemDelete' => __('Are you sure you want to delete this item?', 'mvcanvas')
      ],
      'nonces' => [
        'deleteShortcode' => wp_create_nonce('mv-delete-shortcode'),
        'deleteItem' => wp_create_nonce('mv-delete-item')
      ]
    ];

    wp_localize_script('mv-admin', 'mvJSData', $jsdata);
  }

  public function loadCSS()
  {
    wp_enqueue_style('mv-style', plugins_url('../../../public/build/admin.min.css', __FILE__), false, DS_MEDIAVIEW_VERSION);
  }

  public function shortcodesPanel()
  {
    global $wpdb;

    $view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : null;
    $id = isset($_GET['id']) ? sanitize_key($_GET['id']) : null;

    if (isset($view)) {
      if ($view == 'addshortcode')
        AddShortcodeView::render();
      else if ($view == 'editshortcode') {
        $shortcode = DatasetRepository::getDataset($id);
        
        if (!isset($shortcode)) {
          _e('Invalid shortcode ID', 'mvcanvas');
          return;
        }

        EditShortcodeView::render($shortcode);
      } else if ($view == 'viewshortcode') {
        $shortcode = DatasetRepository::getDataset($id);
        
        if (!isset($shortcode)) {
          _e('Invalid shortcode ID', 'mvcanvas');
          return;
        }
        
        ShortcodeDetailsView::render($shortcode);
      } else if ($view == 'addmedia')
        AddMediaView::render($id);
      else if ($view == 'edititem') {
        $media = MediaRepository::getMedia($id);

        if (!isset($media)) {
          _e('Invalid media ID', 'mvcanvas');
          return;
        }

        EditMediaView::render($media);
      }
    } else {
      ShortcodeOverviewView::render();
    }
  }

  public function optionPanel()
  {
    $options = get_option('mediaview_main_opts');
    GeneralOptionsView::render($options);
  }

  public function processor()
  {
    new FormProcessor();
  }
}