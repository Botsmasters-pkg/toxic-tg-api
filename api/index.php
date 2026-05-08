<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$userid = $_GET['userid'] ?? $_GET['user'] ?? null;

if (!$userid) {
    echo json_encode(["success" => false, "message" => "userid required"]);
    exit;
}

$userid = preg_replace('/[^0-9]/', '', $userid);

$apiUrl = "https://abhigyan-codes-tg-to-number-api.onrender.com/@abhigyan_codes/userid=" . $userid;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 25,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode([
        "success" => false,
        "message" => "Backend API connection failed",
        "userid" => $userid
    ]);
    exit;
}

$data = json_decode($response, true);

if (!$data || empty($data['result']['success'] ?? $data['success'] ?? false)) {
    echo json_encode([
        "success" => false,
        "message" => $data['result']['msg'] ?? $data['message'] ?? "No data found",
        "userid" => $userid
    ]);
    exit;
}

// New Structure ke hisaab se output
$result = $data['result'] ?? $data;

$output = [
    "success" => true,
    "credit" => "@botadminshere",
    "api" => "@abhigyan_codes",
    "result" => [
        "country"       => $result['country'] ?? null,
        "country_code"  => $result['country_code'] ?? null,
        "number"        => $result['number'] ?? null,
        "full_name"     => $result['full_name'] ?? null,
        "username"      => $result['username'] ?? null,
        "msg"           => $result['msg'] ?? null
    ]
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
