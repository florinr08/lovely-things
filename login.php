<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/scripts/initialisation.php');
$privileges = null; // the privileges needed to see the site - refer to /secure/account_types.txt
beforeFileLoad();

include_once($home . 'secure/login_page.php');
?>