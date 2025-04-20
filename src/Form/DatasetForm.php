<?php

namespace Dscarberry\MediaView\Form;

class DatasetForm
{
  private $id;
  private $shortName;
  private $displayHeight;
  private $canvasColor;
  private $captionColor;

  function __construct($req)
  {
    if (isset($req['key']))
      $this->id = sanitize_key($req['key']);

    if (isset($req['shortName']))
      $this->shortName = sanitize_text_field($req['shortName']);

    if (isset($req['displayHeight']))
      $this->displayHeight = sanitize_text_field($req['displayHeight']);

    if (isset($req['canvasColor']))
      $this->canvasColor = sanitize_text_field($req['canvasColor']);

    if (isset($req['captionColor']))
      $this->captionColor = sanitize_text_field($req['captionColor']);
  }

  public function getId()
  {
    return $this->id;
  }

  public function getShortName()
  {
    return $this->shortName;
  }

  public function getDisplayHeight()
  {
    return $this->displayHeight;
  }

  public function getCanvasColor()
  {
    return $this->canvasColor;
  }

  public function getCaptionColor()
  {
    return $this->captionColor;
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
      empty($this->shortName)
      || empty($this->displayHeight)
      || !is_numeric($this->displayHeight)
      || empty($this->canvasColor)
      || empty($this->captionColor)
    ) return false;

    return true;
  }

  private function validateUpdate()
  {
    if (
      empty($this->id)
      || !is_numeric($this->id)
      || empty($this->shortName)
      || empty($this->displayHeight)
      || !is_numeric($this->displayHeight)
      || empty($this->canvasColor)
      || empty($this->captionColor)
    ) return false;

    return true;
  }
}