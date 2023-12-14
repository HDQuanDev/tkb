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
    16 => ['start' => '02:10', 'end' => '03:00'],
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
foreach ($numberFiles as $file) {

    $fileName = basename($file);
    $tach = explode('.', $fileName);
    $chat_id = $tach[0];

    $content = file_get_contents($file);
    $data = json_decode($content, true);

    $username = $data['username'];

    $json = file_get_contents("data/$username.json");
    $json = str_replace("\u00a0", '', $json);
    $get = json_decode($json, true);
    $count = count($get) - 1;
    $dates = [];
    for ($i = 0; $i < $count; $i++) {
        $thoigian = $get[$i]['thoi-gian'];
        preg_match_all('/Từ (\d{2}\/\d{2}\/\d{4}) đến (\d{2}\/\d{2}\/\d{4}): \((\d+)\)\s*Thứ (\d) tiết ([\d,]+) \((\w+)\)/', $thoigian, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $start = strtotime(str_replace("/", "-", $match[1]));
            $end = strtotime(str_replace("/", "-", $match[2]));
            $ngay = $match[4] - 1;
            for ($ii = $start; $ii <= $end; $ii += 24 * 60 * 60) {
                if (date('N', $ii) == $ngay) {
                    $dates[] = [
                        'date' => $ii,
                        'subject' => $get[$i]['lop-hoc-phan'],
                        'period' => $match[5],
                        'class' => $get[$i]['dia-diem'],
                        'teacher' => $get[$i]['giang-vien'],
                        'buoi' => $match[3]
                    ];
                }
            }
        }
    }

    usort($dates, function ($a, $b) {
        if ($a['date'] == $b['date']) {
            $periodA = explode(',', $a['period']);
            $periodB = explode(',', $b['period']);
            return $periodA[0] - $periodB[0];
        }
        return $a['date'] - $b['date'];
    });

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
        $time_noti = $date['date'] - 1800;
        if (($time >= $time_noti)) {
            // $telegram->sendMessage([
            //     'chat_id' => $chat_id,
            //     'text' => "🔔 Thông báo: \n\n📅 Ngày: " . date('d/m/Y', $date["date"]) . "\n⏰ Tiết: " . getStartAndEndTime($date['period']) . "\n📚 Môn: " . $date['subject'] . "\n👨‍🏫 Giáo viên: " . $date['teacher'] . "\n🏫 Phòng: " . $date['class']
            // ]);

        }

        echo $time . " - " . $time_noti;
        echo "<br>";
    }
}
