<?php
function correctLogin($requiredPermissions = 0) {
  global $mysqli;
  if ($requiredPermissions === null) {
    return true;
  }
  if (isset($_COOKIE['u']) && isset($_COOKIE['p']) && isUserPassCorrect($_COOKIE['u'], $_COOKIE['p'], true)) {
    if (!$requiredPermissions) {
      return true;
    }
    else {
      return userPermitted($requiredPermissions);
    }
  }
  return false;
}

function userPermitted($requiredPermissions) {
  $requiredPermissions = explode(',', $requiredPermissions);
  
  foreach ($requiredPermissions as $permission) {
    if (!hasPermission($permission)) {
      return false;
    }
  }
  return true;
}

function isUserPassCorrect($username, $password, $isHash = false) {
  global $mysqli;
  $result_num_rows = false;
  if (@$stmt = $mysqli->prepare('SELECT id FROM `users` WHERE username=? AND password=' . ($isHash ? '?' : 'SHA1(CONCAT(salt, ?))') . ' AND active=\'Y\' AND deleted=\'N\' LIMIT 1')) {
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $stmt->store_result();
    $result_num_rows = $stmt->num_rows;
    $stmt->close();
  }
  return $result_num_rows ? true : false;
}

function getUsername($id) {
  global $mysqli;
  $username = false;
  if (@$stmt = $mysqli->prepare('SELECT username FROM `users` WHERE id=? LIMIT 1')) {
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
  }
  return $username;
}

function getPassword($id) {
  global $mysqli;
  $password = false;
  if (@$stmt = $mysqli->prepare('SELECT password FROM `users` WHERE id=? LIMIT 1')) {
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($password);
    $stmt->fetch();
    $stmt->close();
  }
  return $password;
}

function getUsern($username) {
  global $mysqli;
  $usern = false;
  if (@$stmt = $mysqli->prepare('SELECT id FROM `users` WHERE username=? LIMIT 1')) {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows) {
      $stmt->bind_result($usern);
      $stmt->fetch();
    }
    $stmt->close();
  }
  return $usern;
}

function getCurrentUsern() {
  return getUsern(getCurrentUsername());
}

function getCurrentUsername() {
  return isset($_COOKIE['u']) ? $_COOKIE['u'] : false;
}

function getCurrentFullName() {
  global $mysqli;
  $username = '';
  if (isset($_COOKIE['u'])) {
    $username = $_COOKIE['u'];
  }
  $fullName = false;
  if (@$stmt = $mysqli->prepare('SELECT fullName FROM `users` WHERE username=? LIMIT 1')) {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($fullName);
    $stmt->fetch();
    $stmt->close();
  }
  return $fullName;
}

function hasPermission($permission = '*') {
  global $mysqli;
  $username = '';
  if (isset($_COOKIE['u'])) {
    $username = $_COOKIE['u'];
  }
  if (@$stmt = $mysqli->prepare('SELECT PERMISSIONS_FULL,'.$permission.' FROM `users` WHERE username=? LIMIT 1')) {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($adminPrivileges, $dbPermission);
    $stmt->fetch();
    $stmt->close();
    if ($adminPrivileges == 'Y' || $dbPermission == 'Y') {
      return true;
    }
  }
  return false;
}

function changePassword($username, $password, $newpassword) {
  global $mysqli;
  if(($password === false) || isUserPassCorrect($username, $password)) {
    $salt = sha1(uniqid(mt_rand(), true) . SALT_KEY . strtolower($username));
    $newpassword = sha1($salt . $newpassword);
    if (@$stmt = $mysqli->prepare('UPDATE `users` SET password=?, salt=? WHERE username=? LIMIT 1')) {
      $stmt->bind_param('sss', $newpassword, $salt, $username);
      $stmt->execute();
      $affected_rows = $stmt->affected_rows;
      $stmt->close();
      if ($affected_rows) {
        if (isset($_COOKIE['u']) && strtolower($_COOKIE['u']) == strtolower($username)) {
          setcookie('p', $newpassword, time()+60*60*24*365, '/', '.'.$_SERVER['SERVER_NAME']);
        }
        return true;
      }
    }
  }
  return false;
}

function addUser($username, $newpassword, $fullName = '', $permissions = array(), $active = 'Y') {
  global $mysqli;
  if (!ctype_alnum(str_replace(array('.'), '', $_POST['username'])) || getUsern($username)) {
    if ($username == '') {
      return false;
    }
  }
  $salt = sha1(uniqid(mt_rand(), true) . SALT_KEY . strtolower($username));
  $newpassword = sha1($salt . $newpassword);
  $permission_name = '';
  $permission_value = '';
  foreach ($permissions as $permission=>$value) {
    $permission_name .= ', ' . $permission;
    $permission_value .= ', \'' . $value . '\'';
  }
  if (@$stmt = $mysqli->prepare('INSERT INTO `users` (active, username, password, salt, fullName' . $permission_name . ') VALUES (?, ?, ?, ?, ?' . $permission_value . ')')) {
    $stmt->bind_param('sssss', $active, $username, $newpassword, $salt, $fullName);
    $stmt->execute();
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    if ($affected_rows) {
      return true;
    }
  }
  return false;
}

function deleteUser($id) {
  global $mysqli;
  if (@$stmt = $mysqli->prepare('UPDATE `users` SET active=\'N\', deleted=\'Y\' WHERE id=? LIMIT 1')) {
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $errno = $stmt->errno;
    $stmt->close();
    if (!$errno) {
      return true;
    }
    else {
      return false;
    }
  }
  return false;
}
?>