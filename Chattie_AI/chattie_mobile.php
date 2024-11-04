<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: /Chattie_AI/?logout");
    exit();
} else {
    $username = $_SESSION['username'];
}

$conn = mysqli_connect("localhost", "chattie", "tbDn2983", "chat_tie");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

$result = mysqli_query($conn, "SELECT gender, note FROM login WHERE username = '$username'");
if ($result) {
    $row = mysqli_fetch_assoc($result); // Lấy hàng đầu tiên từ kết quả
    $character = $row["note"];
    $genderValue = $row['gender'];
    $gender = $genderValue === 0 ? "FeMale" : "Male";
    if ($character == "") {
        header("Location: create.php");
        exit();
    }
} else {
    die("Error in query: " . mysqli_error($conn)); // Thêm thông báo lỗi
}

$generatedText = "Hello, I'm " . htmlspecialchars($character) . "! <br> What can I help you with?";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generative AI Form with Character</title>
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=J4GZxNN1"></script>
    <style>
        body {
            transform: scale(0.85); /* Thu nhỏ toàn bộ xuống */
            transform-origin: top left; /* Đặt gốc của biến đổi ở góc trên bên trái */
            overflow: hidden; /* Ẩn bất kỳ phần nào bị tràn ra ngoài */
            min-width: 520px;
            max-width: 960px;
            margin: 0 auto;
            padding: 0;
            text-align: center;    
            font-family: Tahoma;
            font-size: 13px;
            background-repeat: repeat-x;
            background-image: url(https://vinacheap.com/images/bg.png);
            background-color: #FFF;
        }

        .container {
            display: flex; /* Sử dụng flexbox */
            flex-direction: column; /* Sắp xếp theo cột */
            align-items: left; /* Căn trái theo chiều ngang */
            width: 100%; /* Đảm bảo chiếm toàn bộ chiều rộng */
        }

        .character-container {
            position: relative;
            width: 100%;
            max-width: 250px;
            margin-top: 20px;
        }

        .character-container img {
            width: 100%;
            max-width: 100%;
        }

        .character-name {
            margin-top: 8px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .create-link {
            display: none;
            position: absolute;
            top: 90%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #5cb85c;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
        }
        
        .character-name:hover .create-link {
            display: inline-block;
        }

        .speech-bubble {
            position: absolute;
            top: 5%;
            right: -110%;
            background-color: #fff;
            border: 2px solid #333;
            border-radius: 15px;
            padding: 10px;
            width: 250px;
            height: 100px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
            overflow-y: auto;
        }

        .thought-bubbles {
            position: absolute;
            top: 10%;
            right: 5%;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 5px;
        }

        .thought-bubble {
            background-color: #fff;
            border: 2px solid #333;
            border-radius: 50%;
            width: 10px;
            height: 10px;
        }

        .thought-bubble.large {
            width: 15px;
            height: 15px;
        }

        .form-container {
            width: 100%;
            background-color: #f0f0f0;
            padding: 10px;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, .1);
            text-align: center;
            box-sizing: border-box;
        }

        .form-container textarea {
            width: 90%;
            max-width: 500px;
            height: 50px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        button {
            padding: 10px;
            background-color: #5cb85c;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }
    </style>
    
<script>
    let intervalId;
    const gender = <?php echo json_encode($gender); ?>;
    
    if (!responsiveVoice.voiceSupport()) {
        alert("Your browser does not support it, please download Chrome to continue");
    }

    function showThinking() {
        const speechBubble = document.querySelector(".speech-bubble");
        let dots = 0;
        intervalId = setInterval(() => {
            dots = (dots + 1) % 4;
            let dotText = "Thinking" + ".".repeat(dots);
            speechBubble.innerText = dotText;
        }, 500);
    }

    function stopThinking() {
        clearInterval(intervalId);
    }
    
    function text2speach(textInput, gender) {
        var voicelist = responsiveVoice.getVoices();
        if (gender == "Male") {
            responsiveVoice.speak(textInput, "UK English Male");
        } else {
            responsiveVoice.speak(textInput, "UK English Female");
        }
    }
    
    window.onload = () => {
        stopThinking();
    };
</script>

</head>
<body>
    <div class="container">
        <a href="/Chattie_AI/?logout" style="float: right; margin-bottom: 10px;">Đăng xuất</a>
        <div class="character-container">
            <img src="characters/<?php echo htmlspecialchars($character); ?>.png?v=<?= time(); ?>" alt="Character">
            <div class="character-name">
                <?php echo htmlspecialchars($character); ?>
                <a href="/Chattie_AI/?character_del=<?php echo htmlspecialchars($character); ?>" class="create-link">Create a new character</a>
            </div>
            <div class="speech-bubble" id="speech-bubble">
                <?php
                    echo $generatedText;
                    echo "<script> text2speach(".json_encode($generatedText).", ".json_encode($gender)."); </script>";
                ?>
            </div>
            <div class="thought-bubbles">
                <div class="thought-bubble"></div>
                <div class="thought-bubble"></div>
                <div class="thought-bubble large"></div>
            </div>
        </div>

        <div class="form-container">
            <form id="speech-form" onsubmit="event.preventDefault(); generateContent();">
                <label for="prompt">Enter interactive content:</label><br>
                <textarea id="prompt" name="prompt" placeholder="Enter content here..."></textarea><br>
                <button id="start_speak">Speak</button>
            </form>
        </div>
    </div>
    <script>
        const startSpeak = document.getElementById('start_speak');
        const resultElement = document.getElementById('prompt');

        const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'en-US'; // Ngôn ngữ tiếng Anh

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            resultElement.value = transcript; // Cập nhật nội dung vào textarea
        };

        recognition.onerror = function(event) {
            console.error('Error occurred in recognition: ', event.error);
        };

        startSpeak.addEventListener('click', (event) => {
            event.preventDefault(); // Ngăn chặn việc gửi form
            resultElement.value = ""; // Xóa textarea
            recognition.start();
        });
        
        recognition.onend = function() {
            showThinking();
            const promptValue = document.getElementById('prompt').value;
            generateContent();
        };
        
        async function generateContent() {
            const prompt = document.getElementById('prompt').value;
            const response = await fetch('gemini.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ prompt: prompt })
            });
            const text = await response.text();
            stopThinking();
            document.getElementById('speech-bubble').innerHTML = text;
            text2speach(text, gender);
        }
    </script>
</body>
</html>