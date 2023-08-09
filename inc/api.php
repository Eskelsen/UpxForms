<?php

# UpxForms API

# Dev Mode: Show Errors
ini_set('display_errors', true);
ini_set('display_startup_erros', true);
error_reporting(E_ALL);

# Includes
require dirname(__DIR__, 1) . '/vendor/autoload.php';
include dirname(__DIR__, 1) . '/inc/UpxForms.php';

use Microframeworks\UpxForms;

# Input
$content = file_get_contents('php://input');
$form	 = json_decode($content, 1);

if (empty($form)) {
	upx_log_exit('Erro: sem dados de formulário.');
}

if (!empty($form['time'])) {
	if ($form['time']>=(time()-20)) {
		upx_log('Possível spam.');
		exit(json_encode([
		'status' => false,
		'msg'    => 'Mensagem marcada como spam'
		], JSON_PRETTY_PRINT));
	}
	unset($form['time']);
}


$options = get_option('upxforms_api_settings');

if (empty($options['planilha'])) {
	upx_log_exit('Erro: ID de planilha ausente.');
	
}

if (empty($options['credenciais'])) {
	upx_log_exit('Erro: credenciais ausentes.');
}

if (empty($options['token'])) {
	upx_log_exit('Erro: token ausente.');
}

$callback = function($options){
	update_option('upxforms_api_settings', $options);
};

$status = true;
$msg = 'Mensagem enviada com sucesso';
$rows = [array_values($form)];
$valueRange = UpxForms::valueRange();
$valueRange->setValues($rows);
$googleOptions = ['valueInputOption' => 'USER_ENTERED'];
$result = UpxForms::insertRows($options['planilha'], 'Sheet1', $valueRange, $googleOptions, $options, $callback);

if (!$result) {
	UpxForms::saveData($rows, time() . '.json');
	$status = false;
	$msg = 'Algum problema aconteceu. Mensagem salva em backup.';
}

exit(json_encode(['status' => $status, 'msg' => $msg, 'result' => $result], JSON_PRETTY_PRINT));
