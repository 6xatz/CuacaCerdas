<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username'])) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); exit; }

$payload = json_decode(file_get_contents('php://input') ?: 'null', true) ?: [];
$city = trim($payload['city'] ?? '');
$t = isset($payload['temperature']) ? (float)$payload['temperature'] : null;
$h = isset($payload['humidity']) ? (float)$payload['humidity'] : null;
$w = isset($payload['wind']) ? (float)$payload['wind'] : null;
$action = trim($payload['action'] ?? '');
$conf = isset($payload['confidence']) ? (float)$payload['confidence'] : null;

if ($city === '' || $t === null || $w === null || $action === ''){ http_response_code(422); echo json_encode(['error'=>'invalid']); exit; }

$file = __DIR__ . '/../data/snapshots.json';
@mkdir(dirname($file), 0777, true);
if (!file_exists($file)) file_put_contents($file, json_encode([]));
$arr = json_decode(file_get_contents($file) ?: '[]', true);
if (!is_array($arr)) $arr = [];

$arr[] = [
	'time' => time(),
	'city' => $city,
	'temperature' => $t,
	'humidity' => $h,
	'wind' => $w,
	'action' => $action,
	'confidence' => $conf,
	'user' => $_SESSION['username'] ?? '-'
];

$tmp = $file.'.tmp';
file_put_contents($tmp, json_encode($arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
rename($tmp, $file);
echo json_encode(['ok'=>true]);
