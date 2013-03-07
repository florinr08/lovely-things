<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/scripts/initialisation.php');
$privileges = PERMISSIONS_MODERATE_USERS; // the privileges needed to see the site - refer to /secure/account_types.txt
beforeFileLoad();
?>
<?php
include_once(HOME_DIR . 'scripts/headTagsSnippet.php');
echo showDOCTYPE();
echo headTagsSnippet('ThePo!ntC.O.C. - Manage Users', false);
?>
  <link rel="stylesheet" href="<?php echo HOME_DIR; ?>css/login.css" />
  <!--script src="<?php echo HOME_DIR; ?>scripts/users.js"></script-->
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
<h1>Manage Users</h1>
<div class="dottedBreak dotted"></div>
<?php
$items_per_page = 20;
$num_items = num_items();
$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page_number < 1) {
  $page_number = 1;
}
$pages = ceil($num_items / $items_per_page);

function num_items() {
  global $mysqli;
  $num_items = 0;
  if (@$result = $mysqli->query('SELECT COUNT(*) FROM `users` WHERE deleted = \'N\'')) {
    list($num_items) = $result->fetch_row();
    $result->close();
  }
  return $num_items;
}

function list_items() {
  global $mysqli, $items_per_page, $page_number, $pages;
  $return_value = false;
  $limit_start = ($page_number - 1) * $items_per_page;
  if (@$stmt = $mysqli->prepare('SELECT id, username, fullName, PERMISSIONS_FULL, PERMISSIONS_MODERATE_USERS, PERMISSIONS_EDIT_OTHER, PERMISSIONS_EDIT_CORETEAM, PERMISSIONS_EDIT_MUSOS, PERMISSIONS_EDIT_ROTAS, PERMISSIONS_EDIT_EVENTS, PERMISSIONS_EDIT_POST_ITS, PERMISSIONS_VIEW_CORETEAM, PERMISSIONS_VIEW_MUSOS, PERMISSIONS_VIEW_ROTAS, PERMISSIONS_VIEW_CALENDARS, PERMISSIONS_VIEW_OTHER, active FROM `users` WHERE deleted = \'N\' LIMIT ?, ?')) {
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
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?new" class="custom_btn">Add item</a>', PHP_EOL;
    $stmt->bind_result($id, $username, $fullName, $PERMISSIONS_FULL, $PERMISSIONS_MODERATE_USERS, $PERMISSIONS_EDIT_OTHER, $PERMISSIONS_EDIT_CORETEAM, $PERMISSIONS_EDIT_MUSOS, $PERMISSIONS_EDIT_ROTAS, $PERMISSIONS_EDIT_EVENTS, $PERMISSIONS_EDIT_POST_ITS, $PERMISSIONS_VIEW_CORETEAM, $PERMISSIONS_VIEW_MUSOS, $PERMISSIONS_VIEW_ROTAS, $PERMISSIONS_VIEW_CALENDARS, $PERMISSIONS_VIEW_OTHER, $active);
    echo '<div class="items_list">', PHP_EOL;
    $strtr_from = 'YN';
    $strtr_to = 'x-';
    while ($stmt->fetch()) {
      $PERMISSIONS_FULL = strtr($PERMISSIONS_FULL, $strtr_from, $strtr_to);
      $PERMISSIONS_MODERATE_USERS = strtr($PERMISSIONS_MODERATE_USERS, $strtr_from, $strtr_to);
      $PERMISSIONS_EDIT_OTHER = strtr($PERMISSIONS_EDIT_OTHER, $strtr_from, $strtr_to);
      $PERMISSIONS_EDIT_CORETEAM = strtr($PERMISSIONS_EDIT_CORETEAM, $strtr_from, $strtr_to);
      $PERMISSIONS_EDIT_MUSOS = strtr($PERMISSIONS_EDIT_MUSOS, $strtr_from, $strtr_to);
      $PERMISSIONS_EDIT_ROTAS = strtr($PERMISSIONS_EDIT_ROTAS, $strtr_from, $strtr_to);
      $PERMISSIONS_EDIT_EVENTS = strtr($PERMISSIONS_EDIT_EVENTS, $strtr_from, $strtr_to);
      $PERMISSIONS_EDIT_POST_ITS = strtr($PERMISSIONS_EDIT_POST_ITS, $strtr_from, $strtr_to);
      $PERMISSIONS_VIEW_CORETEAM = strtr($PERMISSIONS_VIEW_CORETEAM, $strtr_from, $strtr_to);
      $PERMISSIONS_VIEW_MUSOS = strtr($PERMISSIONS_VIEW_MUSOS, $strtr_from, $strtr_to);
      $PERMISSIONS_VIEW_ROTAS = strtr($PERMISSIONS_VIEW_ROTAS, $strtr_from, $strtr_to);
      $PERMISSIONS_VIEW_CALENDARS = strtr($PERMISSIONS_VIEW_CALENDARS, $strtr_from, $strtr_to);
      $PERMISSIONS_VIEW_OTHER = strtr($PERMISSIONS_VIEW_OTHER, $strtr_from, $strtr_to);
      echo '  <a href="', basename($_SERVER['PHP_SELF']), '?item=', $id, '"', ($active == 'N') ? ' class="inactive"' : '' , '>', $fullName, ' (\'<b>', $username, '</b>\') ', $PERMISSIONS_FULL, $PERMISSIONS_MODERATE_USERS, $PERMISSIONS_EDIT_OTHER, $PERMISSIONS_EDIT_CORETEAM, $PERMISSIONS_EDIT_MUSOS, $PERMISSIONS_EDIT_ROTAS, $PERMISSIONS_EDIT_EVENTS, $PERMISSIONS_EDIT_POST_ITS, $PERMISSIONS_VIEW_CORETEAM, $PERMISSIONS_VIEW_MUSOS, $PERMISSIONS_VIEW_ROTAS, $PERMISSIONS_VIEW_CALENDARS, $PERMISSIONS_VIEW_OTHER, ($active == 'N') ? ' <em>INACTIVE</em>' : '' , '</a>', PHP_EOL;
    }
    echo '</div>', PHP_EOL;
    $stmt->close();
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Failed to retrieve list of items and could not connect to the database.', PHP_EOL;
    echo '</div>', PHP_EOL;
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?new" class="custom_btn">Add item</a>', PHP_EOL;
  }
  
  if ($page_number > 1) {
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?page=', ($page_number - 1), '" class="custom_btn pages">&lt; Previous</a>', PHP_EOL;
  }
  else {
    echo '<div class="custom_btn pages disabled">&lt; Previous</div>', PHP_EOL;
  }
  
  if ($pages > $page_number) {
    echo '<a href="', basename($_SERVER['PHP_SELF']), '?page=', ($page_number + 1), '" class="custom_btn pages">Next &gt;</a>', PHP_EOL;
  }
  else {
    echo '<div class="custom_btn pages disabled">Next &gt;</div>', PHP_EOL;
  }
  
  echo '<div class="break"></div>', PHP_EOL;
  return $return_value;
}

