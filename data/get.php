<?php
require_once '../telegram/function.php';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    switch ($_GET['act']) {
        case 'log':
            $chat_id = $_GET['id'];
            $sql = "SELECT * FROM `users` WHERE `chatid` = '$chat_id'";
            $result = mysqli_query($db, $sql);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $get_log = mysqli_query($db, "SELECT * FROM `log` WHERE `chatid` = '$chat_id' ORDER BY `id`");
                echo 'id|message|reply|time' . "<br>";
                while ($row_log = mysqli_fetch_assoc($get_log)) {
                    echo $row_log['id'] . "|" . $row_log['message'] . "|" . $row_log['reply'] . "|" . date('d/m/Y H:i:s', $row_log['time']) . "<br>";
                }
            } else {
                echo json_encode(array("status" => "error", "message" => "user not found"));
            }
            break;
        case 'data':
            $chat_id = $_GET['id'];
            $username = $_GET['username'];
            $sql = "SELECT * FROM `users` WHERE `chatid` = '$chat_id'";
            $result = mysqli_query($db, $sql);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $get_tkb = mysqli_query($db, "SELECT * FROM `tkb` WHERE `chatid` = '$chat_id' ORDER BY `id`");
                echo 'id|date|subject|period|class|teacher' . "<br>";
                while ($row_tkb = mysqli_fetch_assoc($get_tkb)) {
                    echo $row_tkb['id'] . "|" . date('d/m/Y', $row_tkb['date']) . "|" . $row_tkb['subject'] . "|" . $row_tkb['period'] . "|" . $row_tkb['class'] . "|" . $row_tkb['teacher'] . "<br>";
                }
            } else {
                echo json_encode(array("status" => "error", "message" => "user not found"));
            }
            break;
    }
} else {
    echo json_encode(array("status" => "error", "message" => "method not allowed"));
}
