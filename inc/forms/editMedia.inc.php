<?php

$key = sanitize_text_field($_GET['id']);
$dir = wp_upload_dir();
$dir = $dir['baseurl'];

$data = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'mediaview_media WHERE MEDIA_ID = ' . $key, ARRAY_A);

if (!isset($data[0]))
{
  _e('Invalid media ID', 'mvcanvas');
  return;
}

$data = $data[0];
$intdata = unserialize($data['MEDIA_VALUE']);

?>

<div class="mv-left-column">
  <div class="mv-form-box mv-topform-box card">
    <form method="post">
      <h3><?php _e('Edit Media Item', 'mvcanvas'); ?></h3>
      <p>
        <img src="<?php echo $dir . '/mediaview-cache/' . $intdata['thumb']; ?>" class="mv-prev-thumb mv-individual-thumb"/>
      </p>
      <p>
        <label><?php _e('Source:', 'mvcanvas'); ?></label>
        <input type="text" value="<?php echo ucwords($data['MEDIA_TYPE']); ?>" tabindex="1" readonly/>
      </p>

      <?php
      if ($data['MEDIA_TYPE'] == 'youtube')
      {
      ?>

        <p>
          <label><?php _e('Resolution:', 'mvcanvas'); ?></label>
          <select name="mediaRes"/>

          <?php

          $opts = array(array('text' => '480p', 'value' => 'large'), array('text' => '720p', 'value' => 'hd720'), array('text' => '1080p', 'value' => 'hd1080'));

          foreach ($opts as $value)
          {
            if ($value['value'] == $intdata['hd'])
              echo '<option value="' . $value['value'] . '" selected>' . $value['text'] . '</option>';
            else
              echo '<option value="' . $value['value'] . '">' . $value['text'] . '</option>';
          }

          ?>

        </select>
        <span class="mv-hint"><?php _e('ex: resolution for media item', 'mvcanvas'); ?></span>
      </p>

    <?php
    }
    ?>

    <p>
      <label><?php _e('Caption:', 'mvcanvas'); ?></label>
      <input type="text" name="caption" value="<?php echo $data['MEDIA_CAPTION']; ?>"/>
      <span class="mv-hint"><?php _e('ex: caption for media item', 'mvcanvas'); ?></span>
    </p>
    <p class="submit">
      <input type="hidden" name="key" value="<?php echo $key; ?>"/>
      <input type="submit" name="mvSaveItemEdit" value="<?php _e('Save Changes', 'mvcanvas') ?>" class="button-primary"/>
      <?php wp_nonce_field('mediaview_edit_item'); ?>
      <a href="?page=mediaview&view=viewshortcode&id=<?php echo $data['DATA_ID']; ?>" class="mv-cancel"><?php _e('Go Back', 'mvcanvas'); ?></a>
    </p>
  </form>
</div>
</div>
<div class="mv-right-column">
  <div class="mv-form-box mv-topform-box mv-centeralign-box card">

    <?php

    if ($data['MEDIA_TYPE'] == 'youtube' || $data['MEDIA_TYPE'] == 'vimeo')
    {
      if ($data['MEDIA_TYPE'] == 'youtube')
        $iframeurl = 'https://www.youtube.com/embed/' . $intdata['vid'] . '?modestbranding=1&rel=0&showinfo=0&autohide=0&iv_load_policy=3&color=white&theme=dark&autoplay=0';
      else
        $iframeurl = 'https://player.vimeo.com/video/' . $intdata['vid'] . '?&title=0&portrait=0&byline=0badge=0&autoplay=0#';

      ?>

        <div class="mv-flexvideo">
          <iframe src="<?php echo $iframeurl; ?>" allowfullscreen></iframe>
        </div>

      <?php
    }
    else
    {
    ?>

      <img class="mv-photo-preview" src="<?php echo $intdata['full']; ?>"/>

    <?php
    }
    ?>
        
  </div>
</div>
