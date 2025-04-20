<?php

namespace Dscarberry\MediaView\Repository;

class MediaRepository
{
  // get all media for dataset
  public static function getMediaByDataset(int $id)
  {
    if (!$id) return false;

    global $wpdb;

    return $wpdb->get_results(
      $wpdb->prepare('
        SELECT *
        FROM ' . $wpdb->prefix . 'mediaview_media
        WHERE DATA_ID = %d
        ORDER BY MEDIA_POS',
        $id
      )
    );
  }

  // get all media for selected datasets
  public static function getMediaForSelectedDatasets(array $ids)
  {
    global $wpdb;

    $idsQueryString = implode(', ', array_map('intval', $ids));

    return $wpdb->get_results(
      $wpdb->prepare('
        SELECT *
        FROM ' . $wpdb->prefix . 'mediaview_media
        WHERE DATA_ID IN (' . $idsQueryString . ')
      ')
    );
  }

  // get all media for selected ids
  public static function getMediaForSelectedIds(array $ids)
  {
    global $wpdb;

    $idsQueryString = implode(', ', array_map('intval', $ids));

    return $wpdb->get_results(
      $wpdb->prepare('
        SELECT *
        FROM ' . $wpdb->prefix . 'mediaview_media
        WHERE MEDIA_ID IN (' . $idsQueryString . ')
      ')
    );
  }

  // get media item
  public static function getMedia(int $id)
  {
    if (!$id) return false;

    global $wpdb;

    return $wpdb->get_row(
      $wpdb->prepare('
        SELECT *
        FROM ' . $wpdb->prefix . 'mediaview_media
        WHERE MEDIA_ID = %d',
        $id
      )
    );
  }

  // create media item
  public static function createMedia(int $shortcodeId, $mediaUrl, $mediaType, $mediaCaption, $shortcodeMediaItems)
  {
    if (!$shortcodeId) return false;

    global $wpdb;

    if ($wpdb->insert(
      $wpdb->prefix . 'mediaview_media',
      [
        'MEDIA_VALUE' => $mediaUrl,
        'MEDIA_TYPE' => $mediaType,
        'MEDIA_CAPTION' => $mediaCaption,
        'MEDIA_POS' => $shortcodeMediaItems,
        'MEDIA_UPDATEDATE' => current_time('timestamp'),
        'DATA_ID' => $shortcodeId
      ]
      ) && $wpdb->update(
        $wpdb->prefix . 'mediaview_dataset',
        [
          'DATA_MEDIACOUNT' => $shortcodeMediaItems + 1
        ],
        ['DATA_ID' => $shortcodeId]
    ) !== false) return true;

    return false;
  }

  // update media item
  public static function updateMedia(int $id, $mediaCaption, $mediaUrl)
  {
    if (!$id) return false;

    global $wpdb;

    if ($wpdb->update(
      $wpdb->prefix . 'mediaview_media',
      [
        'MEDIA_CAPTION' => $mediaCaption,
        'MEDIA_VALUE' => $mediaUrl
      ],
      ['MEDIA_ID' => $id]
    ) !== false) return true;

    return false;
  }

  // update media ordering in dataset
  public static function updateMediaOrderInDataset($ids)
  {
    global $wpdb;

    $totalIds = count($ids);

    for ($i = 0; $i < $totalIds; $i++) {
      $wpdb->update(
        $wpdb->prefix . 'mediaview_media',
        ['MEDIA_POS' => $i],
        ['MEDIA_ID' => $ids[$i]]
      );
    }
  }

  // delete all media for selected datasets
  public static function deleteMediaForSelectedDatasets(array $ids)
  {
    global $wpdb;

    $idsQueryString = implode(', ', array_map('intval', $ids));

    return $wpdb->query(
      $wpdb->prepare('
        DELETE FROM ' . $wpdb->prefix . 'mediaview_media
        WHERE DATA_ID IN (' . $idsQueryString . ')
      ')
    );
  }

  // delete all media for selected ids
  public static function deleteMediaForSelectedIds(array $ids)
  {
    global $wpdb;

    $idsQueryString = implode(', ', array_map('intval', $ids));

    return $wpdb->query(
      $wpdb->prepare('
        DELETE FROM ' . $wpdb->prefix . 'mediaview_media
        WHERE MEDIA_ID IN (' . $idsQueryString . ')
      ')
    );
  }
}