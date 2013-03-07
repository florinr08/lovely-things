<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/scripts/initialisation.php');
$privileges = PERMISSIONS_EDIT_EVENTS; // the privileges needed to see the site - refer to /secure/account_types.txt
beforeFileLoad();
?>
<?php
include_once(HOME_DIR . 'scripts/headTagsSnippet.php');
echo showDOCTYPE();
echo headTagsSnippet('ThePo!ntC.O.C. - Edit Events', false);
?>
  <link rel="stylesheet" href="<?php echo HOME_DIR; ?>css/edit_events.css" />
  <script src="<?php echo HOME_DIR; ?>scripts/edit_events.js"></script>
</head>
<!-- HEADTAGS -->
<body>

<!-- HEADER -->
<?php
include_once(HOME_DIR . 'scripts/headerSnippet.php');
echo headerSnippet();
?>
<!-- HEADER -->

<!-- CONTENT -->
<div id="content">
<div id="contentHolder" class="dotted">
<?php
include_once(HOME_DIR . 'scripts/navigationSnippet.php');
echo navigationSnippet();
?>
<div class="dottedBreak dotted"></div>
<h1>Edit Events</h1>
<div class="dottedBreak dotted"></div>
<?php
$items_per_page = 20;
$num_items = num_items();
$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page_number < 1) {
  $page_number = 1;
}
$pages = ceil($num_items / $items_per_page);
$list_types = array(
  'ConnectEvents',
  'Fundraising',
  'Guest Speakers',
  'Training',
  'Conferences',
  'Weekly'
  );

function num_items() {
  global $mysqli;
  $num_items = 0;
  if (@$result = $mysqli->query('SELECT COUNT(*) FROM `events` WHERE deleted = \'N\'')) {
    list($num_items) = $result->fetch_row();
    $result->close();
  }
  return $num_items;
}

function list_items() {
  global $mysqli, $items_per_page, $page_number, $pages;
  $return_value = false;
  $limit_start = ($page_number - 1) * $items_per_page;
  $sql_past = 'SELECT id, datetime, title, active FROM `events` WHERE deleted = \'N\' AND date(datetime) < date(now()) ORDER BY datetime DESC LIMIT ?, ?';
  $sql_upcoming = 'SELECT id, datetime, title, active FROM `events` WHERE deleted = \'N\' AND date(datetime) >= date(now()) ORDER BY datetime ASC LIMIT ?, ?';
  for ($i = 0; $i < 2; ++$i) {
    $sql = $sql_past;
    if ($i == 0) {
      $sql = $sql_upcoming;
    }
    if (@$stmt = $mysqli->prepare($sql)) {
      $stmt->bind_param('ii', $limit_start, $items_per_page);
      $stmt->execute();
      $stmt->store_result();
      if (!$stmt->errno) {
        $return_value = true;
      }
      else {
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  Failed to retrieve list of items.', PHP_EOL;
        echo '</div>', PHP_EOL;
      }
      if ($i == 0) {
        echo '<a href="', basename($_SERVER['PHP_SELF']), '?new" class="custom_btn">Add item</a>', PHP_EOL;
      }
      $stmt->bind_result($id, $datetime, $title, $active);
      echo '<div class="items_list">', PHP_EOL;
      while ($stmt->fetch()) {
        echo '  <a href="', basename($_SERVER['PHP_SELF']), '?item=', $id, '"', ($active == 'N') ? ' class="inactive"' : '' , '>', $title, date(' (D <\b>jS M</\b> \'y)', strtotime($datetime)), ($active == 'N') ? ' <em>INACTIVE</em>' : '' , '</a>', PHP_EOL;
      }
      echo '</div>', PHP_EOL;
      $stmt->close();
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Failed to retrieve list of items and could not connect to the database.', PHP_EOL;
      echo '</div>', PHP_EOL;
      if ($i == 0) {
        echo '<a href="', basename($_SERVER['PHP_SELF']), '?new" class="custom_btn">Add item</a>', PHP_EOL;
      }
    }
  }
  
  if ($page_number > 1) {
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?page=', ($page_number - 1), '" class="custom_btn pages">&lt; Newer</a>', PHP_EOL;
  }
  else {
    echo '<div class="custom_btn pages disabled">&lt; Newer</div>', PHP_EOL;
  }
  
  if ($pages > $page_number) {
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?page=', ($page_number + 1), '" class="custom_btn pages">Older &gt;</a>', PHP_EOL;
  }
  else {
    echo '<div class="custom_btn pages disabled">Older &gt;</div>', PHP_EOL;
  }
  
  echo '<div class="break"></div>', PHP_EOL;
  return $return_value;
}

