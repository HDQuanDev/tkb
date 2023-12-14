<?php
require 'vendor/autoload.php';

use Telegram\Bot\Api;

$telegram = new Api('5945931731:AAF3FzfZaQB2-SqdHGVeLnu-saQVDkxs9uA');

$response = $telegram->setWebhook(['url' => 'https://tkb.qdevs.tech/telegram.php']);
var_dump($response);
