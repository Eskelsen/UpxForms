<?php

# Logs

function upx_log($msg){
	if (UPXFORMS_LOGS_ON) {
		$data = time() . '|' . date('Y-m-d H:i:s') . '|' . $msg . "\r\n";
		file_put_contents(UPXFORMS_LOG, $data, FILE_APPEND);
	}
}
