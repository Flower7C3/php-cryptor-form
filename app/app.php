<?php
error_reporting(0);
require_once 'Cryptor.php';

if (strtolower($_SERVER['SERVER_SOFTWARE']) === 'apache') {
    $config = [
        'share_url' => '/encrypted/%s',
        'form_url' => '/%s.html',
    ];
} else {
    $config = [
        'share_url' => '/index.php?action=decrypt&encrypted=%s',
        'form_url' => '/index.php?action=%s',
    ];
}

$invalid = [];

$action = @$_GET['action'] ?: 'encrypt';
$page = $action;
$secret = "";
$decrypted = "";
$encrypted = "";

if (empty($secret)) {
    if (isset($_POST['secret'])) {
        $secret = $_POST['secret'];
        if (empty($secret)) {
            $invalid['secret'][] = 'Field can not be empty!';
        }
    } elseif ($action === 'encrypt') {
        $secret = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 32);
    }
}

if (empty($encrypted) && isset($_GET['encrypted'])) {
    $encrypted = $_GET['encrypted'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'encrypt';
            if (empty($decrypted) && isset($_POST['decrypted'])) {
                $decrypted = $_POST['decrypted'];
            }
            if (empty($decrypted)) {
                $invalid['decrypted'][] = 'Field can not be empty!';
            } else {
                $decryptedClean = strip_tags($decrypted);
                if ($decrypted !== $decryptedClean) {
                    $invalid['decrypted'][] = 'Invalid characters found!';
                }
            }
            if (empty($invalid)) {
                $cryptor = new Cryptor($secret);
                $encrypted = $cryptor->encrypt($decrypted);
                if (!empty($encrypted)) {
                    $page = 'status';
                } else {
                    $invalid['form'][] = 'Invalid enrypt response!';
                }
            }
            break;
        case 'decrypt';
            if (empty($encrypted) && isset($_POST['encrypted'])) {
                $encrypted = $_POST['encrypted'];
            }
            if (empty($encrypted)) {
                $invalid['encrypted'][] = 'Field can not be empty!';
            }
            if (empty($invalid)) {
                $cryptor = new Cryptor($secret);
                $decrypted = $cryptor->decrypt($encrypted);
                if (!empty($decrypted)) {
                    $page = 'status';
                } else {
                    $invalid['form'][] = 'Invalid decrypt response!';
                }
            }
            break;
    }
}
