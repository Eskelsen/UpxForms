<?php

# Google Sheets Class

namespace Microframeworks;

class UpxForms {
	
	public static function valueRange()
	{
		return new \Google\Service\Sheets\ValueRange();
	}

	private static function newClient($credenciais)
	{
		if (empty($credenciais)) {
			self::log('Credenciais ausentes.', 1);
			exit;
		}

		$client = new \Google\Client();
		$client->setApplicationName('Google Sheets API PHP Quickstart');
		$client->setScopes('https://www.googleapis.com/auth/spreadsheets');
		$client->setAuthConfig($credenciais);
		$client->setAccessType('offline');
		$client->setPrompt('select_account consent');
		return $client;
	}

	public static function getAccessToken(Array $options, Callable $saveToken) : bool
	{
		if (php_sapi_name()!='cli') {
			self::log('Acesso negado. A configuracao deve ser feita pelo modo cli.', 1);
			return false;
		}

		$client = self::newClient($options['credenciais']);

		$authUrl = $client->createAuthUrl();
		
		echo "Abra o seguinte link em seu navegador:\n\n" . $authUrl . "\n\n";
		echo 'Introduza o codigo de verificacao: ';
		$authCode = trim(fgets(STDIN));

		$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
		$client->setAccessToken($accessToken);

		if (array_key_exists('error', $accessToken)) {
			self::log('Mensagem: ' . implode(', ', $accessToken), 1);
			return false;
		}
		
		$options['token'] = $client->getAccessToken();

        return call_user_func($saveToken, $options);
	}
	
	public static function getClient($options)
	{
		$client = self::newClient($options['credenciais']);

		if (empty($options['token'])) {
			self::log('Token ausente.', 1);
			return false;
		}

		$client->setAccessToken($options['token']);

		if ($client->isAccessTokenExpired()) {
			if ($client->getRefreshToken()) {
				$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
				if (empty($options['saveToken'])) {
					self::saveToken(json_encode($client->getAccessToken()));
				} else {
					$options['token'] = $client->getAccessToken();
					return call_user_func($options['saveToken'], $client->getAccessToken());
				}
			} else {
				self::log('Nao foi possivel renovar o token de acesso, a configuracao deve ser feita pelo modo cli.', 1);
				return false;
			}
		}
		return $client;
	}

	public function updateValues($spreadsheetId, $range, $valueInputOption, $values, $options)
	{
		$client = self::getClient($options);

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
			self::log('Mensagem: ' . $e->getMessage(), 1);
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
			self::log('Mensagem: ' . $e->getMessage(), 1);
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
			if (UPXFORMS_LOGS_ON) {
				$data = time() . '|' . date('Y-m-d H:i:s') . '|' . $msg . "\r\n";
				file_put_contents(UPXFORMS_LOG_FILE, $data, FILE_APPEND);
			}
		}
		if (php_sapi_name()=='cli') {
			echo $msg . PHP_EOL;
		}
	}
}
