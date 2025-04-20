<?php

namespace Dscarberry\MediaView\View;

class EditMediaView
{
  public static function render($media)
  {
    $intdata = unserialize($media->MEDIA_VALUE);
    $uploadDir = wp_upload_dir()['baseurl'];

    $youtubeResolutionConfig = '';
    if ($media->MEDIA_TYPE == 'youtube') {
      $opts = [
        ['text' => '480p', 'value' => 'large'],
        ['text' => '720p', 'value' => 'hd720'],
        ['text' => '1080p', 'value' => 'hd1080']
      ];

      $optionsHtml = '';
   
      foreach ($opts as $value) {
        if ($value['value'] == $intdata['hd'])
          $optionsHtml .= '<option value="' . $value['value'] . '" selected>' . $value['text'] . '</option>';
        else
          $optionsHtml .= '<option value="' . $value['value'] . '">' . $value['text'] . '</option>';
      }

      $youtubeResolutionConfig = '<p>
        <label>' . __('Resolution:', 'mvcanvas') . '</label>
        <select name="mediaRes"/>' . $optionsHtml . '</select>
        <span class="mv-hint">' . __('ex: resolution for media item', 'mvcanvas') . '</span>
      </p>';
    }

    $mediaPreview = '';
    if ($media->MEDIA_TYPE == 'youtube' || $media->MEDIA_TYPE == 'vimeo') {
      if ($media->MEDIA_TYPE == 'youtube')
        $iframeurl = 'https://www.youtube.com/embed/' . $intdata['vid'] . '?modestbranding=1&rel=0&showinfo=0&autohide=0&iv_load_policy=3&color=white&theme=dark&autoplay=0';
      else
        $iframeurl = 'https://player.vimeo.com/video/' . $intdata['vid'] . '?&title=0&portrait=0&byline=0badge=0&autoplay=0#';


      $mediaPreview = '<div class="mv-flexvideo">
        <iframe src="' . $iframeurl . '" allowfullscreen></iframe>
      </div>';
    } else 
      $mediaPreview = '<img class="mv-photo-preview" src="' . $intdata['full'] . '"/>';

    echo '<div class="wrap mv-admin">  
      <h2 id="mv-masthead">MediaView ' . __('Shortcodes', 'mvcanvas') . '</h2>
      <div class="mv-left-column">
        <div class="mv-form-box mv-topform-box card">
          <form method="post">
            <h3>' . __('Edit Media Item', 'mvcanvas') . '</h3>
            <p>
              <img src="' . $uploadDir . '/mediaview-cache/' . $intdata['thumb'] . '" class="mv-prev-thumb mv-individual-thumb"/>
            </p>
            <p>
              <label>' . __('Source:', 'mvcanvas') . '</label>
              <input type="text" value="' . ucwords($media->MEDIA_TYPE) . '" tabindex="1" readonly/>
            </p>
            ' . $youtubeResolutionConfig . '
            <p>
              <label>' . __('Caption:', 'mvcanvas') . '</label>
              <input type="text" name="mediaCaption" value="' . $media->MEDIA_CAPTION . '"/>
              <span class="mv-hint">' . __('ex: caption for media item', 'mvcanvas') . '</span>
            </p>
            <p class="submit">
              <input type="hidden" name="id" value="' . $media->MEDIA_ID . '"/>
              <input type="submit" name="mvSaveItemEdit" value="' . __('Save Changes', 'mvcanvas') . '" class="button-primary"/>
              ' . wp_nonce_field('mediaview_edit_item') . '
              <a href="?page=mediaview&view=viewshortcode&id=' . $media->DATA_ID . '" class="mv-cancel">' . __('Go Back', 'mvcanvas') . '</a>
            </p>
          </form>
        </div>
      </div>
      <div class="mv-right-column">
        <div class="mv-form-box mv-topform-box mv-centeralign-box card">' . $mediaPreview . '</div>
      </div>
    </div>';
  }
}