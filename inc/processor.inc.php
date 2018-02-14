<?php

if (!empty($_POST))
{
  //declare globals
  global $wpdb;

  //save main options script//
  if (isset($_POST['mvSaveOptions']))
  {
    if (check_admin_referer('mediaview_update_options'))
    {
      $opts['ytHideControls'] = (isset($_POST['ytHideControls']) ? 'yes' : 'no');
      $opts['ytProgressColor'] = $_POST['ytProgressColor'];

      if (!empty($_POST['vimeoControlColor']))
        $opts['vimeoControlColor'] = $_POST['vimeoControlColor'];
      else
        $opts['vimeoControlColor'] = '#00adef';

      if (update_option('mediaview_main_opts', $opts))
        echo '<div class="updated"><p>Settings saved</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong or there were no changes needed', 'mvcanvas') . '</p></div>';
    }
  }
  //save new shortcode script//
  elseif (isset($_POST['mvSaveShortcode']))
  {
    if (check_admin_referer('mediaview_save_shortcode'))
    {
      $shortname = htmlentities($_POST['shortName'], ENT_QUOTES);
      $height = htmlentities($_POST['displayHeight'], ENT_QUOTES);
      $canvascolor = htmlentities($_POST['canvasColor'], ENT_QUOTES);
      $captioncolor = htmlentities($_POST['captionColor'], ENT_QUOTES);
      $time = current_time('timestamp');

      if (empty($shortname) || empty($height) || !is_numeric($height) || empty($canvascolor) || empty($captioncolor))
      {
        echo '<div class="error error-message"><p>' . __('Oops... all fields must have a value', 'mvcanvas') . '</p></div>';
        return;
      }

      if ($wpdb->insert(
        $wpdb->prefix . 'mediaview_dataset',
        array(
          'DATA_NAME' => $shortname,
          'DATA_DISPLAYHEIGHT' => $height,
          'DATA_CANVASCOLOR' => $canvascolor,
          'DATA_CAPTIONCOLOR' => $captioncolor,
          'DATA_UPDATEDATE' => $time
        )
      ))
        echo '<div class="updated"><p>' . __('Shortcode created', 'mvcanvas') . '</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong', 'mvcanvas') . '</p></div>';
    }
  }
  //save a shortcode edit script//
  elseif (isset($_POST['mvSaveShortcodeEdit']))
  {
    if (check_admin_referer('mediaview_edit_shortcode'))
    {
      $shortname = htmlentities($_POST['shortName'], ENT_QUOTES);
      $height = htmlentities($_POST['displayHeight'], ENT_QUOTES);
      $canvascolor = htmlentities($_POST['canvasColor'], ENT_QUOTES);
      $captioncolor = htmlentities($_POST['captionColor'], ENT_QUOTES);
      $key = sanitize_text_field($_POST['key']);

      if (empty($shortname) || empty($height) || !is_numeric($height) || empty($canvascolor) || empty($captioncolor))
      {
        echo '<div class="error e-message"><p>' . __('Oops... all fields must have a value', 'mvcanvas') . '</p></div>';
        return;
      }

      if ($wpdb->update(
        $wpdb->prefix . 'mediaview_dataset',
        array(
          'DATA_NAME' => $shortname,
          'DATA_DISPLAYHEIGHT' => $height,
          'DATA_CANVASCOLOR' => $canvascolor,
          'DATA_CAPTIONCOLOR' => $captioncolor
        ),
        array('DATA_ID' => $key)
      ) >= 0)
        echo '<div class="updated"><p>' . __('Shortcode updated', 'mvcanvas') . '</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong', 'mvcanvas') . '</p></div>';
    }
  }
  //save a new media item script//
  elseif (isset($_POST['mvSaveMedia']))
  {
    if (check_admin_referer('mediaview_save_media'))
    {
      $mediatype = htmlentities($_POST['mediaType'], ENT_QUOTES);
      $mediacaption = htmlentities($_POST['mediaCaption'], ENT_QUOTES);
      $mediasalt = rand();
      $time = current_time('timestamp');
      $key = sanitize_text_field($_POST['key']);

      $dir = wp_upload_dir();
      $dir = $dir['basedir'];

      $cnt = $wpdb->get_results('SELECT DATA_MEDIACOUNT FROM ' . $wpdb->prefix . 'mediaview_dataset WHERE DATA_ID = ' . $key, ARRAY_A);
      $cnt = $cnt[0]['DATA_MEDIACOUNT'] + 1;

      if ($mediatype == 'vimeo')
      {
        $mediaurl['vid'] = htmlentities($_POST['vimeoMediaURL'], ENT_QUOTES);

        preg_match('/vimeo.com\/([0-9]+)/', $mediaurl['vid'], $matches);
        $mediaurl['vid'] = $matches[1];

        $data = wp_remote_get('http://vimeo.com/api/v2/video/' . $mediaurl['vid'] . '.json');

        if (isset($data['body']))
          $data = json_decode($data['body'], true);

        $image = wp_get_image_editor($data[0]['thumbnail_small']);

        $mediaurl['thumb'] = 'vimeo-' . $mediasalt . '-' . $mediaurl['vid'] . '.jpg';

        $spath = $dir . '/mediaview-cache/' . $mediaurl['thumb'];
      }
      elseif ($mediatype == 'youtube')
      {
        $mediaurl['vid'] = $_POST['youtubeMediaURL'];
        $mediaurl['hd'] = htmlentities($_POST['mediaRes'], ENT_QUOTES);

        $mediaurl['vid'] = parse_url($mediaurl['vid']);
        parse_str($mediaurl['vid']['query']);
        $mediaurl['vid'] = $v;

        $image = wp_get_image_editor('http://img.youtube.com/vi/' . $mediaurl['vid'] . '/0.jpg');

        $mediaurl['thumb'] = 'youtube-' . $mediasalt . '-' . $mediaurl['vid'] . '.jpg';

        $spath = $dir . '/mediaview-cache/' . $mediaurl['thumb'];
      }
      elseif ($mediatype == 'photo')
      {
        $mediaurl['full'] = htmlentities($_POST['photoMediaURL'], ENT_QUOTES);
        $mediaurl['thumb'] = 'photo-' . $mediasalt . '-' . rand() . '.jpg';

        $image = wp_get_image_editor($mediaurl['full']);
        $spath = $dir . '/mediaview-cache/' . $mediaurl['thumb'];
      }

      if (!is_wp_error($image))
      {
        $image->resize(65, 65, true);
        $image->save($spath);
      }
      //break for image processing errors//
      else
      {
        echo '<div class="error"><p>' . __('Oops... there seems to be a problem saving the thumbnail, please send the following information to the developer if the problem persists.', 'mvcanvas') . '</p><p><pre>' . print_r($image, true) . '</pre></p></div>';
        return;
      }

      $mediaurl = serialize($mediaurl);

      if ($wpdb->insert(
        $wpdb->prefix . 'mediaview_media',
        array(
          'MEDIA_VALUE' => $mediaurl,
          'MEDIA_TYPE' => $mediatype,
          'MEDIA_CAPTION' => $mediacaption,
          'MEDIA_POS' => $cnt - 1,
          'MEDIA_UPDATEDATE' => $time,
          'DATA_ID' => $key
        )
        ) && $wpdb->update(
          $wpdb->prefix . 'mediaview_dataset',
          array(
            'DATA_MEDIACOUNT' => $cnt
          ),
          array('DATA_ID' => $key)
      ) >= 0)
        echo '<div class="updated"><p>' . __('Item added', 'mvcanvas') . '</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong', 'mvcanvas') . '</p></div>';
    }
  }
  //save a media item edit script//
  elseif(isset($_POST['mvSaveItemEdit']))
  {
    if (check_admin_referer('mediaview_edit_item'))
    {
      $caption = htmlentities($_POST['caption'], ENT_QUOTES);
      $key = sanitize_text_field($_POST['key']);

      $data = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'mediaview_media WHERE MEDIA_ID = ' . $key, ARRAY_A);

      $intdata = unserialize($data[0]['MEDIA_VALUE']);

      if (isset($_POST['mediaRes']))
        $intdata['hd'] = htmlentities($_POST['mediaRes'], ENT_QUOTES);

      $intdata = serialize($intdata);

      if ($wpdb->update(
        $wpdb->prefix . 'mediaview_media',
        array(
          'MEDIA_CAPTION' => $caption,
          'MEDIA_VALUE' => $intdata
        ),
        array('MEDIA_ID' => $key)
      ) >= 0)
        echo '<div class="updated"><p>' . __('Media item updated', 'mvcanvas') . '</p></div>';
      else
        echo '<div class="error e-message"><p>' . __('Oops... something went wrong', 'mvcanvas') . '</p></div>';
    }
  }
}

?>
