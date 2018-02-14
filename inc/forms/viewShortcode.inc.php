<?php

$id = sanitize_text_field($_GET['id']);
$data = $wpdb->get_results('SELECT DATA_NAME, DATA_MEDIACOUNT	 FROM ' . $wpdb->prefix . 'mediaview_dataset WHERE DATA_ID = "' . $id . '"', ARRAY_A);

if (!isset($data[0]))
{
  _e('Invalid shortcode ID', 'mvcanvas');
  return;
}

$data = $data[0];

require_once(dirname(__FILE__) . '/../../class/mvMediaItemListTable.class.php');

$mediaItems = new mvMediaItemListTable($id);
$mediaItems->prepare_items();

?>

<div class="mv-form-box mv-topform-box">
  <p class="mv-action-bar">
    <a href="?page=mediaview&view=addmedia&id=<?php echo $id; ?>" class="mv-link-submit-button"><?php _e('Add Media Item', 'mvcanvas'); ?></a>
    <a href="?page=mediaview" class="mv-cancel"><?php _e('Go Back', 'mvcanvas'); ?></a>
  </p>
  <h3><?php _e('Media Items', 'mvcanvas'); ?><span class="mv-sub-H3"> ( <?php echo $data['DATA_NAME']; ?> ) - <?php echo $data['DATA_MEDIACOUNT'] . ' ' . __('items', 'mvcanvas'); ?></span></h3>
  <form method="post">

    <?php echo $mediaItems->display(); ?>

  </form>
</div>
