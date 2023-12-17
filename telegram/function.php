<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');
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
    16 => ['start' => '23:30', 'end' => '00:20'],
];
$db = mysqli_connect('localhost', 'qdevs_tkb', '2S.XY0?()JmL', 'qdevs_tkb');
mysqli_set_charset($db, 'utf8');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
function CheckIdChat($chat_id)
{
    global $db;
    $sql = "SELECT * FROM `users` WHERE `chatid` = '$chat_id'";
    $result = mysqli_query($db, $sql);
    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}
function AddLogChat($chat_id, $message, $reply = null)
{
    global $db;
    $message = mysqli_real_escape_string($db, $message);
    $reply = mysqli_real_escape_string($db, $reply);
    $chat_id = mysqli_real_escape_string($db, $chat_id);
    $sql = "INSERT INTO `log` (`chatid`, `message`, `reply`) VALUES ('$chat_id', '$message', '$reply')";
    $result = mysqli_query($db, $sql);
    if ($result) {
        return true;
    } else {
        return false;
    }
}
function GetDataUser($chat_id)
{
    global $db;
    $sql = "SELECT * FROM `users` WHERE `chatid` = '$chat_id'";
    $result = mysqli_query($db, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return json_encode($row);
    } else {
        return false;
    }
}
function DeleteAllDataUser($chat_id)
{
    global $db;
    $tables = ['users', 'log', 'tkb'];
    foreach ($tables as $table) {
        $sql = "DELETE FROM `$table` WHERE `chatid` = '$chat_id'";
        $result = mysqli_query($db, $sql);
        if (!$result) {
            return false;
        }
    }
    return true;
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
    if (is_numeric($dateString)) {
        // If the input is a Unix timestamp, return it as is
        return (int)$dateString;
    } else {
        // Otherwise, try to parse it as a date string
        $date = DateTime::createFromFormat('H:i d/m/Y', $dateString);
        if ($date === false) {
            // If the date string could not be parsed, return null
            return null;
        } else {
            return $date->getTimestamp();
        }
    }
}
function getSubjecttoDay($chatid)
{
    global $db;
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $currentDate = date('Y-m-d');
    $sql = "SELECT * FROM `tkb` WHERE `chatid` = '$chatid'";
    $result = mysqli_query($db, $sql);
    $subjects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $date_formatted = date('Y-m-d', $row['date']);
        if ($date_formatted == $currentDate) {
            $subjects[] = $row;
        }
    }
    if (count($subjects) == 0) {
        return "[]";
    }
    return json_encode($subjects);
}
function getSubjecttoWeek($chatid)
{
    global $db;
    $currentDate = new DateTime();
    $currentDate->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh')); // Đặt múi giờ về múi giờ của Việt Nam
    $week = $currentDate->format("W");
    $year = $currentDate->format("o");

    // Tính toán thời điểm bắt đầu và kết thúc của tuần
    $weekStart = new DateTime();
    $weekStart->setISODate($year, $week);
    $weekStart->setTime(0, 0, 0); // Đặt thời điểm bắt đầu của ngày về 00:00:00
    $weekEnd = clone $weekStart;
    $weekEnd->modify('+6 days');
    $weekEnd->setTime(23, 59, 59); // Đặt thời điểm kết thúc của ngày về 23:59:59

    $weekStart = $weekStart->getTimestamp();
    $weekEnd = $weekEnd->getTimestamp();

    $sql = "SELECT * FROM `tkb` WHERE `chatid` = '$chatid'";
    $result = mysqli_query($db, $sql);

    $subjects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $date_get = $row['date'];
        if ($date_get >= $weekStart && $date_get <= $weekEnd) {
            $subjects[] = $row;
        }
    }

    if (count($subjects) == 0) {
        return "[]";
    }

    return json_encode($subjects);
}
function getSubjectTomorrow($chatid)
{
    global $db;
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $tomorrowDate = date('Y-m-d', strtotime("+1 day"));
    $sql = "SELECT * FROM `tkb` WHERE `chatid` = '$chatid'";
    $result = mysqli_query($db, $sql);
    $subjects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $date_formatted = date('Y-m-d', $row['date']);
        if ($date_formatted == $tomorrowDate) {
            $subjects[] = $row;
        }
    }
    if (count($subjects) == 0) {
        return "[]";
    }
    return json_encode($subjects);
}
