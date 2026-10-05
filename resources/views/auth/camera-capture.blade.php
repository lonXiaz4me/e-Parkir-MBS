{{-- ============================================================
     resources/views/camera-capture.blade.php
     ============================================================ --}}
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Ambil Gambar — MBS</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --gold:   #F5C518;
      --gold-d: #c0950a;
      --red:    #C0392B;
      --dark:   #0d0d0d;
      --bg:     #0f0f0f;
      --card:   #1a1a1a;
      --border: #2e2e2e;
      --text:   #f0f0f0;
      --muted:  #888;
    }
    html, body { height:100%; background:var(--bg); font-family:'IBM Plex Sans','Segoe UI',sans-serif; color:var(--text); overflow:hidden; }

    .shell { display:flex; flex-direction:column; height:100%; max-width:520px; margin:0 auto; }

    /* HEADER */
    .cam-header { display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--border); background:var(--card); flex-shrink:0; }
    .cam-header-icon { width:32px; height:32px; background:#fff8d6; border:1px solid var(--gold); border-radius:6px; display:flex; align-items:center; justify-content:center; }
    .cam-header-icon svg { width:16px; height:16px; stroke:#a07000; fill:none; stroke-width:1.8; }
    .cam-header-title { font-weight:700; font-size:15px; color:var(--text); }
    .cam-header-sub   { font-size:11px; color:var(--muted); margin-top:1px; }

    /* VIEWFINDER */
    .viewfinder-wrap { flex:1; position:relative; background:#000; overflow:hidden; }
    #video { width:100%; height:100%; object-fit:cover; display:block; }
    .vf-grid {
      position:absolute; inset:0; pointer-events:none;
      background:
        linear-gradient(to right,  transparent 33.33%, rgba(255,255,255,.07) 33.33%, rgba(255,255,255,.07) calc(33.33% + 1px), transparent calc(33.33% + 1px)),
        linear-gradient(to right,  transparent 66.66%, rgba(255,255,255,.07) 66.66%, rgba(255,255,255,.07) calc(66.66% + 1px), transparent calc(66.66% + 1px)),
        linear-gradient(to bottom, transparent 33.33%, rgba(255,255,255,.07) 33.33%, rgba(255,255,255,.07) calc(33.33% + 1px), transparent calc(33.33% + 1px)),
        linear-gradient(to bottom, transparent 66.66%, rgba(255,255,255,.07) 66.66%, rgba(255,255,255,.07) calc(66.66% + 1px), transparent calc(66.66% + 1px));
    }
    .vf-corners { position:absolute; inset:16px; pointer-events:none; }
    .vf-corners::before, .vf-corners::after,
    .vf-corner-br::before, .vf-corner-br::after {
      content:''; position:absolute; width:22px; height:22px;
      border-color:var(--gold); border-style:solid; border-width:0;
    }
    .vf-corners::before   { top:0;    left:0;  border-top-width:2px;    border-left-width:2px;   border-radius:2px 0 0 0; }
    .vf-corners::after    { top:0;    right:0; border-top-width:2px;    border-right-width:2px;  border-radius:0 2px 0 0; }
    .vf-corner-br::before { bottom:0; left:0;  border-bottom-width:2px; border-left-width:2px;   border-radius:0 0 0 2px; }
    .vf-corner-br::after  { bottom:0; right:0; border-bottom-width:2px; border-right-width:2px;  border-radius:0 0 2px 0; }

    #canvas { display:none; }
    #preview-img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:none; }

    .shutter-flash { position:absolute; inset:0; background:#fff; opacity:0; pointer-events:none; }
    .shutter-flash.flash { animation:doFlash 0.35s ease forwards; }
    @keyframes doFlash { 0%{opacity:.85} 100%{opacity:0} }

    /* STATUS BAR */
    .status-bar { position:absolute; bottom:0; left:0; right:0; padding:8px 14px; background:linear-gradient(to top,rgba(0,0,0,.75),transparent); display:flex; align-items:center; gap:6px; font-size:11px; color:rgba(255,255,255,.8); }
    .status-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; background:var(--muted); }
    .status-dot.ok   { background:#27ae60; }
    .status-dot.err  { background:var(--red); }
    .status-dot.spin { background:var(--gold); animation:pulse 1s infinite; }
    @keyframes pulse { 0%,100%{opacity:.4} 50%{opacity:1} }
    .coords-chip { display:none; align-items:center; gap:6px; background:rgba(245,197,24,.12); border:1px solid rgba(245,197,24,.3); border-radius:20px; padding:4px 10px; font-size:10px; color:rgba(255,255,255,.7); }
    .coords-chip.visible { display:flex; }
    .coords-chip svg { width:10px; height:10px; stroke:var(--gold); fill:none; stroke-width:2; }

    /* FOOTER */
    .cam-footer { flex-shrink:0; background:var(--card); border-top:1px solid var(--border); padding:16px; }
    .shutter-row { display:flex; align-items:center; justify-content:center; gap:20px; margin-bottom:12px; }
    .side-btn-ghost { width:44px; height:44px; }
    .btn-shutter { width:64px; height:64px; border-radius:50%; background:#fff; border:4px solid var(--gold); cursor:pointer; position:relative; flex-shrink:0; transition:transform .12s,box-shadow .12s; box-shadow:0 0 0 3px rgba(245,197,24,.25); }
    .btn-shutter:hover  { transform:scale(1.05); box-shadow:0 0 0 5px rgba(245,197,24,.35); }
    .btn-shutter:active { transform:scale(.95); }
    .btn-shutter::after { content:''; position:absolute; inset:6px; border-radius:50%; background:var(--gold); }
    .btn-side { width:44px; height:44px; border-radius:50%; background:rgba(255,255,255,.08); border:1px solid var(--border); cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--text); transition:background .15s; }
    .btn-side:hover { background:rgba(255,255,255,.14); }
    .btn-side svg { width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:1.8; }
    .confirm-row { display:none; gap:10px; }
    .confirm-row.visible { display:flex; }
    .btn-retake { flex:1; background:transparent; border:1.5px solid var(--border); border-radius:6px; padding:11px; font-family:inherit; font-weight:600; font-size:13px; color:var(--muted); cursor:pointer; letter-spacing:.05em; text-transform:uppercase; transition:border-color .15s,color .15s; }
    .btn-retake:hover { border-color:#666; color:var(--text); }
    .btn-confirm { flex:2; background:var(--gold); border:none; border-radius:6px; padding:11px; font-family:inherit; font-weight:700; font-size:13px; color:var(--dark); cursor:pointer; letter-spacing:.06em; text-transform:uppercase; position:relative; overflow:hidden; transition:background .15s; }
    .btn-confirm:hover { background:#ffd22e; }
    .btn-confirm::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; background:var(--red); border-radius:6px 0 0 6px; }

    /* PERMISSION OVERLAY */
    .perm-overlay { position:absolute; inset:0; background:rgba(0,0,0,.92); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:16px; padding:32px 24px; text-align:center; z-index:10; }
    .perm-overlay.hidden { display:none; }
    .perm-icon { width:52px; height:52px; background:#fff8d6; border:1.5px solid var(--gold); border-radius:12px; display:flex; align-items:center; justify-content:center; }
    .perm-icon svg { width:24px; height:24px; stroke:#a07000; fill:none; stroke-width:1.8; }
    .perm-title { font-size:17px; font-weight:700; color:var(--text); }
    .perm-desc  { font-size:13px; color:var(--muted); line-height:1.6; }
    .perm-list  { text-align:left; width:100%; }
    .perm-item  { display:flex; align-items:center; gap:10px; padding:8px 12px; background:rgba(255,255,255,.04); border-radius:6px; margin-bottom:6px; }
    .perm-item svg { width:16px; height:16px; stroke:var(--gold); fill:none; stroke-width:1.8; flex-shrink:0; }
    .perm-item span { font-size:12px; color:var(--text); line-height:1.4; }
    .perm-item.hidden { display:none; }
    .btn-grant { width:100%; background:var(--gold); border:none; border-radius:6px; padding:13px; font-family:inherit; font-weight:700; font-size:14px; color:var(--dark); cursor:pointer; letter-spacing:.07em; text-transform:uppercase; transition:background .15s; }
    .btn-grant:hover { background:#ffd22e; }
    .btn-close-perm { background:none; border:none; color:var(--muted); font-size:12px; cursor:pointer; text-decoration:underline; font-family:inherit; }

    /* ERROR OVERLAY */
    .error-overlay { position:absolute; inset:0; background:rgba(0,0,0,.92); display:none; flex-direction:column; align-items:center; justify-content:center; gap:14px; padding:32px 24px; text-align:center; z-index:10; }
    .error-overlay.visible { display:flex; }
    .err-icon  { font-size:40px; }
    .err-title { font-size:16px; font-weight:700; color:#e07070; }
    .err-msg   { font-size:13px; color:var(--muted); line-height:1.5; }
    .btn-retry { background:var(--red); border:none; border-radius:6px; padding:10px 24px; font-family:inherit; font-weight:600; font-size:13px; color:#fff; cursor:pointer; letter-spacing:.06em; text-transform:uppercase; }
    .btn-retry:hover { background:#a93226; }
  </style>
</head>
<body>
<div class="shell">

  <!-- HEADER -->
  <div class="cam-header">
    <div class="cam-header-icon">
      <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
    </div>
    <div>
      <div class="cam-header-title" id="headerTitle">Ambil Gambar</div>
      <div class="cam-header-sub"  id="headerSub">Pastikan dokumen kelihatan jelas.</div>
    </div>
  </div>

  <!-- VIEWFINDER -->
  <div class="viewfinder-wrap" id="viewfinderWrap">
    <video id="video" autoplay playsinline muted></video>
    <canvas id="canvas"></canvas>
    <img id="preview-img" alt="Gambar yang diambil">
    <div class="vf-grid"></div>
    <div class="vf-corners"><div class="vf-corner-br"></div></div>
    <div class="shutter-flash" id="shutterFlash"></div>

    <!-- Status bar -->
    <div class="status-bar" id="statusBar" style="display:none;">
      <div class="status-dot spin" id="geoStatusDot"></div>
      <span id="geoStatusText">Mendapatkan lokasi…</span>
      <span class="coords-chip" id="coordsChip">
        <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <span id="coordsText"></span>
      </span>
    </div>

    <!-- Permission overlay -->
    <div class="perm-overlay" id="permOverlay">
      <div class="perm-icon">
        <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
      </div>
      <div class="perm-title">Kebenaran Diperlukan</div>
      <div class="perm-desc" id="permDesc">Untuk mengambil gambar, aplikasi memerlukan akses berikut:</div>
      <div class="perm-list">
        <div class="perm-item" id="permItemCamera">
          <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
          <span><strong>Kamera</strong> — untuk mengambil gambar dokumen</span>
        </div>
        <div class="perm-item" id="permItemGPS">
          <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span><strong>Lokasi</strong> — koordinat GPS akan disimpan bersama gambar</span>
        </div>
      </div>
      <button type="button" class="btn-grant" onclick="requestPermissions()">Benarkan Akses</button>
      <button type="button" class="btn-close-perm" onclick="window.close()">Batal &amp; Tutup</button>
    </div>

    <!-- Error overlay -->
    <div class="error-overlay" id="errorOverlay">
      <div class="err-icon">⚠️</div>
      <div class="err-title" id="errTitle">Ralat Kamera</div>
      <div class="err-msg"   id="errMsg">Kamera tidak dapat diakses.</div>
      <button type="button" class="btn-retry" onclick="retryCamera()">Cuba Semula</button>
      <button type="button" class="btn-close-perm" style="margin-top:6px;" onclick="window.close()">Tutup</button>
    </div>
  </div>

  <!-- FOOTER CONTROLS -->
  <div class="cam-footer">
    <div class="shutter-row" id="shutterRow">
      <button type="button" class="btn-side" id="btnFlip" title="Tukar kamera" onclick="flipCamera()">
        <svg viewBox="0 0 24 24"><path d="M1 4v6h6"/><path d="M23 20v-6h-6"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4-4.64 4.36A9 9 0 0 1 3.51 15"/></svg>
      </button>
      <button type="button" class="btn-shutter" id="btnShutter" title="Ambil gambar" onclick="capturePhoto()"></button>
      <div class="side-btn-ghost"></div>
    </div>
    <div class="confirm-row" id="confirmRow">
      <button type="button" class="btn-retake" onclick="retakePhoto()">↩ Ambil Semula</button>
      <button type="button" class="btn-confirm" id="btnConfirm" onclick="confirmPhoto()">Gunakan Gambar →</button>
    </div>
  </div>

</div>

<script>
// ── Read URL params immediately ────────────────────────────────
const urlParams  = new URLSearchParams(window.location.search);
const FIELD_KEY  = urlParams.get('field') || 'loc';  // ssm | ic | lic | loc
const NEEDS_GPS  = urlParams.get('gps') === '1';      // true only for loc

// ── State ──────────────────────────────────────────────────────
let stream          = null;
let facingMode      = 'environment';
let capturedDataUrl = null;
let coordsString    = '';   // filled by geolocation if NEEDS_GPS

// ── DOM refs ───────────────────────────────────────────────────
const video        = document.getElementById('video');
const canvas       = document.getElementById('canvas');
const previewImg   = document.getElementById('preview-img');
const shutterFlash = document.getElementById('shutterFlash');
const shutterRow   = document.getElementById('shutterRow');
const confirmRow   = document.getElementById('confirmRow');
const permOverlay  = document.getElementById('permOverlay');
const errorOverlay = document.getElementById('errorOverlay');
const statusBar    = document.getElementById('statusBar');
const geoStatusDot = document.getElementById('geoStatusDot');
const geoStatusText= document.getElementById('geoStatusText');
const coordsChip   = document.getElementById('coordsChip');
const coordsText   = document.getElementById('coordsText');

// ── On load: customise UI based on field + GPS params ──────────
window.addEventListener('load', function () {
  // Update header text
  const titleMap = {
    ssm: 'Ambil Gambar SSM',
    ic:  'Ambil Gambar IC',
    lic: 'Ambil Gambar Lesen Perniagaan',
    loc: 'Ambil Gambar Lokasi',
  };
  const subMap = {
    ssm: 'Pastikan dokumen SSM kelihatan jelas.',
    ic:  'Pastikan IC pemohon kelihatan jelas.',
    lic: 'Pastikan lesen perniagaan kelihatan jelas.',
    loc: 'Pastikan lokasi yang ingin disewa kelihatan jelas.',
  };
  document.getElementById('headerTitle').textContent = titleMap[FIELD_KEY] || titleMap.loc;
  document.getElementById('headerSub').textContent   = subMap[FIELD_KEY]   || subMap.loc;

  // Hide GPS permission item if GPS not needed
  if (!NEEDS_GPS) {
    const gpsItem = document.getElementById('permItemGPS');
    if (gpsItem) gpsItem.classList.add('hidden');
    document.getElementById('permDesc').textContent = 'Untuk mengambil gambar, aplikasi memerlukan akses kamera:';
  }

  // Show permission overlay
  permOverlay.classList.remove('hidden');
});

// ── Request permissions & start camera ────────────────────────
async function requestPermissions() {
  permOverlay.classList.add('hidden');
  await startCamera();
  if (NEEDS_GPS) {
    statusBar.style.display = 'flex';
    startGeolocation();
  }
}

// ── Start camera stream ────────────────────────────────────────
async function startCamera() {
  try {
    if (stream) stream.getTracks().forEach(t => t.stop());
    stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: facingMode, width: { ideal: 1920 }, height: { ideal: 1080 } },
      audio: false
    });
    video.srcObject = stream;
    errorOverlay.classList.remove('visible');
  } catch (err) {
    showCameraError(err);
  }
}

// ── Flip camera ────────────────────────────────────────────────
async function flipCamera() {
  facingMode = facingMode === 'environment' ? 'user' : 'environment';
  await startCamera();
}

// ── Geolocation (only called when NEEDS_GPS = true) ────────────
function startGeolocation() {
  if (!navigator.geolocation) {
    setGeoStatus('err', 'GPS tidak disokong peranti ini.');
    return;
  }
  setGeoStatus('spin', 'Mendapatkan lokasi…');
  navigator.geolocation.getCurrentPosition(
    function (pos) {
      const lat = pos.coords.latitude.toFixed(6);
      const lng = pos.coords.longitude.toFixed(6);
      coordsString = lat + ', ' + lng;
      coordsText.textContent = '📍 ' + coordsString;
      coordsChip.classList.add('visible');
      setGeoStatus('ok', 'Lokasi diperoleh.');
    },
    function () {
      coordsString = '';
      setGeoStatus('err', 'GPS gagal. Gambar tetap boleh dihantar.');
    },
    { enableHighAccuracy: true, timeout: 15000 }
  );
}

function setGeoStatus(state, text) {
  geoStatusDot.className = 'status-dot ' + state;
  geoStatusText.textContent = text;
}

// ── Capture photo ──────────────────────────────────────────────
//
// FIX #14: Previously this always exported at a fixed quality of 0.88 with
// no size check. On high-resolution phone cameras (4000x3000 or higher),
// this can easily produce a JPEG well over the server's 5MB limit
// (validated in ApplicationController as 'max:5120'). The user would only
// find out AFTER going through the entire camera capture + confirm flow,
// when the form submission gets rejected with a generic file-size error —
// a frustrating dead end with no indication that the photo itself was the
// problem.
//
// FIX: Cap the maximum dimension to a sane value (1920px on the long edge,
// which is more than sufficient for legible document photos) and
// progressively step down JPEG quality until the encoded size is
// comfortably under the server limit. This guarantees every photo that
// leaves this page will pass server-side validation.
const MAX_DIMENSION   = 1920;        // px, long edge cap
const MAX_FILE_BYTES  = 4.5 * 1024 * 1024; // 4.5MB safety margin under the 5MB server limit
const MIN_JPEG_QUALITY = 0.5;        // never go below this — avoid visible artifacts

function dataUrlSizeInBytes(dataUrl) {
  // Rough byte size of a base64 data URL: strip the header, then each base64
  // char represents 6 bits, so length * 0.75 ≈ decoded byte size.
  const base64 = dataUrl.split(',')[1] || '';
  return Math.ceil(base64.length * 0.75);
}

function capturePhoto() {
  if (!stream) return;

  // Flash effect
  shutterFlash.classList.remove('flash');
  void shutterFlash.offsetWidth;
  shutterFlash.classList.add('flash');

  // Draw frame to canvas — scale down if the source exceeds MAX_DIMENSION
  const srcW = video.videoWidth  || 1280;
  const srcH = video.videoHeight || 720;

  let vw = srcW;
  let vh = srcH;
  if (Math.max(vw, vh) > MAX_DIMENSION) {
    const scale = MAX_DIMENSION / Math.max(vw, vh);
    vw = Math.round(vw * scale);
    vh = Math.round(vh * scale);
  }

  canvas.width  = vw;
  canvas.height = vh;
  const ctx = canvas.getContext('2d');

  // Mirror front camera so preview isn't backwards
  if (facingMode === 'user') {
    ctx.translate(vw, 0);
    ctx.scale(-1, 1);
  }
  ctx.drawImage(video, 0, 0, vw, vh);

  // Progressively reduce quality until under the size limit
  let quality = 0.88;
  capturedDataUrl = canvas.toDataURL('image/jpeg', quality);

  while (dataUrlSizeInBytes(capturedDataUrl) > MAX_FILE_BYTES && quality > MIN_JPEG_QUALITY) {
    quality = Math.max(MIN_JPEG_QUALITY, quality - 0.1);
    capturedDataUrl = canvas.toDataURL('image/jpeg', quality);
  }

  // Show snapshot preview
  previewImg.src            = capturedDataUrl;
  previewImg.style.display  = 'block';
  video.style.display       = 'none';

  // Swap controls
  shutterRow.style.display = 'none';
  confirmRow.classList.add('visible');
}

// ── Retake ─────────────────────────────────────────────────────
function retakePhoto() {
  capturedDataUrl           = null;
  previewImg.style.display  = 'none';
  video.style.display       = 'block';
  shutterRow.style.display  = '';
  confirmRow.classList.remove('visible');
}

// ── Confirm: send captured image back to parent form ───────────
function confirmPhoto() {
  if (!capturedDataUrl) return;

  const btn = document.getElementById('btnConfirm');
  btn.textContent        = 'Menghantar…';
  btn.style.opacity      = '.7';
  btn.style.pointerEvents= 'none';

  const fileName = FIELD_KEY + '_' + Date.now() + '.jpg';

  // Send to parent window via postMessage
  if (window.opener) {
    window.opener.postMessage({
      type:         'CAMERA_CAPTURE_DONE',
      dataUrl:      capturedDataUrl,
      coordsString: NEEDS_GPS ? coordsString : '',
      fileName:     fileName,
      field:        FIELD_KEY,
    }, window.location.origin);
  }

  setTimeout(function () { window.close(); }, 400);
}

// ── Camera error handling ──────────────────────────────────────
function showCameraError(err) {
  let msg = 'Kamera tidak dapat diakses. Pastikan anda telah membenarkan akses kamera.';
  if (err && err.name === 'NotAllowedError')  msg = 'Akses kamera dinafikan. Sila benarkan kebenaran kamera dalam tetapan pelayar anda.';
  if (err && err.name === 'NotFoundError')    msg = 'Tiada kamera ditemui pada peranti ini.';
  if (err && err.name === 'NotReadableError') msg = 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi lain dan cuba semula.';
  document.getElementById('errTitle').textContent = 'Ralat Kamera';
  document.getElementById('errMsg').textContent   = msg;
  errorOverlay.classList.add('visible');
}

async function retryCamera() {
  errorOverlay.classList.remove('visible');
  await startCamera();
}

// ── Cleanup stream on window close ────────────────────────────
window.addEventListener('beforeunload', function () {
  if (stream) stream.getTracks().forEach(t => t.stop());
});
</script>
</body>
</html>