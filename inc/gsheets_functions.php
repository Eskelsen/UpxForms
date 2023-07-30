<?php

# Google Sheets Functions

namespace Microframeworks;

use Google\Client;
use Google\Service\Sheets\ValueRange;

class UpxForms {
	
	public static function valueRange()
	{
		return new \Google_Service_Sheets_ValueRange();
	}
	
	public function getClient($credentials, $token = false)
	{
		$client = new Google\Client();
		$client->setApplicationName('Google Sheets API PHP Quickstart');
		$client->setScopes('https://www.googleapis.com/auth/spreadsheets');
		$client->setAuthConfig($credentials);
		$client->setAccessType('offline');
		$client->setPrompt('select_account consent');
		
		if ($stringToken)
			$dataToken = json_decode($token, true);
			$client->setAccessToken($dataToken);
		}

		if ($client->isAccessTokenExpired()) {
			if ($client->getRefreshToken()) {
				$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			} else {
				$authUrl = $client->createAuthUrl();
				printf("Open the following link in your browser:\n%s\n", $authUrl); // ToChange
				print 'Enter verification code: '; // ToChange
				$authCode = trim(fgets(STDIN));

				$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
				$client->setAccessToken($accessToken);

				if (array_key_exists('error', $accessToken)) {
					throw new Exception(join(', ', $accessToken)); // ToChange
				}
			}

			if (!file_exists(dirname($tokenPath))) { // ToChange
				mkdir(dirname($tokenPath), 0700, true); // ToChange
			} // ToChange
			file_put_contents($tokenPath, json_encode($client->getAccessToken())); // ToChange
		}
		return $client;
	}

	public function updateValues($spreadsheetId, $range, $valueInputOption, $values)
	{
		$client = getClient();
		$service = new Google_Service_Sheets($client);
		try{
			$body = new Google_Service_Sheets_ValueRange([
			'values' => $values
			]);
			$params = [
			'valueInputOption' => $valueInputOption
			];

			$result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
			printf("%d cells updated.\n", $result->getUpdatedCells()); // ToChange
			return $result;
		}
		catch(Exception $e) {
			echo 'Message: ' .$e->getMessage(); // ToChange
		}
	}

	public function insertRows($spreadsheetId, $range, $valueRange, $options)
	{
		$client = getClient();
		$service = new Google_Service_Sheets($client);
		try{
			$result = $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
			printf("%s cells updated.\n", json_encode($result)); // ToChange
			return $result;
		}
		catch(Exception $e) {
			echo 'Message: ' .$e->getMessage(); // ToChange
		}
	}
}
