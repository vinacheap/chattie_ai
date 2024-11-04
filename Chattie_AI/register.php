<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$conn = mysqli_connect("localhost", "chattie", "tbDn2983", "chat_tie") or die("Kết nối thất bại: " . mysqli_connect_error());
mysqli_query($conn, "set names 'utf8'");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Kiểm tra tên đăng nhập đã tồn tại chưa
    $checkResult = mysqli_query($conn, "SELECT * FROM login WHERE username = '$username'");
    if (mysqli_num_rows($checkResult) > 0) {
        $error = "Tên đăng nhập đã tồn tại.";
    }
    else {
        // Tìm số thứ tự
        $countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM login");
        $countRow = mysqli_fetch_assoc($countResult);
        $total = $countRow['total'] + 1; // Tăng thêm 1 cho số thứ tự mới
        // Thêm người dùng mới
        $insertQuery = "INSERT INTO login (id, username, password, gender, note) VALUES ('$total', '$username', '$password', '1', '')";
        if (!file_exists($username)) {
            if (!mysqli_query($conn, $insertQuery)) {
                $error = "Đăng ký không thành công: " . mysqli_error($conn);
            }
            else {
                $_SESSION['username'] = $username;
                header("Location: /Chattie_AI/");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Đăng Ký</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .error {
            color: red;
            text-align: center;
        }
        .login-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Đăng Ký</h1>
        <form method="post">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Đăng Ký">
        </form>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <div class="login-link">
            <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
        </div>
    </div>
</body>
</html>