<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/scripts/initialisation.php');
$privileges = PERMISSIONS_EDIT_MUSOS; // the privileges needed to see the site - refer to /secure/account_types.txt
beforeFileLoad();
?>
<?php
include_once(HOME_DIR . 'scripts/headTagsSnippet.php');
echo showDOCTYPE();
echo headTagsSnippet('ThePo!ntC.O.C. - Edit Musos', false);
?>
  <link rel="stylesheet" href="<?php echo HOME_DIR; ?>css/edit_musos.css" />
  <script src="<?php echo HOME_DIR; ?>scripts/edit_musos.js"></script>
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
<h1>Edit Musos</h1>
<div class="dottedBreak dotted"></div>
<?php
$items_per_page = 5;
$num_services = num_services();
$next_service_item_number = next_service_item_number();
$next_service_page_number = ceil($next_service_item_number / $items_per_page);
$page_number = isset($_GET['page']) ? (int)$_GET['page'] : $next_service_page_number;
if ($page_number < 1) {
  $page_number = 1;
}
$pages = ceil($num_services / $items_per_page);
$locations = array(
  'Dundee' => '19:00:00',
  'St Andrews' => '11:00:00'
  );

function next_service_item_number() {
  global $mysqli;
  $num_services = 0;
  $nextSunday = date('Y-m-d', strtotime('next Sunday'));
  if (@$result = $mysqli->query('SELECT count(*) FROM `services` WHERE datetime >= \'' . $nextSunday . '\'')) {
    list($num_services) = $result->fetch_row();
    $result->close();
  }
  return $num_services;
}

function num_services() {
  global $mysqli;
  $num_services = 0;
  if (@$result = $mysqli->query('SELECT COUNT(*) FROM `services`')) {
    list($num_services) = $result->fetch_row();
    $result->close();
  }
  return $num_services;
}

