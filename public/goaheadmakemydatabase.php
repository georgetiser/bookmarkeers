<!DOCTYPE HTML>

<?php
require('php/templateer.php');

$dropEverything = TRUE;
/*
 * Setting $dropEverything to TRUE destroys all existing tables before initializing new ones.
 * Setting $dropEverything to FALSE overwrites any entries that share a unique ID.
 * Setting $dropEverything to NULL preserves existing entries.
 *
 */
?>

<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="css/foundation.css" />
	<title>Go Ahead, Make My Database</title>
</head>

<body>
<h3>Go Ahead, Make MySQL Database</h3>

A PHP script to initialize and populate this website's MySQL database, by George Tiser. It initializes these tables:
<br/><code>popular sites</code> (random number of links, only used in this script)
<br/><code>users</code> (random number of users)
<br/><code>links</code> (random number of links assigned to each user).

<?php

/*
<br/><br/>This program is for development purposes only. Regardless, it has been malicious-user-proofed to the best of my ability -- I believe there's no attack surface for cross-site scripting or SQL injection.
<br/><br/>The <code>popular_sites</code> table isn't used elsewhere in the website. It's just used as a source of random links for the random users.
*/

//Settings

// New User query parameters:
$username = ' '; $password = ' '; $email = ' '; $job = ' '; $joindate = time(); $confirmcode = 0;

// New Bookmark query parameters:
$link_id = 0; $linkname = ' '; $url = ' ';
?>

<?php
print('<br/><hr/><hr/>');

if ($dropEverything === NULL) {
	print('MODE: Retain tables, do not overwrite existing entries.');
} elseif ($dropEverything === FALSE) {
	print('MODE: Retain tables, but overwrite existing entries.');
} elseif ($dropEverything === TRUE) {
	print('MODE: Drop tables, deleting existing entries.');
} else {
	print('<br/>MODE Error. Setting MODE to retain.');
	$dropEverything = NULL;
}

/*
 *
 *
 *
 *
 * database
 *
 * Create the database and connect to it.
 *
 */
print('<br/><br/>INITIALIZE <code>' . DB::NAME . '</code> DATABASE:');
/*
 * Connect to the server.
 */
try {
	$dsn = "mysql:host=" . DB::HOST . ";";
	$server_conn = new PDO($dsn, DB::USER, DB::PASS);
	$server_conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
	print('<br/>Connection to database server established!');
} catch (PDOException $pe) {
	die('<br/><br/>Error connecting to database server, L' . $pe->getLine() . ":" . $pe->getMessage());
}
/*
 * Create the database. If database exists, keep it / don't destroy it, regardless of $dropEverything.
 * (Preserving the existing db is future-proofing, to ensure we don't accidentally drop any tables this script doesn't re-create.
 */
try {
	$createDatabaseQuery = 'CREATE DATABASE IF NOT EXISTS :db;';
	$createDatabaseStatement = $server_conn->prepare($createDatabaseQuery);
	$createDatabaseStatement->bindValue(':db', DB::NAME);
	$createDatabaseStatement->execute();
	print('<br/>Database created!');
} catch (PDOException $pe) {
	die('<br/><br/>Error creating database, L' . $pe->getLine() . ":" . $pe->getMessage());
}
/*
 * Clear the connection, connect again, this time to the database.
 * (This saves us the trouble of prepending 'USE :db' to every query.)
 */
$server_conn = NULL;
try {
	$dsn = "mysql:host=" . DB::HOST . ";dbname=" . DB::NAME . ";";
	$conn = new PDO($dsn, DB::USER, DB::PASS);
	$conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
	print(' Connection to database established!');
} catch (PDOException $pe) {
	die('<br/><br/>Error connecting to database, L' . $pe->getLine() . ":" . $pe->getMessage());
}

/*
 *
 *
 *
 *
 * popular_sites
 *
 * Initialize the popular_sites table:
 *
 */
print('<br/><br/>INITIALIZE <code>popular_sites</code> TABLE:');
/*
 * popular_sites - Create table.
 */
try {
	//We'll want to destroy and re-create this table regardless of $dropEverything.
	$query_createTable_popular_sites =
			'DROP TABLE IF EXISTS popular_sites;
			CREATE TABLE IF NOT EXISTS popular_sites
			(
				id INTEGER NOT NULL PRIMARY KEY,
				linkname varchar(255) NOT NULL,
				url varchar(255) NOT NULL
			);';
	$createPopularSites = $conn->prepare($query_createTable_popular_sites);
	$createPopularSites->execute();
	print('<br/>Table configured!');
} catch (PDOException $pe) {
	die('<br/><br/>Error creating table for popular sites, L' . $pe->getLine() . ":" . $pe->getMessage());
}
unset($query_createTable_popular_sites, $createPopularSites);
/*
 * popular_sites - Prepare query to insert new entry.
 */
