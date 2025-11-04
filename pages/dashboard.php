<div class="header">
	<h1>Dashboard</h1>
	<span class="spacer"></span>
	<span class="badge">Login: <?php echo htmlspecialchars($_SESSION['username'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
</div>

<section class="card" style="display:flex;gap:16px;flex-wrap:wrap">
	<div style="flex:1;min-width:300px">
		<h2 class="card-title">Data Fuzzy & Cuaca</h2>
		<form id="item-form" autocomplete="off">
			<input type="hidden" id="item-id">
			<label class="field">
				<span>Lokasi</span>
				<select name="location" id="location-select"></select>
			</label>
		</form>
		<div id="weather-box" style="margin-top:10px">
			<div class="muted">Belum ada data.</div>
		</div>
		<div style="margin-top:10px;display:flex;gap:8px">
			<button class="btn" type="button" id="save-snapshot-btn">Simpan Snapshot</button>
		</div>
		<h2 class="card-title" style="margin-top:16px">Rekomendasi Fuzzy</h2>
		<div id="reco-box">
			<div class="muted">Menunggu data cuaca.</div>
		</div>
	</div>
</section>

<section class="card" style="display:flex;gap:16px;flex-wrap:wrap;margin-top:16px">
	<div style="flex:1;min-width:300px">
		<h2 class="card-title">Input Manual</h2>
		<p class="muted">Hitung rekomendasi berdasarkan angka yang Anda masukkan.</p>
		<form id="manual-form" autocomplete="off">
			<div class="grid" style="gap:12px">
				<label class="field"><span>Suhu (°C)</span><input name="t" type="number" step="0.1" required></label>
				<label class="field"><span>Kelembaban (%)</span><input name="h" type="number" step="1" required></label>
				<label class="field"><span>Angin (km/j)</span><input name="w" type="number" step="0.1" required></label>
			</div>
			<button class="btn" type="submit" style="margin-top:8px;">Hitung Manual</button>
		</form>
	</div>
	<div style="flex:1;min-width:300px">
		<h2 class="card-title">Hasil Manual</h2>
		<div id="manual-result" class="muted">Belum dihitung.</div>
	</div>
</section>
