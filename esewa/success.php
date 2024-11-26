<?php

    $data = isset( $_GET['data'] ) ? $_GET['data'] : '';
    $decodeddata = base64_decode( $data );

    $json_string = substr($decodeddata, strpos($decodeddata, '{'));

    // Decode the JSON string
    $response = json_decode($json_string, true);

    if($response['status'] !== 'COMPLETE'){
        header('Location: ./../index.php?purchaseStatus=failure');
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
        header('Location: ./../index.php?purchaseStatus=success');
    }
?>