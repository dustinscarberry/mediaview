<?php

namespace Dscarberry\MediaView\Form;

class SettingsForm
{
  private $ytHideControls;
  private $ytProgressColor;
  private $vimeoControlColor = '#00adef';

  function __construct($req)
  {
    $this->ytHideControls = (isset($req['ytHideControls']) ? 'yes' : 'no');
  
    if (isset($req['ytProgressColor']))
      $this->ytProgressColor = sanitize_text_field($req['ytProgressColor']);

    if (!empty($req['vimeoControlColor']))
      $this->vimeoControlColor = sanitize_text_field($req['vimeoControlColor']);
  }

  public function getYtHideControls()
  {
    return $this->ytHideControls;
  }

  public function getYtProgressColor()
  {
    return $this->ytProgressColor;
  }

  public function getVimeoControlColor()
  {
    return $this->vimeoControlColor;
  }
}