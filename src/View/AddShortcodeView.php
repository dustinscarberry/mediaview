<?php

namespace Dscarberry\MediaView\View;

class AddShortcodeView
{
  public static function render()
  {
    echo '<div class="wrap mv-admin">
      <h2 id="mv-masthead">MediaView ' . __('Shortcodes', 'mvcanvas') . '</h2>
      <div id="mv-create-shortcode-form" class="mv-left-column">
        <div class="mv-form-box mv-topform-box card">
          <form method="post" id="mvSaveShortcodeFrm">
            <h3>' . __('Create Shortcode', 'mvcanvas') . '</h3>
            <p>
              <label>' . __('Shortcode Name:', 'mvcanvas') . '</label>
              <input type="text" name="shortName" id="shortName"/>
              <span class="mv-hint">' . __('ex: name of shortcode for your reference', 'mvcanvas') . '</span>
            </p>
            <p>
              <label>' . __('Display Height:', 'mvcanvas') . '</label>
              <input type="number" id="displayHeight" name="displayHeight" value="400"/>
              <button id="resetHeight" class="button-secondary">' . __('Reset', 'mvcanvas') . '</button>
              <span class="mv-hint">' . __('ex: max height of media viewer (does not include caption)', 'mvcanvas') . '</span>
            </p>
            <p>
              <label>' . __('Canvas Color:', 'mvcanvas') . '</label>
              <input type="text" id="canvasColor" name="canvasColor" value="#111"/>
              <button id="resetCanvasColor" class="button-secondary">' . __('Reset', 'mvcanvas') . '</button>
              <span class="mv-hint">' . __('ex: color of media view canvas (any hex value with #)', 'mvcanvas') . '</span>
            </p>
            <p>
              <label>' . __('Caption Text Color:', 'mvcanvas') . '</label>
              <input type="text" id="captionColor" name="captionColor" value="#fff"/>
              <button id="resetCaptionColor" class="button-secondary">' . __('Reset', 'mvcanvas') . '</button>
              <span class="mv-hint">' . __('ex: color of caption text (any hex value with #)', 'mvcanvas') . '</span>
            </p>
            <p class="submit">
              <input type="submit" name="mvSaveShortcode" id="mvSaveShortcode" value="' . __('Save Shortcode', 'mvcanvas') . '" class="button-primary"/>
              ' . wp_nonce_field('mediaview_save_shortcode') . '
              <a href="?page=mediaview" class="mv-cancel">' . __('Go Back', 'mvcanvas') . '</a>
            </p>
          </form>
        </div>
      </div>
    </div>';
  }
}