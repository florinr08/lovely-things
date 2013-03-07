<?php
if(isset($_SERVER['HTTP_USER_AGENT'])) $ua = $_SERVER['HTTP_USER_AGENT']; else $ua = '';
$actualDeviceType = detectDeviceType(); 
$deviceType = detectChosenDeviceType();
if(isset($_REQUEST['changeDevType'])) changeDevType($_REQUEST['changeDevType']);

function detectDeviceType() {
  global $ua;
  switch(true) {
    case stristr($ua, 'Android'):
    case stristr($ua, 'AvantGo'):
    case stristr($ua, 'BlackBerry'):
    case stristr($ua, 'Blazer'):
    case stristr($ua, 'Doris'):
    case stristr($ua, 'Fennec'):
    case stristr($ua, 'GoBrowser'):
    case stristr($ua, 'Iris'):
    case stristr($ua, 'Maemo'):
    case stristr($ua, 'Mazingo'):
    case stristr($ua, 'MIB'):
    case stristr($ua, 'Minimo'):
    case stristr($ua, 'Mobile'):
    case stristr($ua, 'Opera Mini'):
    case stristr($ua, 'Opera Mobi'):
    case stristr($ua, 'Series60'):
    case stristr($ua, 'Skyfire'):
    case stristr($ua, 'SonyEricsson'):
    case stristr($ua, 'T68'):
    case stristr($ua, 'TeaShark'):
    case stristr($ua, 'Windows CE'):
      return 'SCREEN';    // TO ENABLE MOBILE VERSION COMMENT THIS LINE OUT
      return 'HANDHELD';
      break;
    default:
      return 'SCREEN';    // TO DEBUG HANDHELD COMMENT THIS LINE OUT
      return 'HANDHELD';  // DEBUG HANDHELD
  }
}

function detectChosenDeviceType() {
  global $deviceType, $actualDeviceType;
  if($actualDeviceType != 'HANDHELD') return 'SCREEN';
  if(isset($_COOKIE['chosenDevType']))
    if($_COOKIE['chosenDevType'] == 'HANDHELD') return 'HANDHELD';
    else return 'SCREEN';
  else {
    changeDevType($actualDeviceType);
    return $actualDeviceType;
  }
}

function changeDevType($devType) {
  global $deviceType;
  $time = time()+60*60*24*365;
  setcookie('chosenDevType', strtoupper($devType), $time, '/');
  $deviceType = strtoupper($devType);
}

function write_access_log($log_file) {
  global $mysqli, $deviceType, $ua, $time_start, $time_end;
  chdir(CURRENT_WORKING_DIRECTORY);
  $time = number_format($time_end - $time_start, 4);
  $log_line = date('[d-M-Y H:i:s] ').'['.$deviceType.'] ['.getCurrentUsername().'] '.$ua.' ['.$_SERVER['REMOTE_ADDR'].'] ['.$_SERVER['SCRIPT_NAME'].'] in '.$time.' seconds'.PHP_EOL;
  $handle = @fopen($log_file, 'a');
  if($handle) {
    fwrite($handle, $log_line);
    fclose($handle);
    return true;
  }
  else {
    return false;
  }
}

function beforeFileLoad() {
  global $privileges, $redirectPage;
  if (isset($privileges) && !correctLogin($privileges)) {
    if (isset($redirectPage)) {
      include($redirectPage);
    }
    else {
      include(HOME_DIR . 'noPrivileges.php');
    }
    die();
  }
}

if (isset($privileges)) {
  beforeFileLoad();
}
?>