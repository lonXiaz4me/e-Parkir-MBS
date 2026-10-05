<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Permohonan Baru — Sistem Petak Sewa MBS</title>
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/application.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
</head>
<body>

<div class="shell">

  @include('auth.partials.sidebar')

  <!-- TOPBAR -->
  <div class="topbar">
    <span class="breadcrumb">
      <a href="#" data-i18n="breadcrumbHome">Laman Utama</a><span class="sep">/</span>
      <span class="current" data-i18n="crumbApplication">Permohonan Baru</span>
    </span>
    <div class="topbar-right">
      @include('auth.partials.notif-panel')
    </div>
  </div>

  @include('auth.partials.confirm-modal')

  <!-- MAIN -->
  <div class="main">

    <!-- TOAST NOTIFICATION -->
    <div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div>

    <div class="page-title" data-i18n="appPageTitle">Permohonan Petak Letak Kereta</div>
    <div class="page-sub"><span data-i18n="appPageSub">Sila lengkapkan semua maklumat yang diperlukan. Medan bertanda</span> <span class="f-required">*</span> <span data-i18n="appPageSubRequired">adalah wajib diisi.</span></div>

    <div class="step-bar">
      <div class="step"><div class="step-num current">1</div><span class="step-label current" data-i18n-html data-i18n="stepPersonalCompany">Maklumat Peribadi &amp; Syarikat</span></div>
      <div class="step"><div class="step-num todo">2</div><span class="step-label" data-i18n-html data-i18n="stepParkingPayment">Butiran Petak &amp; Bayaran</span></div>
      <div class="step"><div class="step-num todo">3</div><span class="step-label" data-i18n="stepDocuments">Muat Naik Dokumentasi</span></div>
      <div class="step"><div class="step-num todo">4</div><span class="step-label" data-i18n-html data-i18n="stepReviewSubmit">Semakan &amp; Hantar</span></div>
    </div>

    <!-- DRAFT BANNER -->
    <div class="draft-banner" id="draftBanner">
      <div class="draft-banner-icon">
        <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
          <path d="M5 1v4l2 2" stroke="#0d0d0d" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="draft-banner-text"><strong data-i18n="draftFound">Draf ditemui.</strong> <span data-i18n="draftFoundMsg">Maklumat sebelum ini telah dipulihkan secara automatik. Sila semak sebelum menghantar.</span></div>
      <button type="button" class="draft-banner-clear" id="draftBannerClearBtn" data-i18n="draftDiscard">Buang Draf</button>
    </div>

    <div class="info-strip">
      <div class="info-dot"></div>
      <div class="info-text" data-i18n="infoIncomplete">Permohonan yang tidak lengkap akan ditolak. Pastikan semua dokumen sokongan dimuat naik sebelum menghantar.</div>
    </div>

    <!-- ERROR SUMMARY BAR -->
    <div class="error-summary-bar" id="errorSummaryBar">
      <div id="errorSummaryList"></div>
    </div>

    <form action="{{ route('application.store') }}" method="POST" enctype="multipart/form-data" id="applicationForm" novalidate>
      @csrf

      <!-- SECTION 1: Maklumat Peribadi -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="card-icon-box">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <circle cx="8" cy="5.5" r="3" stroke="#a07000" stroke-width="1.5"/>
              <path d="M2 13.5c0-2.5 2.7-4.5 6-4.5s6 2 6 4.5" stroke="#a07000" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
          </div>
          <span class="form-card-title" data-i18n="cardTitlePersonal">Maklumat Peribadi</span>
        </div>
        <div class="form-card-body">
          <div class="form-grid-2">
            <div class="form-field full">
              <label class="f-label"><span data-i18n="labelFullName">Nama Penuh</span><span class="f-required">*</span></label>
              <input type="text" name="full_name" class="f-input" value="{{ auth()->user()->full_name }}" disabled>
            </div>
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelIcNo">No. Kad Pengenalan</span><span class="f-required">*</span></label>
              <input type="text" name="ic_no" class="f-input" value="{{ auth()->user()->ic_no }}" disabled>
            </div>
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelPersonalPhone">No. Telefon Peribadi</span><span class="f-required">*</span></label>
              <input type="tel" name="personal_phone" class="f-input" value="{{ auth()->user()->phone_no }}" disabled>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 2: Maklumat Syarikat -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="card-icon-box">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <rect x="2" y="5" width="12" height="9" rx="1.5" stroke="#a07000" stroke-width="1.5"/>
              <path d="M5 5V4a3 3 0 0 1 6 0v1" stroke="#a07000" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
          </div>
          <span class="form-card-title" data-i18n="cardTitleCompany">Maklumat Syarikat</span>
        </div>
        <div class="form-card-body">
          <div class="form-grid-2">
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelCompanyName">Nama Syarikat</span><span class="f-required">*</span></label>
              <input type="text" name="company_name" id="company_name" class="f-input" placeholder="cth: Acme Services Sdn Bhd" value="{{ old('company_name', $reapplyData['company_name'] ?? '') }}">
              <span class="f-error-msg" id="err-company_name" data-i18n="errCompanyName">Sila masukkan nama syarikat.</span>
            </div>
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelSsmNo">No Pendaftaran SSM</span><span class="f-required">*</span></label>
              <input type="text" name="ssm_no" id="ssm_no" class="f-input" placeholder="cth: 202301XXXXXX" 	value="{{ old('ssm_no', $reapplyData['ssm_no'] ?? '') }}">
              <span class="f-error-msg" id="err-ssm_no" data-i18n="errSsmNo">Sila masukkan no. pendaftaran SSM.</span>
            </div>
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelCompanyEmail">Email Rasmi Syarikat</span><span class="f-required">*</span></label>
              <input type="email" name="company_email" id="company_email" class="f-input" placeholder="cth: info@syarikat.com.my" value="{{ old('company_email', $reapplyData['company_email'] ?? '') }}">
              <span class="f-error-msg" id="err-company_email" data-i18n="errCompanyEmail">Sila masukkan alamat email yang sah.</span>
            </div>
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelCompanyPhone">No. Telefon Syarikat</span><span class="f-required">*</span></label>
              <input type="tel" name="company_no" id="company_no" class="f-input" placeholder="cth: 03-12345678" value="{{ old('company_no', $reapplyData['company_no'] ?? '') }}">
              <span class="f-error-msg" id="err-company_no" data-i18n="errCompanyPhone">Sila masukkan no. telefon syarikat.</span>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 3: Butiran Petak -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="card-icon-box">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <rect x="2" y="2" width="12" height="12" rx="1.5" stroke="#a07000" stroke-width="1.5"/>
              <rect x="5" y="5" width="2.5" height="6" rx=".5" fill="#a07000"/>
              <rect x="8.5" y="5" width="2.5" height="6" rx=".5" fill="#a07000"/>
            </svg>
          </div>
          <span class="form-card-title" data-i18n="cardTitleParking">Butiran Petak</span>
        </div>
        <div class="form-card-body">
          <div class="form-grid-2">
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelCategory">Kategori Perniagaan</span><span class="f-required">*</span></label>
              <select name="category" class="f-input" id="categorySelect">
                <option value="" disabled {{ old('category') ? '' : 'selected' }} data-i18n="selCategory">-- Pilih Kategori --</option>
                <option value="Kategori A" {{ old('category')=='Kategori A'?'selected':'' }}>Kategori A</option>
                <option value="Kategori B" {{ old('category')=='Kategori B'?'selected':'' }}>Kategori B</option>
              </select>
              <span class="f-error-msg" id="err-category" data-i18n="errCategory">Sila pilih kategori perniagaan.</span>
            </div>
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelBusinessType">Jenis Perniagaan</span><span class="f-required">*</span></label>
              <select name="type_of_business" class="f-input" id="businessTypeSelect">
                <option value="" disabled {{ old('type_of_business') ? '' : 'selected' }} data-i18n="selBusinessType">-- Pilih Jenis Perniagaan --</option>
                <option value="Premis perniagaan berkaitan pameran / menjual - kereta atau motosikal" data-category="Kategori A">a) Premis perniagaan berkaitan pameran / menjual - kereta atau motosikal</option>
                <option value="Premis perniagaan mencuci / mengilat / mengetuk / mengecat kenderaan" data-category="Kategori A">b) Premis perniagaan mencuci / mengilat / mengetuk / mengecat kenderaan</option>
                <option value="Premis perniagaan menjual alat ganti / aksesori / penghawa dingin kenderaan" data-category="Kategori A">c) Premis perniagaan menjual alat ganti / aksesori / penghawa dingin kenderaan</option>
                <option value="Premis perniagaan menjual tayar / menjual bateri / bengkel kenderaan" data-category="Kategori A">d) Premis perniagaan menjual tayar / menjual bateri / bengkel kenderaan</option>
                <option value="Bank / kedai pajak gadai / syarikat kewangan" data-category="Kategori B">a) Bank / kedai pajak gadai / syarikat kewangan</option>
                <option value="Klinik / hospital swasta / farmasi" data-category="Kategori B">b) Klinik / hospital swasta / farmasi</option>
                <option value="Hotel atau rumah tumpangan berlesen" data-category="Kategori B">c) Hotel atau rumah tumpangan berlesen</option>
                <option value="Pusat pendidikan awal kanak-kanak/ sekolah pendidikan khas" data-category="Kategori B">d) Pusat pendidikan awal kanak-kanak/ sekolah pendidikan khas</option>
                <option value="Pasaraya/runcit/kedai serbaneka/pemborong (pemunggahan barang, stor)" data-category="Kategori B">e) Pasaraya/runcit/kedai serbaneka/pemborong</option>
                <option value="Kedai perabot (pemunggahan barang / stor)" data-category="Kategori B">f) Kedai perabot (pemunggahan barang / stor)</option>
                <option value="Agensi kerajaan & GLC" data-category="Kategori B">g) Agensi kerajaan &amp; GLC</option>
                <option value="Premis perniagaan hardware" data-category="Kategori B">h) Premis perniagaan hardware</option>
                <option value="Premis perniagaan penghantaran ekspress" data-category="Kategori B">i) Premis perniagaan penghantaran ekspress</option>
                <option value="Premis perniagaan pembinaan (stor ,menjual dan pameran)" data-category="Kategori B">j) Premis perniagaan pembinaan</option>
                <option value="Premis makanan segera (yang terdapat perkhidmatan &quot;Delivery&quot; sahaja)" data-category="Kategori B">k) Premis makanan segera (Delivery sahaja)</option>
              </select>
              <span class="f-error-msg" id="err-type_of_business" data-i18n="errBusinessType">Sila pilih jenis perniagaan.</span>
            </div>
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelLocation">Lokasi</span><span class="f-required">*</span></label>
              <select name="location" class="f-input" id="locationSelect">
                <option value="" disabled {{ old('location') ? '' : 'selected' }} data-i18n="selLocation">-- Pilih Lokasi --</option>
                <option value="Seremban – Hadapan (Kategori A)">a) Seremban – Hadapan (Kategori A)</option>
                <option value="Seremban – Hadapan (Kategori B)">b) Seremban – Hadapan (Kategori B)</option>
                <option value="Seremban">c) Seremban</option>
                <option value="Nilai – Hadapan / Lorong / Belakang">d) Nilai – Hadapan / Lorong / Belakang</option>
                <option value="Seremban – Kawasan belum warta">e) Seremban – Kawasan belum warta</option>
              </select>
              <span class="f-error-msg" id="err-location" data-i18n="errLocation">Sila pilih lokasi.</span>
            </div>
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelTotalParking">Jumlah Petak Dimohon</span><span class="f-required">*</span></label>
              <input type="number" name="total_parking" id="totalParkingSelect" class="f-input" data-i18n-placeholder="phTotalParking" placeholder="Masukkan jumlah petak" min="1" value="{{ old('total_parking') }}">
              <span class="f-error-msg" id="err-total_parking" data-i18n="errTotalParking">Sila masukkan jumlah petak (minimum 1).</span>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 4: Pengiraan Bayaran -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="card-icon-box">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <rect x="1.5" y="3.5" width="13" height="9" rx="1.5" stroke="#a07000" stroke-width="1.5"/>
              <line x1="1.5" y1="6.5" x2="14.5" y2="6.5" stroke="#a07000" stroke-width="1.2"/>
              <rect x="4" y="9" width="3" height="1.5" rx=".5" fill="#a07000"/>
            </svg>
          </div>
          <span class="form-card-title" data-i18n="cardTitlePayment">Pengiraan Bayaran</span>
        </div>
        <div class="form-card-body">
          <div class="payment-summary" id="paymentSummary">
            <div class="payment-empty" id="paymentEmpty">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="1.5">
                <rect x="2" y="5" width="20" height="14" rx="2"/>
                <line x1="2" y1="10" x2="22" y2="10"/>
              </svg>
              <span class="payment-empty-text" data-i18n="payEmptyHint">Pilih lokasi dan jumlah petak untuk melihat anggaran bayaran.</span>
            </div>
            <div id="paymentRows" style="display:none;width:100%;">
              <div class="payment-row"><span class="label" data-i18n="payRowLocation">Lokasi</span><span class="value" id="pay-location">—</span></div>
              <div class="payment-row"><span class="label" data-i18n="payRowRate">Kadar Sewaan (sebulan / petak)</span><span class="value" id="pay-rate">RM —</span></div>
              <div class="payment-row"><span class="label" data-i18n="payRowLots">Jumlah Petak</span><span class="value" id="pay-lots">—</span></div>
              <div class="payment-divider"></div>
              <div class="payment-total">
                <span class="label" data-i18n="payRowTotal">Jumlah Bayaran Anggaran / Bulan</span>
                <span class="value" id="pay-total">RM —</span>
                <input type="hidden" name="grand_total" id="hidden-total" value="">
              </div>
              <div class="payment-note" data-i18n="payEstimateNote">* Anggaran sahaja. Jumlah sebenar tertakluk kepada kelulusan pihak pengurusan.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 5: Dokumentasi -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="card-icon-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#a07000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
              <circle cx="12" cy="13" r="4"/>
            </svg>
          </div>
          <span class="form-card-title" data-i18n="cardTitleDocs">Dokumentasi</span>
        </div>
        <div class="form-card-body">
          <div class="info-strip info-strip--amber">
            <div class="info-dot info-dot--amber"></div>
            <div class="info-text info-text--amber" data-i18n="docInfoStrip">Dokumen SSM, IC, dan Lesen Perniagaan boleh dimuat naik sebagai fail atau diambil menggunakan tab kamera. Gambar Lokasi mesti diambil menggunakan kamera berserta GPS.</div>
          </div>
          <div class="form-grid-2">

            <!-- ── SSM ── -->
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelSsmDoc">Salinan SSM Syarikat</span><span class="f-required">*</span></label>
              <div class="upload-zone" id="zone-ssm">
                <div class="upload-tabs">
                  <button type="button" class="upload-tab active" data-target="panel-ssm-file" onclick="switchTab('ssm','file')">
                    <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span data-i18n="uploadTabFile">Muat Naik</span>
                  </button>
                  <button type="button" class="upload-tab" data-target="panel-ssm-cam" onclick="switchTab('ssm','cam')">
                    <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <span data-i18n="uploadTabCamera">Kamera</span>
                  </button>
                </div>
                <div class="upload-panel active" id="panel-ssm-file">
                  <div class="upload-file-drop" onclick="document.getElementById('ssm_img').click()">
                    <div class="upload-file-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    <div class="upload-file-title" data-i18n="uploadFileTitle">Pilih Fail</div>
                    <div class="upload-file-sub" data-i18n="uploadFileSub">PDF, JPG, PNG atau WEBP · Maks 5MB</div>
                    <button type="button" class="upload-file-btn" data-i18n="uploadFileBrowse">📂 Semak Imbas</button>
                  </div>
                </div>
                <div class="upload-panel" id="panel-ssm-cam">
                  <div class="upload-cam-icon"><svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
                  <div class="upload-cam-title" data-i18n="uploadCamTitleSsm">Ambil Gambar SSM</div>
                  <div class="upload-cam-sub" data-i18n="uploadCamSubSsm">Buka kamera untuk mengambil gambar dokumen SSM</div>
                  <button type="button" class="upload-cam-btn" onclick="openCameraForField('ssm', false)" data-i18n="uploadCameraOpen">📷 Buka Kamera</button>
                </div>
                <div class="upload-preview" id="preview-ssm">
                  <div class="upload-preview-img-wrap">
                    <a id="link-ssm" href="#" target="_blank"><img id="img-ssm" src="" alt="Preview SSM"></a>
                    <button type="button" class="upload-preview-remove" onclick="removeUpload('ssm')">✕</button>
                  </div>
                  <div class="upload-preview-meta">
                    <span class="upload-preview-badge" data-i18n="uploadPreviewReady">✓ Sedia</span>
                    <span class="upload-preview-name" id="name-ssm"></span>
                  </div>
                </div>
              </div>
              <span class="f-error-msg" id="err-ssm_img" data-i18n="errSsmImg">Sila muat naik salinan SSM syarikat.</span>
              <input type="file" id="ssm_img" name="ssm_img" accept="image/*,application/pdf" onchange="showDualPreview(this,'ssm')" style="display:none;">
            </div>

            <!-- ── IC ── -->
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelIcDoc">Salinan IC Pemohon</span><span class="f-required">*</span></label>
              <div class="upload-zone" id="zone-ic">
                <div class="upload-tabs">
                  <button type="button" class="upload-tab active" data-target="panel-ic-file" onclick="switchTab('ic','file')">
                    <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span data-i18n="uploadTabFile">Muat Naik</span>
                  </button>
                  <button type="button" class="upload-tab" data-target="panel-ic-cam" onclick="switchTab('ic','cam')">
                    <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <span data-i18n="uploadTabCamera">Kamera</span>
                  </button>
                </div>
                <div class="upload-panel active" id="panel-ic-file">
                  <div class="upload-file-drop" onclick="document.getElementById('ic_img').click()">
                    <div class="upload-file-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    <div class="upload-file-title" data-i18n="uploadFileTitle">Pilih Fail</div>
                    <div class="upload-file-sub" data-i18n="uploadFileSub">PDF, JPG, PNG atau WEBP · Maks 5MB</div>
                    <button type="button" class="upload-file-btn" data-i18n="uploadFileBrowse">📂 Semak Imbas</button>
                  </div>
                </div>
                <div class="upload-panel" id="panel-ic-cam">
                  <div class="upload-cam-icon"><svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
                  <div class="upload-cam-title" data-i18n="uploadCamTitleIc">Ambil Gambar IC</div>
                  <div class="upload-cam-sub" data-i18n="uploadCamSubIc">Buka kamera untuk mengambil gambar IC pemohon</div>
                  <button type="button" class="upload-cam-btn" onclick="openCameraForField('ic', false)" data-i18n="uploadCameraOpen">📷 Buka Kamera</button>
                </div>
                <div class="upload-preview" id="preview-ic">
                  <div class="upload-preview-img-wrap">
                    <a id="link-ic" href="#" target="_blank"><img id="img-ic" src="" alt="Preview IC"></a>
                    <button type="button" class="upload-preview-remove" onclick="removeUpload('ic')">✕</button>
                  </div>
                  <div class="upload-preview-meta">
                    <span class="upload-preview-badge" data-i18n="uploadPreviewReady">✓ Sedia</span>
                    <span class="upload-preview-name" id="name-ic"></span>
                  </div>
                </div>
              </div>
              <span class="f-error-msg" id="err-ic_img" data-i18n="errIcImg">Sila muat naik salinan IC pemohon.</span>
              <input type="file" id="ic_img" name="ic_img" accept="image/*,application/pdf" onchange="showDualPreview(this,'ic')" style="display:none;">
            </div>

            <!-- ── LESEN ── -->
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelLicenceDoc">Salinan Lesen Perniagaan</span><span class="f-required">*</span></label>
              <div class="upload-zone" id="zone-lic">
                <div class="upload-tabs">
                  <button type="button" class="upload-tab active" data-target="panel-lic-file" onclick="switchTab('lic','file')">
                    <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span data-i18n="uploadTabFile">Muat Naik</span>
                  </button>
                  <button type="button" class="upload-tab" data-target="panel-lic-cam" onclick="switchTab('lic','cam')">
                    <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <span data-i18n="uploadTabCamera">Kamera</span>
                  </button>
                </div>
                <div class="upload-panel active" id="panel-lic-file">
                  <div class="upload-file-drop" onclick="document.getElementById('licence_img').click()">
                    <div class="upload-file-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    <div class="upload-file-title" data-i18n="uploadFileTitle">Pilih Fail</div>
                    <div class="upload-file-sub" data-i18n="uploadFileSub">PDF, JPG, PNG atau WEBP · Maks 5MB</div>
                    <button type="button" class="upload-file-btn" data-i18n="uploadFileBrowse">📂 Semak Imbas</button>
                  </div>
                </div>
                <div class="upload-panel" id="panel-lic-cam">
                  <div class="upload-cam-icon"><svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
                  <div class="upload-cam-title" data-i18n="uploadCamTitleLic">Ambil Gambar Lesen</div>
                  <div class="upload-cam-sub" data-i18n="uploadCamSubLic">Buka kamera untuk mengambil gambar lesen perniagaan</div>
                  <button type="button" class="upload-cam-btn" onclick="openCameraForField('lic', false)" data-i18n="uploadCameraOpen">📷 Buka Kamera</button>
                </div>
                <div class="upload-preview" id="preview-lic">
                  <div class="upload-preview-img-wrap">
                    <a id="link-lic" href="#" target="_blank"><img id="img-lic" src="" alt="Preview Lesen"></a>
                    <button type="button" class="upload-preview-remove" onclick="removeUpload('lic')">✕</button>
                  </div>
                  <div class="upload-preview-meta">
                    <span class="upload-preview-badge" data-i18n="uploadPreviewReady">✓ Sedia</span>
                    <span class="upload-preview-name" id="name-lic"></span>
                  </div>
                </div>
              </div>
              <span class="f-error-msg" id="err-licence_img" data-i18n="errLicenceImg">Sila muat naik salinan lesen perniagaan.</span>
              <input type="file" id="licence_img" name="licence_img" accept="image/*,application/pdf" onchange="showDualPreview(this,'lic')" style="display:none;">
            </div>

            <!-- ── GAMBAR LOKASI ── (camera only) -->
            <div class="form-field">
              <label class="f-label"><span data-i18n="labelLocationDoc">Gambar Lokasi Yang Ingin Disewa</span><span class="f-required">*</span></label>
              <div class="upload-zone" id="zone-loc">
                <div class="upload-tabs">
                  <button type="button" class="upload-tab active" data-target="panel-loc-cam" onclick="switchTab('loc','cam')">
                    <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <span data-i18n="uploadTabCamera">Kamera</span>
                  </button>
                </div>
                <div class="upload-panel active" id="panel-loc-cam">
                  <div class="upload-cam-icon"><svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
                  <div class="upload-cam-title" data-i18n="uploadCamTitleLoc">Gambar Lokasi</div>
                  <div class="upload-cam-sub" data-i18n="uploadCamSubLoc">Kamera akan mengambil gambar berserta koordinat GPS lokasi sebenar.</div>
                  <button type="button" class="upload-cam-btn" onclick="openCameraForField('loc', true)" data-i18n="uploadCameraOpen">📷 Buka Kamera</button>
                </div>
                <div class="upload-preview" id="preview-loc">
                  <div class="upload-preview-img-wrap">
                    <a id="link-loc" href="#" target="_blank"><img id="img-loc" src="" alt="Preview Lokasi"></a>
                    <button type="button" class="upload-preview-remove" onclick="removeUpload('loc')">✕</button>
                  </div>
                  <div class="upload-preview-meta">
                    <span class="upload-preview-badge" data-i18n="uploadPreviewReady">✓ Sedia</span>
                    <span class="upload-preview-name" id="name-loc"></span>
                  </div>
                  <div class="loc-coords-chip" id="loc-coords-chip">
                    <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span id="loc-coords-text"></span>
                  </div>
                </div>
              </div>
              <span class="f-error-msg" id="err-location_img" data-i18n="errLocationImg">Sila ambil gambar lokasi yang ingin disewa.</span>
              <input type="file" id="location_img" name="location_img" style="display:none;" aria-hidden="true">
              <input type="hidden" id="location_coords" name="location_coords" value="">
            </div>

          </div>
        </div>
      </div>

      <!-- DECLARATION -->
      <div class="declaration-box">
        <label class="decl-container" style="display:flex;align-items:flex-start;cursor:pointer;gap:10px;">
          <div style="position:relative;margin-top:4px;">
            <input type="checkbox" name="declaration" id="declaration" style="position:absolute;opacity:0;cursor:pointer;height:0;width:0;">
            <div id="custom-check" style="width:18px;height:18px;border:2px solid #a07000;border-radius:4px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;">
              <svg id="check-svg" width="12" height="12" viewBox="0 0 10 10" fill="none" style="display:none;">
                <path d="M2 5l2.5 2.5L8 3" stroke="#0d0d0d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
          <div class="decl-text" data-i18n="declText">Saya mengaku bahawa semua maklumat yang diberikan adalah benar dan tepat. Saya memahami bahawa maklumat palsu atau dokumen yang tidak sah boleh menyebabkan permohonan ini ditolak atau permit dibatalkan.</div>
        </label>
      </div>
      <span class="f-error-msg" id="err-declaration" data-i18n="errDeclaration">Sila tandakan perakuan sebelum menghantar.</span>

      @if ($errors->any())
        <div class="error-alert" style="margin-bottom:16px;">
          <ul class="error-list">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- ACTION BAR -->
      <div class="action-bar">
        <button type="button" onclick="window.location.href='{{ route('dashboard') }}';" class="btn-ghost" data-i18n="btnBack">Kembali</button>
        <div class="action-right">
          <button type="button" class="btn-draft" id="saveDraftBtn" onclick="saveDraft()">
            <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            <span id="draftBtnLabel" data-i18n="btnSaveDraft">Simpan Draf</span>
          </button>
          <button type="submit" class="btn-submit" data-i18n="btnSubmitApp">Hantar Permohonan</button>
        </div>
      </div>

    </form>
  </div>