function list_item($item) {
  global $mysqli;
  $checked = ' checked="checked"';
  $selected = ' selected="selected"';
  echo '<a href="', basename($_SERVER['PHP_SELF']), '" class="custom_btn">Cancel/Back</a>', PHP_EOL;
  if ($item && @$stmt = $mysqli->prepare('SELECT active, id, username, fullName, PERMISSIONS_FULL, PERMISSIONS_MODERATE_USERS, PERMISSIONS_EDIT_OTHER, PERMISSIONS_EDIT_CORETEAM, PERMISSIONS_EDIT_MUSOS, PERMISSIONS_EDIT_ROTAS, PERMISSIONS_EDIT_EVENTS, PERMISSIONS_EDIT_POST_ITS, PERMISSIONS_VIEW_CORETEAM, PERMISSIONS_VIEW_MUSOS, PERMISSIONS_VIEW_ROTAS, PERMISSIONS_VIEW_CALENDARS, PERMISSIONS_VIEW_OTHER FROM `users` WHERE deleted = \'N\' AND id = ? LIMIT 1')) {
    $stmt->bind_param('i', $item);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($active, $id, $username, $fullName, $PERMISSIONS_FULL, $PERMISSIONS_MODERATE_USERS, $PERMISSIONS_EDIT_OTHER, $PERMISSIONS_EDIT_CORETEAM, $PERMISSIONS_EDIT_MUSOS, $PERMISSIONS_EDIT_ROTAS, $PERMISSIONS_EDIT_EVENTS, $PERMISSIONS_EDIT_POST_ITS, $PERMISSIONS_VIEW_CORETEAM, $PERMISSIONS_VIEW_MUSOS, $PERMISSIONS_VIEW_ROTAS, $PERMISSIONS_VIEW_CALENDARS, $PERMISSIONS_VIEW_OTHER);
    $stmt->fetch();
    $stmt->close();
  }
  else {
    $id = $item;
    $active = 'Y';
    if (isset($_POST['id'])) {
      $active = isset($_POST['active']) ? 'Y' : 'N';
    }
    $fullName = isset($_POST['fullName']) ? $_POST['fullName'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $PERMISSIONS_FULL = isset($_POST['PERMISSIONS_FULL']) ? 'Y' : 'N';
    $PERMISSIONS_MODERATE_USERS = isset($_POST['PERMISSIONS_MODERATE_USERS']) ? 'Y' : 'N';
    $PERMISSIONS_EDIT_OTHER = isset($_POST['PERMISSIONS_EDIT_OTHER']) ? 'Y' : 'N';
    $PERMISSIONS_EDIT_CORETEAM = isset($_POST['PERMISSIONS_EDIT_CORETEAM']) ? 'Y' : 'N';
    $PERMISSIONS_EDIT_MUSOS = isset($_POST['PERMISSIONS_EDIT_MUSOS']) ? 'Y' : 'N';
    $PERMISSIONS_EDIT_ROTAS = isset($_POST['PERMISSIONS_EDIT_ROTAS']) ? 'Y' : 'N';
    $PERMISSIONS_EDIT_EVENTS = isset($_POST['PERMISSIONS_EDIT_EVENTS']) ? 'Y' : 'N';
    $PERMISSIONS_EDIT_POST_ITS = isset($_POST['PERMISSIONS_EDIT_POST_ITS']) ? 'Y' : 'N';
    $PERMISSIONS_VIEW_CORETEAM = isset($_POST['PERMISSIONS_VIEW_CORETEAM']) ? 'Y' : 'N';
    $PERMISSIONS_VIEW_MUSOS = isset($_POST['PERMISSIONS_VIEW_MUSOS']) ? 'Y' : 'N';
    $PERMISSIONS_VIEW_ROTAS = isset($_POST['PERMISSIONS_VIEW_ROTAS']) ? 'Y' : 'N';
    $PERMISSIONS_VIEW_CALENDARS = isset($_POST['PERMISSIONS_VIEW_CALENDARS']) ? 'Y' : 'N';
    $PERMISSIONS_VIEW_OTHER = isset($_POST['PERMISSIONS_VIEW_OTHER']) ? 'Y' : 'N';
  }
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
  echo '    <div>Full Name:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="fullName" value="', $fullName, '" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Username:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <input type="text" name="username" value="', $username, '" />', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Password:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  if ($item) {
    echo '    <a href="', basename($_SERVER['PHP_SELF']), '?reset_password=', $id, '" class="custom_btn">Reset Password</a>', PHP_EOL;
  }
  else {
    echo '    <input type="password" name="password" value="" />', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Confirm Password:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <input type="password" name="password_confirm" value="" />', PHP_EOL;
  }
  echo '  </div>', PHP_EOL;
  echo '  <div class="label">', PHP_EOL;
  echo '    <div>Permissions:</div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="input">', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_FULL" id="PERMISSIONS_FULL"', ($PERMISSIONS_FULL == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_FULL">Full</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_MODERATE_USERS" id="PERMISSIONS_MODERATE_USERS"', ($PERMISSIONS_MODERATE_USERS == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_MODERATE_USERS">Moderate Users</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_EDIT_OTHER" id="PERMISSIONS_EDIT_OTHER"', ($PERMISSIONS_EDIT_OTHER == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_EDIT_OTHER">Edit Other</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_EDIT_CORETEAM" id="PERMISSIONS_EDIT_CORETEAM"', ($PERMISSIONS_EDIT_CORETEAM == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_EDIT_CORETEAM">Edit CoreTeam</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_EDIT_MUSOS" id="PERMISSIONS_EDIT_MUSOS"', ($PERMISSIONS_EDIT_MUSOS == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_EDIT_MUSOS">Edit Musos</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_EDIT_ROTAS" id="PERMISSIONS_EDIT_ROTAS"', ($PERMISSIONS_EDIT_ROTAS == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_EDIT_ROTAS">Edit Rotas</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_EDIT_EVENTS" id="PERMISSIONS_EDIT_EVENTS"', ($PERMISSIONS_EDIT_EVENTS == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_EDIT_EVENTS">Edit Events</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_EDIT_POST_ITS" id="PERMISSIONS_EDIT_POST_ITS"', ($PERMISSIONS_EDIT_POST_ITS == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_EDIT_POST_ITS">Edit Post-its</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_VIEW_CORETEAM" id="PERMISSIONS_VIEW_CORETEAM"', ($PERMISSIONS_VIEW_CORETEAM == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_VIEW_CORETEAM">View CoreTeam</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_VIEW_MUSOS" id="PERMISSIONS_VIEW_MUSOS"', ($PERMISSIONS_VIEW_MUSOS == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_VIEW_MUSOS">View Musos</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_VIEW_ROTAS" id="PERMISSIONS_VIEW_ROTAS"', ($PERMISSIONS_VIEW_ROTAS == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_VIEW_ROTAS">View Rotas</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_VIEW_CALENDARS" id="PERMISSIONS_VIEW_CALENDARS"', ($PERMISSIONS_VIEW_CALENDARS == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_VIEW_CALENDARS">View Calendars</label></div>', PHP_EOL;
  echo '    <div><input type="checkbox" name="PERMISSIONS_VIEW_OTHER" id="PERMISSIONS_VIEW_OTHER"', ($PERMISSIONS_VIEW_OTHER == 'Y') ? $checked : '', ' class="ignore" /> <label for="PERMISSIONS_VIEW_OTHER">View Other</label></div>', PHP_EOL;
  echo '  </div>', PHP_EOL;
  echo '  <div class="breakBoth"></div>', PHP_EOL;
  echo '  <input type="submit" value="Save" id="save_btn" class="custom_btn" />', PHP_EOL;
  echo '</form>', PHP_EOL;
  echo '<a href="', basename($_SERVER['PHP_SELF']), '?delete=', $id, '" class="custom_btn delete">Delete</a>', PHP_EOL;
}

function reset_password() {
  if (isset($_POST['password']) && ($_POST['password'] != $_POST['password_confirm'])) {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Passwords do not match.', PHP_EOL;
    echo '</div>', PHP_EOL;
    unset($_POST['reset_password']);
  }
  $id = 0;
  $id = isset($_GET['reset_password']) ? $_GET['reset_password'] : $id;
  $id = isset($_POST['id']) ? $_POST['id'] : $id;
  if (!($username = getUsername($id))) {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Could not find username.', PHP_EOL;
    echo '</div>', PHP_EOL;
    return true;
  }
  
  if (isset($_POST['reset_password'])) {
    $newpassword = $_POST['password'];
    if (changePassword($username, false, $newpassword)) {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Password successfully changed.', PHP_EOL;
      echo '</div>', PHP_EOL;
      return true;
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Password could not be changed.', PHP_EOL;
      echo '</div>', PHP_EOL;
    }
  }
  else {
    echo '<a href="', basename($_SERVER['PHP_SELF']), '" class="custom_btn">Cancel/Back</a>', PHP_EOL;
    echo '<form action="', basename($_SERVER['PHP_SELF']), '" method="post" class="item_form">', PHP_EOL;
    echo '  <input type="hidden" name="reset_password" value="reset_password" />', PHP_EOL;
    echo '  <input type="hidden" name="id" value="', $id, '" />', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Username:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    ', $username, PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>New Password:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <input type="password" name="password" value="" />', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="label">', PHP_EOL;
    echo '    <div>Repeat Password:</div>', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="input">', PHP_EOL;
    echo '    <input type="password" name="password_confirm" value="" />', PHP_EOL;
    echo '  </div>', PHP_EOL;
    echo '  <div class="breakBoth"></div>', PHP_EOL;
    echo '  <input type="submit" value="Save" id="save_btn" class="custom_btn" />', PHP_EOL;
    echo '</form>', PHP_EOL;
  }
  return false;
}

function delete_item() {
  $return_value = false;
  if (isset($_GET['confirm'])) {
    if ($_GET['confirm']) {
      if (deleteUser($_GET['delete'])) {
        echo '<div class="feedback_msg success">', PHP_EOL;
        echo '  Successfully deleted item.', PHP_EOL;
        echo '</div>', PHP_EOL;
        $return_value = true;
      }
      else {
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  Failed to delete item.', PHP_EOL;
        echo '</div>', PHP_EOL;
      }
    }
    else {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Did not delete item.', PHP_EOL;
      echo '</div>', PHP_EOL;
    }
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
  
  if (!ctype_alnum(str_replace(array('.'), '', $_POST['username']))) {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Invalid username.', PHP_EOL;
    echo '</div>', PHP_EOL;
    return false;
  }
  
  if (isset($_POST['password']) && ($_POST['password'] != $_POST['password_confirm'])) {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Passwords do not match.', PHP_EOL;
    echo '</div>', PHP_EOL;
    return false;
  }
  
  $id = (int)$_POST['id'];
  $active = isset($_POST['active']) ? 'Y' : 'N';
  $username = $_POST['username'];
  $password = isset($_POST['password']) ? $_POST['password'] : null;
  $fullName = $_POST['fullName'];
  $PERMISSIONS_FULL = isset($_POST['PERMISSIONS_FULL']) ? 'Y' : 'N';
  $PERMISSIONS_MODERATE_USERS = isset($_POST['PERMISSIONS_MODERATE_USERS']) ? 'Y' : 'N';
  $PERMISSIONS_EDIT_OTHER = isset($_POST['PERMISSIONS_EDIT_OTHER']) ? 'Y' : 'N';
  $PERMISSIONS_EDIT_CORETEAM = isset($_POST['PERMISSIONS_EDIT_CORETEAM']) ? 'Y' : 'N';
  $PERMISSIONS_EDIT_MUSOS = isset($_POST['PERMISSIONS_EDIT_MUSOS']) ? 'Y' : 'N';
  $PERMISSIONS_EDIT_ROTAS = isset($_POST['PERMISSIONS_EDIT_ROTAS']) ? 'Y' : 'N';
  $PERMISSIONS_EDIT_EVENTS = isset($_POST['PERMISSIONS_EDIT_EVENTS']) ? 'Y' : 'N';
  $PERMISSIONS_EDIT_POST_ITS = isset($_POST['PERMISSIONS_EDIT_POST_ITS']) ? 'Y' : 'N';
  $PERMISSIONS_VIEW_CORETEAM = isset($_POST['PERMISSIONS_VIEW_CORETEAM']) ? 'Y' : 'N';
  $PERMISSIONS_VIEW_MUSOS = isset($_POST['PERMISSIONS_VIEW_MUSOS']) ? 'Y' : 'N';
  $PERMISSIONS_VIEW_ROTAS = isset($_POST['PERMISSIONS_VIEW_ROTAS']) ? 'Y' : 'N';
  $PERMISSIONS_VIEW_CALENDARS = isset($_POST['PERMISSIONS_VIEW_CALENDARS']) ? 'Y' : 'N';
  $PERMISSIONS_VIEW_OTHER = isset($_POST['PERMISSIONS_VIEW_OTHER']) ? 'Y' : 'N';
  $permissions = array(
    'PERMISSIONS_FULL'=>$PERMISSIONS_FULL,
    'PERMISSIONS_MODERATE_USERS'=>$PERMISSIONS_MODERATE_USERS,
    'PERMISSIONS_EDIT_OTHER'=>$PERMISSIONS_EDIT_OTHER,
    'PERMISSIONS_EDIT_CORETEAM'=>$PERMISSIONS_EDIT_CORETEAM,
    'PERMISSIONS_EDIT_MUSOS'=>$PERMISSIONS_EDIT_MUSOS,
    'PERMISSIONS_EDIT_ROTAS'=>$PERMISSIONS_EDIT_ROTAS,
    'PERMISSIONS_EDIT_EVENTS'=>$PERMISSIONS_EDIT_EVENTS,
    'PERMISSIONS_EDIT_POST_ITS'=>$PERMISSIONS_EDIT_POST_ITS,
    'PERMISSIONS_VIEW_CORETEAM'=>$PERMISSIONS_VIEW_CORETEAM,
    'PERMISSIONS_VIEW_MUSOS'=>$PERMISSIONS_VIEW_MUSOS,
    'PERMISSIONS_VIEW_ROTAS'=>$PERMISSIONS_VIEW_ROTAS,
    'PERMISSIONS_VIEW_CALENDARS'=>$PERMISSIONS_VIEW_CALENDARS,
    'PERMISSIONS_VIEW_OTHER'=>$PERMISSIONS_VIEW_OTHER);
  
  if ($_POST['save'] == 'item') {
    if (@$stmt = $mysqli->prepare('UPDATE `users` SET active=?, username=?, fullName=?, PERMISSIONS_FULL=?, PERMISSIONS_MODERATE_USERS=?, PERMISSIONS_EDIT_OTHER=?, PERMISSIONS_EDIT_CORETEAM=?, PERMISSIONS_EDIT_MUSOS=?, PERMISSIONS_EDIT_ROTAS=?, PERMISSIONS_EDIT_EVENTS=?, PERMISSIONS_EDIT_POST_ITS=?, PERMISSIONS_VIEW_CORETEAM=?, PERMISSIONS_VIEW_MUSOS=?, PERMISSIONS_VIEW_ROTAS=?, PERMISSIONS_VIEW_CALENDARS=?, PERMISSIONS_VIEW_OTHER=? WHERE id=' . $id . ' LIMIT 1')) {
      $stmt->bind_param('ssssssssssssssss', $active, $username, $fullName, $PERMISSIONS_FULL, $PERMISSIONS_MODERATE_USERS, $PERMISSIONS_EDIT_OTHER, $PERMISSIONS_EDIT_CORETEAM, $PERMISSIONS_EDIT_MUSOS, $PERMISSIONS_EDIT_ROTAS, $PERMISSIONS_EDIT_EVENTS, $PERMISSIONS_EDIT_POST_ITS, $PERMISSIONS_VIEW_CORETEAM, $PERMISSIONS_VIEW_MUSOS, $PERMISSIONS_VIEW_ROTAS, $PERMISSIONS_VIEW_CALENDARS, $PERMISSIONS_VIEW_OTHER);
      $stmt->execute();
      if (!$stmt->errno) {
        echo '<div class="feedback_msg success">', PHP_EOL;
        echo '  Successfully updated item.', PHP_EOL;
        echo '</div>', PHP_EOL;
        $return_value = true;
      }
      else {
        echo '<div class="feedback_msg failed">', PHP_EOL;
        echo '  Failed to update item.', PHP_EOL;
        echo '</div>', PHP_EOL;
      }
      $stmt->close();
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Failed to update item and could not connect to the database.', PHP_EOL;
      echo '</div>', PHP_EOL;
    }
  }
  elseif ($_POST['save'] == 'new') {
    if (addUser($username, $password, $fullName, $permissions, $active)) {
      echo '<div class="feedback_msg success">', PHP_EOL;
      echo '  Successfully inserted item.', PHP_EOL;
      echo '</div>', PHP_EOL;
      $return_value = true;
    }
    else {
      echo '<div class="feedback_msg failed">', PHP_EOL;
      echo '  Failed to insert item.', PHP_EOL;
      echo '</div>', PHP_EOL;
    }
  }
  else {
    echo '<div class="feedback_msg failed">', PHP_EOL;
    echo '  Internal error!', PHP_EOL;
    echo '</div>', PHP_EOL;
    return false;
  }
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
  else {
    list_item($_GET['delete']);
  }
}
elseif (isset($_POST['save'])) {
  if (save_item()) {
    if ($_POST['save'] == 'item') {
      list_item((int)$_POST['id']);
    }
    else {
      list_items();
    }
  }
  else {
    list_item((int)$_POST['id']);
  }
}
elseif (isset($_GET['reset_password']) || isset($_POST['reset_password'])) {
  if (reset_password()) {
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