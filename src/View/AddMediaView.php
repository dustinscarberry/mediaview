<?php

namespace Dscarberry\MediaView\View;

class AddMediaView
{
  public static function render($shortcodeId)
  {
    echo '<div class="wrap mv-admin">
      <h2 id="mv-masthead">MediaView ' . __('Shortcodes', 'mvcanvas') . '</h2>
      <div class="mv-left-column">
        <div class="mv-form-box mv-topform-box card">
          <form method="post" id="mvSaveMediaFrm">
            <h3>' . __('Add Media Item', 'mvcanvas') . '</h3>
            <p>
              <label>' . __('Media Type:', 'mvcanvas') . '</label>
              <select name="mediaType" id="mediaType">
                <option></option>
                <option value="vimeo">Vimeo</option>
                <option value="youtube">Youtube</option>
                <option value="photo">Photo</option>
              </select>
              <span class="mv-hint">' . __('ex: type of media to add', 'mvcanvas') . '</span>
            </p>
            <p id="vimeoURLBox" class="mv-invis">
              <label>' . __('Video URL:', 'mvcanvas') . '</label>
              <input type="text" name="vimeoMediaURL" id="vimeoURLInput"/>
              <span class="mv-hint">' . __('ex: url of video such as - http://vimeo.com/xxxxxxxx', 'mvcanvas') . '</span>
            </p>
            <p id="youtubeURLBox" class="mv-invis">
              <label>' . __('Video URL:', 'mvcanvas') . '</label>
              <input type="text" name="youtubeMediaURL" id="youtubeURLInput"/>
              <span class="mv-hint">' . __('ex: url of video such as - http://www.youtube.com/watch?v=xxxxxxxxxxxx', 'mvcanvas') . '</span>
            </p>
            <p id="photoURLBox" class="mv-invis">
              <label>' . __('Photo URL:', 'mvcanvas') . '</label>
              <input type="text" name="photoMediaURL" id="photoURLInput" readonly/>
              <span class="mv-hint">' . __('ex: photo that you want to add', 'mvcanvas') . '</span>
            </p>
            <p id="resBox" class="mv-invis">
              <label>' . __('Resolution:', 'mvcanvas') . '</label>
              <select name="mediaRes">
                <option value="large">480p</option>
                <option value="hd720">720p</option>
                <option value="hd1080">1080p</option>
              </select>
              <span class="mv-hint">' . __('ex: resolution of video', 'mvcanvas') . '</span>
            </p>
            <p>
              <label>' . __('Caption:', 'mvcanvas') . '</label>
              <input type="text" name="mediaCaption"/>
              <span class="mv-hint">' . __('ex: an optional caption', 'mvcanvas') . '</span>
            </p>
            <p class="submit">
              <input type="submit" id="mvSaveMedia" name="mvSaveMedia" value="' . __('Save Media','mvcanvas') . '" class="button-primary" disabled/>
              <input type="hidden" name="shortcodeId" value="' . $shortcodeId . '"/>
              ' . wp_nonce_field('mediaview_save_media') . '
              <a href="?page=mediaview&view=viewshortcode&id=' . $shortcodeId . '" class="mv-cancel">' . __('Go Back', 'mvcanvas') . '</a>
            </p>
          </form>
        </div>
      </div>
    </div>';
  }
}