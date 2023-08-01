<?php

# Google Sheets Class

namespace Microframeworks;
class UpxForms {
	
	public static function valueRange()
	{
		return new \Google\Service\Sheets\ValueRange();
	}

	private static function newClient($credentials)
	{
		if (empty($credentials)) {
			self::log('Credenciais ausentes.', 1);
			exit;
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
		
		echo "Abra o seguinte link em seu navegador:\n\n" . $authUrl;
		echo 'Introduza o codigo de verificacao: ';
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
					self::saveToken(json_encode($client->getAccessToken()));
				} else {
					call_user_func($data['saveToken'], json_encode($client->getAccessToken()));
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
			$body = new \Google\Service\Sheets\ValueRange(['values' => $values]);
			$params = ['valueInputOption' => $valueInputOption];
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

	public static function getAll($spreadsheetId, $range, $data)
	{
		$client = self::getClient($data);
		$service = new \Google\Service\Sheets($client);
		try{
			$response = $service->spreadsheets_values->get($spreadsheetId, $range);
			return $response->getValues();
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
		$u = file_put_contents($arquivo, $token);
		return ($u) ? true : false;
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
