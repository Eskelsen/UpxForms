<?php

# Dev Mode: Show Errors
ini_set('display_errors', true);
ini_set('display_startup_erros', true);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/inc/UpxForms.php';

use Microframeworks\UpxForms;

if (php_sapi_name() != 'cli') {
    exit('This application must be run on the command line.');
}

$credentials = json_decode(file_get_contents('data/credentials.json'), 1);
$token = json_decode(file_get_contents('data/token.json'), 1);

$data = [
	'credentials' 	=> $credentials,
	'token' 		=> $token,
	'saveToken' 	=> false 	// callback, false or (=) absent
];

$newRow = [
	'1 Teste',
	'2 Outro teste',
	'3 https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
	"4 Alguma longa frase sobre alguma coisa.",
	'5 123123123',
	'6 São Paulo, SP'
];

$spreadsheetId = '1XY-OMSIlS7Rb4l2sVqsquy_grq519RuBN_uxeFI1IZ8';

if (empty($argv[1])) {
	$rows = [$newRow];
	$valueRange = UpxForms::valueRange();
	$valueRange->setValues($rows);
	$options = ['valueInputOption' => 'USER_ENTERED'];
	UpxForms::insertRows($spreadsheetId, 'Sheet1', $valueRange, $options, $data);
} else {
	$u = UpxForms::getAll($spreadsheetId, 'Sheet1', $data);
	var_dump($u);
}
