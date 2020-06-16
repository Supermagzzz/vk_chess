<?php
	function jsonPrint($arr) {
		echo json_encode($arr);
		exit();
	}
	
	function jsonFail($message) {
		jsonPrint(['status' => 'Fail', 'message' => $message]);
	}

	function jsonOK($message) {
		jsonPrint(['status' => 'OK', $message]);
	}
?>