<?php
//namespace sessions;

function S($variable)
{
	if (!empty($_SESSION[$variable])) {
		return $_SESSION[$variable];
	} elseif (empty($variable)) {
		$_SESSION['caught'][] = 'The key ' . $variable . ' was empty.';
	} elseif (empty(($_SESSION[$variable]))) {
		$_SESSION['caught'][] = 'The key ' . $variable . ' did not exist.';
	} else {
		$_SESSION['caught'][] = 'Undiagnosed variable reference error. ';
		var_dump($variable);
		return 1;
	}
}