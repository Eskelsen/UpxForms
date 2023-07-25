<?php

# Logs

function vericar_log($msg){
	if (VERICAR_LOGS_ON) {
		$data = time() . '|' . date('Y-m-d H:i:s') . '|' . $msg . "\r\n";
		file_put_contents(VERICAR_LOG, $data, FILE_APPEND);
	}
}
