<!doctype html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<title>Grid Tour Creator</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
	<script src="https://cdn.tailwindcss.com"></script>
	<style>
		:root{--bg:#0b1220}
		body{font-family: 'Inter',system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji','Segoe UI Emoji';}
		.upload-tile{position:relative}
		.upload-tile input{position:absolute; inset:0; opacity:.001; cursor:pointer}
		.upload-tile .thumb{position:absolute; inset:0; width:100%; height:100%; object-fit:cover; border-radius:12px}
		.upload-tile.has-image input{pointer-events:none}
		canvas{background:#111827; border-radius:16px}
	</style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">
	<div class="mx-auto max-w-7xl p-4 md:p-8">
		<header class="flex items-center justify-between gap-4 flex-wrap">
			<h1 class="text-xl md:text-2xl font-extrabold tracking-tight">Grid Tour Creator</h1>
			<div class="flex items-center gap-3">
				<button id="clearState" class="text-slate-300 hover:text-white text-sm">Hapus data</button>
				<a id="resetPage" class="text-slate-300 hover:text-white text-sm" href="<?= site_url('collage'); ?>">Reset</a>
			</div>
		</header>

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
			<section class="space-y-5">
				<div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
					<?php for($i=1;$i<=6;$i++): ?>
					<div class="upload-tile aspect-[10/9] rounded-xl bg-slate-800/60 ring-1 ring-slate-700 flex items-center justify-center text-slate-400 overflow-hidden transition-colors" data-slot="<?= $i ?>">
						<input type="file" accept="image/*" id="img<?= $i ?>" data-slot="<?= $i ?>">
						<img class="thumb hidden" alt="">
						<div class="text-center placeholder select-none pointer-events-none">
							<p class="text-xs uppercase tracking-wide">Gambar <?= $i ?></p>
							<p class="text-[10px] text-slate-500">klik untuk pilih</p>
						</div>
					</div>
					<?php endfor; ?>
				</div>

				<div class="grid grid-cols-2 gap-4">
					<label class="block">
						<span class="text-sm">Nama kiri</span>
						<input id="leftName" class="mt-1 w-full rounded-lg bg-slate-800/60 ring-1 ring-slate-700 px-3 py-2" placeholder="Player 1">
					</label>
					<label class="block">
						<span class="text-sm">Nama kanan</span>
						<input id="rightName" class="mt-1 w-full rounded-lg bg-slate-800/60 ring-1 ring-slate-700 px-3 py-2" placeholder="Player 2">
					</label>
				</div>

				<div class="grid grid-cols-3 gap-3">
					<label class="block">
						<span class="text-sm">Label R1</span>
						<input id="r1" value="R1" class="mt-1 w-full rounded-lg bg-slate-800/60 ring-1 ring-slate-700 px-3 py-2">
					</label>
					<label class="block">
						<span class="text-sm">Label R2</span>
						<input id="r2" value="R2" class="mt-1 w-full rounded-lg bg-slate-800/60 ring-1 ring-slate-700 px-3 py-2">
					</label>
					<label class="block">
						<span class="text-sm">Label R3</span>
						<input id="r3" value="R3" class="mt-1 w-full rounded-lg bg-slate-800/60 ring-1 ring-slate-700 px-3 py-2">
					</label>
				</div>

				<div class="flex flex-wrap items-center gap-3">
					<label class="flex items-center gap-2 text-sm">
						<span>Ukuran</span>
						<select id="size" class="rounded-lg bg-slate-800/60 ring-1 ring-slate-700 px-3 py-2">
							<option value="1440x2160">1440x2160 (9:16)</option>
							<option value="1080x1920">1080x1920</option>
							<option value="2048x3072">2048x3072</option>
						</select>
					</label>
					<label class="flex items-center gap-2 text-sm">
						<span>Background</span>
						<input id="bg" type="color" class="rounded-md bg-slate-800/60 ring-1 ring-slate-700 px-2 py-2" value="#111827">
					</label>
					<button id="download" class="ml-auto inline-flex items-center gap-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-white px-4 py-2 font-semibold">Unduh PNG</button>
					<button id="saveServer" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-white px-4 py-2 font-semibold">Render Server</button>
				</div>
			</section>

			<section class="space-y-3">
				<div class="flex items-center justify-between">
					<h2 class="font-semibold">Preview</h2>
					<span class="text-xs text-slate-400">Drag gambar untuk ganti slot</span>
				</div>
				<canvas id="board" width="1440" height="2160" class="w-full h-auto max-h-[85vh] shadow-xl"></canvas>
			</section>
		</div>
	</div>

	<script>
	(function(){
		const board = document.getElementById('board');
		const ctx = board.getContext('2d');
		const inputs = [...Array(6).keys()].map(i=>document.getElementById('img'+(i+1)));
		const tiles = [...document.querySelectorAll('.upload-tile')];
		const names = {left: document.getElementById('leftName'), right: document.getElementById('rightName')};
		const labels = {r1: document.getElementById('r1'), r2: document.getElementById('r2'), r3: document.getElementById('r3')};
		const bg = document.getElementById('bg');
		const sizeSel = document.getElementById('size');
		const downloadBtn = document.getElementById('download');
		const saveServerBtn = document.getElementById('saveServer');

		// state kini menyimpan dataUrls untuk restore
		const STORAGE_KEY = 'grid-tour-creator-v1';
		const state = { images: new Array(6).fill(null), dataUrls: new Array(6).fill(null) };

		function updateTileUI(i){
			const tile = tiles[i];
			const thumb = tile.querySelector('.thumb');
			const has = !!(state.images[i] || state.dataUrls[i]);
			tile.setAttribute('draggable', has ? 'true' : 'false');
			if(has){
				thumb.src = state.dataUrls[i] || (state.images[i] ? state.images[i].src : '');
				thumb.classList.remove('hidden');
				tile.querySelector('.placeholder').classList.add('hidden');
				tile.classList.add('has-image');
			}else{
				thumb.src = '';
				thumb.classList.add('hidden');
				tile.querySelector('.placeholder').classList.remove('hidden');
				tile.classList.remove('has-image');
			}
		}

		inputs.forEach(input=>{
			input.addEventListener('change', ev=>{
				const slot = Number(input.dataset.slot)-1;
				const file = input.files[0];
				if(!file) return;
				const img = new Image();
				img.onload = ()=>{
					state.images[slot] = img;
					state.dataUrls[slot] = makeDataUrl(img);
					updateTileUI(slot); draw(); saveState();
				};
				img.src = URL.createObjectURL(file);
			});
		});

		// Drag & drop antar slot
		let dragIndex = null;
		tiles.forEach((tile, idx)=>{
			tile.setAttribute('draggable', 'false');
			tile.addEventListener('dragstart', (e)=>{
				if(!state.images[idx] && !state.dataUrls[idx]){ e.preventDefault(); return; }
				dragIndex = idx; e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain','1');
				tile.classList.add('ring-2','ring-indigo-400');
			});
			tile.addEventListener('dragend', ()=>{
				tile.classList.remove('ring-2','ring-indigo-400');
			});
			tile.addEventListener('dragover', (e)=>{ if(dragIndex!==null && (state.images[dragIndex]||state.dataUrls[dragIndex])) e.preventDefault(); });
			tile.addEventListener('drop', (e)=>{
				if(!(dragIndex!==null && (state.images[dragIndex]||state.dataUrls[dragIndex]))) return;
				e.preventDefault();
				if(dragIndex===idx) return;
				const tmp = state.images[idx]; state.images[idx] = state.images[dragIndex]; state.images[dragIndex] = tmp;
				const tmpU = state.dataUrls[idx]; state.dataUrls[idx] = state.dataUrls[dragIndex]; state.dataUrls[dragIndex] = tmpU;
				updateTileUI(idx); updateTileUI(dragIndex);
				draw(); saveState(); dragIndex = null;
			});
		});

		// Init draggable state
		for(let i=0;i<6;i++) updateTileUI(i);

		// Tombol Hapus data (clear storage dan reset UI)
		document.getElementById('clearState').addEventListener('click', (e)=>{
			e.preventDefault();
			localStorage.removeItem('grid-tour-creator-v1');
			state.images = new Array(6).fill(null);
			state.dataUrls = new Array(6).fill(null);
			for(let i=0;i<6;i++) updateTileUI(i);
			names.left.value=''; names.right.value='';
			labels.r1.value='R1'; labels.r2.value='R2'; labels.r3.value='R3';
			bg.value = '#111827'; sizeSel.value='1440x2160';
			board.width=1440; board.height=2160; draw();
		});

		// Link Reset: bersihkan storage lalu reload
		document.getElementById('resetPage').addEventListener('click', (e)=>{
			localStorage.removeItem('grid-tour-creator-v1');
		});

		names.left.addEventListener('input', ()=>{ draw(); saveState(); });
		names.right.addEventListener('input', ()=>{ draw(); saveState(); });
		labels.r1.addEventListener('input', ()=>{ draw(); saveState(); });
		labels.r2.addEventListener('input', ()=>{ draw(); saveState(); });
		labels.r3.addEventListener('input', ()=>{ draw(); saveState(); });
		bg.addEventListener('change', ()=>{ draw(); saveState(); });
		sizeSel.addEventListener('change', ()=>{
			const [w,h] = sizeSel.value.split('x').map(Number);
			board.width = w; board.height = h; draw(); saveState();
		});

		function draw(){
			ctx.fillStyle = bg.value; ctx.fillRect(0,0,board.width,board.height);
			const cols=2, rows=3, gap=28;
			const tileW = Math.floor((board.width - (gap * (cols+1))) / cols);
			const tileH = Math.floor((board.height - (gap * (rows+1))) / rows);
			ctx.save();
			for(let i=0;i<6;i++){
				const c = i%2, r = Math.floor(i/2);
				const x = gap + c*(tileW+gap);
				const y = gap + r*(tileH+gap);
				drawRoundedImage(state.images[i], x, y, tileW, tileH, 18);
			}
			ctx.restore();
			// center labels
			ctx.fillStyle = '#fff';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.font = Math.floor(tileH*0.35)+'px Inter, system-ui, sans-serif';
			const centerX = board.width/2;
			const centersY = [gap+tileH/2, gap*2+tileH*1.5, gap*3+tileH*2.5];
			[labels.r1.value, labels.r2.value, labels.r3.value].forEach((t,idx)=>{
				ctx.save(); ctx.globalAlpha=.35; ctx.fillText(t, centerX+4, centersY[idx]+4); ctx.restore();
				ctx.fillText(t, centerX, centersY[idx]);
			});
			// names - tebal + outline
			ctx.textAlign = 'left'; ctx.textBaseline = 'alphabetic';
			ctx.font = '800 '+Math.floor(board.width*0.065)+'px Inter, system-ui, sans-serif';
			ctx.lineWidth = 6; ctx.strokeStyle = 'rgba(0,0,0,.6)';
			ctx.strokeText(names.left.value||'', 28, 28 + parseInt(board.width*0.065));
			ctx.fillStyle = '#fff';
			ctx.fillText(names.left.value||'', 28, 28 + parseInt(board.width*0.065));
			const txt = names.right.value||'';
			const w = ctx.measureText(txt).width;
			ctx.strokeText(txt, board.width - 28 - w, 28 + parseInt(board.width*0.065));
			ctx.fillText(txt, board.width - 28 - w, 28 + parseInt(board.width*0.065));
		}

		function drawRoundedImage(img, x, y, w, h, r){
			ctx.save();
			r = Math.min(r, w/2, h/2);
			ctx.beginPath();
			ctx.moveTo(x+r, y);
			ctx.arcTo(x+w, y, x+w, y+h, r);
			ctx.arcTo(x+w, y+h, x, y+h, r);
			ctx.arcTo(x, y+h, x, y, r);
			ctx.arcTo(x, y, x+w, y, r);
			ctx.closePath();
			ctx.clip();
			if(img){
				const iw = img.naturalWidth, ih = img.naturalHeight;
				const sr = iw/ih, dr = w/h;
				let sw, sh, sx, sy;
				if(sr>dr){ sh=ih; sw=ih*dr; sx=0; sy=0; } else { sw=iw; sh=iw/dr; sx=0; sy=(ih-sh)/2; }
				ctx.drawImage(img, sx, sy, sw, sh, x, y, w, h);
			}
			ctx.restore();
		}

		// actions
		downloadBtn.addEventListener('click', ()=>{
			draw(); saveState();
			const a = document.createElement('a');
			a.href = board.toDataURL('image/png');
			a.download = 'collage.png';
			a.click();
		});

		saveServerBtn.addEventListener('click', async ()=>{
			const fd = new FormData();
			const [w,h] = sizeSel.value.split('x').map(Number);
			fd.append('width', w); fd.append('height', h); fd.append('bg', bg.value);
			fd.append('leftName', names.left.value||'');
			fd.append('rightName', names.right.value||'');
			fd.append('r1', labels.r1.value||'R1');
			fd.append('r2', labels.r2.value||'R2');
			fd.append('r3', labels.r3.value||'R3');
			inputs.forEach((input,idx)=>{ if(input.files[0]) fd.append('img'+(idx+1), input.files[0]); });
			draw(); saveState();
			const res = await fetch('<?= site_url('collage/generate'); ?>', { method:'POST', body: fd });
			const blob = await res.blob();
			const url = URL.createObjectURL(blob);
			const a = document.createElement('a'); a.href=url; a.download='collage.png'; a.click();
			URL.revokeObjectURL(url);
		});

		function getLayout(){
			const cols=2, rows=3, gap=28;
			const tileW = Math.floor((board.width - (gap * (cols+1))) / cols);
			const tileH = Math.floor((board.height - (gap * (rows+1))) / rows);
			return {tileW, tileH};
		}
		function makeDataUrl(image){
			try{
				const {tileW, tileH} = getLayout();
				const oc = document.createElement('canvas'); oc.width = tileW; oc.height = tileH;
				const ox = oc.getContext('2d');
				const iw = image.naturalWidth, ih = image.naturalHeight; const dr = tileW/tileH; const sr = iw/ih;
				let sw, sh, sx, sy;
				if(sr>dr){ sh=ih; sw=ih*dr; sx=0; sy=0; } else { sw=iw; sh=iw/dr; sx=0; sy=(ih-sh)/2; }
				ox.drawImage(image, sx, sy, sw, sh, 0, 0, tileW, tileH);
				return oc.toDataURL('image/jpeg', 0.9);
			}catch(e){ return null; }
		}

		function saveState(){
			try{
				const payload = {
					leftName: names.left.value||'', rightName: names.right.value||'',
					r1: labels.r1.value||'R1', r2: labels.r2.value||'R2', r3: labels.r3.value||'R3',
					bg: bg.value, size: sizeSel.value,
					images: state.dataUrls
				};
				localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
			}catch(e){ console.warn('Save state gagal', e); }
		}

		function restoreState(){
			try{
				const raw = localStorage.getItem(STORAGE_KEY); if(!raw){ draw(); return; }
				const p = JSON.parse(raw);
				names.left.value = p.leftName||''; names.right.value = p.rightName||'';
				labels.r1.value = p.r1||'R1'; labels.r2.value = p.r2||'R2'; labels.r3.value = p.r3||'R3';
				bg.value = p.bg||'#111827'; sizeSel.value = p.size||'1440x2160';
				const [w,h] = sizeSel.value.split('x').map(Number); board.width=w; board.height=h;
				state.images = new Array(6).fill(null);
				state.dataUrls = (p.images||new Array(6).fill(null));
				state.dataUrls.forEach((src, i)=>{
					if(!src) return; const img = new Image(); img.onload = ()=>{ state.images[i]=img; updateTileUI(i); draw(); }; img.src = src;
				});
				draw();
			}catch(e){ console.warn('Restore state gagal', e); draw(); }
		}

		restoreState();
	})();
	</script>
</body>
</html>