</div>

<script>

// ─── TAB SWITCHER ─────────────────────────────────────────────────────────────
function switchTab(key, mode) {
  const zone = document.getElementById('zone-' + key);
  if (!zone) return;
  zone.querySelectorAll('.upload-tab').forEach(t => t.classList.remove('active'));
  zone.querySelectorAll('.upload-panel').forEach(p => { p.classList.remove('active'); p.style.display = ''; });
  const targetPanel = document.getElementById('panel-' + key + '-' + mode);
  const targetTab   = zone.querySelector('[data-target="panel-' + key + '-' + mode + '"]');
  if (targetPanel) targetPanel.classList.add('active');
  if (targetTab)   targetTab.classList.add('active');
}

// ─── FILE UPLOAD PREVIEW ──────────────────────────────────────────────────────
function showDualPreview(input, key) {
  const file = input.files[0];
  if (!file) return;
  const preview  = document.getElementById('preview-' + key);
  const nameSpan = document.getElementById('name-' + key);
  const imgEl    = document.getElementById('img-' + key);
  const linkEl   = document.getElementById('link-' + key);
  const zone     = document.getElementById('zone-' + key);
  nameSpan.textContent = file.name;
  if (file.type === 'application/pdf') {
    imgEl.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80"><rect width="80" height="80" rx="8" fill="%23fff0f0"/><text x="50%25" y="54%25" dominant-baseline="middle" text-anchor="middle" font-size="13" fill="%23C0392B" font-family="monospace" font-weight="bold">PDF</text></svg>';
    if (linkEl) linkEl.href = URL.createObjectURL(file);
    imgEl.style.objectFit = 'contain';
    imgEl.style.background = '#fafaf8';
  } else {
    const objectUrl = URL.createObjectURL(file);
    imgEl.src = objectUrl;
    if (linkEl) linkEl.href = objectUrl;
    imgEl.style.objectFit = 'contain';
    imgEl.style.background = '';
  }
  zone.querySelectorAll('.upload-tabs, .upload-panel').forEach(el => el.style.display = 'none');
  preview.classList.add('visible');
  zone.classList.remove('field-error');
  const errEl = document.getElementById('err-' + (key === 'lic' ? 'licence_img' : key + '_img'));
  if (errEl) errEl.classList.remove('visible');
}

