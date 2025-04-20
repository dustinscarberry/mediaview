<?php

namespace Dscarberry\MediaView\View;

use Dscarberry\MediaView\Service\Factory\DatasetFactory;
use Dscarberry\MediaView\Service\Factory\MediaFactory;

class MediaItemListTableView extends WPListTableBaseView
{
  private $_id;

  function __construct($id)
  {
    global $status, $page;

    parent::__construct([
      'singular' => '',
      'plural' => 'mv-sortable-table',
      'ajax' => false
    ]);

    $this->_id = $id;
  }

  function get_columns()
  {
    $columns = [
      'cb' => '<input type="checkbox"/>',
      'mv-itemthumbnail' => __('Thumbnail', 'mvcanvas'),
      'caption' => __('Caption', 'mvcanvas'),
      'mv-itemtype' => __('Type', 'mvcanvas'),
      'dateadd' => __('Date Added', 'mvcanvas')
    ];

    return $columns;
  }

  function prepare_items()
  {
    $this->process_bulk_action();

    $columns = $this->get_columns();
    $hidden = [];
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = [$columns, $hidden, $sortable];

    $this->items = $this->setup_items();

    if (!empty($_GET['orderby']) && !empty($_GET['order']))
      usort($this->items, [$this, 'usort_reorder']);
  }

  function column_default($item, $column_name)
  {
    switch ($column_name)
    {
      case 'mv-itemthumbnail':
      case 'mv-itemtype':
      case 'caption':
      case 'dateadd':
        return $item[ $column_name ];
      default:
        return 'An unknown error has occured';
    }
  }

  function get_sortable_columns()
  {
    $sortable_columns = [
      'mv-itemtype'  => ['mv-itemtype', false],
      'caption' => ['caption', false],
      'dateadd' => ['dateadd', false]
    ];

    return $sortable_columns;
  }

  function usort_reorder($a, $b)
  {
    // If no sort, default to title
    $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'name';
    // If no order, default to asc
    $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
    // Determine sort order
    $result = strcmp($a[$orderby], $b[$orderby]);
    // Send final sort direction to usort
    return ($order === 'asc') ? $result : -$result;
  }

  //add id to table rows
  function single_row($item)
  {
    static $row_class = '';
    $row_class = ( $row_class == '' ? ' class="alternate"' : '');

    echo '<tr id="' . $item['ID'] . '" ' . $row_class . '>';
    $this->single_row_columns($item);
    echo '</tr>';
  }

  function get_bulk_actions()
  {
    $actions = [
      'delete' => __('Delete', 'mvcanvas')
    ];

    return $actions;
  }

  function process_bulk_action()
  {
    $action = $this->current_action();

    if ($action != -1) {
      if ($action == 'delete')
        MediaFactory::deleteMediaItems($_POST['mediaitem']);
    }
  }

  function column_cb($item)
  {
    return sprintf('<input type="checkbox" name="mediaitem[]" value="%s" />', $item['ID']);
  }

  function no_items()
  {
    _e('No media items found', 'mvcanvas');
  }

  function setup_items()
  {
    global $wpdb;
    $cells = [];

    $data = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'mediaview_media WHERE DATA_ID = ' . $this->_id . ' ORDER BY MEDIA_POS', ARRAY_A);
    $dir = wp_upload_dir();
    $dir = $dir['baseurl'];

    foreach ($data as $val)
    {
      $data = unserialize($val['MEDIA_VALUE']);

      if ($val['MEDIA_TYPE'] == 'vimeo')
        $viewLink = 'http://www.vimeo.com/' . $data['vid'];
      elseif ($val['MEDIA_TYPE'] == 'youtube')
        $viewLink = 'http://www.youtube.com/watch?v=' . $data['vid'];
      else
        $viewLink = $data['full'];

      array_push($cells, [
        'ID' => $val['MEDIA_ID'],
        'mv-itemthumbnail' => '<img src="' . $dir . '/mediaview-cache/' . $data['thumb'] . '" class="mv-prev-thumb"/><span class="mv-sortable-handle" title="' . __('Click and drag to reorder') . '">::</span>',
        'caption' => '<span class="mv-row-title">' . (stripslashes($val['MEDIA_CAPTION']) == '' ? '-----' : stripslashes($val['MEDIA_CAPTION'])) . '</span>
        <div class="mv-row-actions">
        <a href="?page=mediaview&view=edititem&id=' . $val['MEDIA_ID'] . '" title="' . __('Edit this item', 'mvcanvas') . '">' . __('Edit', 'mvcanvas') . '</a>
        <span class="mv-row-divider">|</span>
        <a href="" class="mv-delete-item" title="' . __('Delete this item', 'mvcanvas') . '">' . __('Delete', 'mvcanvas') . '</a>
        <span class="mv-row-divider">|</span>
        <a href="' . $viewLink . '" title="' . __('View', 'mvcanvas') . '" target="_blank">' . __('View', 'mvcanvas') . '</a>
        </div>',
        'mv-itemtype' => ucwords(stripslashes($val['MEDIA_TYPE'])),
        'dateadd' => date('Y/m/d', $val['MEDIA_UPDATEDATE'])
      ]);
    }

    return $cells;
  }
}