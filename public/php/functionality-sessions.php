<?php
//namespace sessions;

function SSS($variable) // Safe Session String
{
	if (!empty($_SESSION[$variable])) {
		if (is_string($_SESSION[$variable])) {
			return $_SESSION[$variable];
		} elseif (is_array(($_SESSION[$variable]))) {
			$errorMessage = 'The key ' . $variable . ' holds an array.';
		} elseif (is_object(($_SESSION[$variable]))) {
			$errorMessage = 'The key ' . $variable . ' is an object.';
		} else {
			$errorMessage = 'The key ' . $variable . ' is not a string.';
		}
	} elseif (empty($variable)) {
		$errorMessage = 'The key ' . $variable . ' was empty.';
	} elseif (!array_key_exists($variable, $_SESSION)) {
		$errorMessage = 'The key ' . $variable . ' did not exist.';
	} else {
		$errorMessage = 'Undiagnosed variable reference error. ';
	}
	$_SESSION['caught'][] = $errorMessage;
	return $errorMessage;
}

