<?php

# Google Sheets Class

namespace Microframeworks;
class UpxForms {
	
	public static function valueRange()
	{
		// return new \Google_Service_Sheets_ValueRange();
		return new \Google\Service\Sheets\ValueRange();
	}

	private static function newClient($credentials)
	{
		if (empty($data['credentials'])) {
			self::log('Credenciais ausentes.', 1);
			return false;
		}

		$client = new \Google\Client();
		$client->setApplicationName('Google Sheets API PHP Quickstart');
		$client->setScopes('https://www.googleapis.com/auth/spreadsheets');
		$client->setAuthConfig($credentials);
		$client->setAccessType('offline');
		$client->setPrompt('select_account consent');
		return $client;
	}

	public static function getAccessToken(Array $credentials, Callable $saveToken) : bool
	{
		if (php_sapi_name()!='cli') {
			self::log('Acesso negado. A configuracao deve ser feita pelo modo cli.', 1);
			return false;
		}

		$client = self::newClient($credentials);

		$authUrl = $client->createAuthUrl();
		
		printf("Abra o seguinte link em seu navegador:\n%s\n", $authUrl);
		print 'Introduza o codigo de verificacao do link (use urldecode): ';
		$authCode = trim(fgets(STDIN));

		$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
		$client->setAccessToken($accessToken);

		if (array_key_exists('error', $accessToken)) {
			self::log('Mensagem: ' . implode(', ', $accessToken), 1);
			return false;
		}

        return call_user_func($saveToken, json_encode($client->getAccessToken()));
	}
	
	public static function getClient($data)
	{
		$client = self::newClient($data['credentials']);

		if (empty($data['token'])) {
			self::log('Token ausente.', 1);
			return false;
		}

		$client->setAccessToken($data['token']);

		if ($client->isAccessTokenExpired()) {
			if ($client->getRefreshToken()) {
				$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
				if (empty($data['saveToken'])) {
					call_user_func($data['saveToken'], json_encode($client->getAccessToken()));
				} else {
					self::saveToken(json_encode($client->getAccessToken()));
				}
			} else {
				self::log('Nao foi possivel renovar o token de acesso, a configuracao deve ser feita pelo modo cli.', 1);
				return false;
			}
		}
		return $client;
	}

	public function updateValues($spreadsheetId, $range, $valueInputOption, $values, $data)
	{
		$client = self::getClient($data);

		if (!$client) {
			exit('Falha ao iniciar app' . PHP_EOL);
		}

		$service = new \Google\Service\Sheets($client);
		try{
			$body = new \Google\Service\Sheets\ValueRange([
			'values' => $values
			]);
			$params = [
			'valueInputOption' => $valueInputOption
			];

			$result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
			// printf("%d cells updated.\n", $result->getUpdatedCells());
			// self::log('Mensagem: ' .$e->getMessage(), 1);
			return $result;
		}
		catch(\Exception $e) {
			self::log('Mensagem: ' .$e->getMessage(), 1);
			return false;
		}
	}

	public static function insertRows($spreadsheetId, $range, $valueRange, $options, $data)
	{
		$client = self::getClient($data);
		// $client->getAccessToken();
		$service = new \Google\Service\Sheets($client);
		try{
			$result = $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
			self::log($result->getUpdates()->getUpdatedRows() . ' linha(s) inserida(s).', 1);
			return $result;
		}
		catch(\Exception $e) {
			self::log('Mensagem: ' .$e->getMessage(), 1);
			return false;
		}
	}

	public static function getAll($spreadsheetId, $data)
	{
		$client = self::getClient($data);
		// $client->getAccessToken();
		$service = new \Google\Service\Sheets($client);
		try{
			$range = 'Lentes'; // here we use the name of the Sheet to get all the rows
			$response = $service->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			var_dump($values);
		}
		catch(\Exception $e) {
			self::log('Mensagem: ' .$e->getMessage(), 1);
			return false;
		}
	}
	
	public static function saveToken($in)
	{
		$token = is_string($in) ? $in : json_encode($in);
		$arquivo = dirname(__DIR__, 1) . '/data/token.json';
		if (!is_file($arquivo)) {
			file_put_contents($arquivo, '');
			chmod($arquivo, 0777);
		}
		file_put_contents($arquivo, $token);
	}

	public static function log($msg, $log = false)
	{
		if ($log) {
			$arquivo = dirname(__DIR__, 1) . '/data/log.txt';
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
