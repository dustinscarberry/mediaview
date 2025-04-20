<?php

namespace Dscarberry\MediaView\Service\Processor;

use Dscarberry\MediaView\Form\DatasetForm;
use Dscarberry\MediaView\Form\MediaForm;
use Dscarberry\MediaView\Form\SettingsForm;
use Dscarberry\MediaView\Repository\DatasetRepository;
use Dscarberry\MediaView\Repository\MediaRepository;
use Dscarberry\MediaView\Service\Factory\MediaFactory;

class FormProcessor
{
  public function __construct()
  {
    if (!empty($_POST)) {
      //save main options script//
      if (isset($_POST['mvSaveOptions']))
      {
        $this->saveSettings($_POST);
      }
      //save new shortcode script//
      elseif (isset($_POST['mvSaveShortcode']))
      {
        $this->createShortcode($_POST);
      }
      //save a shortcode edit script//
      elseif (isset($_POST['mvSaveShortcodeEdit']))
      {
        $this->updateShortcode($_POST);
      }
      //save a new media item script//
      elseif (isset($_POST['mvSaveMedia']))
      {
        $this->createMedia($_POST);
      }
      //save a media item edit script//
      elseif(isset($_POST['mvSaveItemEdit']))
      {
        $this->updateMedia($_POST);
      }
    }
  }

  private function saveSettings()
  {
    if (check_admin_referer('mediaview_update_options')) {
      $form = new SettingsForm($_POST);

      $opts = get_option('mediaview_main_opts');
      $opts['ytHideControls'] = $form->getYtHideControls();
      $opts['ytProgressColor'] = $form->getYtProgressColor();
      $opts['vimeoControlColor'] = $form->getVimeoControlColor();

      if (update_option('mediaview_main_opts', $opts))
        echo '<div class="updated"><p>Settings saved</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong or there were no changes needed', 'mvcanvas') . '</p></div>';
    }
  }

  private function createShortcode()
  {
    if (check_admin_referer('mediaview_save_shortcode')) {
      $form = new DatasetForm($_POST);

      if (!$form->validate('create')) {
        echo '<div class="error error-message"><p>' . __('Oops... all fields must have a value', 'mvcanvas') . '</p></div>';
        return;
      }
      
      if (DatasetRepository::createDataset(
        $form->getShortName(),
        $form->getDisplayHeight(),
        $form->getCanvasColor(),
        $form->getCaptionColor()
      ))
        echo '<div class="updated"><p>' . __('Shortcode created', 'mvcanvas') . '</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong', 'mvcanvas') . '</p></div>';
    }
  }

  private function createMedia()
  {
    if (check_admin_referer('mediaview_save_media')) {
      $form = new MediaForm($_POST);

      if (!$form->validate('create')) {
        echo '<div class="error error-message"><p>' . __('Oops... all fields must have a value', 'mvcanvas') . '</p></div>';
        return;
      }

      $shortcodeCurrentMediaCount = DatasetRepository::getDatasetMediaCount($form->getShortcodeId());

      if ($shortcodeCurrentMediaCount === false) {
        echo '<div class="error"><p>' . __('Oops... there appears to be an issue with the database.', 'mvcanvas') . '</p></div>';
        return;
      }

      $parsedMediaData = MediaFactory::parseMediaUrl($form->getMediaUrl(), $form->getMediaType(), $form->getMediaRes());

      if (!MediaFactory::saveMediaThumbnail($parsedMediaData, $form->getMediaType())) {
        echo '<div class="error"><p>' . __('Oops... there seems to be a problem saving the thumbnail.', 'mvcanvas') . '</p></div>';
        return;
      }

      if (MediaRepository::createMedia(
        $form->getShortcodeId(),
        serialize($parsedMediaData),
        $form->getMediaType(),
        $form->getMediaCaption(),
        $shortcodeCurrentMediaCount
      ))
        echo '<div class="updated"><p>' . __('Media added', 'mvcanvas') . '</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong', 'mvcanvas') . '</p></div>';
    }
  }

  private function updateShortcode()
  {
    if (check_admin_referer('mediaview_edit_shortcode')) {
      $form = new DatasetForm($_POST);

      if (!$form->validate('update')) {
        echo '<div class="error e-message"><p>' . __('Oops... all fields must have a value', 'mvcanvas') . '</p></div>';
        return;
      }

      // check for existing shortcode
      if (!DatasetRepository::getDataset($form->getId())) {
        echo '<div class="error e-message"><p>' . __('Invalid shortcode', 'mvcanvas') . '</p></div>';
        return;
      }

      if (DatasetRepository::updateDataset(
        $form->getId(),
        $form->getShortName(),
        $form->getDisplayHeight(),
        $form->getCanvasColor(),
        $form->getCaptionColor()
      ))
        echo '<div class="updated"><p>' . __('Shortcode updated', 'mvcanvas') . '</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong', 'mvcanvas') . '</p></div>';
    }
  }

  private function updateMedia()
  {
    if (check_admin_referer('mediaview_edit_item')) {
      $form = new MediaForm($_POST);

      if (!$form->validate('update')) {
        echo '<div class="error error-message"><p>' . __('Oops... all fields must have a value', 'mvcanvas') . '</p></div>';
        return;
      }

      $media = MediaRepository::getMedia($form->getId());

      if (!$media) {
        echo '<div class="error error-message"><p>' . __('Invalid media', 'mvcanvas') . '</p></div>';
        return;
      }

      $unserializedMedia = unserialize($media->MEDIA_VALUE);

      if (!empty($form->getMediaRes()))
        $unserializedMedia['hd'] = $form->getMediaRes();
   
      if (MediaRepository::updateMedia(
        $form->getId(),
        $form->getMediaCaption(),
        serialize($unserializedMedia)
      ))
        echo '<div class="updated"><p>' . __('Media item updated', 'mvcanvas') . '</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong', 'mvcanvas') . '</p></div>';
    }
  }
}