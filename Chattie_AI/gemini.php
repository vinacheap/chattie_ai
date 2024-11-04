<?php
function generateAIContent($apiKey, $prompt) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";

    $postData = json_encode([
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.9,
            "topK" => 1,
            "topP" => 1,
            "maxOutputTokens" => 2000,
            "stopSequences" => []
        ],
        "safetySettings" => [
            [
                "category" => "HARM_CATEGORY_HARASSMENT",
                "threshold" => "BLOCK_ONLY_HIGH"
            ],
            [
                "category" => "HARM_CATEGORY_HATE_SPEECH",
                "threshold" => "BLOCK_ONLY_HIGH"
            ],
            [
                "category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT",
                "threshold" => "BLOCK_ONLY_HIGH"
            ],
            [
                "category" => "HARM_CATEGORY_DANGEROUS_CONTENT",
                "threshold" => "BLOCK_ONLY_HIGH"
            ]
        ]
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return "CURL Error: " . curl_error($ch);
    }

    // Ghi lại phản hồi từ API
    file_put_contents('debug.log', $response);

    $responseData = json_decode($response, true);

    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        return $responseData['candidates'][0]['content']['parts'][0]['text'];
    } else {
        return "No generated text found. Response: " . json_encode($responseData);
    }
}

$apiKey = "AIzaSyAW3ifIHipRBe-NjMPae7gn9SjtlBI3yTU";  // Nhập khóa API của bạn ở đây

$generatedText = ""; // Khởi tạo biến cho nội dung được tạo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prompt = $_POST['prompt']; // Lấy prompt từ form
    if ($prompt == "") {
        $generatedText = "Sorry, I didn't hear you clearly!";
    }
    else {
        $generatedText = generateAIContent($apiKey, $prompt); // Gọi hàm tạo nội dung
    }
} else {
    $generatedText = "Hello, I'm your AI assistant! <br> What can I help you with?";
}
echo $generatedText;
?>