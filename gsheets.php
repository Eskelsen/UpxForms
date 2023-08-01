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

// Nao vai precisar
$arg = $argv[1] ?? false;
$json = hex2bin($arg);
$data = json_decode($json, 1);

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
UpxForms::insertRows($spreadsheetId, $range, $valueRange, $options, $data);
