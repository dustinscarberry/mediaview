<?php

namespace Dscarberry\MediaView\View;

class ShortcodeView
{
  public static function render($dataset, $media, $mvOptions)
  {
    $uploadDir = wp_upload_dir()['baseurl'];

    // get playerWidth
    $playerWidth = round(intval($baseData['DATA_DISPLAYHEIGHT']) * 1.77);

    // default value if needed
    if ($playerWidth == null || $playerWidth == 0)
      $playerWidth = 708;

    $content = '
      <div class="mv-gallery" data-viewheight="' . $dataset->DATA_DISPLAYHEIGHT . '" data-canvascolor="' . $dataset->DATA_CANVASCOLOR . '" data-captioncolor="' . $dataset->DATA_CAPTIONCOLOR . '">
        <div class="mv-view">
          <a href="#" class="mv-view-full"></a>
          <a href="#" class="mv-view-thumbs mv-view-selected"></a>
        </div>
        <div class="mv-canvas">
          <div class="mv-canvas-inner" style="max-width: ' . $playerWidth . 'px; max-height: ' . $dataset->DATA_DISPLAYHEIGHT . 'px;">
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

    foreach ($media as $item) {
      $data = unserialize($item->MEDIA_VALUE);
      $caption = $item->MEDIA_CAPTION;

      if ($item->MEDIA_TYPE == 'vimeo') {
        $internval = $data['vid'];
        $controlcolor = str_replace('#', '', $mvOptions['vimeoControlColor']);

        $intern = "https://player.vimeo.com/video/" . $internval . "?title=0&color=" . $controlcolor;

        $content .= '<li><a href="#" style="background-image: url(' . $uploadDir . '/mediaview-cache/' . $data['thumb'] . ');" data-type="iframe" data-large="' . $intern . '"' . ($caption ? 'data-description="' . $caption . '"' : '') . '><span class="mv-video"></span></a></li>';
      } else if ($item->MEDIA_TYPE == 'youtube') {
        $internval = $data['vid'];
        $hd = $data['hd'];
        $hidecontrols = ($mvOptions['ytHideControls'] == 'yes' ? 0 : 1);
        $controlcolor = $mvOptions['ytProgressColor'];

        $intern = "https://www.youtube.com/embed/" . $internval . "?iv_load_policy=3&autoplay=0&autohide=1&showinfo=0&controls=" . $hidecontrols . "&color=" . $controlcolor . "&rel=0&vq=" . $hd;
        $content .= '<li><a href="#" style="background-image: url(' . $uploadDir . '/mediaview-cache/' . $data['thumb'] . ');" data-type="iframe" data-large="' . $intern . '"' . ($caption ? 'data-description="' . $caption . '"' : '') . '><span class="mv-video"></span></a></li>';
      } else if ($item->MEDIA_TYPE == 'photo') {
        $thumb = $data['thumb'];
        $full = $data['full'];

        $content .= '<li><a href="#" style="background-image: url(' . $uploadDir . '/mediaview-cache/' . $thumb . ');" data-large="' . $full . '"' . ($caption ? 'data-description="' . $caption . '"' : '') . '></a></li>';
      }
    }

    $content .= '
              </ul>
            </div>
          </div>
        </div>
      </div>';

    return $content;
  }
}