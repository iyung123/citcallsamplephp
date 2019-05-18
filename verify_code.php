<?php
session_start();
header('Content-Type: application/json');
if(!isset($_SESSION['trxid']) OR !isset($_SESSION['trxid'])) {
	$return = array(
		"error" => TRUE,
		"info" => "Pleasr do request first!"
	);
	echo json_encode($return);
	exit();
}
$code = $_POST['code'];
$trxid = $_POST['trxid'];

$error = TRUE;

if($code === "" OR $trxid === "") {
	$info = "Cannot empty";
} else {
	if($code === $_SESSION['token'] && $trxid === $_SESSION['trxid']) {
		$info = "Success";
		$error = FALSE;
		session_destroy();
	} else {
		$info = "Wrong code";
	}
}
$return = array(
	"error" => $error,
	"info" => $info
);
echo json_encode($return);
exit();
