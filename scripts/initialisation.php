<?php
if (($offset = strpos($_SERVER['SCRIPT_FILENAME'], 'public_html')) === false) {
  if (($offset = strpos($_SERVER['SCRIPT_FILENAME'], 'thepointcoc.co.uk')) === false) {
    $offset = 0;
  }
}
$homen = substr_count($_SERVER['SCRIPT_FILENAME'], '/', $offset) - 1;
$home = str_repeat('../', $homen);

define('HOME_DIR', $home);
define('HOMEN_DIR', $homen);
define('SECURE_DIR', HOME_DIR . 'secure/');
define('SCRIPTS_DIR', HOME_DIR . 'scripts/');
define('ACCESS_LOG', HOME_DIR . 'logs/access_log.ini');
define('CURRENT_WORKING_DIRECTORY', getcwd());

define('SALT_KEY', '');
define('PERMISSIONS_ANY_USER', 0);
define('PERMISSIONS_FULL', 'PERMISSIONS_FULL');
define('PERMISSIONS_MODERATE_USERS', 'PERMISSIONS_MODERATE_USERS');
define('PERMISSIONS_EDIT_OTHER', 'PERMISSIONS_EDIT_OTHER');
define('PERMISSIONS_EDIT_CORETEAM', 'PERMISSIONS_EDIT_CORETEAM');
define('PERMISSIONS_EDIT_MUSOS', 'PERMISSIONS_EDIT_MUSOS');
define('PERMISSIONS_EDIT_ROTAS', 'PERMISSIONS_EDIT_ROTAS');
define('PERMISSIONS_EDIT_EVENTS', 'PERMISSIONS_EDIT_EVENTS');
define('PERMISSIONS_EDIT_POST_ITS', 'PERMISSIONS_EDIT_POST_ITS');
define('PERMISSIONS_VIEW_CORETEAM', 'PERMISSIONS_VIEW_CORETEAM');
define('PERMISSIONS_VIEW_MUSOS', 'PERMISSIONS_VIEW_MUSOS');
define('PERMISSIONS_VIEW_ROTAS', 'PERMISSIONS_VIEW_ROTAS');
define('PERMISSIONS_VIEW_CALENDARS', 'PERMISSIONS_VIEW_CALENDARS');
define('PERMISSIONS_VIEW_OTHER', 'PERMISSIONS_VIEW_OTHER');
define('PERMISSIONS_ALL', 'PERMISSIONS_ALL');

define('USERTYPE_ADMIN', PERMISSIONS_FULL);
define('USERTYPE_PASTOR', PERMISSIONS_EDIT_OTHER.','.PERMISSIONS_EDIT_CORETEAM.','.PERMISSIONS_EDIT_MUSOS.','.PERMISSIONS_EDIT_ROTAS.','.PERMISSIONS_VIEW_CORETEAM.','.PERMISSIONS_VIEW_MUSOS.','.PERMISSIONS_VIEW_ROTAS.','.PERMISSIONS_VIEW_CALENDARS.','.PERMISSIONS_VIEW_OTHER);
define('USERTYPE_SUPERUSER', PERMISSIONS_MODERATE_USERS.','.USERTYPE_PASTOR);

date_default_timezone_set('Europe/London');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', HOME_DIR . 'logs/error.txt');
error_reporting(E_ALL);

$time_start = microtime(true); // SCRIPT TIME START
register_shutdown_function('script_time_end');

include_once(HOME_DIR . 'db/mysql.php');
include_once(SECURE_DIR . 'correctLogin.php');
include_once(SCRIPTS_DIR . 'beforeFileLoad.php');

function script_time_end() {
  global $time_end;
  $time_end = microtime(true); // SCRIPT TIME END
  write_access_log(ACCESS_LOG);
}
?>