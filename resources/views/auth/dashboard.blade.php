<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Papan Pemuka — Sistem Petak Sewa MBS</title>
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
</head>
<body>

<div class="shell">

  @include('auth.partials.sidebar')

  <!-- TOPBAR -->
  <div class="topbar">
    <span class="breadcrumb">
      <a href="#" data-i18n="breadcrumbHome">Laman Utama</a>
      <span class="sep">/</span>
      <span class="current" data-i18n="crumbDashboard">Papan Pemuka</span>
    </span>
    <div class="topbar-right">
      @include('auth.partials.notif-panel')
    </div>
  </div>

  @include('auth.partials.confirm-modal')

  <!-- MAIN -->
  <div class="main">
    <div class="page-title" data-i18n="dashTitle">Papan Pemuka</div>
    <div class="page-sub" data-i18n="dashSub">Ringkasan semua permohonan anda di sini.</div>

    {{-- FIX #6: Variables passed from DashboardController via compact() --}}
    @php
      $getPercent = fn($count) => $totalCount > 0 ? ($count / $totalCount) * 100 : 0;
    @endphp

    <!-- STAT CARDS -->
    <div class="page-title"><span data-i18n="dashTotalApps">Jumlah Semua Permohonan</span>: {{ $totalCount }}</div>
    <div class="stat-row">
      <div class="stat">
        <div class="stat-num">{{ $pendingCount }}</div>
        <div class="stat-lbl" data-i18n="statPending">Proses</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="background:#e6a800;width:{{ $getPercent($pendingCount) }}%"></div></div>
      </div>
      <div class="stat">
        <div class="stat-num">{{ $approvedCount }}</div>
        <div class="stat-lbl" data-i18n="statApproved">Diluluskan</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="background:#3B6D11;width:{{ $getPercent($approvedCount) }}%"></div></div>
      </div>
      <div class="stat">
        <div class="stat-num">{{ $completedCount }}</div>
        <div class="stat-lbl" data-i18n="statCompleted">Selesai</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="background:#007bff;width:{{ $getPercent($completedCount) }}%"></div></div>
      </div>
      <div class="stat">
        <div class="stat-num">{{ $rejectedCount }}</div>
        <div class="stat-lbl" data-i18n="statRejected">Ditolak</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="background:#c0222a;width:{{ $getPercent($rejectedCount) }}%"></div></div>
      </div>
    </div>

    <div class="page-title" data-i18n="dashStatusTitle">Status Permohonan</div>
    <div class="page-sub" data-i18n="dashStatusSub">Semak dan urus semua permohonan anda di sini.</div>

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" data-i18n-placeholder="searchPlaceholder" placeholder="Cari no. permohonan, jenis…"/>
      </div>
      <button class="filter-btn active" data-filter="all" data-i18n="filterAll">Semua</button>
      <button class="filter-btn" data-filter="pending" data-i18n="filterPending">Dalam Proses</button>
      <button class="filter-btn" data-filter="approved" data-i18n="filterApproved">Diluluskan</button>
      <button class="filter-btn" data-filter="completed" data-i18n="filterCompleted">Selesai</button>
      <button class="filter-btn" data-filter="not_approved" data-i18n="filterRejected">Ditolak</button>
      <div class="sort-wrap">
        <button class="sort-btn active" data-sort="newest">
          <span class="sort-arrow">↓</span><span class="sort-label" data-i18n="sortNewest">Terbaru</span>
        </button>
        <div class="sort-divider"></div>
        <button class="sort-btn" data-sort="oldest">
          <span class="sort-arrow">↑</span><span class="sort-label" data-i18n="sortOldest">Tertua</span>
        </button>
      </div>
      <a href="{{ route('application') }}" class="btn-new" data-i18n="btnNewApplication">+ Permohonan Baru</a>
    </div>

    <!-- SPLIT: card list + detail panel -->
    <div class="split">

      <!-- LEFT: APPLICATION LIST -->
      <div class="user-applications">
        @if($allApps->isEmpty())
          <p data-i18n="noApplicationsFound">Tiada permohonan dijumpai.</p>
        @else
          @foreach($allApps as $app)
            @php
              $statusMap = [
                'pending'      => ['badge'=>'b-yellow','card'=>'c-yellow','bar'=>'#F5C518','width'=>'25%', 'labelKey'=>'statusPending',  'btn1'=>['key'=>'btnViewDocs',   'url'=>'#'],        'btn2'=>['key'=>'btnCheckStatus',  'url'=>'#']],
                'approved'     => ['badge'=>'b-green', 'card'=>'c-green', 'bar'=>'#28a745','width'=>'50%', 'labelKey'=>'statusApproved', 'btn1'=>['key'=>'btnViewDocs',   'url'=>'#'],        'btn2'=>['key'=>'btnPayNow',       'url'=>route('payment.index',['id'=>$app->app_no])]],
                'not_approved' => ['badge'=>'b-red',   'card'=>'c-red',   'bar'=>'#dc3545','width'=>'100%','labelKey'=>'statusRejected', 'btn1'=>['key'=>'btnViewReason', 'url'=>'#'],        'btn2'=>['key'=>'btnReapply', 'url'=>route('application', ['reapply'=>$app->app_no])]],
                'completed'    => ['badge'=>'b-blue',  'card'=>'c-blue',  'bar'=>'#007bff','width'=>'100%','labelKey'=>'statusCompleted','btn1'=>['key'=>'btnViewDocs',   'url'=>'#'],        'btn2'=>['key'=>'btnPrintLicence', 'url'=>'#']],
              ];
              $current = $statusMap[$app->app_status] ?? ['badge'=>'b-gray','card'=>'c-gray','bar'=>'#6c757d','width'=>'0%','labelKey'=>'statusUnknown','btn1'=>['key'=>'btnHelp','url'=>'#'],'btn2'=>['key'=>'btnContact','url'=>'#']];
              preg_match('/(\d+)$/', $app->app_no, $m);
              $sortKey = isset($m[1]) ? (int)$m[1] : 0;

              // FIX #5: Only safe display fields — no file paths in HTML
              // FIX #8: Added not_approved_reason and app_status_msg so the
              //         detail panel can show the rejection reason to the user
              $safeApp = [
                'app_no'               => $app->app_no,
                'app_status'           => $app->app_status,
                'company_name'         => $app->company_name,
                'ssm_no'               => $app->ssm_no,
                'category'             => $app->category,
                'type_of_business'     => $app->type_of_business,
                'location'             => $app->location,
                'total_parking'        => $app->total_parking,
                // FIX #8: Pass rejection reason through to JS
                'not_approved_reason'  => $app->not_approved_reason ?? null,
                'app_status_msg'       => $app->app_status_msg ?? null,
                // Boolean flags only — never expose actual storage paths
                'has_ssm'              => !empty($app->ssm_img),
                'has_ic'               => !empty($app->ic_img),
                'has_licence'          => !empty($app->licence_img),
                'has_location'         => !empty($app->location_img),
              ];
            @endphp

            <div class="app-card {{ $current['card'] }}"
                 data-status="{{ $app->app_status }}"
                 data-sort-key="{{ $sortKey }}"
                 data-app="{{ json_encode($safeApp) }}"
                 data-label-key="{{ $current['labelKey'] }}"
                 data-badge="{{ $current['badge'] }}"
                 data-btn1-key="{{ $current['btn1']['key'] }}"
                 data-btn1-url="{{ $current['btn1']['url'] }}"
                 data-btn2-key="{{ $current['btn2']['key'] }}"
                 data-btn2-url="{{ $current['btn2']['url'] }}"
                 onclick="showDetails(this)">
              <div class="card-top">
                <div>
                  <div class="app-id"><span data-i18n="dpAppNoPrefix">No. Permohonan</span>: {{ $app->app_no }}</div>
                  <div class="app-type">{{ $app->company_name }}</div>
                </div>
                <span class="sbadge {{ $current['badge'] }}" data-i18n="{{ $current['labelKey'] }}">{{ $current['labelKey'] }}</span>
              </div>
              <div class="card-meta">
                <div class="meta-item">
                  <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  {{ $app->location }}
                </div>
              </div>

              {{--
                FIX #8: Show rejection reason directly on the card so the user
                can see it without having to open the detail panel.
                Only rendered when status is not_approved AND a reason exists.
              --}}
              @if($app->app_status === 'not_approved' && !empty($app->not_approved_reason))
                <div style="
                  margin-top: 8px;
                  background: #FCEBEB;
                  border: 1px solid #f5c1c1;
                  border-radius: 4px;
                  padding: 8px 10px;
                  font-size: 11px;
                  color: #791F1F;
                  line-height: 1.5;
                ">
                  <strong data-i18n="dpRejectionHeader">Sebab Penolakan</strong>: {{ $app->not_approved_reason }}
                </div>
              @endif

              <div class="prog-track" style="margin-top:10px;">
                <div class="prog-fill" style="background:{{ $current['bar'] }};width:{{ $current['width'] }}"></div>
              </div>
              <div class="prog-steps">
                <span class="ps done" data-i18n="progSubmit">Hantar</span>
                <span class="ps {{ $app->app_status == 'pending' ? 'cur' : ($app->app_status != 'pending' ? 'done' : '') }}" data-i18n="progApprove">Lulus</span>
                <span class="ps {{ $app->app_status == 'approved' ? 'cur' : ($app->app_status == 'completed' ? 'done' : '') }}" data-i18n="progPay">Bayar</span>
                <span class="ps {{ $app->app_status == 'completed' ? 'cur' : '' }}" data-i18n="progDone">Selesai</span>
              </div>
              <div class="card-foot">
                <a href="#" class="act card-btn1"></a>
                <a href="#" class="act pri card-btn2"></a>
              </div>
            </div>
          @endforeach
        @endif
      </div>

      <!-- RIGHT: DETAIL PANEL -->
      <div id="detail-panel" class="detail-panel">
        <div class="dp-header">
          <div>
            <div class="dp-id" id="panel-app-no"></div>
            <div class="dp-title" id="panel-company-name"></div>
          </div>
          <div class="dp-header-right">
            <span class="sbadge" id="panel-status-badge"></span>
            <button class="dp-close-btn" onclick="closeDetailPanel()" title="Tutup">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="dp-body">
          <div class="dp-section">
            <div class="dp-section-label" data-i18n="dpSectionStatus">Status Aliran Kerja</div>
            <div class="timeline" id="dynamic-timeline"></div>
          </div>

          {{-- FIX #8: Rejection reason section — hidden by default, shown via JS --}}
          <div class="dp-section" id="rejection-section" style="display:none;">
            <div class="dp-section-label" data-i18n="dpSectionRejection">Sebab Penolakan</div>
            <div id="rejection-reason-box" style="
              background: #FCEBEB;
              border: 1px solid #f5c1c1;
              border-radius: 5px;
              padding: 12px 14px;
              font-size: 12px;
              color: #791F1F;
              line-height: 1.6;
            "></div>
            <div id="status-msg-box" style="
              margin-top: 8px;
              background: #fff8f8;
              border: 1px solid #f5c1c1;
              border-radius: 5px;
              padding: 10px 14px;
              font-size: 11px;
              color: #888;
              line-height: 1.5;
              display: none;
            "></div>
          </div>

          <div class="dp-section">
            <div class="dp-section-label" data-i18n-html data-i18n="dpSectionCompany">Maklumat Syarikat &amp; Perniagaan</div>
            <div class="info-row"><span class="info-key" data-i18n="labelCompanyName">Nama Syarikat</span><span class="info-val" id="info-company"></span></div>
            <div class="info-row"><span class="info-key" data-i18n="labelSsmNo">No. SSM</span><span class="info-val" id="info-ssm"></span></div>
            <div class="info-row"><span class="info-key" data-i18n="labelCategory">Kategori</span><span class="info-val" id="info-category"></span></div>
            <div class="info-row"><span class="info-key" data-i18n="labelBusinessType">Jenis Perniagaan</span><span class="info-val" id="info-type"></span></div>
          </div>
          <div class="dp-section">
            <div class="dp-section-label" data-i18n-html data-i18n="dpSectionParking">Maklumat Lokasi &amp; Parkir</div>
            <div class="info-row"><span class="info-key" data-i18n="labelLocation">Lokasi</span><span class="info-val" id="info-location" style="font-weight:700;letter-spacing:0.06em"></span></div>
            <div class="info-row"><span class="info-key" data-i18n="labelTotalParking">Jumlah Petak</span><span class="info-val" id="info-parking"></span></div>
          </div>
          <div class="dp-section">
            <div class="dp-section-label" data-i18n="dpSectionDocs">Muat Naik Dokumen Sokongan</div>
            <div class="checklist" id="doc-checklist"></div>
          </div>
        </div>
        <div class="dp-footer">
          <button class="dp-btn" data-i18n="dpDownloadPdf" onclick="generatePDF()">Muat Turun PDF</button>
          <button class="dp-btn pri" data-i18n="dpContactOfficer" onclick="contactOfficer()">Hubungi Pegawai</button>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
// ── CSRF token ────────────────────────────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content
          ?? '{{ csrf_token() }}';

// ─────────────────────────────────────────────────────────────────────────────
// APPLICATION DETAIL PANEL
// ─────────────────────────────────────────────────────────────────────────────
let currentSelectedApp = null;

// FIX #8: Accept optional rejectionReason so the timeline can display it
function updateTimeline(status, rejectionReason) {
  const container = document.getElementById('dynamic-timeline');
  const icons = {
    done:          '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
    active:        '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/></svg>',
    pending:       '<svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
    fail:          '<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
    check_outline: '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
  };

  function item(type, stage, date, note = '', isBold = false, isLast = false) {
    const stageClass = (type === 'pending' || type === 'check_outline') ? 'pending' : (type === 'fail' ? 'fail' : '');
    const dateStyle  = (type === 'pending' || type === 'check_outline') ? 'style="color:#ddd"' : '';
    const bottomPad  = isLast ? 'style="padding-bottom:0"' : '';
    const boldStyle  = isBold ? '' : 'style="font-weight:400"';
    return `
      <div class="tl-item">
        <div class="tl-dot ${type}">${icons[type]}</div>
        <div class="tl-content" ${bottomPad}>
          <div class="tl-stage ${stageClass}" ${boldStyle}>${stage}</div>
          <div class="tl-date" ${dateStyle}>${date}</div>
          ${note ? `<div class="tl-note">${note}</div>` : ''}
        </div>
      </div>`;
  }

  const t = window.MBS_I18N ? window.MBS_I18N.t.bind(window.MBS_I18N) : (k => k);
  let html = item('done', t('tlSubmitted'), t('tlDone') + ' · ' + t('tlSystem'), '', true);

  if (status === 'pending') {
    html += item('active', t('tlAwaitingApproval'), t('tlUnderReview'), t('tlNotifProcessing'), false);
  } else if (status === 'not_approved') {
    // FIX #8: Show the actual rejection reason in the timeline note instead of
    // the generic "Sila semak alasan penolakan melalui emel rasmi." message.
    const reasonNote = rejectionReason
      ? `<strong>Sebab:</strong> ${rejectionReason}`
      : t('tlContactOfficer');
    html += item('fail', t('tlRejected'), t('tlNotApproved'), reasonNote, true, true);
    container.innerHTML = html;
    return;
  } else {
    html += item('done', t('tlDocsVerified'), t('tlDone'), '', true);
  }

  if (status === 'approved') {
    html += item('active', t('tlBillPayment'), t('tlAwaitingPayment'), t('tlActivatePermit'), false);
  } else if (status === 'completed') {
    html += item('done', t('tlPaymentDone'), t('tlDone'), '', true);
  } else {
    html += item('pending', t('tlBillPayment'), t('tlNotYet'), '', false);
  }

  html += status === 'completed'
    ? item('done', t('tlCompleted'), t('tlLicenceActive'), '', true, true)
    : item('check_outline', t('tlCompleted'), t('tlNotYet'), '', false, true);

  container.innerHTML = html;
}

function showDetails(element) {
  const app        = JSON.parse(element.dataset.app);
  const label      = element.dataset.label;
  const badgeClass = element.dataset.badge;

  currentSelectedApp = app;

  document.getElementById('detail-panel').style.display = 'block';
  document.getElementById('panel-app-no').textContent       = (window.MBS_I18N ? window.MBS_I18N.t('dpAppNoPrefix') : 'No. Permohonan') + ': ' + app.app_no;
  document.getElementById('panel-company-name').textContent = app.company_name;

  const labelKey = element.dataset.labelKey;
  const badge    = document.getElementById('panel-status-badge');
  badge.textContent = window.MBS_I18N ? window.MBS_I18N.t(labelKey) : labelKey;
  badge.className   = 'sbadge ' + badgeClass;

  document.getElementById('info-company').textContent  = app.company_name;
  document.getElementById('info-ssm').textContent      = app.ssm_no;
  document.getElementById('info-category').textContent = app.category;
  document.getElementById('info-type').textContent     = app.type_of_business;
  document.getElementById('info-location').textContent = app.location;
  document.getElementById('info-parking').textContent  = app.total_parking + ' ' + (window.MBS_I18N ? window.MBS_I18N.t('unitLots') : 'Petak');

  // FIX #8: Show or hide the rejection reason section in the detail panel
  const rejectionSection = document.getElementById('rejection-section');
  const rejectionBox     = document.getElementById('rejection-reason-box');
  const statusMsgBox     = document.getElementById('status-msg-box');

  if (app.app_status === 'not_approved') {
    rejectionSection.style.display = 'block';

    if (app.not_approved_reason) {
      rejectionBox.innerHTML = `<strong>${window.MBS_I18N ? window.MBS_I18N.t('dpRejectionHeader') : 'Sebab Penolakan'}:</strong><br>${app.not_approved_reason}`;
    } else {
      rejectionBox.innerHTML = window.MBS_I18N ? window.MBS_I18N.t('dpNoRejectionReason') : 'Tiada sebab penolakan dinyatakan. Sila hubungi pegawai untuk maklumat lanjut.';
    }

    if (app.app_status_msg) {
      statusMsgBox.style.display = 'block';
      statusMsgBox.innerHTML     = `<em>${window.MBS_I18N ? window.MBS_I18N.t('dpOfficerNote') : 'Nota pegawai'}:</em> ${app.app_status_msg}`;
    } else {
      statusMsgBox.style.display = 'none';
    }
  } else {
    // Hide rejection section for all other statuses
    rejectionSection.style.display = 'none';
    rejectionBox.innerHTML         = '';
    statusMsgBox.style.display     = 'none';
    statusMsgBox.innerHTML         = '';
  }

  const docs = [
    { name: window.MBS_I18N ? window.MBS_I18N.t('docNameSsm') : 'Dokumen SSM',   uploaded: app.has_ssm },
    { name: window.MBS_I18N ? window.MBS_I18N.t('docNameIc') : 'Salinan IC',    uploaded: app.has_ic },
    { name: window.MBS_I18N ? window.MBS_I18N.t('docNameLocation') : 'Foto Lokasi',   uploaded: app.has_location },
    { name: window.MBS_I18N ? window.MBS_I18N.t('docNameLicence') : 'Salinan Lesen', uploaded: app.has_licence },
  ];

  document.getElementById('doc-checklist').innerHTML = docs.map(doc => `
    <div class="check-item">
      <div class="ck ${doc.uploaded ? 'ok' : 'fail'}">
        ${doc.uploaded
          ? '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>'
          : '<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'}
      </div>
      ${doc.name}
      ${doc.uploaded
        ? '<span style="color:#27ae60;font-size:9px;margin-left:5px">✓ ' + (window.MBS_I18N ? window.MBS_I18N.t('docUploaded') : 'Dimuat naik') + '</span>'
        : '<span style="color:#C0392B;font-size:9px;margin-left:5px">(' + (window.MBS_I18N ? window.MBS_I18N.t('docMissing') : 'Tiada Fail') + ')</span>'}
    </div>`
  ).join('');

  document.querySelectorAll('.app-card').forEach(c => c.classList.remove('selected'));
  element.classList.add('selected');

  // Populate card-foot action buttons with translated text
  const btn1El  = element.querySelector('.card-btn1');
  const btn2El  = element.querySelector('.card-btn2');
  const btn1Key = element.dataset.btn1Key;
  const btn2Key = element.dataset.btn2Key;
  if (btn1El) {
    btn1El.textContent = window.MBS_I18N ? window.MBS_I18N.t(btn1Key) : btn1Key;
    btn1El.href        = element.dataset.btn1Url || '#';
  }
  if (btn2El) {
    btn2El.textContent = window.MBS_I18N ? window.MBS_I18N.t(btn2Key) : btn2Key;
    btn2El.href        = element.dataset.btn2Url || '#';
  }

  // FIX #8: Pass the rejection reason to updateTimeline
  updateTimeline(app.app_status, app.not_approved_reason || null);

  if (window.innerWidth <= 768) {
    document.getElementById('detail-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

function closeDetailPanel() {
  document.getElementById('detail-panel').style.display = 'none';
  document.querySelectorAll('.app-card').forEach(c => c.classList.remove('selected'));
  currentSelectedApp = null;
}

function generatePDF() {
  if (!currentSelectedApp) { alert(window.MBS_I18N ? window.MBS_I18N.t('alertSelectApp') : 'Sila pilih permohonan terlebih dahulu.'); return; }
  window.open(`/application/download/${currentSelectedApp.app_no}`, '_blank');
}

// FIX #16: "Hubungi Pegawai" previously had no onclick handler at all — it
// looked clickable (styled as a primary button) but did nothing. Opens the
// user's email client with a pre-filled subject/body referencing the
// selected application, so the officer receiving it has context immediately.
function contactOfficer() {
  if (!currentSelectedApp) {
    alert(window.MBS_I18N ? window.MBS_I18N.t('alertSelectApp') : 'Sila pilih permohonan terlebih dahulu.');
    return;
  }

  const subject = encodeURIComponent(`${window.MBS_I18N ? window.MBS_I18N.t('emailSubject') : 'Pertanyaan Permohonan'} — ${currentSelectedApp.app_no}`);
  const body = encodeURIComponent(
    (window.MBS_I18N ? window.MBS_I18N.t('emailGreeting') : 'Salam,') + '\n\n' +
    (window.MBS_I18N ? window.MBS_I18N.t('emailIntro') : 'Saya ingin bertanya mengenai permohonan berikut:') + '\n\n' +
    (window.MBS_I18N ? window.MBS_I18N.t('dpAppNoPrefix') : 'No. Permohonan') + ': ' + currentSelectedApp.app_no + '\n' +
    (window.MBS_I18N ? window.MBS_I18N.t('labelCompanyName') : 'Nama Syarikat') + ': ' + currentSelectedApp.company_name + '\n' +
    (window.MBS_I18N ? window.MBS_I18N.t('emailStatusLabel') : 'Status Semasa') + ': ' + currentSelectedApp.app_status + '\n\n' +
    (window.MBS_I18N ? window.MBS_I18N.t('emailQuestion') : 'Pertanyaan saya:') + '\n[' + (window.MBS_I18N ? window.MBS_I18N.t('emailQuestionHint') : 'Sila nyatakan pertanyaan anda di sini') + ']\n\n' +
    (window.MBS_I18N ? window.MBS_I18N.t('emailClosing') : 'Terima kasih.')
  );

  window.location.href = `mailto:info@mbs-parking.gov.my?subject=${subject}&body=${body}`;
}

// ─────────────────────────────────────────────────────────────────────────────
// FILTER, SEARCH & SORT
// ─────────────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
  // Populate translated button text for every card on first render
  document.querySelectorAll('.app-card').forEach(function(card) {
    const btn1El  = card.querySelector('.card-btn1');
    const btn2El  = card.querySelector('.card-btn2');
    if (btn1El) {
      btn1El.textContent = window.MBS_I18N ? window.MBS_I18N.t(card.dataset.btn1Key) : card.dataset.btn1Key;
      btn1El.href        = card.dataset.btn1Url || '#';
    }
    if (btn2El) {
      btn2El.textContent = window.MBS_I18N ? window.MBS_I18N.t(card.dataset.btn2Key) : card.dataset.btn2Key;
      btn2El.href        = card.dataset.btn2Url || '#';
    }
  });

  const searchInput    = document.querySelector('.search-wrap input');
  const filterBtns     = document.querySelectorAll('.filter-btn');
  const sortBtns       = document.querySelectorAll('.sort-btn');
  const cardsContainer = document.querySelector('.user-applications');

  function getCards() {
    return Array.from(cardsContainer.querySelectorAll('.app-card'));
  }

  function sortCards(order) {
    const cards = getCards();
    cards.sort((a, b) => {
      const keyA = parseInt(a.dataset.sortKey, 10) || 0;
      const keyB = parseInt(b.dataset.sortKey, 10) || 0;
      return order === 'newest' ? keyB - keyA : keyA - keyB;
    });
    cards.forEach(card => cardsContainer.appendChild(card));
  }

  function filterApplications() {
    const searchTerm   = searchInput.value.toLowerCase();
    const activeFilter = document.querySelector('.filter-btn.active').dataset.filter || 'all';
    getCards().forEach(card => {
      const appId   = card.querySelector('.app-id').textContent.toLowerCase();
      const company = card.querySelector('.app-type').textContent.toLowerCase();
      const status  = card.dataset.status;
      const matchesSearch = appId.includes(searchTerm) || company.includes(searchTerm);
      const matchesFilter = activeFilter === 'all' || activeFilter === status;
      card.style.display = (matchesSearch && matchesFilter) ? 'block' : 'none';
    });
  }

  searchInput.addEventListener('input', filterApplications);

  filterBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      filterBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      filterApplications();
    });
  });

  sortBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      sortBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      sortCards(this.dataset.sort);
      filterApplications();
    });
  });

  sortCards('newest');
});
</script>

</body>
</html>