try {
	if (isset($dropEverything)) {
		// overwrite existing entries
		$query_addPopularSite = 'REPLACE';
	} else {
		// keep existing entries
		$query_addPopularSite = 'INSERT IGNORE';
	}
	$query_addPopularSite .=
			' INTO popular_sites (id, linkname, url)
			VALUES (:id, :ln, :url)';
	$addPopularSite = $conn->prepare($query_addPopularSite);
	print(' Query prepared!');
} catch (PDOException $pe) {
	die('<br/><br/>Error preparing Popular Sites insertion, L' . $pe->getLine() . ":" . $pe->getMessage());
}
/*
 * popular_sites - Bind parameters to query.
 */
try {
	$addPopularSite->bindParam(':id', $link_id, PDO::PARAM_INT);
	$addPopularSite->bindParam(':ln', $linkname, PDO::PARAM_STR);
	$addPopularSite->bindParam(':url', $url, PDO::PARAM_STR);
	print(' Query parameters bound!');
} catch (PDOException $pe) {
	die('<br/><br/>Error binding parameters to Popular Sites insertion, L' . $pe->getLine() . ":" . $pe->getMessage());
}
/*
 * popular_sites - Insert new entries.
 */
try {
	$qty_popular_sites = rand(60, 140);
	require('php/database_popular_sites.php');
	/*
	 * I moved the PHP code for populating the popular sites database to a separate file.
	 * It is a bit goofy/hacky. I wanted to vary the number of sites available for the randomized default users to choose from.
	 * There's no reason for that other than whimsy.
	 */
	print('<br/>Popular Sites populated! ' . (string)$qty_popular_sites . ' sites entered.');
} catch (PDOException $pe) {
	die('<br/><br/>Error inserting new Popular Site, site #' . (string)$link_id . ', L' . $pe->getLine() . ":" . $pe->getMessage());
}
unset($qty_popular_sites, $query_addPopularSite, $addPopularSite);
/*
 * popular_sites - Retrieve table contents (to verify retrieval works, and to use later when assigning links to users).
 */
try {
	$query_fetchPopularSites = 'SELECT id, linkname, url FROM popular_sites;';
	$popularSitesStatement = $conn->query($query_fetchPopularSites);
	$popularSites = $popularSitesStatement->fetchAll(PDO::FETCH_BOTH);
	print('<br/>Popular Sites fetched! ');
	print((string)$popularSitesStatement->rowCount() . ' links fetched.');
} catch (PDOException $pe) {
	die('Popular Sites fetch error, L' . $pe->getLine() . ":" . $pe->getMessage());
}
unset($query_fetchPopularSites, $popularSitesStatement);
// Don't unset the array PopularSites; we'll be using it.

/*
 *
 *
 *
 *
 * users
 *
 * Initialize the users table:
 *
 */
print('<br/><br/>INITIALIZE <code>users</code> TABLE:');
/*
 * users - Create table.
 */
try {
	$query_createTable_users =
			'CREATE TABLE IF NOT EXISTS users (
			id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
			username varchar(64) NOT NULL UNIQUE,
			password varchar(255) NOT NULL,
			email varchar(255) NOT NULL,
			job varchar(255),
			joindate DATETIME
			);';
	if ($dropEverything === TRUE) {
		$query_dropTable_users = "DROP TABLE IF EXISTS users; ";
		$query_createTable_users = $query_dropTable_users . $query_createTable_users;
	}
	$conn->query($query_createTable_users);
	print('<br/>Table configured!');
} catch (PDOException $pe) {
	die('<br/><br/>Error creating table of users, L' . $pe->getLine() . ":" . $pe->getMessage());
}
unset($query_dropTable_users, $query_createTable_users);
/*
 * users - Prepare query to insert new entry.
 */
try {
	if (isset($dropEverything)) {
		// overwrite existing entries
		$query_createNewUser = 'REPLACE';
	} else {
		// keep existing entries
		$query_createNewUser = 'INSERT IGNORE';
	}
	$query_createNewUser .=
			' INTO users (username, password, email, job, joindate) ' .
			' VALUES (:un, :pw, :em, :jb, :jd);';
	$createNewUser = $conn->prepare($query_createNewUser);
	print(' Query prepared!');
	$createNewUser->bindParam(':un', $username, PDO::PARAM_STR);
	$createNewUser->bindParam(':pw', $password, PDO::PARAM_STR);
	$createNewUser->bindParam(':em', $email, PDO::PARAM_STR);
	$createNewUser->bindParam(':jb', $job, PDO::PARAM_STR);
	$createNewUser->bindParam(':jd', $joindate, PDO::PARAM_STR);
	print(' Query parameters bound!');
} catch (PDOException $pe) {
	die('<br/><br/>Error preparing Create New User query, L' . $pe->getLine() . ":" . $pe->getMessage());
}
/*
 * users - Populate the database.
 */
