<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/scripts/initialisation.php');
$privileges = PERMISSIONS_EDIT_POST_ITS; // the privileges needed to see the site - refer to /secure/account_types.txt
beforeFileLoad();
?>
<?php
include_once(HOME_DIR . 'scripts/headTagsSnippet.php');
echo showDOCTYPE();
echo headTagsSnippet('ThePo!ntC.O.C. - Edit Post-its', false);
?>
  <script src="<?php echo HOME_DIR; ?>scripts/edit_post_its.js"></script>
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
<h1>Edit Post-its</h1>
<div class="dottedBreak dotted"></div>
<?php
$post_its = array(
  '\'Check these out\' 1',
  '\'Check these out\' 2',
  '\'Check these out\' 3',
  '\'Check these out\' 4',
  '\'Wee Word\' 1',
  '\'Wee Word\' 2',
  '\'Wee Word\' 3'
  );
$post_its_class_addon = array(
  'one',
  'two',
  'three',
  'four',
  'one weew',
  'two weew',
  'three weew'
  );

function list_items() {
  global $post_its;
  echo '<div class="items_list">', PHP_EOL;
  for ($i = 0; $i < 7; ++$i) {
    echo '  <a href="', basename($_SERVER['PHP_SELF']), '?item=', $i + 1, '">Post-it #', $i + 1, ' (', $post_its[$i], ')</a>', PHP_EOL;
  }
  echo '</div>', PHP_EOL;
  
  echo '<div class="break"></div>', PHP_EOL;
  return true;
}

function list_item($item) {
  global $mysqli, $post_its, $post_its_class_addon;
  $checked = ' checked="checked"';
  $selected = ' selected="selected"';
  echo '<a href="', basename($_SERVER['PHP_SELF']), '" class="custom_btn">Cancel/Back</a>', PHP_EOL;
  if (!($item > 0) || !($item <= sizeof($post_its))) {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Couldn\'t recognise selected item. Please go back and try again.', PHP_EOL;
    echo '</div>', PHP_EOL;
    return false;
  }
  echo '<div class="post-it-note ', $post_its_class_addon[$item - 1], '">', PHP_EOL;
  echo '  <div class="post-it-content">', PHP_EOL;
  echo '    <div class="blank"></div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '</div>', PHP_EOL;
  if (@$stmt = $mysqli->prepare('SELECT datetime, title, subtitle, content, coveruri, cover_title, cover_href FROM `post_it_notes` WHERE post_it_n = ? ORDER BY timestamp DESC LIMIT 1')) {
    $stmt->bind_param('i', $item);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($datetime, $title, $subtitle, $content, $coveruri, $cover_title, $cover_href);
    $stmt->fetch();
    $stmt->close();
    if (!is_null($datetime)) {
      $datetime = strtotime($datetime);
    }
  }
  else {
    $datetime = strtotime('next week 19:00');
    $title = '';
    $subtitle = '';
    $content = '';
    $coveruri = '';
    $cover_title = '';
    $cover_href = '';
  }
  if (!is_null($datetime)) {
    $datetime = date('Y-m-d H:i:s', $datetime);
  }
  echo '<form action="', basename($_SERVER['PHP_SELF']), '" method="post" class="item_form">', PHP_EOL;
  echo '  <input type="hidden" name="save" value="', $item, '" />', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Post-it:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    Post-it #', $item, ' (', $post_its[$item - 1], ')', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Date/Time:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="datetime" id="form_datetime" value="', $datetime, '" />', PHP_EOL;
  echo '    <div class="description">The current format is YYYY-MM-DD HH:MM:SS</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Title:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="title" id="form_title" value="', $title, '" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Subtitle:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="subtitle" id="form_subtitle" value="', $subtitle, '" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Content:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <textarea rows="8" cols="30" name="content" id="form_content">', PHP_EOL, $content, '</textarea>', PHP_EOL;
  echo '    <div class="description"></div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Cover URI:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="coveruri" id="form_coveruri" value="', $coveruri, '" />', PHP_EOL;
  echo '    <div class="clear"></div>', PHP_EOL;
  echo '    <div class="description">This can be in the form of a local link such as "/images/events/123.jpg"</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Cover title:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="cover_title" id="form_cover_title" value="', $cover_title, '" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Cover link:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="cover_href" id="form_cover_href" value="', $cover_href, '" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="breakBoth"></div>', PHP_EOL;
  echo '  <input type="submit" value="Save" id="save_btn" class="custom_btn" />', PHP_EOL;
  echo '</form>', PHP_EOL;
  echo '<a href="', basename($_SERVER['PHP_SELF']), '?clear=', $item, '" class="custom_btn delete">Clear</a>', PHP_EOL;
}

