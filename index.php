<!doctype html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Trang web xem thời khóa biểu ICTU do Hứa Đức Quân phát triển và thiết kế">
    <meta name="keywords" content="thời khóa biểu, ICTU, Hứa Đức Quân">
    <meta name="author" content="Hứa Đức Quân">
    <title>Thời Khóa Biểu By HDQuanDev</title>
    <link rel="shortcut icon" href="assets/img/fav.png">
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://tkb.qdevs.tech">
    <meta property="og:title" content="Thời Khóa Biểu By HDQuanDev">
    <meta property="og:description" content="Trang web xem thời khóa biểu ICTU do Hứa Đức Quân phát triển và thiết kế">
    <meta property="og:image" content="/assets/img/TKB.QDEVS.TECH.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://tkb.qdevs.tech">
    <meta property="twitter:title" content="Thời Khóa Biểu By HDQuanDev">
    <meta property="twitter:description" content="Trang web xem thời khóa biểu ICTU do Hứa Đức Quân phát triển và thiết kế">
    <meta property="twitter:image" content="/assets/img/TKB.QDEVS.TECH.png">

    <!-- Bootstrap Grid -->
    <link rel="stylesheet" href="assets/css/bootstrap-grid.min.css">

    <!-- General Style -->
    <link rel="stylesheet" href="assets/css/general.css?v=<?= time(); ?>">

    <!-- Elegant Font Icon -->
    <link rel="stylesheet" href="assets/css/elegant-font.css">

    <!-- ContentBox Style -->
    <link rel="stylesheet" href="assets/css/contentbox.css?v=<?= time(); ?>">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>

        #loginPopup {
            background: #1c768f;
            width: 450px;
            margin: 50px auto;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 5px 10px 30px rgba(0, 0, 0, 0.4);
        }

        #loginForm {
            background: #f0f0f0;
            padding: 40px;
            border-radius: 5px;
            text-align: center;
            box-shadow:
                inset 2px 2px 2px rgba(255, 255, 255, 0.5),
                inset -2px -2px 2px rgba(0, 0, 0, 0.1);
        }

        #loginForm h2 {
            padding-bottom: 15px;
        }

        #loginForm input {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 2px solid #ccc;
            color: #333;
            font-size: 18px;
        }

        #loginButton {
            background: linear-gradient(#4eb8dd, #1c768f);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            cursor: pointer;
            box-shadow:
                1px 2px 4px rgba(0, 0, 0, 0.4);
        }

        #loginForm input:focus {
            outline: none;
            border-color: #4eb8dd;
        }

        @media (max-width: 500px) {

            #loginPopup {
                width: 350px;
                padding: 15px;
            }

            #loginForm {
                text-align: center;
            }

            #loginForm input {
                font-size: 14px;
            }


            #loginButton {
                font-size: 16px;
                border-radius: 25px;
                padding: 15px 40px;
            }

        }
    </style>
</head>
<?php
error_reporting(0);
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
];
$view = file_get_contents('viewer.txt');
$view = $view + 1;
file_put_contents('viewer.txt', $view);
?>

