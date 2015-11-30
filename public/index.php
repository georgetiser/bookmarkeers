<?php
session_start();
$_SESSION['start'] = TRUE;
$_SESSION['base_directory'] = pathinfo(__FILE__, PATHINFO_DIRNAME);
//$_SESSION['knownfiles'] = array();
require('functionality.php');
require('content.php');
