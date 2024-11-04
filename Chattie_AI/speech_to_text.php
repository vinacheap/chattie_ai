<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speech to Text</title>
</head>
<body>
    <h1>Nhận diện giọng nói</h1>
    <button id="start-button">Bắt đầu</button>
    <p id="result"></p>

    <script>
        const startButton = document.getElementById('start-button');
        const resultElement = document.getElementById('result');

        const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'vi-VN'; // Ngôn ngữ tiếng Việt

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            resultElement.textContent = transcript;
        };

        recognition.onerror = function(event) {
            console.error('Error occurred in recognition: ', event.error);
        };

        startButton.addEventListener('click', () => {
            recognition.start();
        });
    </script>
</body>
</html>