<?php

require 'vendor/autoload.php';
require_once 'telegram/function.php';
$botToken = '5945931731:AAF3FzfZaQB2-SqdHGVeLnu-saQVDkxs9uA';

$telegram = new Telegram\Bot\Api($botToken);

$update = $telegram->getWebhookUpdates();

$chatId = $update['message']['chat']['id'];

$text = $update['message']['text'];

$tach = explode(' ', $text);
$command = $tach[0];
switch ($command) {
    case '/start':
        if (CheckIdChat($chatId)) {
            $reply = "Bạn đã thêm tài khoản rồi. Để xem data thời khóa biểu vui lòng gõ /data. Để xóa tài khoản vui lòng gõ /delete";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $reply = "Chào mừng bạn đến với bot thông báo thời khóa biểu của trường Đại Học CNTT & TT - Thái Nguyên. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] ví dụ: /addaccount ictu123456 123456";
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply
        ]);
        AddLogChat($chatId, $text, $reply);
        break;
    case '/addaccount':
        if (CheckIdChat($chatId)) {
            $reply = "Bạn đã thêm tài khoản rồi. Để xem data thời khóa biểu vui lòng gõ /data. Để xóa tài khoản vui lòng gõ /delete";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $message = explode(' ', $text);
        $username = $message[1];
        $password = $message[2];
        if (empty($username) || empty($password)) {
            $reply = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $reply = "Đang thêm tài khoản $username vào hệ thống...";
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply
        ]);
        AddLogChat($chatId, $text, $reply);
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
            $reply = "Đã xảy ra lỗi: " . $response['message'];
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        } else if ($response['status'] == 'success') {
            $reply = "Đã thêm tài khoản $username vào hệ thống. Hệ thống sẽ tự động báo thời khóa biểu cho bạn mỗi khi gần đến giờ học";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        break;
    case '/data':
        if (!CheckIdChat($chatId)) {
            $reply = "Bạn chưa thêm tài khoản. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] để thêm tài khoản";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $data = file_get_contents("data/$chatId.json");
        $data = json_decode($data, true);
        $username = $data['username'];
        $reply = "Đây là các dữ liệu của bạn được lưu trên hệ thống:
        - <a href='https://tkb.qdevs.tech/data/get.php?act=log&id=$chatId'>Dữ liệu chat</a>
        - <a href='https://tkb.qdevs.tech/data/get.php?act=data&username=$username&id=$chatId'>Dữ liệu thời khóa biểu</a>";
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply,
            'parse_mode' => 'HTML'
        ]);
        AddLogChat($chatId, $text, $reply);
        break;
    case '/load':
        if (!CheckIdChat($chatId)) {
            $reply = "Bạn chưa thêm tài khoản. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] để thêm tài khoản";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $data = GetDataUser($chatId);
        $data = json_decode($data, true);
        $username = $data['username'];
        $password = $data['password'];
        $reply = "Đang tải lại dữ liệu...";
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply
        ]);
        AddLogChat($chatId, $text, $reply);
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
            $reply = "Đã xảy ra lỗi: " . $response['message'];
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        } else if ($response['status'] == 'success') {
            $reply = "Đã tải lại dữ liệu thành công";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        break;
    case '/delete':
        if (!CheckIdChat($chatId)) {
            $reply = "Bạn chưa thêm tài khoản. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] để thêm tài khoản";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }

        $reply = "Bạn có chắc chắn muốn xóa tài khoản của bạn? Thao tác này sẽ xóa toàn bộ dữ liệu của bạn trên hệ thống!";
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => '✅ Xác nhận', 'callback_data' => '/confirm_delete'],
                        ['text' => '❌ Hủy', 'callback_data' => '/cancel_delete'],
                    ],
                ],
            ]),
        ]);
        break;

    case '/confirm_delete':
        $reply = "Đang xóa tài khoản của bạn...";
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply
        ]);
        AddLogChat($chatId, $text, $reply);
        if (DeleteAllDataUser($chatId)) {
            $reply = "Đã xóa tài khoản của bạn thành công";
        } else {
            $reply = "Đã xảy ra lỗi khi xóa tài khoản của bạn";
        }
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply
        ]);
        AddLogChat($chatId, $text, $reply);
        break;

    case '/cancel_delete':
        $reply = "Hủy xóa tài khoản";
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply
        ]);
        AddLogChat($chatId, $text, $reply);
        break;
    case '/getsubjecttoday':
        if (!CheckIdChat($chatId)) {
            $reply = "Bạn chưa thêm tài khoản. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] để thêm tài khoản";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $getSubject = getSubjecttoDay($chatId);
        if ($getSubject == "[]") {
            $reply = "Hôm nay bạn không có tiết học nào cả";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $json = json_decode($getSubject, true);
        $count = count($json);
        $reply = "🔔 Danh sách môn học trong ngày hôm nay: \n\n";
        $q = 1;
        for ($i = 0; $i < $count; $i++) {
            $subject = $json[$i]['subject'];
            $period = $json[$i]['period'];
            $class = $json[$i]['class'];
            $teacher = $json[$i]['teacher'];
            $buoi = $json[$i]['buoi'];
            $date = $json[$i]['date'];
            $date = date('d/m/Y', $date);
            $getstartandend = getStartAndEndTime($period);
            $reply .= "#$q: $getstartandend\n📚 Môn học: $subject \n⏰ Tiết: $period \n🏫 Phòng: $class \n👨‍🏫 Giáo viên: $teacher \n📅 Ngày: $date\n\n";
            $q++;
        }
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply
        ]);
        AddLogChat($chatId, $text, $reply);
        break;
    case '/getsubjecttoweak':
        if (!CheckIdChat($chatId)) {
            $reply = "Bạn chưa thêm tài khoản. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] để thêm tài khoản";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $getSubject = getSubjecttoWeek($chatId);
        if ($getSubject == "[]") {
            $reply = "Tuần này bạn không có tiết học nào cả";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $json = json_decode($getSubject, true);
        $count = count($json);
        $reply = "🔔 Danh sách môn học trong tuần này: \n\n";
        $q = 1;
        for ($i = 0; $i < $count; $i++) {
            $subject = $json[$i]['subject'];
            $period = $json[$i]['period'];
            $class = $json[$i]['class'];
            $teacher = $json[$i]['teacher'];
            $buoi = $json[$i]['buoi'];
            $date = $json[$i]['date'];
            $date = date('d/m/Y', $date);
            $getstartandend = getStartAndEndTime($period);
            $reply .= "#$q: $getstartandend\n📚 Môn học: $subject \n⏰ Tiết: $period \n🏫 Phòng: $class \n👨‍🏫 Giáo viên: $teacher \n📅 Ngày: $date\n\n";
            $q++;
        }
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply
        ]);
        AddLogChat($chatId, $text, $reply);
        break;
    case '/getsubjecttomorrow':
        if (!CheckIdChat($chatId)) {
            $reply = "Bạn chưa thêm tài khoản. Để thêm tài khoản vui lòng gõ /addaccount [tên đăng nhập ictu] [mật khẩu ictu] để thêm tài khoản";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $getSubject = getSubjectTomorrow($chatId);
        if ($getSubject == "[]") {
            $reply = "Ngày mai bạn không có tiết học nào cả";
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $reply
            ]);
            AddLogChat($chatId, $text, $reply);
            break;
        }
        $json = json_decode($getSubject, true);
        $count = count($json);
        $reply = "🔔 Danh sách môn học trong ngày mai: \n\n";
        $q = 1;
        for ($i = 0; $i < $count; $i++) {
            $subject = $json[$i]['subject'];
            $period = $json[$i]['period'];
            $class = $json[$i]['class'];
            $teacher = $json[$i]['teacher'];
            $buoi = $json[$i]['buoi'];
            $date = $json[$i]['date'];
            $date = date('d/m/Y', $date);
            $getstartandend = getStartAndEndTime($period);
            $reply .= "#$q: $getstartandend\n📚 Môn học: $subject \n⏰ Tiết: $period \n🏫 Phòng: $class \n👨‍🏫 Giáo viên: $teacher \n📅 Ngày: $date\n\n";
        }
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply
        ]);
        AddLogChat($chatId, $text, $reply);
        break;
    case '/help':
        $reply = '🛠 Để liên hệ góp ý và báo lỗi vui lòng truy cập FB: <a href="https://www.facebook.com/quancp72h">@quancp72h</a>';
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $reply,
            'parse_mode' => 'HTML'
        ]);
        AddLogChat($chatId, $text, $reply);
        break;
}
