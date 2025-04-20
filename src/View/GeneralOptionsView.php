<?php

namespace Dscarberry\MediaView\View;

class GeneralOptionsView
{
  public static function render($mvOptions)
  {   
    $videoPlayerControlColorOptions = '';

    $opts = [
      ['text' => __('Red', 'mvcanvas'), 'value' => 'red'],
      ['text' => __('White', 'mvcanvas'), 'value' => 'white']
    ];
    
    foreach ($opts as $value) {
      if ($value['value'] == $mvOptions['ytProgressColor'])
        $videoPlayerControlColorOptions .= '<option value="' . $value['value'] . '" selected>' . $value['text'] . '</option>';
      else
        $videoPlayerControlColorOptions .= '<option value="' . $value['value'] . '">' . $value['text'] . '</option>';
    }

    echo '<div class="wrap">
      <h2 id="mv-masthead">MediaView Settings</h2>
      <div class="mv-left-column mv-options-column">
        <div class="mv-form-box mv-topform-box card">
          <form method="post" id="mvSaveOptionsFrm">
            <h3>Youtube Settings</h3>
            <p>
              <label>' . __('Hide Controls:', 'mvcanvas') . '</label>
              <input type="checkbox" name="ytHideControls" ' . ($mvOptions['ytHideControls'] == 'yes' ? 'checked' : '') . '/>
              <span class="mv-hint">' . __('ex: check to hide video player controls', 'mvcanvas') . '</span>
            </p>
            <p>
              <label>' . __('Video Player Controlbar Color:', 'mvcanvas') . '</label>
              <select name="ytProgressColor">' . $videoPlayerControlColorOptions . ' </select>
              <span class="mv-hint">' . __("ex: set the color of the player's progress bar", "mvcanvas") . '</span>
            </p>
            <hr/>
            <h3>Vimeo Settings</h3>
            <p>
              <label>' . __('Video Controls Color:', 'mvcanvas') . '</label>
              <input type="text" name="vimeoControlColor" id="vimeoControlColor" value="' . $mvOptions['vimeoControlColor'] . '"/>
              <button id="resetColor" class="button-secondary">' . __('Reset', 'mvcanvas') . '</button>
              <span class="mv-hint">' . __('ex: color for video player controls (any hex value with #)', 'mvcanvas') . '</span>
            </p>
            <p class="submit">
              <input type="submit" name="mvSaveOptions" id="mvSaveOptions" value="' . __('Save Changes', 'mvcanvas') . '" class="button-primary"/>'
              . wp_nonce_field('mediaview_update_options') . '
            </p>
          </form>
        </div>
      </div>
    </div>';
  }
}