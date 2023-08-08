<?php

# UpxForms API

# Dev Mode: Show Errors
ini_set('display_errors', true);
ini_set('display_startup_erros', true);
error_reporting(E_ALL);

# Includes
require dirname(__DIR__, 1) . '/vendor/autoload.php';
include dirname(__DIR__, 1) . '/inc/UpxForms.php';

# WP Core
include dirname(__DIR__, 3) . '/wp-config.php';

use Microframeworks\UpxForms;

# Input
$content = file_get_contents('php://input');
$form	 = json_decode($content, 1);

if (empty($form)) {
	upx_log_exit('Erro: sem dados de formulário.');
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

$rows = [array_values($form)];
$valueRange = UpxForms::valueRange();
$valueRange->setValues($rows);
$googleOptions = ['valueInputOption' => 'USER_ENTERED'];
$u = UpxForms::insertRows($options['planilha'], 'Sheet1', $valueRange, $googleOptions, $options, 'update_option');
	
exit(json_encode(['status' => true, 'msg' => 'Mensagem enviada com sucesso', 'u' => $u], JSON_PRETTY_PRINT));
