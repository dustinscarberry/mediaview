<?php

class mvAdminGen
{
  private static $_options, $_basePath;

  public static function initialize(&$options)
  {
    self::$_options = $options;
    self::$_basePath = wp_upload_dir();
    self::$_basePath = self::$_basePath['basedir'] . '/mediaview-cache/';
  }

  public static function deleteShortcodes($shortcodes, &$wpdb)
  {
    //create querystring
    $queryString = implode(', ', array_map('intval', $shortcodes));

    //get media items included in selected shortcodes
    $items = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'mediaview_media WHERE DATA_ID IN (' . $queryString . ')', ARRAY_A);

    //delete media items and update count
    if ($wpdb->query('DELETE FROM ' . $wpdb->prefix . 'mediaview_media WHERE DATA_ID IN (' . $queryString . ')') === false
    || $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'mediaview_dataset WHERE DATA_ID IN (' . $queryString . ')') === false)
      return false;

    //remove cached thumbnails
    foreach ($items as $item)
    {
      if (!isset($item['MEDIA_VALUE']))
      continue;

      $temp = unserialize($item['MEDIA_VALUE']);

      if (!isset($temp['thumb']))
        continue;

      unlink(self::$_basePath . $temp['thumb']);
    }

    return true;
  }

  public static function deleteMediaItems($items, &$wpdb)
  {
    //sanitize key array and break apart
    $queryString = implode(', ', array_map('intval', $items));

    $items = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'mediaview_media WHERE MEDIA_ID IN (' . $queryString . ')', ARRAY_A);

    //check for empty items
    if (!isset($items[0]))
      return false;

    $shortcode = $wpdb->get_results('SELECT DATA_MEDIACOUNT FROM ' . $wpdb->prefix . 'mediaview_dataset WHERE DATA_ID = ' . $items[0]['DATA_ID'], ARRAY_A);

    //check for empty shortcode
    if (!isset($shortcode[0]))
      return false;

    $itemcnt = $shortcode[0]['DATA_MEDIACOUNT'] - count($items);

    //delete media items and update count
    if ($wpdb->query('DELETE FROM ' . $wpdb->prefix . 'mediaview_media WHERE MEDIA_ID IN (' . $queryString . ')'
    ) === false || $wpdb->update(
      $wpdb->prefix . 'mediaview_dataset',
      array(
        'DATA_MEDIACOUNT' => $itemcnt
      ),
      array('DATA_ID' => $items[0]['DATA_ID'])
      ) === false)
      return false;

    //delete media item thumbnails from cache
    foreach($items as $item)
    {
      if (!isset($item['MEDIA_VALUE']))
        continue;

      $temp = unserialize($item['MEDIA_VALUE']);

      if (!isset($temp['thumb']))
        continue;
      
      unlink(self::$_basePath . $temp['thumb']);
    }

    return true;
  }
}

  ?>
