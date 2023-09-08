<?php
	if (!isset($_SESSION)) {
		session_start();
	}

	$token = $_GET['token'];
	$tokenSecret = $_GET['tokenSecret'];
	
	$timestamp = date("Y-m-d H:i:s");
	$participant_num = $_SESSION['participant_num'];
	
	file_put_contents('tokens.csv', $timestamp.",".$participant_num.",".$token.",".$tokenSecret."\n", FILE_APPEND | LOCK_EX);

	echo "התהליך הסתיים בהצלחה";
?>