function clear_item() {
  global $mysqli;
  $return_value = false;
  if (isset($_GET['confirm'])) {
    if ($_GET['confirm']) {
      if (@$stmt = $mysqli->prepare('INSERT INTO `post_it_notes` (post_it_n, content) VALUES(?, \'\')')) {
        $stmt->bind_param('i', $_GET['clear']);
        $stmt->execute();
        if (!$stmt->errno) {
          echo '<div class="feedback_msg success">', PHP_EOL;
          echo '  Successfully cleared item.', PHP_EOL;
          $return_value = true;
        }
        else {
          echo '<div class="feedback_msg failed">', PHP_EOL;
          echo '  Failed to clear item: ', $stmt->error, PHP_EOL;
        }
        $stmt->close();
      }
      else {
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  Failed to clear item and could not connect to the database.', PHP_EOL;
      }
    }
    else {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Did not clear item.', PHP_EOL;
    }
    echo '</div>', PHP_EOL;
  }
  else {
    echo '<p>Are you sure you want to clear this item?</p>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?clear=', $_GET['clear'], '&confirm=1" class="custom_btn delete">Confirm clear</a>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?clear=', $_GET['clear'], '&confirm=0" class="custom_btn">Leave untouched</a>', PHP_EOL;
  }
  return $return_value;
}

function save_item() {
  global $mysqli;
  $return_value = false;
  $_POST['datetime'] = str_replace('&', '&amp;', $_POST['datetime']);
  if (!$_POST['datetime']) {
    $_POST['datetime'] = null;
  }
  $_POST['title'] = str_replace('&', '&amp;', $_POST['title']);
  $_POST['subtitle'] = str_replace('&', '&amp;', $_POST['subtitle']);
  $_POST['content'] = str_replace('&', '&amp;', $_POST['content']);
  $_POST['coveruri'] = str_replace('&', '&amp;', $_POST['coveruri']);
  $_POST['cover_title'] = str_replace('&', '&amp;', $_POST['cover_title']);
  $_POST['cover_href'] = str_replace('&', '&amp;', $_POST['cover_href']);
  if (@$stmt = $mysqli->prepare('INSERT INTO `post_it_notes` (post_it_n, datetime, title, subtitle, content, coveruri, cover_title, cover_href) VALUES(?, ?, ?, ?, ?, ?, ?, ?)')) {
    $stmt->bind_param('isssssss', $_POST['save'], $_POST['datetime'], $_POST['title'], $_POST['subtitle'], $_POST['content'], $_POST['coveruri'], $_POST['cover_title'], $_POST['cover_href']);
    $stmt->execute();
    if (!$stmt->errno) {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Successfully updated item.', PHP_EOL;
      $return_value = true;
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Failed to update item.', PHP_EOL;
    }
    $stmt->close();
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Failed to update item and could not connect to the database.', PHP_EOL;
  }
  echo '</div>', PHP_EOL;
  return $return_value;
}

if (isset($_GET['item'])) {
  list_item((int)$_GET['item']);
}
elseif (isset($_GET['clear'])) {
  if (clear_item()) {
    list_items();
  }
}
elseif (isset($_POST['save'])) {
  save_item();
  list_item((int)$_POST['save']);
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