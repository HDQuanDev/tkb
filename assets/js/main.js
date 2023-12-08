// Xóa toàn bộ dữ liệu

$("#delete-all-data").click(function () {
    swal({
        title: "Xóa toàn bộ dữ liệu?",
        text: "Bạn có chắc chắn muốn xóa toàn bộ dữ liệu? Dữ liệu sẽ bị xóa vĩnh viễn! Vui lòng cân nhắc trước khi xóa!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        closeOnClickOutside: false
    }).then(function (willDelete) {
        if (willDelete) {
            swal({
                title: "Đang xóa dữ liệu!",
                text: "Vui lòng chờ trong giây lát!",
                icon: "info",
                button: false,
                closeOnClickOutside: false
            });
            var date = new Date();
            date.setFullYear(date.getFullYear() - 1); // Set the expiry date to 1 year ago
            document.cookie = "username=;expires=" + date.toUTCString() + ";path=/";
            document.cookie = "password=;expires=" + date.toUTCString() + ";path=/";
            document.cookie = "name=;expires=" + date.toUTCString() + ";path=/";
            document.cookie = "data=;expires=" + date.toUTCString() + ";path=/";
            document.cookie = "update=;expires=" + date.toUTCString() + ";path=/";
            swal("Xóa thành công!", "Dữ liệu đã được xóa thành công!", "success").then((value) => {
                window.location.href = "/index.php";
            });
        } else {
            swal("Xóa thất bại!", "Dữ liệu chưa được xóa!", "error");
        }
    });
});
// Cập nhật dữ liệu
$("#update-data").click(function () {
    swal({
        title: "Cập nhật nhật dữ liệu mới nhất?",
        text: "Bạn có chắc chắn muốn cập nhật dữ liệu mới nhất? Dữ liệu cũ sẽ bị xóa! Thời gian cập nhật sẽ diễn ra trong khoảng 15s-60s, vui lòng chờ cập nhật xong mới rời khỏi trang!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        closeOnClickOutside: false
    }).then(function (willDelete) {
        if (willDelete) {
            swal({
                title: "Đang cập nhật dữ liệu!",
                text: "Vui lòng chờ trong giây lát!",
                icon: "info",
                button: false,
                closeOnClickOutside: false
            });
            $.ajax({
                url: '/api/connect.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: getCookie('username'),
                    password: getCookie('password')
                },
                success: function (response) {
                    if (response.status === 'success') {
                        var date = new Date();
                        date.setFullYear(date.getFullYear() + 1); // Set the expiry date to 1 year from now
                        document.cookie = "data=" + JSON.stringify(response.data) + ";expires=" + date.toUTCString() + ";path=/";
                        document.cookie = "update=" + Date.now() + ";expires=" + date.toUTCString() + ";path=/";
                        swal("Cập nhật thành công!", "Dữ liệu đã được cập nhật thành công!", "success").then((value) => {
                            window.location.href = "/index.php";
                        });
                    } else {
                        swal("Cập nhật thất bại!", "Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!", "error").then((value) => {
                            window.location.href = "/index.php";
                        });
                    }
                },
                error: function (error) {
                    swal("Cập nhật thất bại!", "Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!", "error").then((value) => {
                        window.location.href = "/index.php";
                    });
                }
            });
        } else {
            swal("Nhắc nhở!", "Hãy ấn cấp nhật lại dữ liệu để tải lại dữ liệu thời khóa biểu mới nhất từ DangKyTinChi!", "info");
        }
    });
});
// Hiển thị popup login
if (getCookie('data') == null || getCookie('username') == null || getCookie('password') == null || getCookie('name') == null || getCookie('update') == null) {

    let loginPopup = document.getElementById('loginPopup');
    loginPopup.style.display = 'block';

    $('#loginButton').click(function () {
        swal({
            title: "Đang đăng nhập!",
            text: "Vui lòng không đóng cửa sổ này, quá trình mất từ 15s-60s vui lòng chờ!",
            icon: "info",
            button: false,
            closeOnClickOutside: false
        });
        var username = $('#username').val();
        var password = $('#password').val();
        $('#loginButton').html('<i class="spinner-border spinner-border-sm"></i> Đang đăng nhập...');
        $('#loginButton').attr('disabled', 'disabled');
        $.ajax({
            url: "/api/connect.php",
            type: "POST",
            dataType: "json",
            data: {
                username: username,
                password: password
            },
            success: function (data) {
                if (data.status === 'success') {
                    var date = new Date();
                    date.setFullYear(date.getFullYear() + 1); // Set the expiry date to 1 year from now
                    document.cookie = "username=" + username + ";expires=" + date.toUTCString() + ";path=/";
                    document.cookie = "password=" + password + ";expires=" + date.toUTCString() + ";path=/";
                    var result = data.data;
                    var get_name = Object.keys(result).length;
                    var name = data.data[get_name - 1].name;
                    document.cookie = "name=" + name + ";expires=" + date.toUTCString() + ";path=/";
                    document.cookie = "data=" + JSON.stringify(data.data) + ";expires=" + date.toUTCString() + ";path=/";
                    document.cookie = "update=" + Date.now() + ";expires=" + date.toUTCString() + ";path=/";
                    $('#loginButton').text('Đăng nhập');
                    $('#loginButton').removeAttr('disabled');
                    swal("Đăng nhập thành công!", "Chào mừng bạn đến với thời khóa biểu online, ấn OK để tiếp tục!", "success").then((value) => {
                        window.location.href = "/index.php";
                    });
                } else {
                    swal("Đăng nhập thất bại!", "Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!", "error");
                    $('#loginButton').text('Đăng nhập');
                    $('#loginButton').removeAttr('disabled');
                }
            }
        });
    });
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}