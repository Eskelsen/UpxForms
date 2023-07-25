<?php

# API

exit(json_encode(['status' => 'false', 'msg' => 'Recurso desabilitado.'], JSON_PRETTY_PRINT));

$form_fields = $_POST['form_fields'] ?? null;

$content = file_get_contents('php://input');
$data = json_decode($content, 1);
$form_fields = $data['form_fields'] ?? null;

$options = get_option('vericar_api_settings');

$logs = empty($options['logs']) ? false : ($options['logs']=='yes');

if (empty($options['token']) || empty($options['token']) || empty($options['token']) || empty($form_fields)) {
	exit(json_encode(['error' => 'error'], JSON_PRETTY_PRINT));
}

// echo json_encode(['logs' => $logs], JSON_PRETTY_PRINT);
// echo json_encode($options, JSON_PRETTY_PRINT);

$token = $options['token'];
$email = $options['token'];
$pswd  = $options['token'];
$plate = $form_fields;

$raw = [
	'token' 	=> $token,
	'userName' 	=> $email,
	'password' 	=> $pswd,
	'placa' 	=> $plate
];

exit(json_encode($raw, JSON_PRETTY_PRINT));

$data = http_build_query($raw);

$request_url = 'https://ws1.tige.com.br/wspuc/veicular.asmx/GetBHVbyPlacaContingente';

$headers = [
	'Content-Type: application/x-www-form-urlencoded',
	'Content-Length: ' . strlen($data),
];

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL             => $request_url,
	CURLOPT_RETURNTRANSFER  => true,
	CURLOPT_MAXREDIRS       => 10,
	CURLOPT_TIMEOUT         => 30,
	CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST   => 'POST',
	CURLOPT_POSTFIELDS      => $data,
	CURLOPT_HTTPHEADER      => $headers
]);

$content = curl_exec($curl);

$e = curl_error($curl);

if ($e) {
	echo 'Error: ' . $e;
	exit('erro');
}

curl_close($curl);

$xml = simplexml_load_string($content);

var_dump(json_decode(json_encode($xml), 1));