function list_item($item) {
  global $mysqli, $list_types;
  $checked = ' checked="checked"';
  $selected = ' selected="selected"';
  echo '<a href="', basename($_SERVER['PHP_SELF']), '" class="custom_btn">Cancel/Back</a>', PHP_EOL;
  if ($item && @$stmt = $mysqli->prepare('SELECT active, id, type, datetime, title, coveruri, cover_title, cover_href, cover_main, guesturi, content, content_is_html, supp, supp_is_html FROM `events` WHERE deleted = \'N\' AND id = ? LIMIT 1')) {
    $stmt->bind_param('i', $item);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($active, $id, $type, $datetime, $title, $coveruri, $cover_title, $cover_href, $cover_main, $guesturi, $content, $content_is_html, $supp, $supp_is_html);
    $stmt->fetch();
    $stmt->close();
    $datetime = strtotime($datetime);
  }
  else {
    $active = 'Y';
    $id = $item;
    $type = '';
    $datetime = strtotime('today 19:00');
    $title = '';
    $coveruri = '';
    $cover_title = '';
    $cover_href = '';
    $cover_main = 'Y';
    $content = '';
    $content_is_html = 'N';
    $supp = '';
    $supp_is_html = 'Y';
  }
  $datetime = date('Y-m-d H:i:s', $datetime);
  echo '<form action="', basename($_SERVER['PHP_SELF']), '" method="post" class="item_form">', PHP_EOL;
  echo '  <input type="hidden" name="save" value="', $item ? 'item' : 'new', '" />', PHP_EOL;
  echo '  <input type="hidden" name="id" value="', $id, '" />', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Active:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="checkbox" name="active"', ($active == 'Y') ? $checked : '', ' class="ignore" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Type:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <select name="type">', PHP_EOL;
  foreach ($list_types as $list_type) {
    echo '      <option value="', $list_type, '"', ($list_type == $type) ? $selected : '', '>', $list_type, '</option>', PHP_EOL;
  }
  echo '    </select>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Date/Time:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="datetime" value="', $datetime, '" />', PHP_EOL;
  echo '    <div class="description">The current format is YYYY-MM-DD HH:MM:SS</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Title:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="title" value="', $title, '" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Cover URI:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="coveruri" value="', $coveruri, '" />', PHP_EOL;
  echo '    <div class="clear"></div>', PHP_EOL;
  echo '    <div class="description">This can be in the form of a local link such as "/images/events/123.jpg" or even a YouTube link such as "http://www.youtube.com/watch?v=QiI0xHAonxY"</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Cover title:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="cover_title" value="', $cover_title, '" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Cover link:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="cover_href" value="', $cover_href, '" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Cover in the centre:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="radio" name="cover_main" id="cover_main_y" value="Y" class="ignore"', ($cover_main == 'Y') ? $checked : '', ' />', PHP_EOL;
  echo '    <label for="cover_main_y" class="ignore">Yes</label>', PHP_EOL;
  echo '    <input type="radio" name="cover_main" id="cover_main_n" value="N" class="ignore"', ($cover_main == 'N') ? $checked : '', ' />', PHP_EOL;
  echo '    <label for="cover_main_n" class="ignore">No</label>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Guest URI:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="guesturi" value="', $guesturi, '" />', PHP_EOL;
  echo '    <div class="description">Image preferable optimal size: 150x150px</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Content:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <textarea rows="8" cols="30" name="content">', PHP_EOL, $content, '</textarea>', PHP_EOL;
  echo '    <div class="clear"></div>', PHP_EOL;
  echo '    <input type="radio" name="content_is_html" id="content_is_html_y" value="Y" class="ignore"', ($content_is_html == 'Y') ? $checked : '', ' />', PHP_EOL;
  echo '    <label for="content_is_html_y" class="ignore">HTML</label>', PHP_EOL;
  echo '    <input type="radio" name="content_is_html" id="content_is_html_n" value="N" class="ignore"', ($content_is_html == 'N') ? $checked : '', ' />', PHP_EOL;
  echo '    <label for="content_is_html_n" class="ignore">Plain text</label>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Suppliment:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <textarea rows="8" cols="30" name="supp">', PHP_EOL, $supp, '</textarea>', PHP_EOL;
  echo '    <div class="clear"></div>', PHP_EOL;
  echo '    <input type="radio" name="supp_is_html" id="supp_is_html_y" value="Y" class="ignore"', ($supp_is_html == 'Y') ? $checked : '', ' />', PHP_EOL;
  echo '    <label for="supp_is_html_y" class="ignore">HTML</label>', PHP_EOL;
  echo '    <input type="radio" name="supp_is_html" id="supp_is_html_n" value="N" class="ignore"', ($supp_is_html == 'N') ? $checked : '', ' />', PHP_EOL;
  echo '    <label for="supp_is_html_n" class="ignore">Plain text</label>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="breakBoth"></div>', PHP_EOL;
  echo '  <input type="submit" value="Save" id="save_btn" class="custom_btn" />', PHP_EOL;
  echo '</form>', PHP_EOL;
  echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $id, '" class="custom_btn delete">Delete</a>', PHP_EOL;
}

