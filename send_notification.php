<?php
error_reporting(0);
date_default_timezone_set('Asia/Ho_Chi_Minh');
require 'vendor/autoload.php';
require_once 'telegram/function.php';
$botToken = '5945931731:AAF3FzfZaQB2-SqdHGVeLnu-saQVDkxs9uA';

$telegram = new Telegram\Bot\Api($botToken);

$files = glob("data/*.json");

$numberFiles = preg_grep('/data\/(\d+)\.json$/', $files);

foreach ($numberFiles as $file) {

    $fileName = basename($file);
    $tach = explode('.', $fileName);
    $chat_id = $tach[0];

    $content = file_get_contents($file);
    $data = json_decode($content, true);

    $username = $data['username'];

    $dates = file_get_contents("data/data-$username.json");
    $dates = json_decode($dates, true);
    $i = 0;
    foreach ($dates as $date) {
        $currentDate = date('Y-m-d');
        $date_get = date('Y-m-d', $date["date"]);
        if ($date_get < $currentDate) {
            continue;
        }
        $rand = rand(1, 5);
        $buoi = $date['buoi'];
        $classFormat = $date['class'];
        if (preg_match_all('/\(([\d,]+)\)\s*([A-Za-z0-9.]+\s[A-Za-z0-9.]+)/', $classFormat, $matches)) {
            foreach ($matches[1] as $key => $match) {
                $numbers = array_map('trim', explode(',', $match)); // Split the numbers by comma and remove any whitespace
                if (in_array($buoi, $numbers)) {
                    $lop = $matches[2][$key]; // This will print the words after the numbers
                }
            }
        } else if (preg_match('/^C\d+\.\d+ C\d+$/', $classFormat)) {

            $lop = $classFormat;
        } else {
            $lop = $classFormat;
        }
        $time = time();
        $time_start = getStartTimestramp($date['period']) . " " . date('d/m/Y', $date["date"]);
        $time_start = convertToTimestamp($time_start);
        $time_start_ex = $time_start + 100;
        $send_success = false;
        if ($time < $time_start_ex) {
            if ($date['30phut'] == false) {
                $time_noti = $time_start - 1800;
                if (($time >= $time_noti)) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "🔔 Thông báo còn 30p nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $date["date"]) . "\n⏰ Tiết: " . $date['period'] . "\n📚 Môn: " . $date['subject'] . "\n👨‍🏫 Giáo viên: " . $date['teacher'] . "\n🏫 Phòng: " . $date['class']
                    ]);
                    $dates[$i]['30phut'] = true;
                    $send_success = true;
                }
            } else if ($date['20phut'] == false) {
                $time_noti = $time_start - 1200;
                if (($time >= $time_noti)) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "🔔 Thông báo còn 20p nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $date["date"]) . "\n⏰ Tiết: " . getStartAndEndTime($date['period']) . "\n📚 Môn: " . $date['subject'] . "\n👨‍🏫 Giáo viên: " . $date['teacher'] . "\n🏫 Phòng: " . $date['class']
                    ]);
                    $dates[$i]['20phut'] = true;
                    $send_success = true;
                }
            } else if ($date['10phut'] == false) {
                $time_noti = $time_start - 600;
                if (($time >= $time_noti)) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "🔔 Thông báo còn 10p nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $date["date"]) . "\n⏰ Tiết: " . getStartAndEndTime($date['period']) . "\n📚 Môn: " . $date['subject'] . "\n👨‍🏫 Giáo viên: " . $date['teacher'] . "\n🏫 Phòng: " . $date['class']
                    ]);
                    $dates[$i]['10phut'] = true;
                    $send_success = true;
                }
            } else if ($date['start'] == false) {
                $time_noti = $time_start;
                if (($time >= $time_noti)) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "🔔 Thông báo bắt đầu tiết học: \n\n📅 Ngày: " . date('d/m/Y', $date["date"]) . "\n⏰ Tiết: " . getStartAndEndTime($date['period']) . "\n📚 Môn: " . $date['subject'] . "\n👨‍🏫 Giáo viên: " . $date['teacher'] . "\n🏫 Phòng: " . $date['class']
                    ]);
                    $dates[$i]['start'] = true;
                    $send_success = true;
                }
            }
        }
        if ($send_success == true) {
            break;
        }
        $i++;
    }
    $dataa = json_encode($dates);
    file_put_contents("data/data-$username.json", $dataa);
    $time = time();
    $data = file_get_contents("data/$chat_id.json");
    $data = json_decode($data, true);
    $time_update = $data['time'];
    $time_update = $time_update + 100000;
    echo $time . " - " . $time_update;
    if ($time > $time_update && $data['tkb_old'] == false) {
        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => "🔔 Thông báo cập nhật thời khóa biểu: \n\n🎨 Dữ liệu thời khóa biểu của bạn đã cũ hơn 1 ngày, để cập nhật lại thời khóa biểu mới, vui lòng sử dụng lệnh /load",
        ]);
        $data['tkb_old'] = true;
        $data = json_encode($data);
        file_put_contents("data/$chat_id.json", $data);
    }
}
