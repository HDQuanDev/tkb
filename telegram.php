<?php

require 'vendor/autoload.php';

$botToken = '5945931731:AAF3FzfZaQB2-SqdHGVeLnu-saQVDkxs9uA';

$telegram = new Telegram\Bot\Api($botToken);

$update = $telegram->getWebhookUpdates();

$chatId = $update['message']['chat']['id'];

$text = $update['message']['text'];

$tach = explode(' ', $text);
$command = $tach[0];
switch ($command) {
    case '/start':
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Chào mừng bạn đến với bot thông báo thời khóa biểu của trường Đại Học CNTT & TT - Thái Nguyên. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu]'
        ]);
        break;
    case '/addaccount':
        if (CheckFileExist($chatId)) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Bạn đã thêm tài khoản rồi. Để xem data thời khóa biểu vui lòng gõ /data. Để xóa tài khoản vui lòng gõ /delete'
            ]);
            break;
        }
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
        curl_setopt($ch, CURLOPT_URL, "https://tkb.qdevs.tech/api/telegram.php");
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
        } else if ($response['status'] == 'success') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Đã thêm tài khoản ' . $username . ' vào hệ thống. Hệ thống sẽ tự động báo thời khóa biểu cho bạn mỗi khi gần đến giờ học'
            ]);
            break;
        }
        break;
    case '/data':
        if (!CheckFileExist($chatId)) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Bạn chưa thêm tài khoản. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] để thêm tài khoản'
            ]);
            break;
        }
        $data = file_get_contents("data/$chatId.json");
        $data = json_decode($data, true);
        $username = $data['username'];
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Đây là các dữ liệu của bạn được lưu trên hệ thống:
            - <a href="https://tkb.qdevs.tech/data/' . $chatId . '.json">Dữ liệu chat</a>
            - <a href="https://tkb.qdevs.tech/data/data-' . $username . '.json">Dữ liệu thời khóa biểu</a>
            - <a href="https://tkb.qdevs.tech/data/' . $username . '-log.json">Dữ liệu xơ</a>',
            'parse_mode' => 'HTML'
        ]);
        break;
    case '/load':
        if (!CheckFileExist($chatId)) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Bạn chưa thêm tài khoản. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] để thêm tài khoản'
            ]);
            break;
        }
        $data = file_get_contents("data/$chatId.json");
        $data = json_decode($data, true);
        $username = $data['username'];
        $password = $data['password'];
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Đang tải lại dữ liệu...',
        ]);
        $postData = [
            'username' => $username,
            'password' => $password,
            'chat_id' => $chatId
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://tkb.qdevs.tech/api/telegram.php");
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
        } else if ($response['status'] == 'success') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Đã tải lại dữ liệu thành công'
            ]);
            break;
        }
        break;
    case '/delete':
        if (!CheckFileExist($chatId)) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Bạn chưa thêm tài khoản. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] để thêm tài khoản'
            ]);
            break;
        }
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Đang xóa tài khoản của bạn...',
        ]);
        unlink("data/$chatId.json");
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Đã xóa tài khoản của bạn thành công'
        ]);
        break;
}
function CheckFileExist($username)
{
    $username = strtolower($username);
    if (file_exists("data/$username.json")) {
        return true;
    } else {
        return false;
    }
}
