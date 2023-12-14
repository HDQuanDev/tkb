<?php

// Nhúng thư viện Telegram Bot SDK
require 'vendor/autoload.php';

// Khai báo Token
$botToken = '5945931731:AAF3FzfZaQB2-SqdHGVeLnu-saQVDkxs9uA'; 

// Khởi tạo đối tượng Telegram bot
$telegram = new Telegram\Bot\Api($botToken);

// Nhận các bản cập nhật từ Telegram
$update = $telegram->getWebhookUpdates();

// Lấy ID trò chuyện
$chatId = $update['message']['chat']['id'];

// Kiểm tra xem tin nhắn là gì  
$text = $update['message']['text'];

// Xử lý dựa trên tin nhắn nhận được
switch ($text) {
    case '/start':
        $telegram->sendMessage([
            'chat_id' => $chatId, 
            'text' => 'Xin chào! Tôi là Bot demo'
        ]);
        break;
    case 'Xin chào':
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Chào bạn!'
        ]);
        break;  
}