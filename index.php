<?php
session_start();

$isAuthenticated = isset($_SESSION['username']);

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

if (!$isAuthenticated) {
	header('Location: login.php');
	exit;
}

$allowedPages = ['dashboard', 'history'];
if (!in_array($page, $allowedPages, true)) {
	$page = 'dashboard';
}

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';

echo "<main class=\"main\">";
require __DIR__ . "/pages/{$page}.php";
echo "</main>";

require __DIR__ . '/partials/footer.php';
?>
