<?php
error_reporting(0);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $chat_id = $_POST['chat_id'];
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
                $array = [];
                $array['chat_id'] = $chat_id;
                $array['username'] = $username;
                $array['password'] = $password;
                $count = count($gop) - 1;
                $get_name = $gop[$count]["name"];
                $array['name'] = $get_name;
                $array['time'] = time();
                $array['send_noti'] = "true";
                $com = json_encode($array);
                $com_save = save_file($com, $chat_id);
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
                                    '30phut' => 'false',
                                    '20phut' => 'false',
                                    '10phut' => 'false',
                                    'start' => 'false'
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
                    save_file($data, 'data-' . $username);
                }
            }
            $data = json_encode($gop);
            $save = save_file($data, $username);
            if ($save['status'] == "error") {
                echo json_encode(array("status" => "error", "message" => "$save[message]"));
                exit();
            } else {
                $get = array("status" => "success", "message" => "get data success", "data" => $gop);
            }
            echo json_encode($get);
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
function save_file($data, $username)
{
    $username = strtolower($username);
    if (!empty($username) && !empty($data)) {
        $file = @fopen("../data/$username.json", "w+");
        if (!$file) {
            $result = array("status" => "error", "message" => "can't open file");
        } else {
            fwrite($file, $data);
            fclose($file);
            $result = array("status" => "success", "message" => "save data success");
        }
    } else {
        $result = array("status" => "error", "message" => "username or data is empty");
    }
    return $result;
}
