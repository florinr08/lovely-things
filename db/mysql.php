<?php
$host = '';
$username = '';
$password = '';
$dbname = '';

if (isset($mysqli)) {
  $mysqli = new mysqli();
}
else {
  $mysqli = new mysqli($host, $username, $password, $dbname);
}

unset($host, $username, $password, $dbname);

if (@$mysqli->connect_error) {
  echo 'Connect Error (', $mysqli->connect_errno, ') ', $mysqli->connect_error, PHP_EOL;
} elseif (mysqli_connect_error()) {
  echo 'Connect Error (', mysqli_connect_errno(), ') ', mysqli_connect_error(), PHP_EOL;
}

@$mysqli->query('SET time_zone = \'' . date('P') . '\'');

function disconnect_mysql() {
  global $mysqli;
  if (isset($mysqli)) {
    if (@!$mysqli->close()) {
      echo 'Failed to close database.', PHP_EOL;
      return false;
    }
    return true;
  }
  return false;
}

register_shutdown_function('disconnect_mysql');
?>