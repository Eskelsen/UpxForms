<?php

# Dev Mode: Show Errors
ini_set('display_errors', true);
ini_set('display_startup_erros', true);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/inc/UpxForms.php';

use Microframeworks\UpxForms;

if (php_sapi_name() != 'cli') { // remover
    exit('This application must be run on the command line.');
}

$credentials = json_decode(file_get_contents('aaa.json'), 1);
$token = json_decode(file_get_contents('toki.json'), 1);

$data = [
	'credentials' 	=> $credentials,
	'token' 		=> $token,
	'saveToken' 	=> false 	// callback, false or (=) absent
];

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
// $spreadsheetId = '1SRrgH819TKYGbzJ88qxpxixfbXEOiai2JnTr8aEz-cc';

$u = UpxForms::getAll($spreadsheetId, $data);
var_dump($u);
exit;

$rows = [$newRow];
$valueRange = UpxForms::valueRange();
$valueRange->setValues($rows);
$range = 'Sheet1';
$options = ['valueInputOption' => 'USER_ENTERED'];
$u = UpxForms::insertRows($spreadsheetId, $range, $valueRange, $options, $data);
var_dump($u);