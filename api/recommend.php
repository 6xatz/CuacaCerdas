<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username'])) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); exit; }

require_once __DIR__ . '/../lib/fuzzy.php';
require_once __DIR__ . '/../lib/weather.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$city = isset($_GET['q']) ? trim($_GET['q']) : '';
	if ($city === '') { http_response_code(400); echo json_encode(['error'=>'missing_city']); exit; }
	$w = get_weather_for_city($city);
	if (!is_array($w) || isset($w['error'])) { http_response_code(502); echo json_encode(['error'=>'weather_failed']); exit; }
	$rec = fuzzy_recommendation((float)$w['temperature'], (float)($w['humidity'] ?? 0), (float)$w['wind']);
	echo json_encode(['weather'=>$w, 'recommendation'=>$rec]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$payload = json_decode(file_get_contents('php://input') ?: 'null', true) ?: [];
	$t = (float)($payload['temperature'] ?? 0);
	$h = (float)($payload['humidity'] ?? 0);
	$w = (float)($payload['wind'] ?? 0);
	$rec = fuzzy_recommendation($t,$h,$w);
	echo json_encode(['weather'=>['temperature'=>$t,'humidity'=>$h,'wind'=>$w],'recommendation'=>$rec]);
	exit;
}

http_response_code(405);
echo json_encode(['error'=>'method_not_allowed']);
