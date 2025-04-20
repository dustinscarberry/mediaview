<?php

namespace Dscarberry\MediaView\Service\Factory;

use Dscarberry\MediaView\Repository\DatasetRepository;
use Dscarberry\MediaView\Repository\MediaRepository;
use Dscarberry\MediaView\Service\Factory\MediaFactory;

class DatasetFactory
{
  public static function deleteDatasets($shortcodeIds)
  {
    $media = MediaRepository::getMediaForSelectedDatasets($shortcodeIds);

    // delete media items and datasets from db
    if (MediaRepository::deleteMediaForSelectedDatasets($shortcodeIds) === false
    || DatasetRepository::deleteDatasetsForSelectedIds($shortcodeIds) === false)
      return false;

    // delete media item thumbnails
    MediaFactory::removeMediaThumbailsForMedia($media);

    return true;
  }
}