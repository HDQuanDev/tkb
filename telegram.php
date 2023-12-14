<?php

require 'vendor/autoload.php';

$botToken = '5945931731:AAF3FzfZaQB2-SqdHGVeLnu-saQVDkxs9uA';

$telegram = new Telegram\Bot\Api($botToken);

$update = $telegram->getWebhookUpdates();

$chatId = $update['message']['chat']['id'];

$text = $update['message']['text'];

switch ($text) {
    case '/start':
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Chào mừng bạn đến với bot thông báo thời khóa biểu của trường Đại Học CNTT & TT - Thái Nguyên. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu]'
        ]);
        break;
    case '/addaccount':
        $message = explode(' ', $text);
        $username = $message[1];
        $password = $message[2];
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Đã thêm tài khoản ' . $username . ' vào hệ thống. Để xem thời khóa biểu vui lòng gõ /tkb'
        ]);
        break;
}