// ─── REMOVE UPLOAD ────────────────────────────────────────────────────────────
function removeUpload(key) {
  const zone    = document.getElementById('zone-' + key);
  const preview = document.getElementById('preview-' + key);

  if (key === 'loc') {
    document.getElementById('location_img').value    = '';
    document.getElementById('location_coords').value = '';
    if (preview) preview.classList.remove('visible');
    const chip = document.getElementById('loc-coords-chip');
    if (chip) chip.classList.remove('visible');
    const linkEl = document.getElementById('link-loc');
    if (linkEl) linkEl.href = '#';
    if (zone) {
      zone.querySelectorAll('.upload-tabs, .upload-panel').forEach(el => el.style.display = '');
      const camPanel = document.getElementById('panel-loc-cam');
      if (camPanel) { camPanel.style.display = ''; camPanel.classList.add('active'); }
    }
    return;
  }

  const mainInputId = key === 'lic' ? 'licence_img' : key + '_img';
  const mainInput   = document.getElementById(mainInputId);
  if (mainInput) mainInput.value = '';
  if (preview)   preview.classList.remove('visible');
  const linkEl = document.getElementById('link-' + key);
  if (linkEl) linkEl.href = '#';
  if (zone) {
    zone.querySelectorAll('.upload-tabs, .upload-panel').forEach(el => el.style.display = '');
    switchTab(key, 'file');
  }
}

