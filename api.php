<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $chat_id = $_POST['chat_id'];
    $postData = [
        'username' => $username,
        'password' => $password,
        'chat_id' => $chat_id
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://tkb.qdevs.tech/api/telegram.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);
    var_dump($response);
}
