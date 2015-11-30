<?php
//namespace files;

function load_resource($resourcename) {
	$file = interpreted_file(findfile($resourcename));
	return $file['html'];
}

function findfile($requestedfile) {
	/*
	if (!empty($_SESSION['knownfiles'][$requestedfile])) {
		if (is_file($_SESSION['knownfiles'][$requestedfile])) {
			return $_SESSION['knownfiles'][$requestedfile];
 		} else {
			unset($_SESSION['knownfiles'][$requestedfile]);
		}
	}
	*/
	$file = parsefilename($requestedfile);
	/*
	 * The array returned by parsefilename is empty/incorrect/unconfirmed.
	 * That's what we need to find.
	 */
	$file['path'] = '';
	$di = new RecursiveDirectoryIterator($_SESSION['base_directory']);
	foreach (new RecursiveIteratorIterator($di) as $filename => $item) {
		if (strpos($item, $file['name'])) {
			$file['full'] = $item;
			$file['path'] = str_replace($file['name'], '', $file['full']);
			$file['href'] = str_replace($_SESSION['base_directory'] . '/', '' , $item);
			//break;
		}
	}
	if ($file['path'] == '') {
		die('File not found! ' . $file['name']);
	} else {
		//$_SESSION['knownfiles'][$file['name']] = $file['href'];
	}
	return $file;
}

function parsefilename($given)
{
	$file = [];
	// Place some limitations on file extension length.
	$min_fileextension_length = 1;
	$max_fileextension_length = 4;
	// Put together a regular expression that parses the given file expression
	$parse_pattern_path = '^(.+\/)?';
	$parse_pattern_basename = '(.+)';
	$parse_pattern_extension = '\.([a-z0-9]{' . $min_fileextension_length . ',' . $max_fileextension_length . '})$';
	$parse_pattern = '/' . $parse_pattern_path . $parse_pattern_basename . $parse_pattern_extension . '/';
	preg_match($parse_pattern, $given, $filenameparts);
	if ([$filenameparts && count($filenameparts) == 3]) {
		$file['path'] = $filenameparts[1]; // can be empty
		$file['base'] = $filenameparts[2];
		$file['ext'] = $filenameparts[3];
		$file['name'] = $filenameparts[2] . '.' . $filenameparts[3];
		$file['full'] = $filenameparts[1] . $file['name'];
		$file['type'] = '';
		$file['html'] = '';
	} else {
		// error
	}
	return $file;
}

function interpreted_file($file) {
	$file['hdir'] = str_replace($_SESSION['base_directory'] . '/', '', $file['path']);
	$file['href'] = $file['hdir'] . $file['name'];
	if (preg_match('/(ico)/', $file['ext'])) {
		$file['type'] = 'icon';
		$file['html'] = '<link rel="icon" href="' . $file['href'] . '" type="image/x-icon">';
	}
	if (preg_match('/(png|jpg|jpeg|gif)/', $file['ext'])) {
		$file['type'] = 'img';
		$file['html'] = '<img alt="' . $file['base'] . '" src="' . $file['href'] . '">';
	}
	elseif (preg_match('/js/', $file['ext'])) {
		$file['type'] = 'text/javascript';
		$file['html'] = '<script text="text/javascript" src="' . $file['href'] . '"></script>';
	}
	elseif (preg_match('/css/', $file['ext'])) {
		$file['type'] = 'text/css';
		$file['html'] = '<link rel="stylesheet" type="text/css" href="' . $file['href'] . '">';
	}
	if (preg_match('/php/', $file['ext'])) {
		$file['type'] = 'php';
		$file['html'] = '';
	}
	if (preg_match('/[htm|html]/', $file['ext'])) {
		$file['type'] = 'text';
		$file['html'] = '<object type="text/html" data="' . $file['href'] . '"></object>';
	}
	return $file;
}

function insert_image($givenfile, $options = NULL) {
	$custom = '';
	$validkeys = ['id', 'class', 'style', 'align', 'border', 'height', 'ismap', 'longdesc', 'usemap', 'width'];
	$file = interpreted_file(findfile($givenfile));
	/*
	if (is_string($options)) {
		foreach ($validkeys as $key) {
			if (strpos($key, $options)) {
				// TODO, implement passing string of options
			}
		}
	}
	*/
	if (is_array($options)) {
		foreach ($options as $key => $value) {
			// src and alt are not permitted.
			// TODO, implement alt
			if (in_array($key, $validkeys)) { $custom .= $key . '="' . $value . '" '; }
		}
	}
	 $file['html'] = str_replace('>', ' ' . $custom . '>', $file['html']);
	return $file['html'];
}