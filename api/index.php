<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// API expiry (optional, agar chahte ho to hata do)
$expiryDate = strtotime('2027-12-31');  // badha diya
$currentDate = time();

if ($currentDate > $expiryDate) {
    echo json_encode([
        "success" => false,
        "message" => "API Expired! Contact @botadminshere",
        "credit" => "@botadminshere"
    ]);
    exit;
}

$userid = $_GET['userid'] ?? null;

if (!$userid) {
    echo json_encode([
        "success" => false,
        "message" => "Please provide a Telegram User ID",
        "credit" => "@botadminshere"
    ]);
    exit;
}

// sanitize
$userid = preg_replace('/[^0-9]/', '', $userid);

$apiUrl = "https://abhigyan-codes-tg-to-number-api.onrender.com/@abhigyan_codes/userid=" . $userid;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_USERAGENT => 'Mozilla/5.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch data from API",
        "credit" => "@botadminshere"
    ]);
    exit;
}

$data = json_decode($response, true);

if (!$data || !isset($data['success']) || $data['success'] !== true) {
    echo json_encode([
        "success" => false,
        "message" => $data['message'] ?? "No data found or API error",
        "credit" => "@botadminshere"
    ]);
    exit;
}

// Final Output
$output = [
    "success" => true,
    "credit" => "@botadminshere",
    "result" => [
        "country"      => $data['data']['contact_intelligence']['country'] ?? null,
        "country_code" => $data['data']['contact_intelligence']['country_code'] ?? null,
        "number"       => $data['data']['contact_intelligence']['phone_number'] ?? null,
        "full_name"    => $data['data']['profile_summary']['full_name'] ?? null,
        "username"     => $data['data']['profile_summary']['username'] ?? null
    ]
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>