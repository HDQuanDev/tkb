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
    17 => ['start' => '23:30', 'end' => '00:20'],
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
    $date = DateTime::createFromFormat('H:i d/m/Y', $dateString);
    return $date->getTimestamp();
}
function getSubjecttoDay($chatid)
{
    global $db;
    $currentDate = date('Y-m-d');
    $sql = "SELECT *, FROM_UNIXTIME(`date`, '%Y-%m-%d') as `date_formatted` FROM `tkb` WHERE `chatid` = '$chatid'";
    $result = mysqli_query($db, $sql);
    $subjects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['date_formatted'] == $currentDate) {
            unset($row['date_formatted']);
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
    $week = $currentDate->format("W");
    $year = $currentDate->format("o");
    $weekStart = new DateTime();
    $weekStart->setISODate($year, $week);
    $weekEnd = clone $weekStart;
    $weekEnd->modify('+6 days');
    $weekStart = $weekStart->format('Y-m-d');
    $weekEnd = $weekEnd->format('Y-m-d');
    $sql = "SELECT *, FROM_UNIXTIME(`date`, '%Y-%m-%d') as `date_formatted` FROM `tkb` WHERE `chatid` = '$chatid'";
    $result = mysqli_query($db, $sql);
    $subjects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $date_get = $row['date_formatted'];
        if ($date_get >= $weekStart && $date_get <= $weekEnd) {
            unset($row['date_formatted']); // remove the formatted date from the result
            $subjects[] = $row;
        }
    }
    if (count($subjects) == 0) {
        return "[]";
    }
    return json_encode($subjects);
}
