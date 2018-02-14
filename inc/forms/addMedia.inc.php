<div class="mv-left-column">
  <div class="mv-form-box mv-topform-box card">
    <form method="post" id="mvSaveMediaFrm">
      <h3><?php _e('Add Media Item', 'mvcanvas'); ?></h3>
      <p>
        <label><?php _e('Media Type:', 'mvcanvas'); ?></label>
        <select name="mediaType" id="mediaType">
          <option></option>
          <option value="vimeo">Vimeo</option>
          <option value="youtube">Youtube</option>
          <option value="photo">Photo</option>
        </select>
        <span class="mv-hint"><?php _e('ex: type of media to add', 'mvcanvas'); ?></span>
      </p>
      <p id="vimeoURLBox" class="mv-invis">
        <label><?php _e('Video URL:', 'mvcanvas'); ?></label>
        <input type="text" name="vimeoMediaURL" id="vimeoURLInput"/>
        <span class="mv-hint"><?php _e('ex: url of video such as - http://vimeo.com/xxxxxxxx', 'mvcanvas'); ?></span>
      </p>
      <p id="youtubeURLBox" class="mv-invis">
        <label><?php _e('Video URL:', 'mvcanvas'); ?></label>
        <input type="text" name="youtubeMediaURL" id="youtubeURLInput"/>
        <span class="mv-hint"><?php _e('ex: url of video such as - http://www.youtube.com/watch?v=xxxxxxxxxxxx', 'mvcanvas'); ?></span>
      </p>
      <p id="photoURLBox" class="mv-invis">
        <label><?php _e('Photo URL:', 'mvcanvas'); ?></label>
        <input type="text" name="photoMediaURL" id="photoURLInput" readonly/>
        <span class="mv-hint"><?php _e('ex: photo that you want to add', 'mvcanvas'); ?></span>
      </p>
      <p id="resBox" class="mv-invis">
        <label><?php _e('Resolution:', 'mvcanvas'); ?></label>
        <select name="mediaRes">
          <option value="large">480p</option>
          <option value="hd720">720p</option>
          <option value="hd1080">1080p</option>
        </select>
        <span class="mv-hint"><?php _e('ex: resolution of video', 'mvcanvas'); ?></span>
      </p>
      <p>
        <label><?php _e('Caption:', 'mvcanvas'); ?></label>
        <input type="text" name="mediaCaption"/>
        <span class="mv-hint"><?php _e('ex: an optional caption', 'mvcanvas'); ?></span>
      </p>
      <p class="submit">
        <input type="submit" id="mvSaveMedia" name="mvSaveMedia" value="<?php _e('Save Media','mvcanvas') ?>" class="button-primary" disabled/>
        <input type="hidden" name="key" value="<?php echo $_GET['id']; ?>"/>
        <?php wp_nonce_field('mediaview_save_media'); ?>
        <a href="?page=mediaview&view=viewshortcode&id=<?php echo $_GET['id']; ?>" class="mv-cancel"><?php _e('Go Back', 'mvcanvas'); ?></a>
      </p>
    </form>
  </div>
</div>
