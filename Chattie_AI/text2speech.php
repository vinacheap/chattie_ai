<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang thứ hai</title>
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=J4GZxNN1"></script>
</head>
<body>
    <h1>Trang thứ hai</h1>

    <script>
        window.addEventListener('load', function() {
            if (localStorage.getItem('userInteracted') === 'true') {
                const text = "hello";
                responsiveVoice.speak(text, "US English Male");
                localStorage.removeItem('userInteracted'); // Xóa giá trị sau khi phát âm
            }
        });
    </script>
</body>
</html>