<body>
    <main id="page">
        <div class="space5"></div>
        <div class="intro">
            <h2>Thời Khóa Biểu ICTU</h2>
            <h3>Số lượt truy cập: <?= number_format(file_get_contents('viewer.txt')); ?></h3>
            <?php
            if (isset($_COOKIE['name']) && isset($_COOKIE['update']) && isset($_COOKIE['data'])) {
            ?>
                <h3>Chào bạn: <?= $_COOKIE['name']; ?></h3>
                <a class="purchase">Hiện Tại Là: <?= date('d-m-Y H:i'); ?> </a>
                <div class="links">
                    <a id="update-data">Cập Nhật Lại Dữ Liệu</a>
                    <a id="delete-all-data">Xóa Toàn Bộ Dữ Liệu</a>
                    <a href="https://www.facebook.com/quancp72h" target="_blank">Liên Hệ Hỗ Trợ</a>
                </div>
            <?php
                if (isset($_COOKIE['update']))
                    echo '<h3>Dữ liệu tkb của bạn cập nhật lần cuối lúc: ' . date('d-m-Y H:i:s', $_COOKIE['update'] / 1000) . '</h3>';
            }
            ?>
        </div>
        <?php
        if (isset($_COOKIE['name']) && isset($_COOKIE['update']) && isset($_COOKIE['data'])) {
        ?>
            <div class="space5"></div>
        <?php
        }
        ?>
        <!-- START Content Box 31 -->

        <section>
            <div class="container">
                <div class="row">
                    <?php
                    if (isset($_COOKIE['data']) && isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
                        $json = $_COOKIE['data'];
                        $json = str_replace("\xc2\xa0", '', $json);
                        $get = json_decode($json, true);
                        $count = count($get) - 1;
                        $dates = [];
                        for ($i = 0; $i < $count; $i++) {
                            //echo 'môn ' . $get[$i]['lop-hoc-phan'] . '<br/>';
                            $thoigian = $get[$i]['thoi-gian'];
                            preg_match_all('/Từ (\d{2}\/\d{2}\/\d{4}) đến (\d{2}\/\d{2}\/\d{4}): \((\d+)\)Thứ (\d) tiết ([\d,]+) \((\w+)\)/', $thoigian, $matches, PREG_SET_ORDER);
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
                                            'buoi' => $match[3]
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

                        function getStartAndEndTime($periodString)
                        {
                            global $periods;
                            $periodsArray = explode(',', $periodString);
                            $startPeriod = min($periodsArray);
                            $endPeriod = max($periodsArray);
                            return $periods[$startPeriod]['start'] . ' - ' . $periods[$endPeriod]['end'];
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
                        foreach ($dates as $date) {
                            $currentDate = date('Y-m-d');
                            $date_get = date('Y-m-d', $date["date"]);
                            if ($date_get < $currentDate) {
                                continue;
                            }
                            $rand = rand(1, 5);
                            $buoi = $date['buoi'];
                            $classFormat = $date['class'];
                            if (preg_match_all('/\(([\d,]+)\)\s*([A-Za-z0-9.]+\s[A-Za-z0-9.]+)/', $classFormat, $matches)) {
                                foreach ($matches[1] as $key => $match) {
                                    $numbers = array_map('trim', explode(',', $match)); // Split the numbers by comma and remove any whitespace
                                    if (in_array($buoi, $numbers)) {
                                        $lop = $matches[2][$key]; // This will print the words after the numbers
                                    }
                                }
                            } else if (preg_match('/^C\d+\.\d+ C\d+$/', $classFormat)) {

                                $lop = $classFormat;
                            } else {
                                $lop = $classFormat;
                            }
                    ?>
                            <div class="col-md-6">
                                <!-- Start Content Box -->
                                <div class="content-box-33 color-<?= $rand; ?>">
                                    <div class="content-box-33-icon-wrapper">
                                        <span class="content-box-33-icon">
                                            <?php if (isCurrentPeriodAndDay(date('Y-m-d', $date["date"]), $buoi)) {
                                                echo '<strong>Đang học</strong>';
                                            } else {
                                                echo date('d/m', $date["date"]);
                                            } ?>
                                        </span><br>
                                    </div>
                                    <div class="content-box-33-content-wrapper">
                                        <h3 class="content-box-33-title"><?= $date['subject']; ?></h3>
                                        <h3>
                                            <center><span class="content-box-33-button" href="#"><?= getStartAndEndTime($date['period']); ?></span></center>
                                        </h3>
                                        <span class="content-box-33-content">- Tiết: <?= $date['period']; ?><br>
                                            - Lớp: <?= $lop; ?><br>
                                            - Giảng viên: <?= $date['teacher']; ?>
                                        </span>
                                    </div>

                                </div>
                            </div>
                        <?php
                        }
                        ?>

                    <?php
                    }
                    ?>
                </div>
            </div>
        </section>
        <!-- END Content Box 31 -->
        <!-- HTML for the login popup -->
        <section>
            <div class="container">
                <!-- Popup -->
                <div id="loginPopup">
                    <form id="loginForm">
                        <h2>Đăng nhập</h2><br>
                        <h4>Vui lòng sử dụng tài khoản DangKyTinChi của ICTU để đăng nhập!</h4><br>
                        <div class="form-group">
                            <label for="username">Tên đăng nhập:</label><br>
                            <input type="text" id="username" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="password">Mật khẩu:</label><br>
                            <input type="password" id="password" class="form-control">
                        </div>
                        <h4>Khi bạn ấn vào nút Đăng Nhập là bạn đã đồng ý cho chúng tôi lưu trữ thông tin của bạn trên Cookie máy bạn, thông tin này sẽ chỉ có bạn mới có thể xem được, và bạn có thể xóa chúng bất cứ lúc nào!</h4><br>
                        <button type="button" id="loginButton" class="btn btn-primary btn-block">Đăng nhập</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- jQuery and AJAX -->

    </main>
    <script src="/assets/js/main.js?v=<?= time(); ?>"></script>
</body>

</html>