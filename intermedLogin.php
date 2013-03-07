<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/scripts/initialisation.php');

$return_page = isset($_POST['returnpage']) ? $_POST['returnpage'] : $_SERVER['HTTP_REFERER'];
if (!strpos($return_page, '?')) {
  $return_page = substr($return_page, 0);
}
else {
  $return_page = substr($return_page, 0, strpos($return_page, '?'));
}
$location = 'Location:' . $return_page;
$cookie_expire = isset($_POST['remember']) ? time()+60*60*24*365 : 0;
$cookie_expire_x = time()-60*60*24*365;
$cookie_domain = ($_SERVER['SERVER_NAME'] != 'thepointcoc.co.uk') ? $_SERVER['SERVER_NAME'] : '.'.$_SERVER['SERVER_NAME'];

if (isset($_POST['login'])) {
  if (isUserPassCorrect($_POST['username'], $_POST['password'])) {
    setcookie('u', $_POST['username'], $cookie_expire, '/', $cookie_domain);
    setcookie('p', getPassword(getUsern($_POST['username'])), $cookie_expire, '/', $cookie_domain);
  }
  else {
    setcookie('u', $_POST['username'], $cookie_expire_x, '/', $cookie_domain);
    setcookie('p', getPassword(getUsern($_POST['username'])), $cookie_expire_x, '/', $cookie_domain);
    $location .= '?failed';
  }
}
elseif (isset($_GET['logout'])) {
  setcookie('u', '', $cookie_expire_x, '/', $cookie_domain);
  setcookie('p', '', $cookie_expire_x, '/', $cookie_domain);
}
elseif (isset($_POST['changePassword'])) {
  $location .= changePassword($_COOKIE['u'], $_POST['password'], $_POST['newpassword']) ? '?passwordchanged' : '?passwordchangefailed';
}

header($location);
?>