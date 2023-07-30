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
				$authUrl = $client->createAuthUrl();
				
				printf("Open the following link in your browser:\n%s\n", $authUrl);
				print 'Enter verification code: ';
				$authCode = trim(fgets(STDIN));

				$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
				$client->setAccessToken($accessToken);

				if (array_key_exists('error', $accessToken)) {
					throw new Exception(join(', ', $accessToken));
				}
			}

			// if (!file_exists(dirname($tokenPath))) { // ToChange
				// mkdir(dirname($tokenPath), 0700, true); // ToChange
			// } // ToChange
			// file_put_contents($tokenPath, json_encode($client->getAccessToken())); // ToChange
		}
		return $client;
	}

	public function updateValues($spreadsheetId, $range, $valueInputOption, $values, $data)
	{
		$client = self::getClient($data['credentials'], $data['token']);
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
			return $result;
		}
		catch(Exception $e) {
			echo 'Message: ' .$e->getMessage(); // ToChange
		}
	}

	public static function insertRows($spreadsheetId, $range, $valueRange, $options, $data)
	{
		$client = self::getClient($data['credentials'], $data['token']);
		// $client->getAccessToken();
		$service = new \Google_Service_Sheets($client);
		try{
			$result = $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
			printf("%s cells inserted.\n", $result->getUpdatedCells());
			return $result;
		}
		catch(Exception $e) {
			echo 'Message: ' .$e->getMessage(); // ToChange
		}
	}
}
