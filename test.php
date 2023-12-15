<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once 'telegram/function.php';
$dates = file_get_contents("https://tkb.qdevs.tech/data/data-dtc21h4802010481.json");
$dates = json_decode($dates, true);
$getSubject = getSubjecttoDay($dates);
$getSubject = json_decode($getSubject, true);
$count = count($getSubject);
echo $count;
for ($i = 0; $i < $count; $i++) {
    $subject = $getSubject[$i]['subject'];
    $period = $getSubject[$i]['period'];
    $class = $getSubject[$i]['class'];
    $teacher = $getSubject[$i]['teacher'];
    $buoi = $getSubject[$i]['buoi'];
    $date = $getSubject[$i]['date'];
    $date = date('d/m/Y', $date);
    $text = "Môn học: $subject \nTiết: $period \nPhòng: $class \nGiáo viên: $teacher \nBuổi: $buoi \nNgày: $date";
    echo $text;
    echo "<br>";
}
