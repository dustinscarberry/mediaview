<?php

namespace Dscarberry\MediaView\View;

class EditShortcodeView
{
  public static function render($shortcode)
  {
    echo '<div class="wrap mv-admin">  
      <h2 id="mv-masthead">MediaView ' . __('Shortcodes', 'mvcanvas') . '</h2>
      <div id="mv-edit-shortcode-form" class="mv-left-column">
        <div class="mv-form-box mv-topform-box card">
          <form method="post" id="mvSaveShortcodeEditFrm">
            <h3>' . __('Edit Shortcode', 'mvcanvas') . '</h3>
            <p>
              <label>' . __('Shortcode Name:', 'mvcanvas') . '</label>
              <input type="text" name="shortName" id="shortName" value="' . $shortcode->DATA_NAME . '"/>
              <span class="mv-hint">' . __('ex: name of shortcode', 'mvcanvas') . '</span>
            </p>
            <p>
              <label>' . __('Display Height:', 'mvcanvas') . '</label>
              <input type="number" id="displayHeight" name="displayHeight" value="' . $shortcode->DATA_DISPLAYHEIGHT . '"/>
              <button id="resetHeight" class="button-secondary">' . __('Reset', 'mvcanvas') . '</button>
              <span class="mv-hint">' . __('ex: max height of media viewer (does not include caption)', 'mvcanvas') . '</span>
            </p>
            <p>
              <label>' . __('Canvas Color:', 'mvcanvas') . '</label>
              <input type="text" id="canvasColor" name="canvasColor" value="' . $shortcode->DATA_CANVASCOLOR . '"/>
              <button id="resetCanvasColor" class="button-secondary">' . __('Reset', 'mvcanvas') . '</button>
              <span class="mv-hint">' . __('ex: color of media view canvas (any hex value with #)', 'mvcanvas') . '</span>
            </p>
            <p>
              <label>' . __('Caption Text Color:', 'mvcanvas') . '</label>
              <input type="text" id="captionColor" name="captionColor" value="' . $shortcode->DATA_CAPTIONCOLOR . '"/>
              <button id="resetCaptionColor" class="button-secondary">' . __('Reset', 'mvcanvas') . '</button>
              <span class="mv-hint">' . __('ex: color of caption text (any hex value with #)', 'mvcanvas') . '</span>
            </p>
            <p class="submit">
              <input type="hidden" name="key" value="' . $shortcode->DATA_ID . '"/>
              <input type="submit" name="mvSaveShortcodeEdit" id="mvSaveShortcodeEdit" value="' . __('Save Changes', 'mvcanvas') . '" class="button-primary"/>
              ' . wp_nonce_field('mediaview_edit_shortcode') . '
              <a href="?page=mediaview" class="mv-cancel">' . __('Go Back', 'mvcanvas') . '</a>
            </p>
          </form>
        </div>
      </div>
    </div>';
  }
}