function list_services() {
  global $mysqli, $items_per_page, $num_services, $page_number, $pages;
  $nextSunday = date('Y-m-d', strtotime('next Sunday'));
  $return_value = false;
  $limit_start = ($page_number - 1) * $items_per_page;
  if (@$stmt = $mysqli->prepare('SELECT id, datetime, location, generalRota, songsNames, songsKeys, worshipRota FROM `services` ORDER BY datetime DESC LIMIT ?, ?')) {
    $stmt->bind_param('ii', $limit_start, $items_per_page);
    $stmt->execute();
    $stmt->store_result();
    if (!$stmt->errno) {
      $return_value = true;
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Failed to retrieve list of services.', PHP_EOL;
      echo '</div>', PHP_EOL;
    }
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?new" class="custom_btn">Add service</a>', PHP_EOL;
    $stmt->bind_result($dbid, $dbdatetime, $dblocation, $dbgeneralRota, $dbsongsNames, $dbsongsKeys, $dbworshipRota);
    echo '<div class="items_list">', PHP_EOL;
    while ($stmt->fetch()) {
      $next_service_class = '';
      $next_service_html = '';
      if (date('Y-m-d', strtotime($dbdatetime)) == date('Y-m-d', strtotime($nextSunday))) {
        $next_service_class = ' class="next"';
        $next_service_html = ' <em>NEXT</em>';
      }
      echo '  <a href="', basename($_SERVER['PHP_SELF']), '?service=', $dbid, '"', $next_service_class, '>', date('D <\b>jS M</\b> \'y \a\t g:ia', strtotime($dbdatetime)), ' in <b>', $dblocation, '</b>', $next_service_html, '</a>', PHP_EOL;
    }
    echo '</div>', PHP_EOL;
    $stmt->close();
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Failed to retrieve list of services and could not connect to the database.', PHP_EOL;
    echo '</div>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?new" class="custom_btn">Add service</a>', PHP_EOL;
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

function new_service() {
  global $locations;
  if (isset($_POST['addSongCallback']) && $_POST['addSongCallback'] == 'new') {
    $dbdatetime = $_POST['dbdatetime'];
    $nextSunday = $dbdatetime;
    $dblocation = $_POST['dblocation'];
    $dbgeneralRota = $_POST['dbgeneralRota'];
    $dbsongsNames = $_POST['dbsongsNames'];
    foreach ($dbsongsNames as &$songName) {
      $songName = urldecode($songName);
    }
    unset($songName);
    $dbsongsKeys = $_POST['dbsongsKeys'];
    foreach ($dbsongsNames as &$songKey) {
      $songKey = urldecode($songKey);
    }
    unset($songKey);
    $dbworshipRota = $_POST['dbworshipRota'];
  }
  else {
    $dbdatetime = date('Y-m-d H:i:s', strtotime('next Sunday'));
    $nextSunday = date('Y-m-d', strtotime($dbdatetime));
    $dblocation = key($locations);
    $dbgeneralRota = 'CoffeePoint: 
Door: 
Money: 
Kidz: 
Media: 
Cleaning: ';
    $dbsongsNames = array();
    $dbsongsKeys = array();
    $dbworshipRota = 'Drums: 
Bass: 
Electric: 
Acoustic: 
Keys: 
Lead singer: 
Backing vocals: ';
  }
  echo '<a href="', basename($_SERVER['PHP_SELF']), '" class="custom_btn">Cancel/Back</a>', PHP_EOL;
  echo '<form action="', basename($_SERVER['PHP_SELF']), '" method="post" class="item_form">', PHP_EOL;
  echo '  <input type="hidden" name="save" value="new" />', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Date/Time:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="dbdatetime" value="', $nextSunday, '" /> <span class="dbTime">', date('g:ia', strtotime($locations[$dblocation])), '</span>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Location:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <select name="dblocation">', PHP_EOL;
  foreach ($locations as $location => $locationTime) {
    $selected = '';
    if ($location == $dblocation) {
      $selected = ' selected="selected"';
    }
    echo '      <option value="', $location, '"', $selected, '>', $location, '</option>', PHP_EOL;
  }
  echo '    </select>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>General Rota:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <textarea rows="8" cols="30" name="dbgeneralRota">', $dbgeneralRota, '</textarea>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Songs:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <div id="dbsongsList">';
  foreach ($dbsongsNames as $n => $songName) {
    echo '<div>';
    echo '<div class="custom_btn song">';
    echo '<input type="hidden" name="dbsongsNames[]" value="', $songName, '" />';
    echo '<input type="hidden" name="dbsongsKeys[]" value="', $dbsongsKeys[$n], '" />';
    if ($songName != '--preach') {
      echo $songName, ' (', $dbsongsKeys[$n], ')';
    }
    else {
      echo 'PREACH';
    }
    echo '</div>';
    echo '<div class="custom_btn delete" name="deleteSong[]">X</div>';
    echo '<div class="custom_btn" name="moveSongUp[]">&uarr;</div>';
    echo '<div class="custom_btn" name="moveSongDown[]">&darr;</div>';
    echo '</div>';
  }
  echo '    </div>', PHP_EOL;
  echo '    <a href="#" id="addSong" class="custom_btn inputBtn">Add song</a>', PHP_EOL;
  echo '    <div id="addPreach" class="custom_btn inputBtn">Add preach</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Worship Rota:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <textarea rows="8" cols="30" name="dbworshipRota">', $dbworshipRota, '</textarea>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="breakBoth"></div>', PHP_EOL;
  echo '  <input type="submit" value="Save" id="save_btn" class="custom_btn" />', PHP_EOL;
  echo '</form>', PHP_EOL;
}

function list_service($service) {
  global $mysqli, $locations;
  echo '<a href="', basename($_SERVER['PHP_SELF']), '" class="custom_btn">Cancel/Back</a>', PHP_EOL;
  if (@$stmt = $mysqli->prepare('SELECT id, datetime, location, generalRota, songsNames, songsKeys, worshipRota FROM `services` WHERE id = ? LIMIT 1')) {
    $stmt->bind_param('i', $service);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($dbid, $dbdatetime, $dblocation, $dbgeneralRota, $dbsongsNames, $dbsongsKeys, $dbworshipRota);
    $stmt->fetch();
    $stmt->close();
    if (!is_null($dbsongsNames)) {
      $dbsongsNames = str_replace(array("\r\n", "\r"), "\n", $dbsongsNames);
    }
    if (!is_null($dbsongsKeys)) {
      $dbsongsKeys = str_replace(array("\r\n", "\r"), "\n", $dbsongsKeys);
    }
    if (!is_null($dbsongsNames) && strlen($dbsongsNames)) {
      $dbsongsNames = explode("\n", $dbsongsNames);
    }
    if (!is_null($dbsongsKeys) && strlen($dbsongsKeys)) {
      $dbsongsKeys = explode("\n", $dbsongsKeys);
    }
    if (isset($_POST['addSongCallback']) && $_POST['addSongCallback'] == 'service') {
      $dbid = $_POST['dbid'];
      $dbdatetime = $_POST['dbdatetime'];
      $dblocation = $_POST['dblocation'];
      $dbdatetime .= date(' H:i:s', strtotime($locations[$dblocation]));
      $dbgeneralRota = $_POST['dbgeneralRota'];
      $dbsongsNames = isset($_POST['dbsongsNames']) ? $_POST['dbsongsNames'] : null;
      if (is_array($dbsongsNames)) {
        foreach ($dbsongsNames as &$songName) {
          $songName = urldecode($songName);
        }
      }
      unset($songName);
      $dbsongsKeys = isset($_POST['dbsongsKeys']) ? $_POST['dbsongsKeys'] : null;
      if (!is_null($dbsongsKeys)) {
        foreach ($dbsongsKeys as &$songKey) {
          $songKey = urldecode($songKey);
        }
      }
      unset($songKey);
      $dbworshipRota = $_POST['dbworshipRota'];
    }
    $dbTime = date('g:ia', strtotime($dbdatetime));
    $dbdatetime = date('Y-m-d', strtotime($dbdatetime));
    echo '<form action="', basename($_SERVER['PHP_SELF']), '" method="post" class="item_form">', PHP_EOL;
    echo '  <input type="hidden" name="save" value="service" />', PHP_EOL;
    echo '  <input type="hidden" name="dbid" value="', $dbid, '" />', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Date/Time:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <input type="text" name="dbdatetime" value="', $dbdatetime, '" /> <span class="dbTime">', $dbTime, '</span>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Location:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <select name="dblocation">', PHP_EOL;
    foreach ($locations as $location => $locationTime) {
      $selected = '';
      if ($location == $dblocation) {
        $selected = ' selected="selected"';
      }
      echo '      <option value="', $location, '"', $selected, '>', $location, '</option>', PHP_EOL;
    }
    echo '    </select>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>General Rota:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <textarea rows="8" cols="30" name="dbgeneralRota">', $dbgeneralRota, '</textarea>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Songs Names:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <div id="dbsongsList">';
    if (is_array($dbsongsNames)) {
      foreach ($dbsongsNames as $n => $songName) {
        echo '<div>';
        echo '<div class="custom_btn song">';
        echo '<input type="hidden" name="dbsongsNames[]" value="', $songName, '" />';
        echo '<input type="hidden" name="dbsongsKeys[]" value="', $dbsongsKeys[$n], '" />';
        if ($songName != '--preach') {
          echo $songName, ' (', $dbsongsKeys[$n], ')';
        }
        else {
          echo 'PREACH';
        }
        echo '</div>';
        echo '<div class="custom_btn delete" name="deleteSong[]">X</div>';
        echo '<div class="custom_btn" name="moveSongUp[]">&uarr;</div>';
        echo '<div class="custom_btn" name="moveSongDown[]">&darr;</div>';
        echo '</div>';
      }
    }
    echo '    </div>', PHP_EOL;
    echo '    <a href="#" id="addSong" class="custom_btn inputBtn">Add song</a>', PHP_EOL;
    echo '    <div id="addPreach" class="custom_btn inputBtn">Add preach</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Worship Rota:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <textarea rows="8" cols="30" name="dbworshipRota">', $dbworshipRota, '</textarea>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="breakBoth"></div>', PHP_EOL;
    echo '  <input type="submit" value="Save" id="save_btn" class="custom_btn" />', PHP_EOL;
    echo '</form>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $dbid, '" class="custom_btn delete">Delete</a>', PHP_EOL;
  }
}

function delete_service() {
  global $mysqli;
  $return_value = false;
  if (isset($_GET['confirm'])) {
    if ($_GET['confirm']) {
      if (@$stmt = $mysqli->prepare('DELETE FROM `services` WHERE id=? LIMIT 1')) {
        $stmt->bind_param('i', $_GET['delete']);
        $stmt->execute();
        if (!$stmt->errno) {
          echo '<div class="feedback_msg success">', PHP_EOL;
          echo '  Successfully deleted service.', PHP_EOL;
          $return_value = true;
        }
        else {
          echo '<div class="feedback_msg failed">', PHP_EOL;
          echo '  Failed to delete service.', PHP_EOL;
        }
        $stmt->close();
      }
      else {
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  Failed to delete service and could not connect to the database.', PHP_EOL;
      }
    }
    else {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Did not delete service.', PHP_EOL;
    }
    echo '</div>', PHP_EOL;
  }
  else {
    echo '<p>Are you sure you want to delete this service?</p>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $_GET['delete'], '&confirm=1" class="custom_btn delete">Confirm delete</a>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $_GET['delete'], '&confirm=0" class="custom_btn">Leave untouched</a>', PHP_EOL;
  }
  return $return_value;
}

function save_new_service() {
  global $mysqli, $locations;
  if (isset($locations[$_POST['dblocation']])) {
    $dbdatetime = $_POST['dbdatetime'] . ' ' . $locations[$_POST['dblocation']];
    $dblocation = $_POST['dblocation'];
  }
  else {
    $dbdatetime = $_POST['dbdatetime'] . ' ' . current($locations);
    $dblocation = key($locations);
  }
  $dbsongsNames = isset($_POST['dbsongsNames']) ? implode("\n", $_POST['dbsongsNames']) : null;
  $dbsongsKeys = isset($_POST['dbsongsKeys']) ? implode("\n", $_POST['dbsongsKeys']) : null;
  $return_value = false;
  if (@$stmt = $mysqli->prepare('INSERT INTO `services` (datetime, location, generalRota, songsNames, songsKeys, worshipRota) VALUES(?, ?, ?, ?, ?, ?)')) {
    $stmt->bind_param('ssssss', $dbdatetime, $dblocation, $_POST['dbgeneralRota'], $dbsongsNames, $dbsongsKeys, $_POST['dbworshipRota']);
    $stmt->execute();
    if (!$stmt->errno) {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Successfully inserted service.', PHP_EOL;
      $return_value = true;
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Failed to insert service.', PHP_EOL;
    }
    $stmt->close();
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Failed to insert service and could not connect to the database.', PHP_EOL;
  }
  echo '</div>', PHP_EOL;
  return $return_value;
}

function save_service() {
  global $mysqli, $locations;
  if (isset($locations[$_POST['dblocation']])) {
    $dbdatetime = $_POST['dbdatetime'] . ' ' . $locations[$_POST['dblocation']];
    $dblocation = $_POST['dblocation'];
  }
  else {
    $dbdatetime = $_POST['dbdatetime'] . ' ' . current($locations);
    $dblocation = key($locations);
  }
  $dbsongsNames = isset($_POST['dbsongsNames']) ? implode("\n", $_POST['dbsongsNames']) : null;
  $dbsongsKeys = isset($_POST['dbsongsKeys']) ? implode("\n", $_POST['dbsongsKeys']) : null;
  $return_value = false;
  if (@$stmt = $mysqli->prepare('UPDATE `services` SET datetime=?, location=?, generalRota=?, songsNames=?, songsKeys=?, worshipRota=? WHERE id=? LIMIT 1')) {
    $stmt->bind_param('ssssssi', $dbdatetime, $dblocation, $_POST['dbgeneralRota'], $dbsongsNames, $dbsongsKeys, $_POST['dbworshipRota'], $_POST['dbid']);
    $stmt->execute();
    if (!$stmt->errno) {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Successfully updated service.', PHP_EOL;
      $return_value = true;
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Failed to update service.', PHP_EOL;
    }
    $stmt->close();
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Failed to update service and could not connect to the database.', PHP_EOL;
  }
  echo '</div>', PHP_EOL;
  return $return_value;
}

if (isset($_GET['service'])) {
  list_service((int)$_GET['service']);
}
elseif (isset($_GET['new'])) {
  new_service();
}
elseif (isset($_POST['addSongCallback']) && $_POST['addSongCallback'] == 'new') {
  new_service();
}
elseif (isset($_GET['delete']) && !isset($_GET['confirm'])) {
  delete_service();
}
elseif (isset($_GET['delete']) && isset($_GET['confirm'])) {
  delete_service();
  list_services();
}
elseif (isset($_POST['save']) && $_POST['save'] == 'new') {
  save_new_service();
  list_services();
}
elseif (isset($_POST['save']) && $_POST['save'] == 'service') {
  save_service();
  list_service((int)$_POST['dbid']);
}
elseif (isset($_POST['addSongCallback']) && $_POST['addSongCallback'] == 'service') {
  list_service((int)$_POST['dbid']);
}
else {
  list_services();
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