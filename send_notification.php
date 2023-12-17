<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require 'vendor/autoload.php';
require_once 'telegram/function.php';
$botToken = '5945931731:AAF3FzfZaQB2-SqdHGVeLnu-saQVDkxs9uA';
$telegram = new Telegram\Bot\Api($botToken);
switch ($_GET['act']) {
    case 'tomorrow':
        $get_user = mysqli_query($db, "SELECT * FROM `users`");
        while ($row_user = mysqli_fetch_assoc($get_user)) {
            $chat_id = $row_user['chatid'];
            $username = $row_user['username'];
            $get_tomorrow = getSubjectTomorrow($chat_id);
            if ($get_tomorrow == '[]') {
                $reply = "🔔 Thông Báo!\n\n📌 Ngày mai bạn rảnh, hãy tận hưởng 1 ngày nghỉ thật tuyệt vời đi nào!!";
                $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $reply
                ]);
                AddLogChat($chat_id, '', $reply);
            } else {
                $get_tomorrow = json_decode($get_tomorrow, true);
                $reply = "🔔 Thông Báo!\n\n📌 Ngày mai bạn có lịch học như sau:\n\n";
                foreach ($get_tomorrow as $key => $value) {
                    $reply .= "📅 Ngày: " . date('d/m/Y', $value["date"]) . "\n⏰ Tiết: " . $value['period'] . "\n📚 Môn: " . $value['subject'] . "\n👨‍🏫 Giáo viên: " . $value['teacher'] . "\n🏫 Phòng: " . $value['class'] . "\n\n";
                }
                $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $reply
                ]);
                AddLogChat($chat_id, '', $reply);
            }
        }
        break;
    default:
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
                        AddLogChat($chat_id, '', $reply);
                        $update_notification = mysqli_query($db, "UPDATE `notification` SET `30phut` = 'true' WHERE `chatid` = '$chat_id' AND `username` = '$username' AND `id_mon` = '$id'");
                        $send_success = true;
                    } else if ($data_notification['20phut'] == 'false' && $time >= $time_start - 1200 && $time <= $time_start - 600) {
                        $reply = "🔔 Thông báo còn $minutes_remaining phút nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $row["date"]) . "\n⏰ Tiết: " . $row['period'] . "\n📚 Môn: " . $row['subject'] . "\n👨‍🏫 Giáo viên: " . $row['teacher'] . "\n🏫 Phòng: " . $lop;
                        $telegram->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => $reply
                        ]);
                        AddLogChat($chat_id, '', $reply);
                        $update_notification = mysqli_query($db, "UPDATE `notification` SET `20phut` = 'true' WHERE `chatid` = '$chat_id' AND `username` = '$username' AND `id_mon` = '$id'");
                        $send_success = true;
                    } else if ($data_notification['10phut'] == 'false' && $time >= $time_start - 600 && $time <= $time_start - 300) {
                        $reply = "🔔 Thông báo còn $minutes_remaining phút nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $row["date"]) . "\n⏰ Tiết: " . $row['period'] . "\n📚 Môn: " . $row['subject'] . "\n👨‍🏫 Giáo viên: " . $row['teacher'] . "\n🏫 Phòng: " . $lop;
                        $telegram->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => $reply
                        ]);
                        AddLogChat($chat_id, '', $reply);
                        $update_notification = mysqli_query($db, "UPDATE `notification` SET `10phut` = 'true' WHERE `chatid` = '$chat_id' AND `username` = '$username' AND `id_mon` = '$id'");
                        $send_success = true;
                    } else if ($data_notification['start'] == 'false' && $time >= $time_start - 100 && $time <= $time_start) {
                        $reply = "🔔 Thông báo đã bắt đầu vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $row["date"]) . "\n⏰ Tiết: " . $row['period'] . "\n📚 Môn: " . $row['subject'] . "\n👨‍🏫 Giáo viên: " . $row['teacher'] . "\n🏫 Phòng: " . $lop;
                        $telegram->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => $reply
                        ]);
                        AddLogChat($chat_id, '', $reply);
                        $update_notification = mysqli_query($db, "UPDATE `notification` SET `start` = 'true' WHERE `chatid` = '$chat_id' AND `username` = '$username' AND `id_mon` = '$id'");
                        $send_success = true;
                    }
                }
                if ($send_success == true) {
                    break;
                }
            }
            $time = time();
            $time_update = convertToTimestamp($get_user['time']);
            $time_update = $time_update + 87000;
            if ($time > $time_update && $get_user['tkb_old'] == 'false') {
                $reply = "🔔 Thông báo cập nhật thời khóa biểu: \n\n🎨 Dữ liệu thời khóa biểu của bạn đã cũ hơn 1 ngày, để cập nhật lại thời khóa biểu mới, vui lòng sử dụng lệnh /load";
                $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $reply
                ]);
                AddLogChat($chat_id, '', $reply);
                $update_time = mysqli_query($db, "UPDATE `users` SET `tkb_old` = 'true' WHERE `chatid` = '$chat_id' AND `username` = '$username'");
            }
        }
        break;
}
