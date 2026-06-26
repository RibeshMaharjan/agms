<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'chandan';
$db_pass = getenv('DB_PASS') ?: 'chandan';
$db_name = getenv('DB_NAME') ?: 'agms';

$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (mysqli_connect_errno()) {
    echo "Connection Fail: " . mysqli_connect_error();
    die();
}
?>
