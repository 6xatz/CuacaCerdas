<?php
session_start();
if (isset($_SESSION['username'])) {
	header('Location: index.php');
	exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = isset($_POST['username']) ? trim($_POST['username']) : '';
	$password = isset($_POST['password']) ? trim($_POST['password']) : '';
	if ($username === 'admin' && $password === 'admin') {
		$_SESSION['username'] = 'admin';
		header('Location: index.php');
		exit;
	}
	$error = 'Username atau password salah';
}
?>
<!doctype html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login · Fuzzy Weather</title>
	<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dark">
	<div class="auth-container">
		<form class="card login-card" method="post" autocomplete="off">
			<h1 class="card-title">Authentication</h1>
			<?php if (!empty($error)): ?>
				<div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php endif; ?>
			<label class="field">
				<span>Username</span>
				<input name="username" required>
			</label>
			<label class="field">
				<span>Password</span>
				<input type="password" name="password" required>
			</label>
			<button class="btn" type="submit">Masuk</button>
			</div>
		</form>
	</div>
</body>
</html>
