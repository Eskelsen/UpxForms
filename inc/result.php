<?php

# Result

# Plate
$plate = $_POST['form_fields']['name'] ?? false;

if (!$plate) {
	$plate = $_POST['form_fields']['name2'] ?? false;
}

# Brand Logo
include VERICAR_INC . 'brands.php';

$codes = ['[vericar_start]', '[chassi]', '[placa]', '[local]', '[renavam]', '[marca]', '[modelo]', '[cor]', '[anodomodelo]', '[anodefabricacao]', '[combust]'];
$logo  = '<img src="' . VERICAR_URL . 'logos/' . $brands['default'] . '">';

# Validate Plate
$regex = '/[A-Z]{3}[0-9][0-9A-Z][0-9]{2}/';

$plate = strtoupper(preg_replace("/[^A-Za-z0-9]/",'',$plate));

if (preg_match($regex, $plate) !== 1) {
	vericar_log('Placa inválida: ' . $plate);
	goto RESULT;
}

if (!$plate) {
	goto RESULT;
}

# Get Options Again
$options = get_option('vericar_api_settings');

if (empty($options['token']) || empty($options['email']) || empty($options['pswd'])) {
	vericar_log('Credenciais ausentes.');
	goto RESULT;
}

$token = $options['token'];
$email = $options['email'];
$pswd  = $options['pswd'];
$plate = $plate;

$raw = [
	'token' 	=> $token,
	'userName' 	=> $email,
	'password' 	=> $pswd,
	'placa' 	=> $plate
];

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
	vericar_log('Algum erro na resposta da API.');
	goto RESULT;
}

curl_close($curl);

# Treatment
$xml  = simplexml_load_string($content);
$data = json_decode(json_encode($xml), 1);

if (!$data) {
	vericar_log('Dados ausentes na resposta da API.');
}

RESULT:

# Message
$msg = $data['Cabecalho']['Mensagem'] ?? 'Indisponível';

# Data
$chassi 	= $data['Chassi'] ?? 'Indisponível';
$placa  	= $data['Placa'] ?? 'Indisponível';
$cidade  	= $data['NomeCidade'] ?? 'Indisponível';
$uf  		= $data['UfJurisdicao'] ?? 'Indisponível';
$local = $cidade . '-' . $uf;
$renavam 	= $data['Renavam'] ?? 'Indisponível';
$mm_tmp 	= $data['MarcaModelo'] 	?? 'Indisponível';
[$marca, $modelo] = (strpos($mm_tmp,'/')!==false) ? explode('/', $mm_tmp) : [$mm_tmp, $mm_tmp];
$cor 		= $data['NomeCor'] ?? 'Indisponível';
$anomod 	= $data['AnoModelo'] ?? 'Indisponível';
$anofab 	= $data['AnoFabricacao'] ?? 'Indisponível';
$combust 	= $data['NomeCombustivel'] ?? 'Indisponível';

# Plate format
if (!empty($data['Placa'])) {
	$placa = substr($placa, 0, 3) . '-' . substr($placa, 3, 7);
}

# Chassi Protect
if (!empty($data['Chassi'])) {
	$chassi = substr($chassi, 0, 5)  . '************';
}

# Renavam problem
$renavam = '***********';

# Map Brands
$slug = str_replace(' ', '-', strtolower($marca));
if (empty($brands[$slug])) {
	vericar_log('Nenhum logo para a marca <strong>' . $marca . '</strong> modelo ' . $modelo);
	$logo = $brands['default'];
} else {
	$logo = $brands[$slug];
}

vericar_log('Consulta realizada para <strong>' . $placa . '</strong>');

$logo = '<img src="' . VERICAR_URL . 'logos/' . $logo . '">';

# Map Fields
$codes  = ['[vericar_start]', '[chassi]', '[placa]', '[local]', '[renavam]', '[marca]', '[modelo]', '[cor]', '[anodomodelo]', '[anodefabricacao]', '[combust]'];
$values = [$logo, $chassi, $placa, $local, $renavam, $marca, $modelo, $cor, $anomod, $anofab, $combust];

// remove_filter('the_content', 'vericar_verify_content');

return str_replace($codes, $values, $in);
