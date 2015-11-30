<?php
//namespace DB; // short for session variable.

function INITIALIZE()
{
	eval(file_get_contents('/var/www/.ht_mysql_login')); // DEFINES DB_PASSFILE
	/*
	 * Connect to the database.
	 */
	if (!empty($_SESSION['dbconn']) && $_SESSION['dbconn'] instanceof PDO) {
		return TRUE;
	} else {
		try {
			$dsn = "mysql:host=" . DB_HOSTNAME . ";dbname=" . DB_DATABASE . ";";
			$_SESSION['dbconn'] = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
			$_SESSION['dbconn']->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
		} catch (PDOException $pe) {
			die('Error connecting to database, L' . $pe->getLine() . ":" . $pe->getMessage());
			return FALSE; // The die statement above prevents this from ever happening.
		}
		return TRUE;
	}
	// Nothing should get this far.
	die('INITIALIZE failed to return a value.');
	return FALSE;
}

function PROCESS_LOGIN_ATTEMPT()
{
	$min_username_length = 3; $max_username_length = 12;
	$min_password_length = 6; $max_password_length = 12;
	$post_raw_username = FILTER_INPUT(INPUT_POST, 'raw_username', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]{" . $min_username_length . "," . $max_username_length . "}$/")));
	$post_raw_password = FILTER_INPUT(INPUT_POST, 'raw_password', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]{" . $min_password_length . "," . $max_password_length . "}$/")));
	if ($post_raw_username == FALSE) {
		if (empty($post_raw_username)) {
			$_SESSION['banner_message'] = "LOGIN ERROR: Don't forget to enter your password!";
		} elseif (strlen($post_raw_username) < $min_username_length || strlen($post_raw_username) > $max_username_length) {
			$_SESSION['banner_message'] = "LOGIN ERROR: Usernames should be $min_username_length to $max_username_length characters long.";
		} else {
			$_SESSION['banner_message'] = 'LOGIN ERROR: Usernames can only contain letters and numbers.';
		}
	} elseif ($post_raw_password == FALSE) {
		if (empty($post_raw_password)) {
			$_SESSION['banner_message'] = "LOGIN ERROR: Don't forget to enter your password!";
		} elseif (strlen($post_raw_password) < $min_password_length || strlen($post_raw_password) > $max_password_length) {
			$_SESSION['banner_message'] = "LOGIN ERROR: Passwords should be " . $min_username_length . " to " . $max_username_length . " characters long (entered " . strlen($post_raw_password) . " characters).";
		} else {
			$_SESSION['banner_message'] = 'LOGIN ERROR: Passwords can only contain letters and numbers.';
		}
	} else { // Raw username and password check out.
		$username = $post_raw_username;
		$passtext = $post_raw_password;
		INITIALIZE();
		if ($_SESSION['dbconn'] instanceof PDO) {
			try {
				$query_finduser = 'SELECT * FROM users;';
				$statement_finduser = $_SESSION['dbconn']->prepare($query_finduser);
				$statement_finduser->bindParam(':un', $post_username, PDO::PARAM_STR);
				$statement_finduser->setFetchMode(PDO::FETCH_ASSOC);
				$statement_finduser->execute();
				$passhash = '';
				foreach ($statement_finduser->fetchAll() as $row) {
					if ($row['username'] == $username) {
							$passhash = $row['password'];
							break;
						}
				}
				if (password_verify($passtext, $passhash)) {
					$_SESSION['username'] = $username;
					$_SESSION['status'] = "loggedin";
					$_SESSION['banner_message'] = "Welcome back, " . $username . "! Your bookmarks are below.";
				} else {
					$_SESSION['banner_message'] = "LOGIN ERROR: Wrong password.";
				}
			} catch (PDOException $pe) {
				die('Error connecting to database, L' . $pe->getLine() . ":" . $pe->getMessage());
				return FALSE; // Login failed.
			}
		} else {
			die('Connection to server failed (connection not PDO.)');
		}
	}
	RETREIVE_USER_DATA();
	unset($min_username_length, $max_username_length, $min_password_length, $max_password_length, $post_raw_username, $post_raw_password);
	unset($passhash, $passtext);
}

function PROCESS_LOGOUT()
{
	$_SESSION['status'] = 'NOBODY';
	$_SESSION['username'] = '';
	$_SESSION['links'] = array();
}

function RETREIVE_USER_DATA() {
	if ($_SESSION['dbconn'] instanceof PDO && $_SESSION['status'] == "loggedin") {
		try {
			$query_getlinks = 'SELECT * FROM links WHERE username = ?';
			$statement_getlinks = $_SESSION['dbconn']->prepare($query_getlinks);
			$statement_getlinks->bindValue(1, SSS('username'), PDO::PARAM_STR);
			$statement_getlinks->setFetchMode(PDO::FETCH_ASSOC);
			$statement_getlinks->execute();
			$linkset = $statement_getlinks->fetchAll();
			$_SESSION['links'] = $linkset;
		} catch (PDOException $pe) {
			die('Error connecting to database, L' . $pe->getLine() . ":" . $pe->getMessage());
			return FALSE; // Login failed.
		}
	} else {
		die('Attempted retrieval when not possible.');
	}
}