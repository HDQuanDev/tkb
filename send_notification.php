<?php
error_reporting(0);
date_default_timezone_set('Asia/Ho_Chi_Minh');
require 'vendor/autoload.php';

$botToken = '5945931731:AAF3FzfZaQB2-SqdHGVeLnu-saQVDkxs9uA';

$telegram = new Telegram\Bot\Api($botToken);

$files = glob("data/*.json");

$numberFiles = preg_grep('/data\/(\d+)\.json$/', $files);

$periods = [
    1 => ['start' => '6:45', 'end' => '7:35'],
    2 => ['start' => '7:40', 'end' => '8:30'],
    3 => ['start' => '8:40', 'end' => '9:30'],
    4 => ['start' => '9:40', 'end' => '10:30'],
    5 => ['start' => '10:35', 'end' => '11:25'],
    6 => ['start' => '13:00', 'end' => '13:50'],
    7 => ['start' => '13:55', 'end' => '14:45'],
    8 => ['start' => '14:50', 'end' => '15:40'],
    9 => ['start' => '15:55', 'end' => '16:45'],
    10 => ['start' => '16:50', 'end' => '17:40'],
    11 => ['start' => '18:15', 'end' => '19:05'],
    12 => ['start' => '19:10', 'end' => '20:00'],
    13 => ['start' => '20:10', 'end' => '21:00'],
    14 => ['start' => '21:10', 'end' => '22:00'],
    15 => ['start' => '22:10', 'end' => '23:00'],
    16 => ['start' => '02:10', 'end' => '03:50'],
];

function CheckFileExist($username)
{
    $username = strtolower($username);
    if (file_exists("data/$username.json")) {
        return true;
    } else {
        return false;
    }
}
function getStartAndEndTime($periodString)
{
    global $periods;
    $periodsArray = explode(',', $periodString);
    $startPeriod = min($periodsArray);
    $endPeriod = max($periodsArray);
    return $periods[$startPeriod]['start'] . ' - ' . $periods[$endPeriod]['end'];
}
function getStartTimestramp($periodString)
{
    global $periods;
    $periodsArray = explode(',', $periodString);
    $startPeriod = min($periodsArray);
    return $periods[$startPeriod]['start'];
}
function isCurrentPeriodAndDay($date, $period)
{
    global $periods;
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i');
    $start = DateTime::createFromFormat('H:i', $periods[$period]['start']);
    $end = DateTime::createFromFormat('H:i', $periods[$period]['end']);
    $now = DateTime::createFromFormat('H:i', $currentTime);
    if ($date == $currentDate && $now >= $start && $now <= $end) {
        return true;
    }
    return false;
}
function convertToTimestamp($dateString)
{
    $date = DateTime::createFromFormat('H:i d/m/Y', $dateString);
    return $date->getTimestamp();
}
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
        $data = json_encode($dates);
        file_put_contents('dates.json', $data);
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
        if ($time < $time_start_ex) {
            if ($date['30phut'] == false) {
                $time_noti = $time_start - 1800;
                if (($time >= $time_noti)) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "🔔 Thông báo còn 30p nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $date["date"]) . "\n⏰ Tiết: " . getStartAndEndTime($date['period']) . "\n📚 Môn: " . $date['subject'] . "\n👨‍🏫 Giáo viên: " . $date['teacher'] . "\n🏫 Phòng: " . $date['class']
                    ]);
                    $dates[$i]['30phut'] = true;
                }
            } else if ($date['20phut'] == false) {
                $time_noti = $time_start - 1200;
                if (($time >= $time_noti)) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "🔔 Thông báo còn 20p nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $date["date"]) . "\n⏰ Tiết: " . getStartAndEndTime($date['period']) . "\n📚 Môn: " . $date['subject'] . "\n👨‍🏫 Giáo viên: " . $date['teacher'] . "\n🏫 Phòng: " . $date['class']
                    ]);
                    $dates[$i]['20phut'] = true;
                }
            } else if ($date['10phut'] == false) {
                $time_noti = $time_start - 600;
                if (($time >= $time_noti)) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "🔔 Thông báo còn 10p nữa vào tiết học: \n\n📅 Ngày: " . date('d/m/Y', $date["date"]) . "\n⏰ Tiết: " . getStartAndEndTime($date['period']) . "\n📚 Môn: " . $date['subject'] . "\n👨‍🏫 Giáo viên: " . $date['teacher'] . "\n🏫 Phòng: " . $date['class']
                    ]);
                    $dates[$i]['10phut'] = true;
                }
            } else if ($date['start'] == false) {
                $time_noti = $time_start;
                if (($time >= $time_noti)) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "🔔 Thông báo bắt đầu tiết học: \n\n📅 Ngày: " . date('d/m/Y', $date["date"]) . "\n⏰ Tiết: " . getStartAndEndTime($date['period']) . "\n📚 Môn: " . $date['subject'] . "\n👨‍🏫 Giáo viên: " . $date['teacher'] . "\n🏫 Phòng: " . $date['class']
                    ]);
                    $dates[$i]['start'] = true;
                }
            }
        }
        $i++;
    }
    $dataa = json_encode($dates);
    file_put_contents("data/data-$username.json", $dataa);
    $time = time();
    $time_update = $data['time'];
    $time_update = $time_update + 86400;
    if ($time > $time_update && $data['tkb_old'] == false) {
        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => "🔔 Thông báo cập nhật thời khóa biểu: \n\n*bold Dữ liệu thời khóa biểu của bạn đã cũ hơn 1 ngày, để cập nhật lại thời khóa biểu mới, vui lòng sử dụng lệnh /load*",
        ]);
        $data['tkb_old'] = true;
        $data = json_encode($data);
        file_put_contents("data/$chat_id.json", $data);
    }
}