// ─── CAMERA POPUP ─────────────────────────────────────────────────────────────
function openCameraForField(key, withGPS) {
  const w    = 500, h = 700;
  const left = Math.max(0, (screen.width  - w) / 2);
  const top  = Math.max(0, (screen.height - h) / 2);
  window.open(
    '{{ route("camera.capture") }}?field=' + key + '&gps=' + (withGPS ? 1 : 0),
    'mbsCamera_' + key,
    'width=' + w + ',height=' + h + ',left=' + left + ',top=' + top +
    ',resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,status=no'
  );
}

// ─── postMessage LISTENER ─────────────────────────────────────────────────────
window.addEventListener('message', function (event) {
  if (event.origin !== window.location.origin) return;
  if (!event.data || event.data.type !== 'CAMERA_CAPTURE_DONE') return;

  const { dataUrl, coordsString, fileName, field } = event.data;
  const key = field || 'loc';

  fetch(dataUrl)
    .then(function(r) { return r.blob(); })
    .then(function(blob) {
      const file = new File(
        [blob],
        fileName || (key + '_' + Date.now() + '.jpg'),
        { type: 'image/jpeg' }
      );
      const dt = new DataTransfer();
      dt.items.add(file);

      const inputIdMap = { ssm: 'ssm_img', ic: 'ic_img', lic: 'licence_img', loc: 'location_img' };
      const targetInput = document.getElementById(inputIdMap[key] || 'location_img');
      if (targetInput) targetInput.files = dt.files;

      const zone     = document.getElementById('zone-' + key);
      const preview  = document.getElementById('preview-' + key);
      const imgEl    = document.getElementById('img-' + key);
      const linkEl   = document.getElementById('link-' + key);
      const nameSpan = document.getElementById('name-' + key);

      imgEl.src  = dataUrl;
      imgEl.style.objectFit = 'contain';
      if (linkEl)   linkEl.href = dataUrl;
      if (nameSpan) nameSpan.textContent = file.name;

      if (zone)    zone.querySelectorAll('.upload-tabs, .upload-panel').forEach(el => el.style.display = 'none');
      if (preview) preview.classList.add('visible');

      if (key === 'loc') {
        document.getElementById('location_coords').value = coordsString || '';
        const chip       = document.getElementById('loc-coords-chip');
        const coordsText = document.getElementById('loc-coords-text');
        if (coordsString && chip && coordsText) {
          coordsText.textContent = '📍 ' + coordsString;
          chip.classList.add('visible');
        } else if (chip) {
          chip.classList.remove('visible');
        }
      }

      if (zone) zone.classList.remove('field-error');
      const errId = key === 'lic' ? 'err-licence_img' : (key === 'loc' ? 'err-location_img' : 'err-' + key + '_img');
      const errEl = document.getElementById(errId);
      if (errEl) errEl.classList.remove('visible');
    });
});

