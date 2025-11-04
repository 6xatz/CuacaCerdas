
<?php
$snapPath = __DIR__ . '/../data/snapshots.json';
if (!file_exists($snapPath)) { @mkdir(dirname($snapPath), 0777, true); file_put_contents($snapPath, json_encode([])); }
$snaps = json_decode(file_get_contents($snapPath) ?: '[]', true);
if (!is_array($snaps)) { $snaps = []; }
// Newest first
usort($snaps, function($a,$b){ return ($b['time'] ?? 0) <=> ($a['time'] ?? 0); });
?>

<div class="header">
	<h1>History</h1>
	<span class="spacer"></span>
	<a class="btn secondary" href="index.php?page=dashboard">Kembali</a>
</div>

<section class="card" style="margin-top:16px">
	<table class="table">
		<thead>
			<tr>
				<th>Waktu</th>
				<th>Lokasi</th>
				<th>Suhu</th>
				<th>Kelembapan</th>
				<th>Angin</th>
				<th>Rekomendasi</th>
				<th>Conf</th>
				<th>User</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!$snaps): ?>
				<tr><td colspan="8" class="muted">Belum ada snapshot</td></tr>
			<?php else: ?>
				<?php foreach ($snaps as $s): ?>
					<tr>
						<td><?php echo date('Y-m-d H:i:s', (int)($s['time'] ?? time())); ?></td>
						<td><?php echo htmlspecialchars($s['city'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string)($s['temperature'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string)($s['humidity'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string)($s['wind'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars($s['action'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo isset($s['confidence']) ? round($s['confidence']*100).'%' : '-'; ?></td>
						<td><?php echo htmlspecialchars($s['user'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</section>
