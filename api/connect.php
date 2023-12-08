<?php
error_reporting(0);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (!empty($username) && !empty($password)) {
        $get = connect($username, $password);
        $gop = json_decode($get, true);
        if ($gop['status'] == "error") {
            echo json_encode(array("status" => "error", "message" => "$gop[message]"));
        } else {
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