// ─── CHECKBOX ─────────────────────────────────────────────────────────────────
const declCheckbox = document.getElementById('declaration');
const customCheck  = document.getElementById('custom-check');
const checkSvg     = document.getElementById('check-svg');

declCheckbox.addEventListener('change', function () {
  if (this.checked) {
    customCheck.style.backgroundColor = '#a07000';
    customCheck.style.borderColor     = '#a07000';
    checkSvg.style.display            = 'block';
    checkSvg.querySelector('path').setAttribute('stroke', '#fff');
    customCheck.classList.remove('check-error');
    document.getElementById('err-declaration').classList.remove('visible');
  } else {
    customCheck.style.backgroundColor = '';
    customCheck.style.borderColor     = '#a07000';
    checkSvg.style.display            = 'none';
  }
});

// ─── PAYMENT CALCULATOR ───────────────────────────────────────────────────────
(function () {
  const locationSelect    = document.getElementById('locationSelect');
  const totalParkingInput = document.getElementById('totalParkingSelect');
  const paymentEmpty      = document.getElementById('paymentEmpty');
  const paymentRows       = document.getElementById('paymentRows');

  function calculatePayment() {
    const locationValue = locationSelect.value;
    const totalLots     = parseInt(totalParkingInput.value, 10);
    if (!locationValue || isNaN(totalLots) || totalLots < 1) {
      paymentEmpty.style.display = 'flex'; paymentRows.style.display = 'none'; return;
    }
    let finalRate = 0;
    switch (locationValue) {
      case 'Seremban – Hadapan (Kategori A)':
        if      (totalLots >= 6) finalRate = 90.00;
        else if (totalLots >= 4) finalRate = 108.00;
        else if (totalLots >= 2) finalRate = 126.00;
        else                     finalRate = 180.00;
        break;
      case 'Seremban – Hadapan (Kategori B)':     finalRate = 180.00; break;
      case 'Seremban':                             finalRate = 65.00;  break;
      case 'Nilai – Hadapan / Lorong / Belakang': finalRate = 80.00;  break;
      case 'Seremban – Kawasan belum warta':       finalRate = 90.00;  break;
    }
    const grandTotal = finalRate * totalLots;
    const fmt = n => 'RM ' + n.toFixed(2);
    document.getElementById('pay-location').textContent = locationValue;
    document.getElementById('pay-rate').textContent     = fmt(finalRate);
    document.getElementById('pay-lots').textContent     = totalLots + ' Petak';
    document.getElementById('pay-total').textContent    = fmt(grandTotal);
    document.getElementById('hidden-total').value       = grandTotal;
    paymentEmpty.style.display = 'none';
    paymentRows.style.display  = 'block';
  }

  locationSelect.addEventListener('change',  calculatePayment);
  totalParkingInput.addEventListener('input', calculatePayment);
  calculatePayment();
})();

