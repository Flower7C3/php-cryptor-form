<?php
error_reporting(0);
require_once 'Cryptor.php';

if (strtolower($_SERVER['SERVER_SOFTWARE']) === 'apache') {
    $config = [
        'share_url' => '/crypt-%s.html',
        'form_url' => '/%s.html',
    ];
} else {
    $config = [
        'share_url' => '/index.php?action=decrypt&encrypted=%s',
        'form_url' => '/index.php?action=%s',
    ];
}

$secret = "";
if (empty($secret) && isset($_POST['secret'])) {
    $secret = $_POST['secret'];
}

$decrypted = "";
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
$invalid = [];
$valid = [];

switch ($action) {
    case 'encrypt';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($secret) && !empty($decrypted)) {
                $cryptor = new Cryptor($secret);
                $encrypted = $cryptor->encrypt($decrypted);
                if (!empty($encrypted)) {
                    $action = 'status';
                } else {
                    $invalid['form'] = 'Invalid enrypt response!';
                }
            } else {
                if (empty($secret)) {
                    $invalid['secret'] = 'Field can not be empty!';
                } else {
                    $valid['secret'] = '';
                }
                if (empty($decrypted)) {
                    $invalid['decrypted'] = 'Field can not be empty!';
                } else {
                    $valid['decrypted'] = '';
                }
            }
        } else {
            if (empty($secret)) {
                $secret = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 32);
            }
        }
        break;
    case 'decrypt';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($secret) && !empty($encrypted)) {
                $cryptor = new Cryptor($secret);
                $decrypted = $cryptor->decrypt($encrypted);
                if (!empty($decrypted)) {
                    $action = 'status';
                } else {
                    $invalid['form'] = 'Invalid decrypt response!';
                }
            } else {
                if (empty($secret)) {
                    $invalid['secret'] = 'Field can not be empty!';
                } else {
                    $valid['secret'] = '';
                }
                if (empty($encrypted)) {
                    $invalid['encrypted'] = 'Field can not be empty!';
                } else {
                    $valid['encrypted'] = '';
                }
            }
        }
        break;
}
