<?php
session_start();
/**
|-------------------------------------------------------------------
| CITCALL
|-------------------------------------------------------------------
| before you connect to the citcall API make sure that:
| 1. You have read the citcall API documentation
| 2. your userid has been registered and your IP has been filtered in citcall system
|
*/
define("APIKEY", "fe01ce2a7fbac8fafaed7c982a04e229");
$msisdn = $_POST['phone'];
header('Content-Type: application/json');
if($msisdn === "") {
	$return = array(
		"error" => TRUE,
		"info" => "Phone Number Cannot empty!"
	);
	echo json_encode($return);
	exit();
}
if(!isset($_SESSION['trying'])) {
	$gateway = 0;
	$_SESSION['gateway'] = $gateway;
} else {
	$gateway = $_SESSION['gateway'] + 1;
	if($gateway > 4)
		$gateway = 0;
	$_SESSION['gateway'] = $gateway;
}

$base_url = "http://104.199.196.122/gateway";
$version = "/v3";
$action = "/asynccall";

$url = $base_url . $version . $action ;
$data = array(
	"msisdn" => $msisdn,
	"gateway" => $gateway
);

$content = json_encode($data);
$ch = curl_init($url);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
curl_setopt($ch,CURLOPT_POSTFIELDS,$content);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_HTTPHEADER,array(
	"Content-Type: application/json",
	"Authorization: Apikey ". APIKEY,
	"Content-Length: " . strlen($content))
);
$response  = curl_exec($ch);
$err  = curl_error($ch);

curl_close($ch);

$json = json_decode($response);
$rc = $json->rc;
$error = TRUE;
if($rc === "00") {
	$error = FALSE;
	$token = $json->token;
	$trxid = $json->trxid;
	$_SESSION['token'] = $token;
	$_SESSION['trxid'] = $trxid;
	$first_token = substr($token, 0, -4);
	$length = strlen($token);
	$return = array(
		"error" => $error,
		"trxid" => $trxid,
		"first_token" => $first_token
	);
} else {
	$info = $json->info;
	$return = array(
		"error" => $error,
		"info" => $info
	);
}
echo json_encode($return);
exit();
