<?php
error_reporting(0);
date_default_timezone_set('Asia/Ho_Chi_Minh');
require 'vendor/autoload.php';
require_once 'telegram/function.php';
$botToken = '5945931731:AAF3FzfZaQB2-SqdHGVeLnu-saQVDkxs9uA';

$telegram = new Telegram\Bot\Api($botToken);

$get_user = mysqli_query($db, "SELECT * FROM `users`");
while ($row_user = mysqli_fetch_assoc($get_user)) {
    $chat_id = $row_user['chatid'];
    $username = $row_user['username'];
    $get_tkb = mysqli_query($db, "SELECT * FROM `tkb` WHERE `username` = '$username' AND `chatid` = '$chat_id'");
    while ($row = mysqli_fetch_assoc($get_tkb)) {
        $id = $row['id'];
        $currentDate = date('Y-m-d');
        $date_get = date('Y-m-d', $row["date"]);

        if ($date_get < $currentDate) {
            continue;
        }
        $rand = rand(1, 5);
        $buoi = $row['buoi'];
        $classFormat = $row['class'];
        if (preg_match_all('/\(([\d,]+)\)\s*([A-Za-z0-9.]+\s[A-Za-z0-9.]+)/', $classFormat, $matches)) {
            foreach ($matches[1] as $key => $match) {
                $numbers = array_map('trim', explode(',', $match));
                if (in_array($buoi, $numbers)) {
                    $lop = $matches[2][$key];
                }
            }
        } else if (preg_match('/^C\d+\.\d+ C\d+$/', $classFormat)) {
            $lop = $classFormat;
        } else {
            $lop = $classFormat;
        }
        $time = time();
        $time_start = getStartTimestramp($row['period']) . " " . date('d/m/Y', $row["date"]);
        $time_start = convertToTimestamp($time_start);
        $time_start_ex = $time_start + 80;
        $send_success = false;
        $check_notification = mysqli_query($db, "SELECT * FROM `notification` WHERE `chatid` = '$chat_id' AND `username` = '$username' AND `id_mon` = '$id'");
        if (mysqli_num_rows($check_notification) == 0 && $time < $time_start_ex) {
            $insert_notification = mysqli_query($db, "INSERT INTO `notification` (`chatid`, `username`, `id_mon`) VALUES ('$chat_id', '$username', '$id')");
        }
        $data_notification = mysqli_fetch_assoc($check_notification);
        if ($time < $time_start_ex) {
            $seconds_remaining = $time_start - $time;
            $minutes_remaining = floor($seconds_remaining / 60);
            if ($data_notification['30phut'] == 'false' && $time >= $time_start - 1800 && $time <= $time_start - 1200) {
                $reply = "🔔 Thông báo còn $minutes_remaining phút nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $row["date"]) . "\n⏰ Tiết: " . $row['period'] . "\n📚 Môn: " . $row['subject'] . "\n👨‍🏫 Giáo viên: " . $row['teacher'] . "\n🏫 Phòng: " . $lop;
                $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $reply
                ]);
                AddLogChat($chat_id, $reply);
                $update_notification = mysqli_query($db, "UPDATE `notification` SET `30phut` = 'true' WHERE `chatid` = '$chat_id' AND `username` = '$username' AND `id_mon` = '$id'");
                $send_success = true;
            } else if ($data_notification['20phut'] == 'false' && $time >= $time_start - 1200 && $time <= $time_start - 600) {
                $reply = "🔔 Thông báo còn $minutes_remaining phút nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $row["date"]) . "\n⏰ Tiết: " . $row['period'] . "\n📚 Môn: " . $row['subject'] . "\n👨‍🏫 Giáo viên: " . $row['teacher'] . "\n🏫 Phòng: " . $lop;
                $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $reply
                ]);
                AddLogChat($chat_id, $reply);
                $update_notification = mysqli_query($db, "UPDATE `notification` SET `20phut` = 'true' WHERE `chatid` = '$chat_id' AND `username` = '$username' AND `id_mon` = '$id'");
                $send_success = true;
            } else if ($data_notification['10phut'] == 'false' && $time >= $time_start - 600 && $time <= $time_start - 300) {
                $reply = "🔔 Thông báo còn $minutes_remaining phút nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $row["date"]) . "\n⏰ Tiết: " . $row['period'] . "\n📚 Môn: " . $row['subject'] . "\n👨‍🏫 Giáo viên: " . $row['teacher'] . "\n🏫 Phòng: " . $lop;
                $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $reply
                ]);
                AddLogChat($chat_id, $reply);
                $update_notification = mysqli_query($db, "UPDATE `notification` SET `10phut` = 'true' WHERE `chatid` = '$chat_id' AND `username` = '$username' AND `id_mon` = '$id'");
                $send_success = true;
            } else if ($data_notification['start'] == 'false' && $time >= $time_start - 300 && $time <= $time_start) {
                $reply = "🔔 Thông báo đã bắt đầu vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $row["date"]) . "\n⏰ Tiết: " . $row['period'] . "\n📚 Môn: " . $row['subject'] . "\n👨‍🏫 Giáo viên: " . $row['teacher'] . "\n🏫 Phòng: " . $lop;
                $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $reply
                ]);
                AddLogChat($chat_id, $reply);
                $update_notification = mysqli_query($db, "UPDATE `notification` SET `start` = 'true' WHERE `chatid` = '$chat_id' AND `username` = '$username' AND `id_mon` = '$id'");
                $send_success = true;
            }
        }
        if ($send_success == true) {
            break;
        }
    }
}
