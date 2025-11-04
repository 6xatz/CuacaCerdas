<aside class="sidebar">
	<h2 class="brand">Fuzzy Weather</h2>
	<nav class="nav">
		<a href="index.php?page=dashboard" class="<?php echo (($_GET['page']??'dashboard')==='dashboard')?'active':''; ?>">Dashboard</a>
		<a href="index.php?page=history" class="<?php echo (($_GET['page']??'dashboard')==='history')?'active':''; ?>">History</a>
		<div style="height:12px"></div>
		<a href="logout.php">Keluar</a>
	</nav>
</aside>
