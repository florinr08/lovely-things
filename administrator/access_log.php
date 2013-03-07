<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/scripts/initialisation.php');
$privileges = USERTYPE_ADMIN; // the privileges needed to see the site - refer to /secure/account_types.txt
beforeFileLoad();

echo '<pre>', PHP_EOL;
include('../logs/access_log.ini');
echo '</pre>', PHP_EOL;
?>