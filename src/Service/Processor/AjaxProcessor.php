<?php

namespace Dscarberry\MediaView\Service\Processor;

use Dscarberry\MediaView\Service\Factory\DatasetFactory;
use Dscarberry\MediaView\Service\Factory\MediaFactory;
use Dscarberry\MediaView\Repository\MediaRepository;

class AjaxProcessor
{
  public static function updateOrder()
  {
    $order = sanitize_text_field($_POST['order']);
    $order = explode(',', $order);
    MediaRepository::updateMediaOrderInDataset($order);
  }

  public static function deleteItem()
  {
    check_ajax_referer('mv-delete-item', 'nonce');

    $keys = [sanitize_text_field($_POST['key'])];

    if (MediaFactory::deleteMediaItems($keys))
      echo 1;

    die();
  }

  public static function deleteShortcode()
  {
    check_ajax_referer('mv-delete-shortcode', 'nonce');

    $keys = [sanitize_text_field($_POST['key'])];

    if (DatasetFactory::deleteDatasets($keys))
      echo 1;

    die();
  }
}