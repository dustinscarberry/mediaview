<?php

namespace Dscarberry\MediaView\Service\Factory;

use Dscarberry\MediaView\Repository\DatasetRepository;
use Dscarberry\MediaView\Repository\MediaRepository;
use Dscarberry\MediaView\Service\Factory\MediaFactory;

class MediaFactory
{
  public static function parseMediaUrl($mediaUrl, $mediaType, $mediaRes)
  {
    $parsedMediaData = [];

    if ($mediaType == 'vimeo') {
      preg_match('/vimeo.com\/([0-9]+)/', $mediaUrl, $matches);
      $parsedMediaData['vid'] = $matches[1];

      $parsedMediaData['thumb'] = 'vimeo-' . rand() . '-' . $parsedMediaData['vid'] . '.jpg';
    } else if ($mediaType == 'youtube') {
      $parsedMediaData['hd'] = $mediaRes;

      $parsedUrl = parse_url($mediaUrl);
      parse_str($parsedUrl['query'], $parsedQueryString);
      $parsedMediaData['vid'] = $parsedQueryString['v'];

      $parsedMediaData['thumb'] = 'youtube-' . rand() . '-' . $parsedMediaData['vid'] . '.jpg';
    }
    else if ($mediaType == 'photo') {
      $parsedMediaData['full'] = $mediaUrl;
      $parsedMediaData['thumb'] = 'photo-' . rand() . '-' . rand() . '.jpg';
    }

    return $parsedMediaData;
  }

  public static function saveMediaThumbnail($parsedMediaData, $mediaType)
  {
    $imageDir = wp_upload_dir()['basedir'];

    // get image editor and save path
    if ($mediaType == 'vimeo') {
      $data = wp_remote_get('http://vimeo.com/api/v2/video/' . $parsedMediaData['vid'] . '.json');

      if (isset($data['body']))
        $data = json_decode($data['body'], true);

      $image = wp_get_image_editor($data[0]['thumbnail_small']);
      $imagePath = $imageDir . '/mediaview-cache/' . $parsedMediaData['thumb'];
    } else if ($mediaType == 'youtube') {
      $image = wp_get_image_editor('http://img.youtube.com/vi/' . $parsedMediaData['vid'] . '/0.jpg');
      $imagePath = $imageDir . '/mediaview-cache/' . $parsedMediaData['thumb'];
    } else if ($mediaType == 'photo') {
      $image = wp_get_image_editor($parsedMediaData['full']);
      $imagePath = $imageDir . '/mediaview-cache/' . $parsedMediaData['thumb'];
    }

    // resize and save thumbnail
    if (!is_wp_error($image)) {
      $image->resize(65, 65, true);
      $image->save($imagePath);
      return true;
    } else
      return false;
  }

  public static function deleteMediaItems($mediaIds)
  {
    $media = MediaRepository::getMediaForSelectedIds($mediaIds);

    // check for no media
    if (!isset($media[0]))
      return false;

    $shortcodeMediaCount = DatasetRepository::getDatasetMediaCount($media[0]->DATA_ID);

    // check for empty shortcode
    if (!$shortcodeMediaCount)
      return false;

    $newShortcodeMediaCount = $shortcodeMediaCount - count($media);

    // delete media items and update count
    if (
      MediaRepository::deleteMediaForSelectedIds($mediaIds) === false 
      || DatasetRepository::updateDatasetMediaCount($media[0]->DATA_ID, $newShortcodeMediaCount) === false
    )
      return false;

    // delete media item thumbnails
    MediaFactory::removeMediaThumbailsForMedia($media);

    return true;
  }

  public static function removeMediaThumbailsForMedia(array $media)
  {
    foreach ($media as $item) {
      if (!isset($item->MEDIA_VALUE))
        continue;

      $unserializedData = unserialize($item->MEDIA_VALUE);

      if (!isset($unserializedData['thumb']))
        continue;

      $deletionPath = wp_upload_dir()['basedir'] . '/mediaview-cache/' . $unserializedData['thumb'];
      unlink($deletionPath);
    }
  }
}