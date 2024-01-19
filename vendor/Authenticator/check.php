<?php
if (!isset($_SESSION)) {
    session_start();
}

require "Authenticator.php";

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("location: index.php");
    die();
}

$Authenticator = new Authenticator();

$checkResult = $Authenticator->verifyCode($_SESSION['auth_secret'], $_POST['code'], 2);    // 2 = 2*30sec clock tolerance

if (!$checkResult) {
    $_SESSION['failed'] = true;
    header("location: index.php");
    die();
} else {
    unset($_SESSION['auth_secret']);
    header("location: ../../index.php");
    die();
}
?>