// ─── CATEGORY FILTER ──────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
  const categorySelect     = document.getElementById('categorySelect');
  const businessTypeSelect = document.getElementById('businessTypeSelect');
  const allOptions         = Array.from(businessTypeSelect.querySelectorAll('option'));

  function filterBusinessTypes() {
    const selectedCategory     = categorySelect.value;
    const currentSelectedValue = businessTypeSelect.value;
    businessTypeSelect.innerHTML = '';
    allOptions.forEach(option => {
      const optionCategory = option.getAttribute('data-category');
      if (!optionCategory || optionCategory === selectedCategory) businessTypeSelect.appendChild(option);
    });
    if (Array.from(businessTypeSelect.options).some(opt => opt.value === currentSelectedValue)) {
      businessTypeSelect.value = currentSelectedValue;
    } else {
      businessTypeSelect.value = '';
    }
  }

  filterBusinessTypes();
  categorySelect.addEventListener('change', filterBusinessTypes);
});

// ─── FORM VALIDATION ──────────────────────────────────────────────────────────
document.getElementById('applicationForm').addEventListener('submit', function (e) {
  const textFields = [
    { name:'company_name',     label: window.MBS_I18N ? window.MBS_I18N.t('labelCompanyName') : 'Nama Syarikat' },
    { name:'ssm_no',           label: window.MBS_I18N ? window.MBS_I18N.t('labelSsmNo') : 'No Pendaftaran SSM' },
    { name:'company_email',    label: window.MBS_I18N ? window.MBS_I18N.t('labelCompanyEmail') : 'Email Rasmi Syarikat' },
    { name:'company_no',    label: window.MBS_I18N ? window.MBS_I18N.t('labelCompanyPhone') : 'No. Telefon Syarikat' },
    { name:'category',         label: window.MBS_I18N ? window.MBS_I18N.t('labelCategory') : 'Kategori Perniagaan' },
    { name:'type_of_business', label: window.MBS_I18N ? window.MBS_I18N.t('labelBusinessType') : 'Jenis Perniagaan' },
    { name:'location',         label: window.MBS_I18N ? window.MBS_I18N.t('labelLocation') : 'Lokasi' },
    { name:'total_parking',    label: window.MBS_I18N ? window.MBS_I18N.t('labelTotalParking') : 'Jumlah Petak Dimohon' },
  ];
  const fileFields = [
    { inputId:'ssm_img',      zoneId:'zone-ssm', errId:'err-ssm_img',     label: window.MBS_I18N ? window.MBS_I18N.t('labelSsmDoc') : 'Salinan SSM Syarikat' },
    { inputId:'ic_img',       zoneId:'zone-ic',  errId:'err-ic_img',       label: window.MBS_I18N ? window.MBS_I18N.t('labelIcDoc') : 'Salinan IC Pemohon' },
    { inputId:'licence_img',  zoneId:'zone-lic', errId:'err-licence_img',  label: window.MBS_I18N ? window.MBS_I18N.t('labelLicenceDoc') : 'Salinan Lesen Perniagaan' },
    { inputId:'location_img', zoneId:'zone-loc', errId:'err-location_img', label: window.MBS_I18N ? window.MBS_I18N.t('labelLocationDoc') : 'Gambar Lokasi' },
  ];
  let errors = [], firstError = null;

  textFields.forEach(function (f) {
    const el    = document.getElementById(f.name) || document.querySelector('[name="' + f.name + '"]');
    const errEl = document.getElementById('err-' + f.name);
    if (!el) return;
    let invalid = false;
    if (el.tagName === 'SELECT')        { invalid = !el.value; }
    else if (f.name === 'company_email'){ invalid = !el.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value.trim()); }
    else if (f.name === 'total_parking'){ invalid = !el.value || parseInt(el.value, 10) < 1; }
    else                                { invalid = !el.value.trim(); }
    el.classList.toggle('field-error', invalid);
    if (errEl) errEl.classList.toggle('visible', invalid);
    if (invalid) { errors.push(f.label); if (!firstError) firstError = el; }
    el.addEventListener('input',  function() { if (this.value.trim()) { this.classList.remove('field-error'); if (errEl) errEl.classList.remove('visible'); } });
    el.addEventListener('change', function() { if (this.value)        { this.classList.remove('field-error'); if (errEl) errEl.classList.remove('visible'); } });
  });

  fileFields.forEach(function (f) {
    const input   = document.getElementById(f.inputId);
    const zone    = document.getElementById(f.zoneId);
    const errEl   = document.getElementById(f.errId);
    const invalid = !input || !input.files || input.files.length === 0;
    if (zone)  zone.classList.toggle('field-error', invalid);
    if (errEl) errEl.classList.toggle('visible', invalid);
    if (invalid) { errors.push(f.label); if (!firstError) firstError = zone || input; }
  });

  const errDeclEl = document.getElementById('err-declaration');
  if (!declCheckbox.checked) {
    customCheck.classList.add('check-error');
    if (errDeclEl) errDeclEl.classList.add('visible');
    errors.push(window.MBS_I18N ? window.MBS_I18N.t('declLabel') : 'Perakuan pemohon');
    if (!firstError) firstError = customCheck;
  } else {
    customCheck.classList.remove('check-error');
    if (errDeclEl) errDeclEl.classList.remove('visible');
  }

  if (errors.length > 0) {
    e.preventDefault();
    const bar  = document.getElementById('errorSummaryBar');
    const list = document.getElementById('errorSummaryList');
    const missingPrefix = window.MBS_I18N ? window.MBS_I18N.t('errorMissingPrefix') : 'Tiada ';
    list.innerHTML = errors.map(label => missingPrefix + label + '*, ').join('');
    bar.classList.add('visible');
    showToast('error', window.MBS_I18N ? window.MBS_I18N.t('toastValidationTitle') : 'Permohonan Tidak Lengkap', window.MBS_I18N ? window.MBS_I18N.t('toastValidationMsg') : 'Sila lengkapkan semua medan bertanda (*) sebelum menghantar.', 7000);
    if (firstError) firstError.scrollIntoView({ behavior:'smooth', block:'center' });
    return;
  }

  document.getElementById('errorSummaryBar').classList.remove('visible');

  // FIX: All required fields pass — but submitting is a one-way action
  // (ApplicationController::store() generates a permanent app_no and
  // can't be undone from this page). Stop the native submit here and
  // show a confirm popup summarizing the key details first, so a user
  // who clicked submit by reflex gets one more chance to catch a typo.
  e.preventDefault();

  const totalEl = document.getElementById('pay-total');
  mbsConfirm({
    intent: 'warning',
    icon: 'question',
    title: window.MBS_I18N ? window.MBS_I18N.t('confirmSubmitTitle') : 'Hantar permohonan ini?',
    message: window.MBS_I18N ? window.MBS_I18N.t('confirmSubmitMsg') : 'Sila pastikan semua maklumat dan dokumen yang dimuat naik adalah betul. Permohonan tidak boleh diubah selepas dihantar.',
    detail: [
      { k: window.MBS_I18N ? window.MBS_I18N.t('labelCompanyName') : 'Nama Syarikat', v: document.getElementById('company_name').value },
      { k: window.MBS_I18N ? window.MBS_I18N.t('labelLocation') : 'Lokasi', v: document.getElementById('locationSelect').value || '—' },
      { k: window.MBS_I18N ? window.MBS_I18N.t('labelTotalParking') : 'Jumlah Petak', v: document.getElementById('totalParkingSelect').value || '—' },
      { k: window.MBS_I18N ? window.MBS_I18N.t('payRowTotal') : 'Anggaran Bayaran', v: totalEl ? totalEl.textContent : '—' },
    ],
    confirmText: window.MBS_I18N ? window.MBS_I18N.t('confirmSubmitOk') : 'Hantar',
    cancelText:  window.MBS_I18N ? window.MBS_I18N.t('confirmSubmitCancel') : 'Semak Semula',
    onConfirm: function () {
      try { localStorage.removeItem(DRAFT_KEY); } catch(ex) {}
      document.getElementById('applicationForm').submit();
    },
  });
});

