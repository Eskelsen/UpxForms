<?php

# Google Sheets Class

namespace Microframeworks;

use Google\Client;
use Google\Service\Sheets\ValueRange;

class UpxForms {
	
	public static function valueRange()
	{
		return new \Google_Service_Sheets_ValueRange();
	}
	
	public static function getClient($credentials, $token = false)
	{
		$client = new Client();
		$client->setApplicationName('Google Sheets API PHP Quickstart');
		$client->setScopes('https://www.googleapis.com/auth/spreadsheets');
		$client->setAuthConfig($credentials);
		$client->setAccessType('offline');
		$client->setPrompt('select_account consent');
		
		if ($token) {
			$client->setAccessToken($token);
		}

		if ($client->isAccessTokenExpired()) {
			if ($client->getRefreshToken()) {
				$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			} else {

				if (php_sapi_name()!='cli') {
					self::log('Nao foi possivel renovar o token de acesso, a configuracao deve ser feita pelo modo cli.', 1);
					return [false, false];
				}

				$authUrl = $client->createAuthUrl();
				
				printf("Abra o seguinte link em seu navegador:\n%s\n", $authUrl);
				print 'Introduza o codigo de verificacao do link (use urldecode): ';
				$authCode = trim(fgets(STDIN));

				$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
				$client->setAccessToken($accessToken);

				if (array_key_exists('error', $accessToken)) {
					self::log('Mensagem: ' . implode(', ', $accessToken), 1);
					return [false, false];
				}
			}
		}
		return [$client, $token];
	}

	public function updateValues($spreadsheetId, $range, $valueInputOption, $values, $data)
	{
		[$client, $token] = self::getClient($data['credentials'], $data['token']);

		$service = new \Google_Service_Sheets($client);
		try{
			$body = new Google_Service_Sheets_ValueRange([
			'values' => $values
			]);
			$params = [
			'valueInputOption' => $valueInputOption
			];

			$result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
			// printf("%d cells updated.\n", $result->getUpdatedCells());
			// self::log('Mensagem: ' .$e->getMessage(), 1);
			return [$result, $token];
		}
		catch(Exception $e) {
			self::log('Mensagem: ' .$e->getMessage(), 1);
		}
	}

	public static function insertRows($spreadsheetId, $range, $valueRange, $options, $data)
	{
		[$client, $token] = self::getClient($data['credentials'], $data['token']);
		// $client->getAccessToken();
		$service = new \Google_Service_Sheets($client);
		try{
			$result = $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
			self::log($result->getUpdates()->getUpdatedRows() . ' linha(s) inserida(s).', 1);
			return [$result, $token];
		}
		catch(Exception $e) {
			self::log('Mensagem: ' .$e->getMessage(), 1);
			return [false, $token];
		}
	}
	
	public static function log($msg, $log = false)
	{
		if ($log) {
			$arquivo = dirname(__DIR__) . '/data/log.txt';
			if (!is_file($arquivo)) {
				file_put_contents($arquivo, '');
				chmod($arquivo, 0777);
			}
			file_put_contents($arquivo, $msg . "\n", FILE_APPEND);
		}
		if (php_sapi_name()=='cli') {
			echo $msg . PHP_EOL;
		}
	}
}
