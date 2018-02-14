<?php

require_once(plugin_dir_path(__FILE__) . 'mvWPListTableBase.class.php');

class mvShortcodeListTable extends mvWPListTableBase
{
  function __construct()
  {
    global $status, $page;

    parent::__construct(array(
      'singular' => '',
      'plural' => '',
      'ajax' => false
    ));

    add_filter('list_table_primary_column', array($this, 'wpdocs_slide_list_table_primary_column'), 10, 2 );
  }

  function wpdocs_slide_list_table_primary_column($column, $screen)
  {
    if ('edit-slide' === $screen)
      $column = 'slide';

    return $column;
  }

  function get_columns()
  {
    $columns = array(
      'cb' => '<input type="checkbox"/>',
      'name' => __('Name', 'mvcanvas'),
      'shortcode' => __('Shortcode', 'mvcanvas'),
      'dateadd' => __('Date Added', 'mvcanvas'),
      'mediaitems' => __('# Media Items', 'mvcanvas')
    );

    return $columns;
  }

  function prepare_items()
  {
    $this->process_bulk_action();

    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable, 'name');

    $this->items = $this->setup_items();

    if (!empty($_GET['orderby']) && !empty($_GET['order']))
      usort($this->items, array($this, 'usort_reorder'));
  }

  function column_default($item, $column_name)
  {
    switch ($column_name)
    {
      case 'name':
      case 'shortcode':
      case 'dateadd':
      case 'mediaitems':
        return $item[ $column_name ];
      default:
        return 'An unknown error has occured';
    }
  }

  function get_sortable_columns()
  {
    $sortable_columns = array(
      'name'  => array('name', false),
      'dateadd' => array('dateadd', false),
      'mediaitems'   => array('mediaitems', false)
    );

    return $sortable_columns;
  }

  function usort_reorder($a, $b)
  {
    // If no sort, default to title
    $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'dateadd';
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
    $row_class = ($row_class == '' ? ' class="alternate"' : '');

    echo '<tr id="' . $item['ID'] . '" ' . $row_class . '>';
    $this->single_row_columns($item);
    echo '</tr>';
  }

  function get_bulk_actions()
  {
    $actions = array(
      'delete' => __('Delete', 'mvcanvas')
    );

    return $actions;
  }

  function process_bulk_action()
  {
    $action = $this->current_action();

    if ($action != -1)
    {
      global $wpdb;
      require_once 'mvAdminGen.class.php';

      $options = get_option('mediaview_main_opts');

      mvAdminGen::initialize($options);

      if ($action == 'delete')
        mvAdminGen::deleteShortcodes($_POST['shortcode'], $wpdb);
    }
  }

  function column_cb($item)
  {
    return sprintf('<input type="checkbox" name="shortcode[]" value="%s" />', $item['ID']);
  }

  function no_items()
  {
    _e('No shortcodes found', 'mvcanvas');
  }

  function setup_items()
  {
    global $wpdb;
    $cells = array();

    $data = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'mediaview_dataset ORDER BY DATA_ID', ARRAY_A);

    foreach ($data as $val)
    {
      array_push($cells, array(
        'ID' => $val['DATA_ID'],
        'name' => '<a href="?page=mediaview&view=viewshortcode&id=' . $val['DATA_ID'] . '" title="' . __('View', 'mvcanvas') . '" class="mv-row-title">' . $val['DATA_NAME'] . '</a>
        <div class="mv-row-actions">
        <a href="?page=mediaview&view=editshortcode&id=' . $val['DATA_ID']  . '" title="' . __('Edit this shortcode', 'mvcanvas') . '">' . __('Edit', 'mvcanvas') . '</a>
        <span class="mv-row-divider">|</span>
        <a href="" class="mv-delete-shortcode" title="' . __('Delete this shortcode', 'mvcanvas') . '">' . __('Delete', 'mvcanvas') . '</a>
        <span class="mv-row-divider">|</span>
        <a href="?page=mediaview&view=viewshortcode&id=' . $val['DATA_ID'] . '" title="' . __('View', 'mvcanvas') . '">' . __('View', 'mvcanvas') . '</a>
        </div>',
        'shortcode' => '[mediaview id="' . $val['DATA_ID'] . '"]',
        'dateadd' => date('Y/m/d', $val['DATA_UPDATEDATE']),
        'mediaitems' => $val['DATA_MEDIACOUNT']
      ));
    }

    return $cells;
  }
}

?>