// ─── SAVE / LOAD DRAFT ────────────────────────────────────────────────────────
const DRAFT_KEY    = 'mbs_application_draft';
const DRAFT_FIELDS = ['company_name','ssm_no','company_email','company_no','category','type_of_business','location','total_parking'];

function saveDraft() {
  const btn   = document.getElementById('saveDraftBtn');
  const label = document.getElementById('draftBtnLabel');
  btn.classList.add('saving');
  label.textContent = window.MBS_I18N ? window.MBS_I18N.t('draftSaving') : 'Menyimpan…';
  const draft = {};
  DRAFT_FIELDS.forEach(function(name) {
    const el = document.querySelector('[name="' + name + '"]');
    if (el) draft[name] = el.value;
  });
  draft._savedAt = new Date().toISOString();
  try { localStorage.setItem(DRAFT_KEY, JSON.stringify(draft)); } catch(ex) {}
  setTimeout(function() {
    btn.classList.remove('saving');
    btn.classList.add('saved');
    label.textContent = (window.MBS_I18N ? window.MBS_I18N.t('draftSaved') : 'Draf Disimpan') + ' ✓';
    setTimeout(function() { btn.classList.remove('saved'); label.textContent = window.MBS_I18N ? window.MBS_I18N.t('btnSaveDraft') : 'Simpan Draf'; }, 2500);
  }, 600);
}

