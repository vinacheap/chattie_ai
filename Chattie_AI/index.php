<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$character = "";

if (isset($_GET["logout"])) {
    // Xóa tất cả các biến phiên
    $_SESSION = array();
    // Hủy phiên
    session_destroy();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$conn = mysqli_connect("localhost", "chattie", "tbDn2983", "chat_tie");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

if (isset($_GET["character_del"])) {
    $character = $_GET["character_del"];
    $file = "characters/".$character.".png";
    if (file_exists($file)) {
        unlink($file);
    }
    echo "<script> alert('$file'); </script>";
    mysqli_query($conn, "UPDATE login SET note = ''");
}

$result = mysqli_query($conn, "SELECT note FROM login WHERE username = '$username'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $character = $row["note"];
    if (!empty($character)) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        // Kiểm tra User-Agent
        if (strpos($userAgent, 'MyCustomApp') !== false) {
            header("Location: chattie_android.php");
        }
        elseif (preg_match('/Mobi|Android/i', $userAgent)) {
            header("Location: chattie_mobile.php");
        }
        else {
            header("Location: chattie.php");
        }
        exit();
    }
    else {
        header("Location: create.php");
        exit();
    }
}
?>