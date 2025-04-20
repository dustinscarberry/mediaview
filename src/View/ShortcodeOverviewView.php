<?php

namespace Dscarberry\MediaView\View;

use Dscarberry\MediaView\View\ShortcodeListTableView;

class ShortcodeOverviewView
{
  public static function render()
  {
    $shortcodeTable = new ShortcodeListTableView();
    $shortcodeTable->prepare_items();
  
    echo '<div class="wrap mv-admin">  
      <h2 id="mv-masthead">MediaView ' . __('Shortcodes', 'mvcanvas') . '</h2>
      <div class="mv-form-box">
        <p class="mv-actionbar">
          <a href="?page=mediaview&view=addshortcode" class="mv-link-submit-button">' . __('Create Shortcode', 'mvcanvas') . '</a>
        </p>
        <h3>Shortcodes</h3>
        <form method="post">';
        
      $shortcodeTable->display();
      
      echo '</form>
      </div>
      <div class="postbox">
        <h3 class="hndle mv-post-box"><span>' . __("FAQ's", "mvcanvas") . '</span></h3>
        <div class="inside">
          <div class="mv-form-box">
            <ul>
              <li>For assistance with this plugin send an email to <a href="mailto:bitsnbytes1001@gmail.com" target="_blank">bitsnbytes1001@gmail.com</a>. Please, if possible, include a working link to the webpage where you are having an issue.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>';
  }
}