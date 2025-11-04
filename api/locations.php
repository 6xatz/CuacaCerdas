<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username'])) {
	http_response_code(401);
	echo json_encode(['error' => 'unauthorized']);
	exit;
}
$path = __DIR__ . '/../data/locations.json';
if (!file_exists($path)) {
	@mkdir(dirname($path), 0777, true);
	file_put_contents($path, json_encode(["Jakarta","Bandung","Surabaya"]));
}
$locations = json_decode(file_get_contents($path) ?: '[]', true);
if (!is_array($locations)) { $locations = []; }
echo json_encode(['locations'=>$locations]);
