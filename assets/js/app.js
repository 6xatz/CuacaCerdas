const $$ = (sel, ctx=document) => ctx.querySelector(sel);
const $$$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

async function api(path, opts={}){
	const res = await fetch(path, {headers:{'Content-Type':'application/json'}, credentials:'same-origin', ...opts});
	if(!res.ok) throw new Error('Request failed');
	return await res.json();
}

async function loadItems(){
	const data = await api('api/items.php');
	const tbody = $$('#items-tbody');
	if (!tbody) return;
	tbody.innerHTML = data.items.map(item => `
		<tr>
			<td>${item.id}</td>
			<td>${item.name}</td>
			<td>${item.category}</td>
			<td><span class="badge">${item.location}</span></td>
			<td class="row-actions">
				<button class="btn secondary" data-edit="${item.id}">Edit</button>
				<button class="btn danger" data-delete="${item.id}">Hapus</button>
			</td>
		</tr>
	`).join('');

	$$$('button[data-delete]').forEach(btn => btn.addEventListener('click', async (e)=>{
		const id = e.currentTarget.getAttribute('data-delete');
		if(!confirm('Yakin hapus item?')) return;
		await api('api/items.php', {method:'POST', body: JSON.stringify({action:'delete', id})});
		loadItems();
	}));

	$$$('button[data-edit]').forEach(btn => btn.addEventListener('click', (e)=>{
		const id = e.currentTarget.getAttribute('data-edit');
		const row = e.currentTarget.closest('tr');
		const [_, nameCell, catCell] = row.children;
		$$('#item-id').value = id;
		$$('input[name="name"]').value = nameCell.textContent;
		$$('input[name="category"]').value = catCell.textContent;
		$$('#submit-btn').textContent = 'Update';
		window.scrollTo({top:0,behavior:'smooth'});
	}));
}

async function submitForm(e){
	e.preventDefault();
	const id = $$('#item-id').value || null;
	const name = $$('input[name="name"]').value.trim();
	const category = $$('input[name="category"]').value.trim();
	const location = $$('select[name="location"]').value;
	if(!name || !category) return alert('Isi semua field');
	const action = id ? 'update' : 'create';
	await api('api/items.php', {method:'POST', body: JSON.stringify({action, id, name, category, location})});
	($$('#item-form')).reset();
	$$('#item-id').value = '';
	$$('#submit-btn').textContent = 'Tambah';
	loadItems();
}

async function loadLocations(){
	const data = await api('api/locations.php');
	const select = $$('select[name="location"]');
	if (!select) return;
	select.innerHTML = data.locations.map(l=>`<option value="${l}">${l}</option>`).join('');
}

document.addEventListener('DOMContentLoaded', ()=>{
	const form = $$('#item-form');
	if(form){
		form.addEventListener('submit', submitForm);
		loadLocations().then(()=>{
			loadItems();
			const sel = $$('#location-select');
			if (sel) {
				sel.addEventListener('change', ()=>{
					refreshWeather(sel.value);
				});
				if (sel.value) refreshWeather(sel.value);
			}
		});
	}

	const manual = $$('#manual-form');
	if (manual){
		manual.addEventListener('submit', async (e)=>{
			e.preventDefault();
			const t = parseFloat($$('input[name="t"]').value);
			const h = parseFloat($$('input[name="h"]').value);
			const w = parseFloat($$('input[name="w"]').value);
			try{
				const rec = await api('api/recommend.php', {method:'POST', body: JSON.stringify({temperature:t, humidity:h, wind:w})});
				const r = rec.recommendation;
				$$('#manual-result').innerHTML = `
					<div style="display:flex;align-items:center;gap:12px">
						<div class="badge">Confidence ${(r.confidence*100).toFixed(0)}%</div>
						<div style="font-size:18px;font-weight:700">${r.action}</div>
					</div>
					<div class="muted" style="margin-top:8px">Suhu ${formatTop(r.memberships.temp)}, Kelembaban ${formatTop(r.memberships.humidity)}, Angin ${formatTop(r.memberships.wind)}</div>
				`;
			}catch(err){
				$$('#manual-result').innerHTML = `<div class="alert alert-error">Gagal menghitung.</div>`;
			}
		});
	}

	const saveBtn = $$('#save-snapshot-btn');
	if (saveBtn){
		saveBtn.addEventListener('click', async ()=>{
			const sel = $$('#location-select');
			if (!sel || !sel.value) return alert('Pilih lokasi dulu');
			try{
				const data = await api('api/recommend.php?q=' + encodeURIComponent(sel.value));
				const payload = {
					city: data.weather.city,
					temperature: data.weather.temperature,
					humidity: data.weather.humidity,
					wind: data.weather.wind,
					action: data.recommendation.action,
					confidence: data.recommendation.confidence
				};
				await api('api/save_snapshot.php', {method:'POST', body: JSON.stringify(payload)});
				alert('Snapshot tersimpan');
			}catch(e){
				alert('Gagal menyimpan snapshot');
			}
		});
	}
});

async function refreshWeather(city){
	try{
		const w = await api('api/weather.php?q=' + encodeURIComponent(city));
		const box = $$('#weather-box');
		box.innerHTML = `
			<div class="grid">
				<div><span class="muted">Lokasi</span><div><strong>${w.city}</strong></div></div>
				<div><span class="muted">Suhu (°C)</span><div><strong>${w.temperature?.toFixed ? w.temperature.toFixed(1) : w.temperature}</strong></div></div>
				<div><span class="muted">Kelembaban (%)</span><div><strong>${w.humidity}</strong></div></div>
				<div><span class="muted">Angin (km/j)</span><div><strong>${w.wind}</strong></div></div>
			</div>`;
		const rec = await api('api/recommend.php?q=' + encodeURIComponent(city));
		const r = rec.recommendation;
		const recoBox = $$('#reco-box');
		recoBox.innerHTML = `
			<div style="display:flex;align-items:center;gap:12px">
				<div class="badge">Confidence ${(r.confidence*100).toFixed(0)}%</div>
				<div style="font-size:18px;font-weight:700">${r.action}</div>
			</div>
			<div class="muted" style="margin-top:8px">Membership ringkas: Suhu ${formatTop(r.memberships.temp)}, Kelembaban ${formatTop(r.memberships.humidity)}, Angin ${formatTop(r.memberships.wind)}</div>
		`;
	}catch(e){
		const recoBox = $$('#reco-box');
		recoBox.innerHTML = `<div class="alert alert-error">Gagal memuat rekomendasi.</div>`;
	}
}

function topLabel(obj){
	let bestK = '-'; let bestV = -1;
	for (const k in obj){ if (obj[k] > bestV){ bestV = obj[k]; bestK = k; } }
	return [bestK, bestV];
}
function formatTop(obj){
	const [k,v] = topLabel(obj);
	return `${k} (${(v*100).toFixed(0)}%)`;
}