$qty_initial_users = rand(6, 12);
// No particular reason for these values of min and max for rand(). Just whimsy.
for ($i = 1; $i <= $qty_initial_users; $i++) {
	$username = 'user' . (string)$i;
	$password = str_repeat(chr(($i % 26) + 96), 8); // aaaaaaaa, bbbbbbbb, etc
	$password = password_hash($password, PASSWORD_DEFAULT);
	$email = 'user000' . chr($i + 48) . '@email.com';
	if ($i % 3 == 0) {
		$job = "QC Agent #" . (string)$i . ", Day Shift";
	} elseif (isset($job)) {
		unset($job);
	}
	// randomly backdate the joindate, just to have some variance:
	$joindate = date("Y-m-d H:i:s", time() - 360*rand(240, 9600));
	try {
		$createNewUser->execute();
		$createNewUser->closeCursor();
	} catch (PDOException $pe) {
		die('Create New User execution error, User #' . (string)$i . ', L' . $pe->getLine() . ":" . $pe->getMessage());
	}
}
print('<br/>Users populated! Created ' . (string)$qty_initial_users . ' users.');
unset($query_createNewUser, $qty_initial_users, $createNewUser);
/*
 * users - Retrieve table contents (to verify retrieval works, and to use later when assigning links to users).
 */
try {
	$query_fetchUsers = 'SELECT * FROM users;';
	$fetchUsersStatement = $conn->query($query_fetchUsers);
	$users = $fetchUsersStatement->fetchAll(PDO::FETCH_BOTH);
	print('<br/>Users fetched! ');
	print((string)$fetchUsersStatement->rowCount() . ' users fetched.');
} catch (PDOException $pe) {
	die('<br/><br/>Error fetching users, L' . $pe->getLine() . ":" . $pe->getMessage());
}
unset($query_fetchUsers, $fetchUsersStatement);
// Don't unset $users, we'll need it

/*
 *
 *
 *
 *
 * links
 *
 * Initialize the links table (of user bookmarks):
 *
 */
print('<br/><br/>INITIALIZE <code>links</code> TABLE:');
/*
 * links - Create table.
 */
try {
	$query_createTable_links =
			'DROP TABLE IF EXISTS links;
			CREATE TABLE IF NOT EXISTS links (
			id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
			username VARCHAR(64) NOT NULL,
			linkname VARCHAR(255) NOT NULL,
			linkicon VARCHAR(64),
			linkfont VARCHAR(64),
			linkcolor VARCHAR(16),
			url VARCHAR(255) NOT NULL,
			adddate DATETIME,
			visitdate DATETIME,
			CONSTRAINT internal_label UNIQUE KEY (username, url));
			CREATE INDEX user_index ON links(username);';
	if ($dropEverything === TRUE) {
		$query_dropTable_users = "DROP TABLE IF EXISTS links; ";
		$query_createTable_links = $query_dropTable_users . $query_createTable_links;
	}
	$conn->query($query_createTable_links);
	print('<br/>Table configured!');
} catch (PDOException $pe) {
	die('<br/><br/>Error creating table of links, L' . $pe->getLine() . ":" . $pe->getMessage());
}
/*
 * links - Prepare query to insert new entry.
 */
try {
	$username = ' '; $linkname = ' '; $url = ' '; $adddate = ' '; $visdate = ' ';
	$query_addNewLink =
			'INSERT INTO links (username, linkname, url, adddate, visitdate)
			VALUES (:un, :ln, :ur, :ad, :vd); SELECT * FROM links;';
	$addNewLink = $conn->prepare($query_addNewLink);
	print(' Query prepared!');
	$addNewLink->bindParam(':un', $username, PDO::PARAM_STR);
	$addNewLink->bindParam(':ln', $linkname, PDO::PARAM_STR);
	$addNewLink->bindParam(':ur', $url, PDO::PARAM_STR);
	$addNewLink->bindParam(':ad', $adddate, PDO::PARAM_STR);
	$addNewLink->bindParam(':vd', $visdate, PDO::PARAM_STR);
	print(' Query parameters bound!');
} catch (PDOException $pe) {
	die('<br/><br/>Error preparing Insert New User, L' . $pe->getLine() . ":" . $pe->getMessage());
}

$max_jump = min(10, count($popularSites));
foreach ($users as $user) {
	$whichlink = rand(1,9);
	while ($whichlink < count($popularSites)):
		$username = $user['username'];
		$linkname = $popularSites[$whichlink]['linkname'];
		$url = $popularSites[$whichlink]['url'];
		$adddate = date("Y-m-d H:i:s", time() - 60*rand(10, 1440));
		$visdate = $adddate;
		$addNewLink->execute();
		$addNewLink->closeCursor();
		$whichlink = $whichlink + rand(1, $max_jump);
	endwhile;
	$query = $conn->query('SELECT * FROM links;');
	$result = $query->fetchAll();
	print('<br/><br/> #rows ' . $query->rowCount());
	print('<br/><br/>');
	foreach ($result as $linkypoo) {
		print('<br/><br/>');
		print_r($linkypoo);
		print('<br/><br/>');
	}
	//print("<br/><br/><br/>" . count($result));
}

die('</br></br>Made it!');

?>
</body>
</html>
