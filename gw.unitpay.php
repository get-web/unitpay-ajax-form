<?php

// ================================
// name: 'unitpay-ajax-form'
// author: 'Vitalii P.'
// homepage: 'https://get-web.site/'
// ================================

// задержка ответа для демонстрации
// sleep(1);

$secretKey = '00000000000000000000000000';
$publicKey = '000000-000000';

$errors = array();
$account = '';
$currency = 'RUB';
$desc = 'Покупка внутриигровой валюты';
$count = '';
$price = '';
$sum = '';
$signature = '';

if (!isset($_POST['account']) || trim($_POST['account']) == "") {
    array_push($errors, 'Необходимо заполнить поле "Аккаунт"');
}

if (!isset($_POST['count']) || trim($_POST['count']) == "") {
    array_push($errors, 'Необходимо заполнить поле "Количество"');
}

if (!is_numeric($_POST['count'])) {
    array_push($errors, 'Поле "Количество" должно быть числом');
}

if (!isset($_POST['price']) || trim($_POST['price']) == "") {
    array_push($errors, 'Неудалось определить цену');
}

if (!isset($_POST['publicKey'])) {
    array_push($errors, 'Не удалось определить ID магазина');
}

if ($publicKey != $_POST['publicKey']) {
    array_push($errors, 'Подмена публичного ключа');
}

if (count($errors)) {
    echo generateResponse('error', $errors, false);
    exit();
}


$account = $_POST['account'];
$currency = $_POST['currency'];
$desc .= ' для ' . $account;

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
        'status'    =>  $status,    // success/error
        'msg'       =>  $msg,       // if error
        'redirect'  =>  $url,       // if success
    ));
}
