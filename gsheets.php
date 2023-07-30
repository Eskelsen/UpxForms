<?php

# Dev Mode: Show Errors
ini_set('display_errors', true);
ini_set('display_startup_erros', true);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use Microframeworks;

include __DIR__ . '/inc/gsheets_functions.php';

if (php_sapi_name() != 'cli') {
    exit('This application must be run on the command line.');
}

$arg = $argv[1] ?? false;

echo $arg;

exit;

	// ToChange
	$newRow = [
		'Teste',
		'Outro teste',
		'https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
		"Alguma longa frase sobre alguma coisa.",
		'123123123',
		'São Paulo, SP'
	];

$spreadsheetId = '1XY-OMSIlS7Rb4l2sVqsquy_grq519RuBN_uxeFI1IZ8'; // ToChange

$rows = [$newRow];
$valueRange = UpxForms::valueRange();
$valueRange->setValues($rows);
$range = 'Sheet1';
$options = ['valueInputOption' => 'USER_ENTERED'];
insertRows($spreadsheetId, $range, $valueRange, $options);
