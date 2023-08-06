<?php

# Logs

function upx_log($msg){
	if (UPXFORMS_LOGS_ON) {
		$data = time() . '|' . date('Y-m-d H:i:s') . '|' . $msg . "\r\n";
		file_put_contents(UPXFORMS_LOG_FILE, $data, FILE_APPEND);
	}
	if (php_sapi_name()=='cli') {
		echo $msg . PHP_EOL;
	}
}

function upx_log_exit($msg){
	upx_log($msg);
	exit(json_encode(['status' => false, 'msg' => $msg], JSON_PRETTY_PRINT));
}
