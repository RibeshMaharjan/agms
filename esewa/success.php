<?php
session_start();

$data = isset($_GET['data']) ? $_GET['data'] : '';
$decodeddata = base64_decode($data);

$json_string = substr($decodeddata, strpos($decodeddata, '{'));

// Decode the JSON string
$response = json_decode($json_string, true);

if($response['status'] !== 'COMPLETE'){
    $_SESSION['error'] = "Payment failed. Please try again.";
    header('Location: ./../index.php');
    exit();
}

$message = $response['signed_field_names'];

$array = explode(",", $message);
$signaturemessage = "";
foreach ($array as $value) {
    if ($value == 'total_amount') {
        $amount = str_replace(',', '', $response[$value]);
        $signaturemessage = $signaturemessage.$value.'='.$amount.',';
    } else {
        $signaturemessage = $signaturemessage.$value.'='.$response[$value].',';
    }
}
$signaturemessage = rtrim($signaturemessage, ',');

$secret = "8gBm/:&EnhH.1/q";
$s = hash_hmac('sha256', "$signaturemessage", $secret, true);
$signature = base64_encode($s);

if ($signature == $response['signature']) {
    $_SESSION['success'] = "Payment successful! Your order has been placed.";
    header('Location: ./../index.php');
} else {
    $_SESSION['error'] = "Payment verification failed. Please contact support.";
    header('Location: ./../index.php');
}
?>