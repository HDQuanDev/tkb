<?php
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
function getSubjecttoDay($dates)
{
    $currentDate = date('Y-m-d');
    $subjects = [];
    $i = 0;
    foreach ($dates as $date) {
        $date_get = date('Y-m-d', $date["date"]);
        if ($date_get == $currentDate) {
            $subjects[$i]["subject"] = $date['subject'];
            $subjects[$i]["period"] = $date['period'];
            $subjects[$i]["class"] = $date['class'];
            $subjects[$i]["teacher"] = $date['teacher'];
            $subjects[$i]["buoi"] = $date['buoi'];
            $subjects[$i]["date"] = $date["date"];
            $i++;
        }
    }
    if ($i == 0) {
        return "[]";
    }
    return json_encode($subjects);
}
function getSubjecttoWeek($dates)
{
    $currentDate = new DateTime();
    $weekStart = $currentDate->modify('Monday this week')->format('Y-m-d');
    $weekEnd = $currentDate->modify('Sunday this week')->format('Y-m-d');

    $subjects = [];
    $i = 0;
    foreach ($dates as $date) {
        $date_get = date('Y-m-d', $date["date"]);
        if ($date_get >= $weekStart && $date_get <= $weekEnd) {
            $subjects[$i]["subject"] = $date['subject'];
            $subjects[$i]["period"] = $date['period'];
            $subjects[$i]["class"] = $date['class'];
            $subjects[$i]["teacher"] = $date['teacher'];
            $subjects[$i]["buoi"] = $date['buoi'];
            $subjects[$i]["date"] = $date["date"];
            $i++;
        }
    }
    if ($i == 0) {
        return "[]";
    }
    return json_encode($subjects);
}
