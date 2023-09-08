<?php

$participant_num = $_POST["participant_num"];

if (!isset($_SESSION)) {
    session_start();
}

$_SESSION['participant_num'] = $participant_num;

header("Location: garmin_auth.php");

?>
