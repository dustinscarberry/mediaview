<?php

//reload options to be sure correct options are displayed
$this->_options = get_option('mediaview_main_opts');

?>

<div class="wrap">
  <h2 id="mv-masthead">MediaView Settings</h2>
  <div class="mv-left-column mv-options-column">
    <div class="mv-form-box mv-topform-box card">
      <form method="post" id="mvSaveOptionsFrm">
        <h3>Youtube Settings</h3>
        <p>
          <label><?php _e('Hide Controls:', 'mvcanvas'); ?></label>
          <input type="checkbox" name="ytHideControls" <?php echo ($this->_options['ytHideControls'] == 'yes' ? 'checked' : ''); ?>/>
          <span class="mv-hint"><?php _e('ex: check to hide video player controls', 'mvcanvas'); ?></span>
        </p>
        <p>
          <label><?php _e('Video Player Controlbar Color:', 'mvcanvas'); ?></label>
          <select name="ytProgressColor">

            <?php

            $opts = array(array('text' => __('Red', 'mvcanvas'), 'value' => 'red'), array('text' => __('White', 'mvcanvas'), 'value' => 'white'));

            foreach ($opts as $value)
            {
              if ($value['value'] == $this->_options['ytProgressColor'])
                echo '<option value="' . $value['value'] . '" selected>' . $value['text'] . '</option>';
              else
                echo '<option value="' . $value['value'] . '">' . $value['text'] . '</option>';
            }

            ?>

          </select>
          <span class="mv-hint"><?php _e("ex: set the color of the player's progress bar", "mvcanvas"); ?></span>
        </p>
        <hr/>
        <h3>Vimeo Settings</h3>
        <p>
          <label><?php _e('Video Controls Color:', 'mvcanvas'); ?></label>
          <input type="text" name="vimeoControlColor" id="vimeoControlColor" value="<?php echo $this->_options['vimeoControlColor']; ?>"/>
          <button id="resetColor" class="button-secondary"><?php _e('Reset', 'mvcanvas'); ?></button>
          <span class="mv-hint"><?php _e('ex: color for video player controls (any hex value with #)', 'mvcanvas'); ?></span>
        </p>
        <p class="submit">
          <input type="submit" name="mvSaveOptions" id="mvSaveOptions" value="<?php _e('Save Changes', 'mvcanvas') ?>" class="button-primary"/>
          <?php wp_nonce_field('mediaview_update_options'); ?>
        </p>
      </form>
    </div>
  </div>
</div>
