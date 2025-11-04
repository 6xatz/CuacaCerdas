<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username'])) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); exit; }

require_once __DIR__ . '/../lib/weather.php';

$city = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($city === '') { http_response_code(400); echo json_encode(['error'=>'missing_city']); exit; }

$w = get_weather_for_city($city);
if (isset($w['error'])){
	http_response_code($w['error']==='unknown_city' ? 404 : 502);
	echo json_encode(['error'=>$w['error']]);
	exit;
}
echo json_encode($w);
