<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // CORS

$dirs = [
    'hairs' => 'img/hairs/',
    'eyebrows' => 'img/eyebrows/',
    'eyes' => 'img/eyes/',
    'glasses' => 'img/glasses/',
    'noses' => 'img/noses/',
    'lips' => 'img/lips/',
    'shirts' => 'img/shirts/',
    'pants' => 'img/pants/',
    'shoes' => 'img/shoes/',
];

$images = [];

foreach ($dirs as $key => $dir) {
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), ['.', '..']); // Lọc bỏ các thư mục '.' và '..'
        foreach ($files as $file) {
            if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) { // Kiểm tra định dạng file hình ảnh
                $images[$key][] = $dir . $file; // Thêm đường dẫn đầy đủ vào mảng
            }
        }
    } else {
        echo json_encode(['error' => "Directory $dir not found."]);
        exit;
    }
}

echo json_encode($images);
?>