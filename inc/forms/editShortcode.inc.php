<?php

$key = sanitize_text_field($_GET['id']);
$data = $wpdb->get_results('SELECT DATA_NAME, DATA_DISPLAYHEIGHT, DATA_CANVASCOLOR, DATA_CAPTIONCOLOR FROM ' . $wpdb->prefix . 'mediaview_dataset WHERE DATA_ID = ' . $key, ARRAY_A);

if (!isset($data[0]))
{
  _e('Invalid shortcode ID', 'mvcanvas');
  return;
}

$data = $data[0];

?>

<div id="mv-edit-shortcode-form" class="mv-left-column">
  <div class="mv-form-box mv-topform-box card">
    <form method="post" id="mvSaveShortcodeEditFrm">
      <h3><?php _e('Edit Shortcode', 'mvcanvas'); ?></h3>
      <p>
        <label><?php _e('Shortcode Name:', 'mvcanvas'); ?></label>
        <input type="text" name="shortName" id="shortName" value="<?php echo $data['DATA_NAME']; ?>"/>
        <span class="mv-hint"><?php _e('ex: name of shortcode', 'mvcanvas'); ?></span>
      </p>
      <p>
        <label><?php _e('Display Height:', 'mvcanvas'); ?></label>
        <input type="number" id="displayHeight" name="displayHeight" value="<?php echo $data['DATA_DISPLAYHEIGHT']; ?>"/>
        <button id="resetHeight" class="button-secondary"><?php _e('Reset', 'mvcanvas'); ?></button>
        <span class="mv-hint"><?php _e('ex: max height of media viewer (does not include caption)', 'mvcanvas'); ?></span>
      </p>
      <p>
        <label><?php _e('Canvas Color:', 'mvcanvas'); ?></label>
        <input type="text" id="canvasColor" name="canvasColor" value="<?php echo $data['DATA_CANVASCOLOR']; ?>"/>
        <button id="resetCanvasColor" class="button-secondary"><?php _e('Reset', 'mvcanvas'); ?></button>
        <span class="mv-hint"><?php _e('ex: color of media view canvas (any hex value with #)', 'mvcanvas'); ?></span>
      </p>
      <p>
        <label><?php _e('Caption Text Color:', 'mvcanvas'); ?></label>
        <input type="text" id="captionColor" name="captionColor" value="<?php echo $data['DATA_CAPTIONCOLOR']; ?>"/>
        <button id="resetCaptionColor" class="button-secondary"><?php _e('Reset', 'mvcanvas'); ?></button>
        <span class="mv-hint"><?php _e('ex: color of caption text (any hex value with #)', 'mvcanvas'); ?></span>
      </p>
      <p class="submit">
        <input type="hidden" name="key" value="<?php echo $key; ?>"/>
        <input type="submit" name="mvSaveShortcodeEdit" id="mvSaveShortcodeEdit" value="<?php _e('Save Changes', 'mvcanvas') ?>" class="button-primary"/>
        <?php wp_nonce_field('mediaview_edit_shortcode'); ?>
        <a href="?page=mediaview" class="mv-cancel"><?php _e('Go Back', 'mvcanvas'); ?></a>
      </p>
    </form>
  </div>
</div>
