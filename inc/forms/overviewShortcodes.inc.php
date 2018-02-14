<?php

require_once(dirname(__FILE__) . '/../../class/mvShortcodeListTable.class.php');

$shortcodes = new mvShortcodeListTable();
$shortcodes->prepare_items();

?>

<div class="mv-form-box">
  <p class="mv-actionbar">
    <a href="?page=mediaview&view=addshortcode" class="mv-link-submit-button"><?php _e('Create Shortcode', 'mvcanvas'); ?></a>
  </p>
  <h3>Shortcodes</h3>
  <form method="post">

    <?php $shortcodes->display(); ?>

  </form>
</div>
<div class="postbox">
  <h3 class="hndle mv-post-box"><span><?php _e("FAQ's", "mvcanvas"); ?></span></h3>
  <div class="inside">
    <div class="mv-form-box">
      <ul>
        <li>For assistance with this plugin send an email to <a href="mailto:dustin@codeclouds.net" target="_blank">dustin@codeclouds.net</a>. Please, if possible, include a working link to the webpage where you are having an issue.</li>
      </ul>
    </div>
  </div>
</div>
