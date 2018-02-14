<div id="mv-create-shortcode-form" class="mv-left-column">
  <div class="mv-form-box mv-topform-box card">
    <form method="post" id="mvSaveShortcodeFrm">
      <h3><?php _e('Create Shortcode', 'mvcanvas'); ?></h3>
      <p>
        <label><?php _e('Shortcode Name:', 'mvcanvas'); ?></label>
        <input type="text" name="shortName" id="shortName"/>
        <span class="mv-hint"><?php _e('ex: name of shortcode for your reference', 'mvcanvas'); ?></span>
      </p>
      <p>
        <label><?php _e('Display Height:', 'mvcanvas'); ?></label>
        <input type="number" id="displayHeight" name="displayHeight" value="400"/>
        <button id="resetHeight" class="button-secondary"><?php _e('Reset', 'mvcanvas'); ?></button>
        <span class="mv-hint"><?php _e('ex: max height of media viewer (does not include caption)', 'mvcanvas'); ?></span>
      </p>
      <p>
        <label><?php _e('Canvas Color:', 'mvcanvas'); ?></label>
        <input type="text" id="canvasColor" name="canvasColor" value="#111"/>
        <button id="resetCanvasColor" class="button-secondary"><?php _e('Reset', 'mvcanvas'); ?></button>
        <span class="mv-hint"><?php _e('ex: color of media view canvas (any hex value with #)', 'mvcanvas'); ?></span>
      </p>
      <p>
        <label><?php _e('Caption Text Color:', 'mvcanvas'); ?></label>
        <input type="text" id="captionColor" name="captionColor" value="#fff"/>
        <button id="resetCaptionColor" class="button-secondary"><?php _e('Reset', 'mvcanvas'); ?></button>
        <span class="mv-hint"><?php _e('ex: color of caption text (any hex value with #)', 'mvcanvas'); ?></span>
      </p>
      <p class="submit">
        <input type="submit" name="mvSaveShortcode" id="mvSaveShortcode" value="<?php _e('Save Shortcode', 'mvcanvas') ?>" class="button-primary"/>
        <?php wp_nonce_field('mediaview_save_shortcode'); ?>
        <a href="?page=mediaview" class="mv-cancel"><?php _e('Go Back', 'mvcanvas'); ?></a>
      </p>
    </form>
  </div>
</div>
