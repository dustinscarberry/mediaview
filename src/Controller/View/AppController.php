<?php

namespace Dscarberry\MediaView\Controller\View;

use Dscarberry\MediaView\Repository\DatasetRepository;
use Dscarberry\MediaView\Repository\MediaRepository;
use Dscarberry\MediaView\View\InvalidShortcodeView;
use Dscarberry\MediaView\View\ShortcodeView;

class AppController
{
  private $options;

  function __construct()
  {
    // load plugin options
    $this->options = get_option('mediaview_main_opts');

    // add hooks
    add_shortcode('mediaview', [$this, 'shortcode']);

    // add scripts
    add_action('wp_enqueue_scripts', [$this, 'loadJS']);
    add_action('wp_enqueue_scripts', [$this, 'loadCSS']);
  }

  public function loadJS()
  {
    wp_enqueue_script('jquery');
    wp_enqueue_script('mv_mediagallery_script', plugins_url('../../../public/static/media-slider.min.js', __FILE__), ['jquery'], DS_MEDIAVIEW_VERSION, true);
    wp_enqueue_script('mv-frontend', plugins_url('../../../public/build/app.min.js', __FILE__), ['mv_mediagallery_script'], DS_MEDIAVIEW_VERSION, true);
  }

  public function loadCSS()
  {
    wp_enqueue_style('mv-app-css', plugins_url('../../../public/build/app.min.css', __FILE__), false, DS_MEDIAVIEW_VERSION);
  }

  public function shortcode($atts)
  {
    global $wpdb;

    if (!isset($atts['id']))
      return InvalidShortcodeView::render();
      
    $id = sanitize_text_field($atts['id']);

    $dataset = DatasetRepository::getDataset($id);
    $media = MediaRepository::getMediaByDataset($id);

    if ($dataset == null || $media == null)
      return InvalidShortcodeView::render();

    return ShortcodeView::render($dataset, $media, $this->options);
  }
}