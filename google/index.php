<?php

# UpxForms Google API Install

# Dev Mode: Show Errors
ini_set('display_errors', true);
ini_set('display_startup_erros', true);
error_reporting(E_ALL);

# WP Core
include dirname(__DIR__, 4) . '/wp-config.php';

defined('UPXFORMS_PATH') || exit('UpxForms indisponivel');

# Includes
require UPXFORMS_PATH . 'vendor/autoload.php';

$options = get_option('upxforms_api_settings');

if (!empty($options['token'])) {
    exit('O token ja esta configurado');
}

if (empty($options['credenciais'])) {
    exit('E necessario configurar as credenciais. Use a url desta pagina como url de redirecionameto no Google.');
}

$client = new \Google\Client();
$client->setApplicationName('Google Sheets API PHP Quickstart');
$client->setScopes('https://www.googleapis.com/auth/spreadsheets');
$client->setAuthConfig($options['credenciais']);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

if (empty($_GET['code'])) {
	$authUrl = $client->createAuthUrl();
	exit('Abra o seguinte link em seu navegador: <a href="' . $authUrl . '">' . $authUrl . '</a>');
}

$accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($accessToken);

if (array_key_exists('error', $accessToken)) {
    self::log('Mensagem: ' . implode(', ', $accessToken), 1);
    exit('Algum erro aconteceu');
}

$options['token'] = $client->getAccessToken();
update_option('upxforms_api_settings', $options);
exit('Token configurado.');
