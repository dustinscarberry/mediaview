<?php
/**
 * mediaViewFrontend - Frontend section for mediaView
 *
 * @package mediaView
 * @author Dustin Scarberry
 *
 * @since 1.0
 */

if (!class_exists('mediaViewFrontend'))
{
  class mediaViewFrontend
  {
    private $_options, $_scripts, $_version;

    public function __construct($version)
    {
      //get plugin options
      $this->_options = get_option('mediaview_main_opts');

      //set version
      $this->_version = $version;

      //add hooks
      add_shortcode('mediaview', array($this, 'shortcode'));
      add_action('wp_enqueue_scripts', array($this, 'addIncludes'));
    }

    public function addIncludes()
    {
      //load jquery and mediagallery js / css
      wp_enqueue_script('jquery');
      wp_enqueue_script('mv_mediagallery_script', plugins_url('responsive-media-gallery/media-slider.min.js', __FILE__), array('jquery'), $this->_version, true);
      wp_enqueue_style('mv_mediagallery_style', plugins_url('responsive-media-gallery/style.min.css', __FILE__), false, $this->_version);
      wp_enqueue_script('mv-frontend', plugins_url('js/frontend.min.js', __FILE__), array('mv_mediagallery_script'), $this->_version, true);
    }

    public function shortcode($atts)
    {
      global $wpdb;

      if (!isset($atts['id']))
        return '<span class="mv-invalid-code">Invalid Shortcode ID</span>';

      $id = sanitize_text_field($atts['id']);

      $dir = wp_upload_dir();
      $dir = $dir['baseurl'];

      $baseData = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'mediaview_dataset WHERE DATA_ID = ' . $id, ARRAY_A);
      $mediaData = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'mediaview_media WHERE DATA_ID = "' . $id . '" ORDER BY MEDIA_POS', ARRAY_A);

      if ($baseData == null || $mediaData == null)
        return '<span class="mv-invalid-code">Invalid Shortcode ID</span>';

      $baseData = $baseData[0];
      $playerWidth = round(intval($baseData['DATA_DISPLAYHEIGHT']) * 1.77);

      //default in case no valid height value given
      if ($playerWidth == null || $playerWidth == 0)
        $playerWidth = 708;

      $content = '
        <div class="mv-gallery" data-viewheight="' . $baseData['DATA_DISPLAYHEIGHT'] . '" data-canvascolor="' . $baseData['DATA_CANVASCOLOR'] . '" data-captioncolor="' . $baseData['DATA_CAPTIONCOLOR'] . '">
          <div class="mv-view">
            <a href="#" class="mv-view-full"></a>
            <a href="#" class="mv-view-thumbs mv-view-selected"></a>
          </div>
          <div class="mv-canvas">
            <div class="mv-canvas-inner" style="max-width: ' . $playerWidth . 'px; max-height: ' . $baseData['DATA_DISPLAYHEIGHT'] . 'px;">
              <div class="mv-iframe-wrapper">
                <iframe class="mv-iframe" src="" frameborder="0" allowfullscreen></iframe>
              </div>
              <img class="mv-photo" src="">
            </div>
            <div class="mv-loader"></div>
            <div class="mv-caption"></div>
          </div>
          <div class="mv-thumbs">
            <div class="mv-carousel-wrapper">
              <div class="mv-carousel">
                <ul>';

      //get data and process
      foreach ($mediaData as $val)
      {
        $data = unserialize($val['MEDIA_VALUE']);
        $caption = $val['MEDIA_CAPTION'];

        if ($val['MEDIA_TYPE'] == 'vimeo')
        {
          $internval = $data['vid'];
          $controlcolor = str_replace('#', '', $this->_options['vimeoControlColor']);

          $intern = "https://player.vimeo.com/video/" . $internval . "?title=0&color=" . $controlcolor;

          $content .= '<li><a href="#" style="background-image: url(' . $dir . '/mediaview-cache/' . $data['thumb'] . ');" data-type="iframe" data-large="' . $intern . '"' . ($caption ? 'data-description="' . $caption . '"' : '') . '><span class="mv-video"></span></a></li>';
        }
        elseif ($val['MEDIA_TYPE'] == 'youtube')
        {
          $internval = $data['vid'];
          $hd = $data['hd'];
          $hidecontrols = ($this->_options['ytHideControls'] == 'yes' ? 0 : 1);
          $controlcolor = $this->_options['ytProgressColor'];

          $intern = "https://www.youtube.com/embed/" . $internval . "?iv_load_policy=3&autoplay=0&autohide=1&showinfo=0&controls=" . $hidecontrols . "&color=" . $controlcolor . "&rel=0&vq=" . $hd;
          $content .= '<li><a href="#" style="background-image: url(' . $dir . '/mediaview-cache/' . $data['thumb'] . ');" data-type="iframe" data-large="' . $intern . '"' . ($caption ? 'data-description="' . $caption . '"' : '') . '><span class="mv-video"></span></a></li>';
        }
        elseif ($val['MEDIA_TYPE'] == 'photo')
        {
          $thumb = $data['thumb'];
          $full = $data['full'];

          $content .= '<li><a href="#" style="background-image: url(' . $dir . '/mediaview-cache/' . $thumb . ');" data-large="' . $full . '"' . ($caption ? 'data-description="' . $caption . '"' : '') . '></a></li>';
        }
      }

      $content .= '
                </ul>
              </div>
            </div>
          </div>
        </div>';

      //return html
      return $content;
    }
  }
}
?>
