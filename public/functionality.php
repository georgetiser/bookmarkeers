<?php
require("php/functionality-files.php");
require("php/functionality-sessions.php");
require("php/functionality-databases.php");

if (empty($_SESSION['status'])) {
	$_SESSION['status'] = 'NOBODY';
}

if ($_SESSION['status'] == 'NOBODY') {
	$_SESSION['banner_message'] = "Please log in using the Menu button (top left corner).";
}

if ($_SESSION['status'] == 'loggedin') {
	INITIALIZE();
	RETRIEVE_USER_DATA();
}

$action_to_take = '';
if (array_key_exists('perform_action', $_POST)) {
	$action_to_take = FILTER_VAR($_POST['perform_action'], FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/[a-zA-Z0-9]{0,12}/")));
}

if ($action_to_take == 'login') {
	PROCESS_LOGIN_ATTEMPT();
}

if ($action_to_take == 'logout') {
	PROCESS_LOGOUT_ATTEMPT();
}

if (empty($_SESSION['title'])) {$_SESSION['title'] = 'Bookmarkeers!';}

function array_dump($a) {
	print("<br/><hr>array infodump");
	foreach ($a as $key => $item) {
		if (empty($item)) {$show = ' ||';} else {$show= " |<code>" . htmlspecialchars($item) . "</code>|";}
		print("<br/>&nbsp;&nbsp;&nbsp;&nbsp;" . $key . $show);
	}
	print("<br/>end array infodump<br/><hr>");
}