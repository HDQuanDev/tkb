<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $chat_id = mysqli_real_escape_string($db, $_POST['chat_id']);
    $postData = [
        'username' => $username,
        'password' => $password,
        'chat_id' => $chatId
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
