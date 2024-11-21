
<?php
session_start();
include 'config.php'; // Kết nối đến cơ sở dữ liệu



// Xử lý khi người dùng gửi form đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        // Đăng nhập
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Truy vấn cơ sở dữ liệu để kiểm tra tài khoản và mật khẩu
        $stmt = $pdo->prepare("SELECT * FROM NguoiDung WHERE TenDangNhap = :username AND MatKhau = :password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch();

        // Kiểm tra tài khoản và mật khẩu
        if ($user) {
            $_SESSION['user_id'] = $user['MaNguoiDung'];  // Lưu ID người dùng vào session
            $_SESSION['username'] = $user['TenDangNhap']; // Lưu tên đăng nhập vào session
            $_SESSION['role'] = $user['VaiTro'];          // Lưu vai trò vào session

            // Chuyển hướng người dùng dựa trên vai trò
            if ($user['VaiTro'] == 'admin') {
                header("Location: home.php"); // Chuyển hướng tới trang quản trị
            } else {
                header("Location: home.php"); // Chuyển hướng tới trang chính
            }
            exit;
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
        }
    } elseif (isset($_POST['register'])) {
         
       
 // Đăng ký
 $username = $_POST['username'];
 $password = $_POST['password'];
 $email = $_POST['email'];
 $hoTen = $_POST['hoTen'];
 $soDienThoai = $_POST['soDienThoai'];

 // Kiểm tra nếu tên đăng nhập đã tồn tại
 $stmt = $pdo->prepare("SELECT * FROM NguoiDung WHERE TenDangNhap = :username");
 $stmt->bindParam(':username', $username);
 $stmt->execute();
 $existingUser = $stmt->fetch();

 if ($existingUser) {
     $_SESSION['error'] = "Tên đăng nhập đã tồn tại!";
 } else {
     // Thêm người dùng mới vào cơ sở dữ liệu
     $stmt = $pdo->prepare("INSERT INTO NguoiDung (TenDangNhap, MatKhau, HoTen, Email, SoDienThoai, VaiTro, NgayTao) 
                             VALUES (?, ?, ?, ?, ?, 'nguoidung', GETDATE())");
     $stmt->execute([$username, $password, $hoTen, $email, $soDienThoai]);

     // Lưu thông báo vào session
     $_SESSION['success'] = "Đăng ký thành công! Bạn có thể đăng nhập ngay.";
 }
 echo "<script>
 alert('Đăng ký thành công! Bạn có thể đăng nhập ngay.');
 window.location.href = 'login.php';
</script>";
exit;
}
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Quản Lý Thư Viện</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Your existing styles */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
            background-image: url('https://i.imgur.com/mj2sQmj.jpg');
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }

        .container {
            position: relative;
            width: 400px;
            perspective: 1000px;
        }

        .form-box {
            position: relative;
            width: 100%;
            height: 500px;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
        }

        .form-box.flipped {
            transform: rotateY(180deg);
        }

        .form-container {
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            backface-visibility: hidden;
        }

        .form-container h2 {
            margin-bottom: 1.5rem;
            color: #343a40;
        }

        .form-container input {
            margin-bottom: 1rem;
        }

        .form-container button {
            background-color: #007bff;
            border: none;
            padding: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            width: 100%;
            transition: background-color 0.3s ease;
            border-radius: 50px;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .form-container a {
            display: block;
            margin-top: 1rem;
            color: #007bff;
            text-decoration: none;
        }

        .form-container a:hover {
            text-decoration: underline;
        }

        .form-back {
            transform: rotateY(180deg);
        }

        .toggle-btn {
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: background-color 0.3s ease;
            margin-top: 1rem;
        }

        .toggle-btn:hover {
            background-color: #0056b3;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.4s;
        }

        .modal-content {
            background-color: #ffffff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.4s;
        }

        .modal-content h3 {
            color: #007bff;
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        .modal-content p {
            font-size: 1rem;
            line-height: 1.6;
            color: #333;
            margin-bottom: 15px;
        }

        .modal-content a {
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }

        .modal-content a:hover {
            text-decoration: underline;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-box" id="formBox">
             <!-- Thông báo đăng ký thành công -->
             <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
                <div class="alert alert-success" role="alert">
                    Đăng ký thành công! Bạn có thể đăng nhập ngay.
                </div>
            <?php endif; ?>
            <!-- Login Form -->
            <div class="form-container form-front">
                <h2>Đăng Nhập</h2>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Tên Đăng Nhập" required>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Mật Khẩu" required>
                    <button type="submit" name="login">Đăng Nhập</button>
                </form>
                <hr />
                <p onclick="toggleForm()">Chuyển sang Đăng Ký</p>
                <a href="javascript:void(0);" onclick="showForgotPasswordModal()">Quên mật khẩu?</a>
            </div>
            

            <!-- Registration Form -->
            <div class="form-container form-back">
                <h2>Đăng Ký</h2>
                <form action="login.php" method="POST">
                    <input type="text" class="form-control" id="reg-username" name="username" placeholder="Tên Đăng Nhập" required>
                    <input type="text" class="form-control" id="reg-hoTen" name="hoTen" placeholder="Họ Tên" required>
                    <input type="text" class="form-control" id="reg-soDienThoai" name="soDienThoai" placeholder="Số Điện Thoại" required>
                    <input type="email" class="form-control" id="reg-email" name="email" placeholder="Email" required>
                    <input type="password" class="form-control" id="reg-password" name="password" placeholder="Mật Khẩu" required>
                    <button type="submit" name="register">Đăng Ký</button>
                </form>
                <hr />
                <p onclick="toggleForm()">Chuyển sang Đăng Nhập</p>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeForgotPasswordModal()">&times;</span>
            <h3>Quên mật khẩu</h3>
            <p>Đối với tài khoản học sinh: Học sinh liên hệ giáo viên phụ trách lớp mình để xin cấp lại tài khoản.</p>
            <p>Đối với tài khoản giáo viên: Giáo viên liên hệ với người quản lý tài khoản tại trường để xin cấp lại mật khẩu.</p>
            <p>Đối với tài khoản quản trị đơn vị: Quản trị viên liên hệ với <a href="mailto: thangkolua@gmail.com">thangkolua@gmail.com</a> hoặc số điện thoại <strong>034.703.6243</strong> để xác nhận thông tin và yêu cầu cấp lại mật khẩu.</p>
        </div>
    </div>
  
    <!-- JavaScript -->
    <script>
        function toggleForm() {
            var formBox = document.getElementById('formBox');
            formBox.classList.toggle('flipped');
        }


        function showForgotPasswordModal() {
            document.getElementById("forgotPasswordModal").style.display = "block";
        }

        function closeForgotPasswordModal() {
            document.getElementById("forgotPasswordModal").style.display = "none";
        }

        window.onclick = function(event) {
            var modal = document.getElementById("forgotPasswordModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>
