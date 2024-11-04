<?php
$audioFile = 'output_' . time() . '.wav'; // Đường dẫn tệp âm thanh

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy văn bản từ form
    $text = escapeshellarg($_POST['text']); // Bảo vệ đầu vào

    // Xác định giọng nói, tốc độ và giới tính
    $language = $_POST['language']; // Lấy ngôn ngữ từ form
    $gender = $_POST['gender']; // Lấy giới tính từ form
    $speed = 150; // Tốc độ nói

    // Chọn giọng nói dựa trên ngôn ngữ và giới tính
    if ($language === 'vi') {
        $voice = ($gender === 'male') ? 'vi+male' : 'vi+female'; // 'vi+male' cho tiếng Việt giọng nam, 'vi+female' cho tiếng Việt giọng nữ
    } else {
        $voice = ($gender === 'male') ? 'en+male' : 'en+female'; // 'en+male' cho tiếng Anh giọng nam, 'en+female' cho tiếng Anh giọng nữ
    }

    // Tạo tệp âm thanh bằng eSpeak
    $command = "espeak -w $audioFile -v $voice -s $speed $text";
    shell_exec($command);

    // Kiểm tra xem tệp có được tạo không
    if (file_exists($audioFile)) {
        $audioCreated = true; // Đánh dấu tệp âm thanh đã được tạo
    } else {
        $errorMessage = "Không thể tạo âm thanh.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chuyển Văn Bản Thành Giọng Nói</title>
</head>
<body>
    <h1>Chuyển Văn Bản Thành Giọng Nói</h1>
    <form method="post">
        <textarea name="text" rows="4" cols="50" required></textarea><br>
        <label for="language">Chọn ngôn ngữ:</label>
        <select name="language" id="language">
            <option value="vi">Tiếng Việt</option>
            <option value="en">Tiếng Anh</option>
        </select><br>
        <label for="gender">Chọn giới tính:</label>
        <select name="gender" id="gender">
            <option value="male">Nam</option>
            <option value="female">Nữ</option>
        </select><br>
        <input type="submit" value="Chuyển Đổi">
    </form>

    <?php if (isset($audioCreated) && $audioCreated): ?>
        <h2>Âm Thanh Đã Được Tạo:</h2>
        <audio autoplay style="display: none;">
            <source src="<?php echo $audioFile . '?t=' . time(); ?>" type="audio/wav">
            Your browser does not support the audio element.
        </audio>
    <?php elseif (isset($errorMessage)): ?>
        <p><?php echo $errorMessage; ?></p>
    <?php endif; ?>
</body>
</html>