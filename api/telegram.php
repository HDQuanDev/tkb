<?php
require_once '../telegram/function.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $chat_id = mysqli_real_escape_string($db, $_POST['chat_id']);
    if (!empty($username) && !empty($password)) {
        $get = connect($username, $password);
        $gop = json_decode($get, true);
        if ($gop['status'] == "error") {
            echo json_encode(array("status" => "error", "message" => "$gop[message]"));
        } else {
            if (!empty($chat_id)) {
                foreach ($gop as &$item) {
                    $item['chat_id'] = $chat_id;
                }
                $check = mysqli_query($db, "SELECT * FROM `users` WHERE `chatid` = '$chat_id'");
                if (mysqli_num_rows($check) == 0) {
                    $count = count($gop) - 1;
                    $get_name = $gop[$count]["name"];
                    $sql = "INSERT INTO `users` (`chatid`, `username`, `password`, `name`) VALUES ('$chat_id', '$username', '$password', '$get_name')";
                    $result = mysqli_query($db, $sql);
                    if (!$result) {
                        echo json_encode(array("status" => "error", "message" => "can't save data to database, info: " . mysqli_error($db)));
                        exit();
                    }
                } else {
                    $time = time();
                    $sql = "UPDATE `users` SET `time` = '$time', `tkb_old` = 'false'  WHERE `chatid` = '$chat_id'";
                    $result = mysqli_query($db, $sql);
                }
                $endgop = json_encode($gop);
                $json = str_replace("\u00a0", '', $endgop);
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
                                    'buoi' => $match[3],
                                    '30phut' => false,
                                    '20phut' => false,
                                    '10phut' => false,
                                    'start' => false
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
                mysqli_query($db, "DELETE FROM `tkb` WHERE `chatid` = '$chat_id' AND `username` = '$username'");
                mysqli_query($db, "DELETE FROM `notification` WHERE `chatid` = '$chat_id' AND `username` = '$username'");
                foreach ($dates as $date) {
                    $time = $date['date'];
                    $subject = $date['subject'];
                    $period = $date['period'];
                    $class = $date['class'];
                    $teacher = $date['teacher'];
                    $buoi = $date['buoi'];
                    $sql_insert = "INSERT INTO `tkb` (`chatid`, `username`, `date`, `subject`, `period`, `class`, `teacher`, `buoi`) VALUES ('$chat_id', '$username', '$time', '$subject', '$period', '$class', '$teacher', '$buoi')";
                    $result_insert = mysqli_query($db, $sql_insert);
                    $id = mysqli_insert_id($db);
                    $time_start = getStartTimestramp($period) . " " . date('d/m/Y', $time);
                    $time_start = convertToTimestamp($time_start);
                    if ($time_start < time()) {
                        $insert_notification = mysqli_query($db, "INSERT INTO `notification` (`chatid`, `username`, `id_mon`, `30phut`, `20phut`, `10phut`, `start`) VALUES ('$chat_id', '$username', '$id', 'true', 'true', 'true', 'true')");
                    }
                }
            }
            if ($result_insert) {
                echo json_encode(array("status" => "success", "message" => "Lưu dữ liệu thành công"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Không thể lưu dữ liệu vào cơ sở dữ liệu"));
            }
            exit();
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "username or password is empty"));
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("status" => "error", "message" => "method not allowed"));
    exit();
}
function connect($username, $password)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://216.9.227.235:5000/get',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "username=$username&password=$password",
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}
