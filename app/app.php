<?php
error_reporting(0);
require_once 'Cryptor.php';

$secret = "";
if (empty($secret) && isset($_GET['secret'])) {
    $secret = $_GET['secret'];
}
if (empty($secret) && isset($_POST['secret'])) {
    $secret = $_POST['secret'];
}

$decrypted = "";
if (empty($decrypted) && isset($_GET['decrypted'])) {
    $decrypted = $_GET['decrypted'];
}
if (empty($decrypted) && isset($_POST['decrypted'])) {
    $decrypted = $_POST['decrypted'];
}

$encrypted = "";
if (empty($encrypted) && isset($_GET['encrypted'])) {
    $encrypted = $_GET['encrypted'];
}
if (empty($encrypted) && isset($_POST['encrypted'])) {
    $encrypted = $_POST['encrypted'];
}

$action = @$_GET['action'] ?: 'encrypt';
$page = $action;
$error = false;

if (!empty($action) && !empty($secret)) {
    $cryptor = new Cryptor($secret);
    switch ($action) {
        case 'encrypt';
            if (!empty($decrypted)) {
                $encrypted = $cryptor->encrypt($decrypted);
                if (!empty($encrypted)) {
                    $action = 'status';
                } else {
                    $error = true;
                }
            }
            break;
        case 'decrypt';
            if (!empty($encrypted)) {
                $decrypted = $cryptor->decrypt($encrypted);
                if (!empty($decrypted)) {
                    $action = 'status';
                } else {
                    $error = true;
                }
            }
            break;
    }
}
if ($action === 'encrypt' && empty($secret)) {
    $secret = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 32);
}
