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
						<div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
							<span>Ukuran</span>
							<input id="r1Size" type="range" min="0.2" max="1.5" step="0.01" value="1" class="w-full">
						</div>
					</label>
					<label class="block">
						<span class="text-sm">Label R2</span>
						<input id="r2" value="R2" class="mt-1 w-full rounded-lg bg-slate-800/60 ring-1 ring-slate-700 px-3 py-2">
						<div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
							<span>Ukuran</span>
							<input id="r2Size" type="range" min="0.2" max="1.5" step="0.01" value="1" class="w-full">
						</div>
					</label>
					<label class="block">
						<span class="text-sm">Label R3</span>
						<input id="r3" value="R3" class="mt-1 w-full rounded-lg bg-slate-800/60 ring-1 ring-slate-700 px-3 py-2">
						<div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
							<span>Ukuran</span>
							<input id="r3Size" type="range" min="0.2" max="1.5" step="0.01" value="1" class="w-full">
						</div>
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
					<!-- <button id="saveServer" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-white px-4 py-2 font-semibold">Render Server</button> -->
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

		<!-- Modal Editor Gambar -->
		<div id="editorModal" class="fixed inset-0 z-50 hidden">
			<div class="absolute inset-0 bg-black/60"></div>
			<div class="relative mx-auto max-w-4xl w-full h-[80vh] mt-10 bg-slate-900 rounded-xl ring-1 ring-slate-700 shadow-2xl flex flex-col">
				<div class="p-3 border-b border-slate-700 flex items-center gap-3">
					<h3 class="font-semibold">Edit Gambar</h3>
					<div class="ml-auto flex items-center gap-2">
						<button id="resetTransform" class="text-sm px-3 py-1 rounded bg-slate-700 hover:bg-slate-600">Reset</button>
						<button id="saveTransform" class="text-sm px-3 py-1 rounded bg-indigo-500 hover:bg-indigo-400 text-white">Simpan</button>
						<button id="closeEditor" class="text-sm px-3 py-1 rounded bg-slate-700 hover:bg-slate-600">Tutup</button>
					</div>
				</div>
				<div class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-0">
					<div class="col-span-3 flex items-center justify-center p-4">
						<canvas id="editCanvas" class="max-w-full max-h-full shadow-lg cursor-move"></canvas>
					</div>
					<div class="border-l border-slate-700 p-4 space-y-4">
						<div>
							<p class="text-xs text-slate-400">Gunakan scroll untuk zoom, drag untuk geser</p>
						</div>
						<label class="block text-sm">Zoom
							<input id="zoomRange" type="range" min="0.5" max="4" step="0.01" value="1" class="w-full">
						</label>
					</div>
				</div>
			</div>
		</div>
		<!-- Modal Tutorial -->
		<div id="tutorialModal" class="fixed inset-0 z-50 hidden">
			<div class="absolute inset-0 bg-black/60"></div>
			<div class="relative mx-auto max-w-xl w-full mt-10 bg-slate-900 rounded-xl ring-1 ring-slate-700 shadow-2xl p-5">
				<h3 class="font-semibold text-lg mb-2">Selamat datang 👋</h3>
				<p class="text-sm text-slate-300 mb-3">Cara cepat menggunakan aplikasi:</p>
				<ol class="list-decimal list-inside space-y-2 text-sm text-slate-200">
					<li>Unggah hingga 6 gambar. Klik untuk pilih atau drag antar slot.</li>
					<li>Isi nama kiri/kanan dan label R1–R3.</li>
					<li>Pilih ukuran dan warna latar; lihat hasil di preview.</li>
					<li>Double click gambar untuk edit posisi dan zoom.</li>
					<li>Klik "Unduh PNG" untuk menyimpan hasil.</li>
				</ol>
				<div class="mt-4 flex items-center justify-between">
					<label class="flex items-center gap-2 text-sm text-slate-300">
						<input id="dontShowTutorial" type="checkbox" class="rounded">
						<span>Jangan tampilkan lagi</span>
					</label>
					<button id="closeTutorial" class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-white px-4 py-2 font-semibold">Mulai</button>
				</div>
			</div>
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
		const labelSizes = { r1: document.getElementById('r1Size'), r2: document.getElementById('r2Size'), r3: document.getElementById('r3Size') };
		const bg = document.getElementById('bg');
		const sizeSel = document.getElementById('size');
		const downloadBtn = document.getElementById('download');
		const saveServerBtn = document.getElementById('saveServer');
		const TUTORIAL_KEY = 'grid-tour-creator-tutorial-v1';
		const tutorialModal = document.getElementById('tutorialModal');
		const closeTutorialBtn = document.getElementById('closeTutorial');
		const dontShowTutorial = document.getElementById('dontShowTutorial');

		// state kini menyimpan dataUrls untuk restore + transform per-slot
		const STORAGE_KEY = 'grid-tour-creator-v1';
		const state = { images: new Array(6).fill(null), dataUrls: new Array(6).fill(null), transforms: new Array(6).fill(0).map(()=>({scale:1, offsetX:0, offsetY:0})) };

		// Editor modal refs
		const editorModal = document.getElementById('editorModal');
		const editCanvas = document.getElementById('editCanvas');
		const editCtx = editCanvas.getContext('2d');
		const zoomRange = document.getElementById('zoomRange');
		const saveTransformBtn = document.getElementById('saveTransform');
		const resetTransformBtn = document.getElementById('resetTransform');
		const closeEditorBtn = document.getElementById('closeEditor');
		let editingIndex = null;
		let workingTransform = null;
		let isDragging = false; let dragStartX = 0; let dragStartY = 0;
		// Disable default touch gestures (pinch/scroll) on editor canvas
		editCanvas.style.touchAction = 'none';

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
					state.transforms[slot] = {scale:1, offsetX:0, offsetY:0};
					state.dataUrls[slot] = makeDataUrl(img, state.transforms[slot]);
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
				const tmpT = state.transforms[idx]; state.transforms[idx] = state.transforms[dragIndex]; state.transforms[dragIndex] = tmpT;
				updateTileUI(idx); updateTileUI(dragIndex);
				draw(); saveState(); dragIndex = null;
			});
		});

		// Buka editor dengan double click pada tile berisi gambar
		tiles.forEach((tile, idx)=>{
			tile.addEventListener('dblclick', ()=>{
				if(!(state.images[idx] || state.dataUrls[idx])) return;
				openEditor(idx);
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
			state.transforms = new Array(6).fill(0).map(()=>({scale:1, offsetX:0, offsetY:0}));
			for(let i=0;i<6;i++) updateTileUI(i);
			names.left.value=''; names.right.value='';
			labels.r1.value='R1'; labels.r2.value='R2'; labels.r3.value='R3';
			if(labelSizes.r1) labelSizes.r1.value='1';
			if(labelSizes.r2) labelSizes.r2.value='1';
			if(labelSizes.r3) labelSizes.r3.value='1';
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
		labelSizes.r1.addEventListener('input', ()=>{ draw(); saveState(); });
		labelSizes.r2.addEventListener('input', ()=>{ draw(); saveState(); });
		labelSizes.r3.addEventListener('input', ()=>{ draw(); saveState(); });
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
				drawRoundedImage(state.images[i], x, y, tileW, tileH, 18, state.transforms[i]);
			}
			ctx.restore();
			// center labels dengan ukuran dinamis per R1/R2/R3
			ctx.fillStyle = '#fff';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			const baseSize = tileH*0.35;
			const centerX = board.width/2;
			const centersY = [gap+tileH/2, gap*2+tileH*1.5, gap*3+tileH*2.5];
			const sizes = [Number(labelSizes.r1.value)||1, Number(labelSizes.r2.value)||1, Number(labelSizes.r3.value)||1];
			[labels.r1.value, labels.r2.value, labels.r3.value].forEach((t,idx)=>{
                const px = Math.floor(baseSize * sizes[idx]);
                ctx.font = px+'px Inter, system-ui, sans-serif';
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

		function drawRoundedImage(img, x, y, w, h, r, t){
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
				const baseScale = Math.max(w/iw, h/ih);
				const scale = (t && t.scale ? t.scale : 1) * baseScale;
				const dw = iw * scale; const dh = ih * scale;
				const ox = t && typeof t.offsetX === 'number' ? t.offsetX : 0;
				const oy = t && typeof t.offsetY === 'number' ? t.offsetY : 0;
				// Anchor kiri secara default (horizontal), vertikal tetap center
				const dx = x + 0 + ox;
				const dy = y + (h - dh)/2 + oy;
				ctx.drawImage(img, 0, 0, iw, ih, dx, dy, dw, dh);
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

		if (saveServerBtn) saveServerBtn.addEventListener('click', async ()=>{
			const fd = new FormData();
			const [w,h] = sizeSel.value.split('x').map(Number);
			fd.append('width', w); fd.append('height', h); fd.append('bg', bg.value);
			fd.append('leftName', names.left.value||'');
			fd.append('rightName', names.right.value||'');
			fd.append('r1', labels.r1.value||'R1');
			fd.append('r2', labels.r2.value||'R2');
			fd.append('r3', labels.r3.value||'R3');
			inputs.forEach((input,idx)=>{ if(input.files[0]) fd.append('img'+(idx+1), input.files[0]); });
			state.transforms.forEach((t,idx)=>{
				const i = idx+1; const tf = t||{scale:1, offsetX:0, offsetY:0};
				fd.append('img'+i+'_scale', String(tf.scale||1));
				fd.append('img'+i+'_ox', String(Math.round((tf.offsetX||0))));
				fd.append('img'+i+'_oy', String(Math.round((tf.offsetY||0))));
			});
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
		function makeDataUrl(image, t){
			try{
				const {tileW, tileH} = getLayout();
				const oc = document.createElement('canvas'); oc.width = tileW; oc.height = tileH;
				const ox = oc.getContext('2d');
				const iw = image.naturalWidth, ih = image.naturalHeight;
				const baseScale = Math.max(tileW/iw, tileH/ih);
				const scale = (t && t.scale ? t.scale : 1) * baseScale;
				const dw = iw * scale; const dh = ih * scale;
				const offX = t && typeof t.offsetX === 'number' ? t.offsetX : 0;
				const offY = t && typeof t.offsetY === 'number' ? t.offsetY : 0;
				// Anchor kiri (horizontal), vertikal center
				const dx = 0 + offX; const dy = (tileH - dh)/2 + offY;
				const r = 18; ox.save();
				const cr = Math.min(r, tileW/2, tileH/2);
				ox.beginPath();
				ox.moveTo(cr, 0);
				ox.arcTo(tileW, 0, tileW, tileH, cr);
				ox.arcTo(tileW, tileH, 0, tileH, cr);
				ox.arcTo(0, tileH, 0, 0, cr);
				ox.arcTo(0, 0, tileW, 0, cr);
				ox.closePath();
				ox.clip();
				ox.drawImage(image, 0, 0, iw, ih, dx, dy, dw, dh);
				ox.restore();
				return oc.toDataURL('image/jpeg', 0.9);
			}catch(e){ return null; }
		}

		function saveState(){
			try{
				const payload = {
					leftName: names.left.value||'', rightName: names.right.value||'',
					r1: labels.r1.value||'R1', r2: labels.r2.value||'R2', r3: labels.r3.value||'R3',
					r1Size: Number(labelSizes.r1.value)||1, r2Size: Number(labelSizes.r2.value)||1, r3Size: Number(labelSizes.r3.value)||1,
					bg: bg.value, size: sizeSel.value,
					images: state.dataUrls,
					transforms: state.transforms
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
				if(labelSizes.r1) labelSizes.r1.value = String(p.r1Size||1);
				if(labelSizes.r2) labelSizes.r2.value = String(p.r2Size||1);
				if(labelSizes.r3) labelSizes.r3.value = String(p.r3Size||1);
				bg.value = p.bg||'#111827'; sizeSel.value = p.size||'1440x2160';
				const [w,h] = sizeSel.value.split('x').map(Number); board.width=w; board.height=h;
				state.images = new Array(6).fill(null);
				state.dataUrls = (p.images||new Array(6).fill(null));
				state.transforms = (p.transforms && Array.isArray(p.transforms) && p.transforms.length===6)
					? p.transforms.map(t=>({scale: Number(t && t.scale)||1, offsetX: Number(t && t.offsetX)||0, offsetY: Number(t && t.offsetY)||0}))
					: new Array(6).fill(0).map(()=>({scale:1, offsetX:0, offsetY:0}));
				state.dataUrls.forEach((src, i)=>{
					if(!src) return; const img = new Image(); img.onload = ()=>{ state.images[i]=img; updateTileUI(i); draw(); }; img.src = src;
				});
				draw();
			}catch(e){ console.warn('Restore state gagal', e); draw(); }
		}

		restoreState();

		function maybeShowTutorial(){
			try{
				if(localStorage.getItem(TUTORIAL_KEY)==='1') return;
				if(tutorialModal) tutorialModal.classList.remove('hidden');
			}catch(e){}
		}
		maybeShowTutorial();

		if (closeTutorialBtn){
			closeTutorialBtn.addEventListener('click', ()=>{
				try{
					if(dontShowTutorial && dontShowTutorial.checked){
						localStorage.setItem(TUTORIAL_KEY, '1');
					}
				}catch(e){}
				if(tutorialModal) tutorialModal.classList.add('hidden');
			});
		}

		function openEditor(idx){
			editingIndex = idx;
			const {tileW, tileH} = getLayout();
			editCanvas.width = tileW; editCanvas.height = tileH;
			workingTransform = Object.assign({scale:1, offsetX:0, offsetY:0}, state.transforms[idx]||{});
			zoomRange.value = String(workingTransform.scale||1);
			editorModal.classList.remove('hidden');
			drawEditor();
		}

		function closeEditor(){
			editorModal.classList.add('hidden');
			editingIndex = null; workingTransform = null; isDragging=false;
		}

		function drawEditor(){
			editCtx.fillStyle = bg.value; editCtx.fillRect(0,0,editCanvas.width,editCanvas.height);
			const img = state.images[editingIndex]; if(!img) return;
			const iw = img.naturalWidth, ih = img.naturalHeight;
			const baseScale = Math.max(editCanvas.width/iw, editCanvas.height/ih);
			const scale = (workingTransform.scale||1) * baseScale;
			const dw = iw*scale, dh = ih*scale;
			// Anchor kiri, vertikal center
			const dx = 0 + (workingTransform.offsetX||0);
			const dy = (editCanvas.height - dh)/2 + (workingTransform.offsetY||0);
			const r = 18; editCtx.save(); const cr = Math.min(r, editCanvas.width/2, editCanvas.height/2);
			editCtx.beginPath();
			editCtx.moveTo(cr, 0);
			editCtx.arcTo(editCanvas.width, 0, editCanvas.width, editCanvas.height, cr);
			editCtx.arcTo(editCanvas.width, editCanvas.height, 0, editCanvas.height, cr);
			editCtx.arcTo(0, editCanvas.height, 0, 0, cr);
			editCtx.arcTo(0, 0, editCanvas.width, 0, cr);
			editCtx.closePath();
			editCtx.clip();
			editCtx.drawImage(img, 0, 0, iw, ih, dx, dy, dw, dh);
			editCtx.restore();
		}

		function clampTransform(tf){
			const img = state.images[editingIndex]; if(!img) return tf;
			const iw = img.naturalWidth, ih = img.naturalHeight;
			const baseScale = Math.max(editCanvas.width/iw, editCanvas.height/ih);
			const scale = (tf.scale||1) * baseScale;
			const dw = iw*scale, dh = ih*scale;
			// Dengan anchor kiri dan ingin tetap cover, offsetX berada di [minX, 0]
			const minX = Math.min(0, editCanvas.width - dw);
			tf.offsetX = Math.max(minX, Math.min(0, tf.offsetX||0));
			if (dh <= editCanvas.height) {
				tf.offsetY = 0;
			} else {
				const maxY = (dh - editCanvas.height)/2;
				tf.offsetY = Math.max(-maxY, Math.min(maxY, tf.offsetY||0));
			}
			return tf;
		}

		// Editor events
		editCanvas.addEventListener('pointerdown', (e)=>{
			if(editingIndex===null) return; e.preventDefault(); isDragging = true; editCanvas.setPointerCapture(e.pointerId);
			dragStartX = e.clientX; dragStartY = e.clientY;
		});
		editCanvas.addEventListener('pointermove', (e)=>{
			if(!isDragging || editingIndex===null) return;
			const dx = e.clientX - dragStartX; const dy = e.clientY - dragStartY;
			dragStartX = e.clientX; dragStartY = e.clientY;
			// Convert movement from CSS pixels to canvas pixels
			const rect = editCanvas.getBoundingClientRect();
			const scaleX = rect.width ? (editCanvas.width / rect.width) : 1;
			const scaleY = rect.height ? (editCanvas.height / rect.height) : 1;
			workingTransform.offsetX = (workingTransform.offsetX||0) + dx * scaleX;
			workingTransform.offsetY = (workingTransform.offsetY||0) + dy * scaleY;
			clampTransform(workingTransform); drawEditor();
		});
		editCanvas.addEventListener('pointerup', (e)=>{ if(!isDragging) return; isDragging=false; editCanvas.releasePointerCapture(e.pointerId); });
		editCanvas.addEventListener('pointerleave', ()=>{ isDragging=false; });
		editCanvas.addEventListener('pointercancel', ()=>{ isDragging=false; });
		editCanvas.addEventListener('wheel', (e)=>{
			if(editingIndex===null) return; e.preventDefault();
			const delta = e.deltaY; const old = workingTransform.scale||1; let next = old * (delta>0 ? 0.95 : 1.05);
			next = Math.max(0.5, Math.min(4, next));
			workingTransform.scale = next; clampTransform(workingTransform); zoomRange.value = String(next); drawEditor();
		},{passive:false});
		zoomRange.addEventListener('input', ()=>{ if(editingIndex===null) return; workingTransform.scale = Number(zoomRange.value)||1; clampTransform(workingTransform); drawEditor(); });
		resetTransformBtn.addEventListener('click', ()=>{ if(editingIndex===null) return; workingTransform = {scale:1, offsetX:0, offsetY:0}; zoomRange.value = '1'; drawEditor(); });
		saveTransformBtn.addEventListener('click', ()=>{
			if(editingIndex===null) return;
			state.transforms[editingIndex] = { scale: Number(workingTransform.scale)||1, offsetX: Number(workingTransform.offsetX)||0, offsetY: Number(workingTransform.offsetY)||0 };
			if(state.images[editingIndex]){
				state.dataUrls[editingIndex] = makeDataUrl(state.images[editingIndex], state.transforms[editingIndex]);
				updateTileUI(editingIndex);
			}
			draw(); saveState(); closeEditor();
		});
		closeEditorBtn.addEventListener('click', ()=> closeEditor());
	})();
	</script>
</body>
</html>


