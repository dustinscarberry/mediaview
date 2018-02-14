<?php

class mvAdminAjax
{
  private $_options;

  public function __construct($options)
  {
    require_once(dirname(__FILE__) . '/../class/mvAdminGen.class.php');

    $this->_options = $options;
    mvAdminGen::initialize($this->_options);

    add_action('wp_ajax_mv_orderupdate', array($this, 'updateOrder'));
    add_action('wp_ajax_mv_deleteitem', array($this, 'deleteItem'));
    add_action('wp_ajax_mv_deleteshortcode', array($this, 'deleteShortcode'));
  }

  public function updateOrder()
  {
    global $wpdb;
    $data = explode(',', $_POST['order']);

    $cnt = count($data);

    for ($i = 0; $i < $cnt; $i++)
    {
      $wpdb->update(
        $wpdb->prefix . 'mediaview_media',
        array(
          'MEDIA_POS' => $i
        ),
        array('MEDIA_ID' => $data[$i])
      );
    }
  }

  public function deleteItem()
  {
    global $wpdb;

    check_ajax_referer('mv-delete-item', 'nonce');

    $keys = array(sanitize_text_field($_POST['key']));

    if (mvAdminGen::deleteMediaItems($keys, $wpdb))
      echo 1;

    die();
  }

  public function deleteShortcode()
  {
    global $wpdb;

    check_ajax_referer('mv-delete-shortcode', 'nonce');

    $keys = array(sanitize_text_field($_POST['key']));

    if (mvAdminGen::deleteShortcodes($keys, $wpdb))
      echo 1;

    die();
  }
}

?>
