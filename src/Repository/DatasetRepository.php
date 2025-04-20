<?php

namespace Dscarberry\MediaView\Repository;

// dataset is old name for shortcode
class DatasetRepository
{
  // get dataset
  public static function getDataset(int $id)
  {
    if (!$id) return false;

    global $wpdb;

    return $wpdb->get_row(
      $wpdb->prepare('
        SELECT *
        FROM ' . $wpdb->prefix . 'mediaview_dataset
        WHERE DATA_ID = %d',
        $id
      )
    );
  }

  // get all datasets
  public static function getDatasets()
  {
    global $wpdb;

    return $wpdb->get_results('
      SELECT *
      FROM ' . $wpdb->prefix . 'mediaview_dataset
      ORDER BY DATA_ID
    ');
  }

  // create dataset
  public static function createDataset($shortcodeName, $displayHeight, $canvasColor, $captionColor)
  {
    global $wpdb;

    if ($wpdb->insert(
      $wpdb->prefix . 'mediaview_dataset',
      [
        'DATA_NAME' => $shortcodeName,
        'DATA_DISPLAYHEIGHT' => $displayHeight,
        'DATA_CANVASCOLOR' => $canvasColor,
        'DATA_CAPTIONCOLOR' => $captionColor,
        'DATA_UPDATEDATE' => current_time('timestamp')
      ]
    ) !== false) return true;

    return false;
  }

  // update dataset
  public static function updateDataset(int $id, $shortcodeName, $displayHeight, $canvasColor, $captionColor)
  {
    if (!$id) return false;

    global $wpdb;

    if ($wpdb->update(
      $wpdb->prefix . 'mediaview_dataset',
      [
        'DATA_NAME' => $shortcodeName,
        'DATA_DISPLAYHEIGHT' => $displayHeight,
        'DATA_CANVASCOLOR' => $canvasColor,
        'DATA_CAPTIONCOLOR' => $captionColor
      ],
      ['DATA_ID' => $id]
    ) >= 0) return true;

    return false;
  }

  // update dataset media count
  public static function updateDatasetMediaCount(int $id, int $mediaCount)
  {
    global $wpdb;

    return $wpdb->update(
      $wpdb->prefix . 'mediaview_dataset',
      ['DATA_MEDIACOUNT' => $mediaCount],
      ['DATA_ID' => $id]
    );
  }

  // get media count for shortcode
  public static function getDatasetMediaCount(int $id)
  {
    if (!$id) return false;

    global $wpdb;

    $data = $wpdb->get_row(
      $wpdb->prepare('
        SELECT DATA_MEDIACOUNT
        FROM ' . $wpdb->prefix . 'mediaview_dataset
        WHERE DATA_ID = %d',
        $id
      )
    );

    if (!$data) return false;

    return $data->DATA_MEDIACOUNT;
  }

  // delete all shortcodes for selected ids
  public static function deleteDatasetsForSelectedIds(array $ids)
  {
    global $wpdb;

    $idsQueryString = implode(', ', array_map('intval', $ids));

    return $wpdb->query(
      $wpdb->prepare('
        DELETE FROM ' . $wpdb->prefix . 'mediaview_dataset
        WHERE DATA_ID IN (' . $idsQueryString . ')
      ')
    );
  }
}