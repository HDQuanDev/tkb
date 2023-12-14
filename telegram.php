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
        if (empty($username) || empty($password)) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu'
            ]);
            break;
        }
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Đang thêm tài khoản ' . $username . ' vào hệ thống...'
        ]);
        $postData = [
            'username' => $username,
            'password' => $password,
            'chat_id' => $chatId
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://tkb.qdevs.tech/api/connect.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if ($response['status'] == 'error') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Đã xảy ra lỗi: ' . $response['message']
            ]);
            break;
        }else if($response['status'] == 'success'){
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Đã thêm tài khoản ' . $username . ' vào hệ thống. Để xem thời khóa biểu vui lòng gõ /tkb'
            ]);
            break;
        }
        break;
}
