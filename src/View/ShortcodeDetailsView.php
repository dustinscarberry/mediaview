<?php

namespace Dscarberry\MediaView\View;

class ShortcodeDetailsView
{
  public static function render($shortcode)
  {
    $mediaItemsTable = new MediaItemListTableView($shortcode->DATA_ID);
    $mediaItemsTable->prepare_items();
     
    echo '<div class="wrap mv-admin">
      <h2 id="mv-masthead">MediaView ' . __('Shortcodes', 'mvcanvas') . '</h2>
      <div class="mv-form-box mv-topform-box">
        <p class="mv-action-bar">
          <a href="?page=mediaview&view=addmedia&id=' . $shortcode->DATA_ID . '" class="mv-link-submit-button">' . __('Add Media Item', 'mvcanvas') . '</a>
          <a href="?page=mediaview" class="mv-cancel">' . __('Go Back', 'mvcanvas') . '</a>
        </p>
        <h3>' . __('Media Items', 'mvcanvas') . '<span class="mv-sub-H3"> ( ' . $shortcode->DATA_NAME . ') - ' . $shortcode->DATA_MEDIACOUNT . ' ' . __('items', 'mvcanvas') . '</span></h3>
        <form method="post">';
        
    $mediaItemsTable->display();
    
    echo '</form>
      </div>    
    </div>';
  }
}