function loadDraft() {
  let draft;
  try { const raw = localStorage.getItem(DRAFT_KEY); if (!raw) return; draft = JSON.parse(raw); } catch(ex) { return; }
  if (!draft) return;
  DRAFT_FIELDS.forEach(function(name) {
    if (draft[name] === undefined) return;
    const el = document.querySelector('[name="' + name + '"]:not([disabled])');
    if (el) el.value = draft[name];
  });
  const catEl = document.getElementById('categorySelect');
  if (catEl && draft.category) {
    catEl.value = draft.category;
    catEl.dispatchEvent(new Event('change'));
    setTimeout(function() {
      const btEl = document.getElementById('businessTypeSelect');
      if (btEl && draft.type_of_business) btEl.value = draft.type_of_business;
    }, 0);
  }
  document.getElementById('locationSelect').dispatchEvent(new Event('change'));
  document.getElementById('draftBanner').classList.add('visible');
}

function clearDraft() {
  try { localStorage.removeItem(DRAFT_KEY); } catch(ex) {}
  document.getElementById('draftBanner').classList.remove('visible');
}

document.getElementById('draftBannerClearBtn').addEventListener('click', function () {
  mbsConfirm({
    intent: 'danger',
    icon: 'trash',
    title: window.MBS_I18N ? window.MBS_I18N.t('confirmDiscardDraftTitle') : 'Buang draf yang disimpan?',
    message: window.MBS_I18N ? window.MBS_I18N.t('draftDiscardConfirm') : 'Buang draf yang disimpan? Semua maklumat yang belum dihantar akan dipadamkan.',
    confirmText: window.MBS_I18N ? window.MBS_I18N.t('draftDiscard') : 'Buang Draf',
    cancelText:  window.MBS_I18N ? window.MBS_I18N.t('confirmKeep') : 'Kekalkan',
    onConfirm: function () {
      clearDraft();
      document.getElementById('applicationForm').reset();
      document.getElementById('categorySelect').dispatchEvent(new Event('change'));
      document.getElementById('locationSelect').dispatchEvent(new Event('change'));
      customCheck.style.backgroundColor = '';
      checkSvg.style.display = 'none';
      ['ssm','ic','lic'].forEach(function(key) { removeUpload(key); });
      removeUpload('loc');
    },
  });
});

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(loadDraft, 50);
});
@if(!$reapplyData)
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(loadDraft, 50);
});
@endif
</script>

<!-- ─── TOAST NOTIFICATION (success / failed) ────────────────────────────────── -->
<script>
function showToast(type, title, message, autoHideMs) {
  const stack = document.getElementById('toastStack');
  if (!stack) return;

  const toast = document.createElement('div');
  toast.className = 'toast toast-' + type;

  const iconSvg = type === 'success'
    ? '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>'
    : '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M9 9l6 6M15 9l-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';

  toast.innerHTML =
    '<div class="toast-icon">' + iconSvg + '</div>' +
    '<div class="toast-body">' +
      '<div class="toast-title"></div>' +
      '<div class="toast-msg"></div>' +
    '</div>' +
    '<button type="button" class="toast-close" aria-label="Tutup">' +
      '<svg viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>' +
    '</button>' +
    '<div class="toast-progress"></div>';

  toast.querySelector('.toast-title').textContent = title;
  toast.querySelector('.toast-msg').textContent   = message;

  stack.appendChild(toast);
  requestAnimationFrame(function () { toast.classList.add('visible'); });

  let hideTimer;
  function dismiss() {
    clearTimeout(hideTimer);
    toast.classList.remove('visible');
    toast.classList.add('leaving');
    setTimeout(function () { toast.remove(); }, 300);
  }

  toast.querySelector('.toast-close').addEventListener('click', dismiss);

  const hideAfter = autoHideMs || 6000;
  const bar = toast.querySelector('.toast-progress');
  bar.style.animationDuration = hideAfter + 'ms';
  hideTimer = setTimeout(dismiss, hideAfter);
}

document.addEventListener('DOMContentLoaded', function () {
  @if(session('success'))
    showToast('success', window.MBS_I18N ? window.MBS_I18N.t('toastSuccess') : 'Berjaya', @json(session('success')));
  @endif

  @if($errors->any())
    showToast(
      'error',
      window.MBS_I18N ? window.MBS_I18N.t('toastFormErrorTitle') : 'Permohonan Gagal Dihantar',
      window.MBS_I18N ? window.MBS_I18N.t('toastFormErrorMsg') : 'Sila semak semula maklumat yang ditandakan di bawah sebelum menghantar.',
      7000
    );
  @endif

  @if(session('error'))
    showToast('error', window.MBS_I18N ? window.MBS_I18N.t('toastError') : 'Ralat', @json(session('error')), 7000);
  @endif
});
</script>

</body>
</html>