function delete_item() {
  global $mysqli;
  $return_value = false;
  if (isset($_GET['confirm'])) {
    if ($_GET['confirm']) {
      if (@$stmt = $mysqli->prepare('UPDATE `events` SET active=\'N\', deleted=\'Y\' WHERE id=? LIMIT 1')) {
        $stmt->bind_param('i', $_GET['delete']);
        $stmt->execute();
        if (!$stmt->errno) {
          echo '<div class="feedback_msg success">', PHP_EOL;
          echo '  Successfully deleted item.', PHP_EOL;
          $return_value = true;
        }
        else {
          echo '<div class="feedback_msg failed">', PHP_EOL;
          echo '  Failed to delete item.', PHP_EOL;
        }
        $stmt->close();
      }
      else {
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  Failed to delete item and could not connect to the database.', PHP_EOL;
      }
    }
    else {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Did not delete item.', PHP_EOL;
    }
    echo '</div>', PHP_EOL;
  }
  else {
    echo '<p>Are you sure you want to delete this item?</p>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $_GET['delete'], '&confirm=1" class="custom_btn delete">Confirm delete</a>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $_GET['delete'], '&confirm=0" class="custom_btn">Leave untouched</a>', PHP_EOL;
  }
  return $return_value;
}

function save_item() {
  global $mysqli;
  $return_value = false;
  if ($_POST['save'] == 'item') {
    $sql = 'UPDATE `events` SET active=?, type=?, datetime=?, title=?, coveruri=?, cover_title=?, cover_href=?, cover_main=?, guesturi=?, content=?, content_is_html=?, supp=?, supp_is_html=? WHERE id=' . $_POST['id'] . ' LIMIT 1';
    $feedback_action_present = 'update';
    $feedback_action_past = 'updated';
  }
  elseif ($_POST['save'] == 'new') {
    $sql = 'INSERT INTO `events` (active, type, datetime, title, coveruri, cover_title, cover_href, cover_main, guesturi, content, content_is_html, supp, supp_is_html) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $feedback_action_present = 'insert';
    $feedback_action_past = 'inserted';
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Internal error!', PHP_EOL;
    echo '</div>', PHP_EOL;
    return false;
  }
  $active = isset($_POST['active']) ? 'Y' : 'N';
  $_POST['datetime'] = str_replace('&', '&amp;', $_POST['datetime']);
  $_POST['title'] = str_replace('&', '&amp;', $_POST['title']);
  $_POST['coveruri'] = str_replace('&', '&amp;', $_POST['coveruri']);
  $_POST['cover_title'] = str_replace('&', '&amp;', $_POST['cover_title']);
  $_POST['cover_href'] = str_replace('&', '&amp;', $_POST['cover_href']);
  $_POST['guesturi'] = str_replace('&', '&amp;', $_POST['guesturi']);
  $_POST['content'] = str_replace('&', '&amp;', $_POST['content']);
  $_POST['supp'] = str_replace('&', '&amp;', $_POST['supp']);
  if (@$stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('sssssssssssss', $active, $_POST['type'], $_POST['datetime'], $_POST['title'], $_POST['coveruri'], $_POST['cover_title'], $_POST['cover_href'], $_POST['cover_main'], $_POST['guesturi'], $_POST['content'], $_POST['content_is_html'], $_POST['supp'], $_POST['supp_is_html']);
    $stmt->execute();
    if (!$stmt->errno) {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Successfully ', $feedback_action_past, ' item.', PHP_EOL;
      $return_value = true;
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Failed to ', $feedback_action_present, ' item.', PHP_EOL;
    }
    $stmt->close();
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Failed to ', $feedback_action_present, ' item and could not connect to the database.', PHP_EOL;
  }
  echo '</div>', PHP_EOL;
  return $return_value;
}

if (isset($_GET['item'])) {
  list_item((int)$_GET['item']);
}
elseif (isset($_GET['new'])) {
  list_item(0);
}
elseif (isset($_GET['delete'])) {
  if (delete_item()) {
    list_items();
  }
}
elseif (isset($_POST['save'])) {
  save_item();
  if ($_POST['save'] == 'item') {
    list_item((int)$_POST['id']);
  }
  else {
    list_items();
  }
}
else {
  list_items();
}
?>
<div class="dottedBreak dotted"></div>
</div>
</div>
<!-- CONTENT -->

<!-- FOOTER -->
<?php
include_once(HOME_DIR . 'scripts/footerSnippet.php');
echo footerSnippet();
?>
<!-- FOOTER -->

</body>
</html>