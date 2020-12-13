<?php

// code by Vitalii P. || https://get-web.site

$secretKey = '00000000000000000000000000';
$publicKey = '000000-000000';

$account = '';
$currency = 'RUB';
$desc = 'Purchase of in-game currency';
$count = '';
$price = '';
$sum = '';
$signature = '';

if (!isset($_POST['account']) || trim($_POST['account']) == "") {
    echo generateResponse('error', 'You must fill in the account field', false);
    exit();
}

if (!isset($_POST['count']) || trim($_POST['count']) == "") {
    echo generateResponse('error', 'You must fill in the count field', false);
    exit();
}

if (!is_numeric($_POST['count'])) {
    echo generateResponse('error', 'The count field is not a number!', false);
    exit();
}

if (!isset($_POST['price']) || trim($_POST['price']) == "") {
    echo generateResponse('error', 'Unknown cost', false);
    exit();
}

if (!isset($_POST['publicKey'])) {
    echo generateResponse('error', 'Store ID not found', false);
    exit();
}

if ($publicKey != $_POST['publicKey']) {
    echo generateResponse('error', 'The substitution of the public key', false);
    exit();
}

if (isset($_POST['desc'])) {
    $desc = $_POST['desc'];
}




$account = $_POST['account'];
$currency = $_POST['currency'];

$count = $_POST['count'];
$price = $_POST['price'];
$sum = $price * $count;

$signature = getFormSignature($account, $currency, $desc, $sum, $secretKey);
$url = 'https://unitpay.money/pay/' . $publicKey . '/card?sum=' . $sum . '&account=' . $account . '&currency=' . $currency . '&desc=' . $desc . '&signature=' . $signature;

echo generateResponse('success', false, $url);
exit();


function getFormSignature($account, $currency, $desc, $sum, $secretKey)
{
    $hashStr = $account . '{up}' . $currency . '{up}' . $desc . '{up}' . $sum . '{up}' . $secretKey;
    return hash('sha256', $hashStr);
}

function generateResponse($status, $msg, $url)
{
    return json_encode(array(
        'status'    =>  $status,    // success/error/warning/info
        'msg'       =>  $msg,       // if error/warning/info
        'redirect'  =>  $url,       // if success
    ));
}
