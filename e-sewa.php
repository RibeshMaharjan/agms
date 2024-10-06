<?php

error_reporting(0);
include('includes/dbconnection.php');

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$product_amount = isset($_GET['product_amount']) ? $_GET['product_amount'] : '';
$message = "total_amount=" . $product_amount . ",transaction_uuid=" . $order_id . ",product_code=EPAYTEST";
$secret = "8gBm/:&EnhH.1/q";
$s = hash_hmac('sha256', $message, $secret, true);
$signature = base64_encode($s);
?>
<form id="paymentForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
    <input type="text" id="amount" name="amount" value="<?= $product_amount ?>" required>
    <input type="text" id="tax_amount" name="tax_amount" value="0" required>
    <input type="text" id="total_amount" name="total_amount" value="<?= $product_amount ?>" required>
    <input type="text" id="transaction_uuid" name="transaction_uuid" value="<?= $order_id ?>" required>
    <input type="text" id="product_code" name="product_code" value="EPAYTEST" required>
    <input type="text" id="product_service_charge" name="product_service_charge" value="0" required>
    <input type="text" id="product_delivery_charge" name="product_delivery_charge" value="0" required>
    <input type="text" id="success_url" name="success_url" value="http://agms.test/esewa/success.php" required>
    <input type="text" id="failure_url" name="failure_url" value="http://agms.test/esewa/failure.php" required>
    <input type="text" id="signed_field_names" name="signed_field_names" value="total_amount,transaction_uuid,product_code" required>
    <input type="text" id="signature" name="signature" value="<?= $signature ?>" required>
    <input value="Submit" type="submit">
</form>

<script>
    window.onload = function() {
        document.getElementById('paymentForm').submit();
    };
</script>