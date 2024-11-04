<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$character = "";

if (!isset($_SESSION['username'])) {
    header("Location: /Chattie_AI/");
    exit();
}
else {
    $username = $_SESSION['username'];
}

$conn = mysqli_connect("localhost", "chattie", "tbDn2983", "chat_tie");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

$result = mysqli_query($conn, "SELECT note FROM login WHERE username = '$username'");
if ($result) {
    $row = mysqli_fetch_assoc($result); // Lấy hàng đầu tiên từ kết quả
    $character = $row["note"] ?? ""; // Sử dụng toán tử null coalescing
}

if (!empty($character)) {
    header("Location: /Chattie_AI/");
    exit();
}

$error = "";

// Kiểm tra xem các trường có được gửi không
if (isset($_POST['character_name']) && isset($_POST['character_image'])) {
    $character_name = $_POST['character_name'];
    $character_image = $_POST['character_image'];
    $gender = $_POST['gender'];
    $genderValue = $gender === "Male" ? 0 : 1;

    $file_path = "characters/" . $character_name . ".png";
    $checkResult = mysqli_query($conn, "SELECT * FROM login WHERE note = '$character_name'");
    if (mysqli_num_rows($checkResult) > 0 || file_exists($file_path)) {
        $error = "Tên nhân vật đã được sử dụng.";
    }
    else {
        $result = mysqli_query($conn, "UPDATE login SET gender = '$genderValue', note = '$character_name' WHERE username = '$username'");
        if ($result) {
            if (strpos($character_image, 'data:image/png;base64,') === 0) {
                $image_data = explode(',', $character_image)[1]; 
                $image_data = base64_decode($image_data);
                if (file_put_contents($file_path, $image_data) !== false) {
                    header("Location: /Chattie_AI/");
                    exit();
                }
                else {
                    $error = "Không thể lưu hình ảnh.";
                }
            }
            else {
                $error = "Dữ liệu hình ảnh không hợp lệ.";
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
    <title>Character Creator</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #B3E0FF;
        }
        .container {
            max-width: 400px;
            height: 100%;
            display: flex;
            flex-direction: column; /* Thay đổi ở đây để xếp theo chiều dọc */
            align-items: center;
            background-color: #66B2FF;
            border-radius: 10px;
            padding: 20px;
        }
        .character {
            width: 100%;
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar {
            width: 100%;
            background-color: #007ACC;
            align-items: center;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .canvas {
            border: 1px solid black;
            width: 100%;
            height: auto;
            aspect-ratio: 4 / 5;
        }
        .name-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        .error {
            color: red;
            text-align: center;
        }
        
        .category-icons, .category-items {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .icons-container {
            display: flex;
            gap: 10px;
            overflow: hidden;
            width: 200px;
        }
        
        .icon {
            background-color: #FFF;
            border-radius: 5px;
            padding: 10px;
            font-size: 24px;
            text-align: center;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s;
            cursor: pointer;
        }

        .items-container {
            display: flex;
            gap: 10px;
            overflow: hidden; /* Ẩn phần bên ngoài */
            width: 100%; /* Đảm bảo chiều rộng đủ lớn */
            height: 100px; /* Chiều cao để hiển thị các mục */
        }
        
        .item {
            background-color: #FFF;
            border-radius: 5px;
            padding: 10px;
            font-size: 24px;
            text-align: center;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s;
            cursor: pointer;
        }
        
        .item img {
            width: 80px; /* Giữ tỉ lệ ảnh với kích thước của item */
            height: 80px; 
            object-fit: contain;
        }
        
        .icon:hover, .item:hover {
            transform: scale(1.1);
        }
        
        .arrow {
            cursor: pointer;
            font-size: 24px;
            color: #FFA500;
            padding: 5px;
        }
        
        .color-options {
            display: flex;
            gap: 10px;
        }
        
        .color-box {
            width: 30px;
            height: 30px;
            border-radius: 5px;
        }
        
        .scroll-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 10px; /* Khoảng cách giữa nút cuộn và item */
        }
        
        .form-row {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-bottom: 15px; /* Khoảng cách giữa hàng textbox và nút tiếp tục */
        }
        
        input[type="text"] {
            padding: 10px;
            margin-right: 10px; /* Khoảng cách giữa textbox và dropdown */
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1; /* Chiếm không gian tương đương */
            min-width: 150px; /* Đảm bảo không gian tối thiểu cho textbox và dropdown */
        }
        
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1; /* Chiếm không gian tương đương */
            max-width: 150px; /* Đảm bảo không gian tối thiểu cho textbox và dropdown */
        }

        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="character">
            <canvas class="canvas" id="canvas" width="400" height="500"></canvas>
            
        </div>
        <div class="sidebar">
            <div class="category-icons">
                <span class="arrow" onclick="scrollIcons(-1)">&#9664;</span>
                <div class="icons-container" id="iconsContainer">
                    <div class="icon" onclick="loadItems('hairs')">👩</div>
                    <div class="icon" onclick="loadItems('eyebrows')">🙆️</div>
                    <div class="icon" onclick="loadItems('eyes')">👁</div>
                    <div class="icon" onclick="loadItems('glasses')">👓</div>
                    <div class="icon" onclick="loadItems('noses')">🐽</div>
                    <div class="icon" onclick="loadItems('lips')">👄</div>
                    <div class="icon" onclick="loadItems('shirts')">👕</div>
                    <div class="icon" onclick="loadItems('pants')">🩳</div>
                    <div class="icon" onclick="loadItems('shoes')">👟</div>
                </div>
                <span class="arrow" onclick="scrollIcons(1)">&#9654;</span>
            </div>
            <div class="category-items">
                <span class="arrow" onclick="scrollItems(-1)">&#9664;</span>
                <div class="items-container" id="itemsContainer">
                    <!-- Các item sẽ được thêm vào đây thông qua JavaScript -->
                </div>
                <span class="arrow" onclick="scrollItems(1)">&#9654;</span>
            </div>
            
            <div class="color-options">
                <div class="color-box" style="background-color: #ff0000;" data-color="#ff0000"></div>
                <div class="color-box" style="background-color: #186a3b;" data-color="#186a3b"></div>
                <div class="color-box" style="background-color: #0000ff;" data-color="#0000ff"></div>
                <div class="color-box" style="background-color: #28b463;" data-color="#28b463"></div>
                <div class="color-box" style="background-color: #7d6608;" data-color="#7d6608"></div>
                <div class="color-box" style="background-color: #ff00ff;" data-color="#ff00ff"></div>
                <div class="color-box" style="background-color: #4a235a;" data-color="#4a235a"></div>
                <div class="color-box" style="background-color: #1f618d;" data-color="#1f618d"></div>
                <div class="color-box" style="background-color: #7b7d7d;" data-color="#7b7d7d"></div>
                <div class="color-box" style="background-color: #000000;" data-color="#000000"></div>
            </div>
        </div>
        <div class="name-container">
                <form method="post">
                    <div class="form-row">
                        <input type="text" id="character_name" name="character_name" required placeholder="Nhập tên nhân vật">
                        <select id="gender" name="gender" required>
                            <option value="Male">Nam</option>
                            <option value="Female">Nữ</option>
                            <option value="Other">Khác</option>
                        </select>
                    </div>
                    <input type="hidden" id="character_image" name="character_image">
                    <input type="submit" value="Tiếp tục">
                </form>
                <?php if ($error): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
            </div>
    </div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');

        let characterLayers = {
            base: 'img/base.png',
            hairs: null,
            eyebrows: null,
            eyes: null,
            glasses: null,
            noses: null,
            lips: null,
            shirts: null,
            pants: null,
            shoes: null,
        };

        let currentLayer = null;
        let layerColors = {
            hairs: '#ffffff',
            eyebrows: '#000000',
            eyes: '#ffffff',
            glasses: '#ffffff',
            noses: '#ffffff',
            lips: '#ffffff',
            shirts: '#ffffff',
            pants: '#ffffff',
            shoes: '#ffffff',
        };

        function drawCharacter() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const baseImg = new Image();
            baseImg.src = characterLayers.base;
            baseImg.onload = function() {
                ctx.drawImage(baseImg, 0, 0, canvas.width, canvas.height);
                drawAdditionalLayers();
            };
        }

        // Định nghĩa layerOrder bên ngoài các hàm
        const layerOrder = ['eyes', 'eyebrows', 'glasses', 'noses', 'hairs', 'lips', 'shoes', 'pants', 'shirts'];

        function drawAdditionalLayers() {
            const promises = layerOrder.map(part => {
                return new Promise((resolve) => {
                    if (characterLayers[part]) {
                        console.log(`Drawing ${part} from ${characterLayers[part]}`); // Log để kiểm tra
                        const img = new Image();
                        img.src = characterLayers[part];
                        img.onload = function() {
                            const tempCanvas = document.createElement('canvas');
                            const tempCtx = tempCanvas.getContext('2d');
                            tempCanvas.width = canvas.width;
                            tempCanvas.height = canvas.height;
                            tempCtx.drawImage(img, 0, 0, canvas.width, canvas.height);
        
                            // Kiểm tra nếu phần là "eyes" thì không tô màu
                            if (part !== 'eyes' && part !== 'lips' && part !== 'noses') {
                                const color = layerColors[part];
                                tempCtx.globalCompositeOperation = 'source-atop'; 
                                tempCtx.fillStyle = color;
                                tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height); // Vẽ màu mới
                            }
                            ctx.drawImage(tempCanvas, 0, 0);
                            resolve();
                        };
                        img.onerror = function() {
                            console.error(`Failed to load image: ${characterLayers[part]}`);
                            resolve(); // Vẫn đánh dấu là đã vẽ, ngay cả khi thất bại
                        };
                    } else {
                        resolve(); // Không có hình ảnh, đánh dấu ngay
                    }
                });
            });
        
            Promise.all(promises).then(() => {
                console.log('All layers drawn');
            });
        }

        function changeLayer(part, image) {
            console.log(`Changing layer: ${part} to ${image}`); // Thêm log để kiểm tra
            characterLayers[part] = image;
            currentLayer = part;
            drawCharacter();
        }

        document.querySelectorAll('.color-box').forEach(box => {
            box.onclick = function() {
                const color = this.getAttribute('data-color');
                if (currentLayer) {
                    layerColors[currentLayer] = color; 
                    drawCharacter(); 
                }
            };
        });
        
        // Mã JavaScript để cuộn icon
        let currentIconScroll = 0;
        const iconsContainer = document.getElementById("iconsContainer");
        const iconWidth = 50;

        function scrollIcons(direction) {
            const maxIconScroll = (iconsContainer.children.length - 1) * iconWidth;
            currentIconScroll += direction * iconWidth;
            if (currentIconScroll < 0) {
                currentIconScroll = 0;
            } else if (currentIconScroll > maxIconScroll) {
                currentIconScroll = maxIconScroll;
            }
            iconsContainer.scrollTo({ left: currentIconScroll, behavior: "smooth" });
        }

        let currentItemScroll = 0;
        const itemsContainer = document.getElementById("itemsContainer");
        const itemWidth = 130;

        function scrollItems(direction) {
            const maxItemScroll = (itemsContainer.children.length - 1) * itemWidth;
            currentItemScroll += direction * itemWidth;
            if (currentItemScroll < 0) {
                currentItemScroll = 0;
            } else if (currentItemScroll > maxItemScroll) {
                currentItemScroll = maxItemScroll;
            }
            itemsContainer.scrollTo({ left: currentItemScroll, behavior: "smooth" });
        }

        function loadItems(category) {
            fetch('get_img.php')
                .then(response => response.json())
                .then(data => {
                    const itemsContainer = document.getElementById("itemsContainer");
                    itemsContainer.innerHTML = ''; // Xóa nội dung cũ

                    // Kiểm tra nếu danh mục tồn tại trong dữ liệu
                    if (data[category]) {
                        data[category].forEach(imgSrc => {
                            const item = document.createElement("div");
                            item.className = "item";
                            const img = document.createElement("img");
                            img.src = imgSrc;
                            img.alt = category; // Thay thế bằng tên danh mục tương ứng nếu cần
                            item.appendChild(img);
                            img.onclick = function() {
                                changeLayer(category, imgSrc);
                            };
                            itemsContainer.appendChild(item);
                        });
                    } else {
                        console.error('Category not found:', category);
                    }
                })
                .catch(error => {
                    console.error('Error loading items:', error);
                });
        }
        
        document.querySelector('form').onsubmit = function() {
            const characterImage = document.getElementById('character_image');
            characterImage.value = canvas.toDataURL('image/png');
        };
        
        drawCharacter();
    </script>
</body>
</html>