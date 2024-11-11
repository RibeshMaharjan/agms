<?php
    // include '../../../config/function.php';

    echo  "Success"; //This is a simple
    $data = isset( $_GET['data'] ) ? $_GET['data'] : '';
    $decodeddata = base64_decode( $data );

    $json_string = substr($decodeddata, strpos($decodeddata, '{'));

    // Decode the JSON string
    $response = json_decode($json_string, true);

    if($response['status'] !== 'COMPLETE'){
        echo "transaction failed";
    }

    echo '<br>';
    // print_r($response);
    echo '<pre>'; print_r($response); echo '</pre>';

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

    echo '<pre>'; print_r($signaturemessage); echo '</pre>';


    $secret = "8gBm/:&EnhH.1/q";
    $s = hash_hmac('sha256', "$signaturemessage", $secret, true);
    $signature = base64_encode($s);

    echo '<br>';
    echo $s;
    echo '<br>';
    echo $signature;
    echo '<br>';
    echo $response['signature'];

    if ($signature == $response['signature']) {
        // redirect('../../medicine.php','Your Order has been submitted.');
    }
?>