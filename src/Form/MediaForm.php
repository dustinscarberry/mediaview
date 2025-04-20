<?php

namespace Dscarberry\MediaView\Form;

class MediaForm
{
  private $id;
  private $shortcodeId;
  private $mediaType;
  private $mediaCaption;
  private $mediaUrl;
  private $mediaRes;

  function __construct($req)
  {
    if (isset($req['id']))
      $this->id = sanitize_key($req['id']);

    if (isset($req['shortcodeId']))
      $this->shortcodeId = sanitize_key($req['shortcodeId']);

    if (isset($req['mediaType']))
      $this->mediaType = sanitize_text_field($req['mediaType']);

    if (isset($req['mediaCaption']))
      $this->mediaCaption = sanitize_text_field($req['mediaCaption']);

    if (!empty($req['vimeoMediaURL']))
      $this->mediaUrl = sanitize_url($req['vimeoMediaURL']);

    if (!empty($req['youtubeMediaURL']))
      $this->mediaUrl = sanitize_url($req['youtubeMediaURL']);

    if (!empty($req['photoMediaURL']))
      $this->mediaUrl = sanitize_url($req['photoMediaURL']);

    if (isset($req['mediaRes']))
      $this->mediaRes = sanitize_text_field($req['mediaRes']);
  }

  public function getId()
  {
    return $this->id;
  }

  public function getShortcodeId()
  {
    return $this->shortcodeId;
  }

  public function getMediaType()
  {
    return $this->mediaType;
  }

  public function getMediaCaption()
  {
    return $this->mediaCaption;
  }

  public function getMediaUrl()
  {
    return $this->mediaUrl;
  }

  public function getMediaRes()
  {
    return $this->mediaRes;
  }

  public function validate(string $action)
  {
    if ($action == 'create')
      return $this->validateCreate();
    else if ($action == 'update')
      return $this->validateUpdate();
  }

  private function validateCreate()
  {
    if (
      empty($this->shortcodeId)
      || empty($this->mediaType)
      || empty($this->mediaUrl)
    ) return false;

    return true;
  }

  private function validateUpdate()
  {
    if (
      empty($this->id)
      || !is_numeric($this->id)
    ) return false;

    return true;
  }
}