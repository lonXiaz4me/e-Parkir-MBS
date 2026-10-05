<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Profil Pengguna — Sistem Petak Sewa MBS</title>
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/payment.css') }}"> 
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
</head>
<body>

<div class="shell">

  @include('auth.partials.sidebar')

  <div class="topbar">
    <span class="breadcrumb">
      <a href="#" data-i18n="breadcrumbHome">Laman Utama</a><span class="sep">/</span>
      <span class="current" data-i18n="crumbProfile">Profil Pengguna</span>
    </span>
    <div class="topbar-right">
      @include('auth.partials.notif-panel')
    </div>
  </div>

  @include('auth.partials.confirm-modal')

  <div class="main">
    <div class="page-title" data-i18n="profPageTitle">Profil Pengguna</div>
    <div class="page-sub" data-i18n="profPageSub">Kemaskini maklumat peribadi dan kata laluan akaun anda.</div>

    @if(session('success'))
    <div class="success-alert">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
      {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="past-card overdue" style="padding: 16px; margin-bottom: 20px; border-radius:8px;">
      <div class="past-icon overdue"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
      <div class="past-info">
        <div class="past-type" style="color:#C0392B; font-weight:600;" data-i18n="formErrorFix">Sila betulkan ralat borang di bawah.</div>
      </div>
    </div>
    @endif

    <div class="pay-grid">

      <div>
        <div class="section-label" data-i18n="profAccountInfo">Maklumat Akaun</div>
        <div class="profile-card">
          <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
              <label data-i18n="profLabelFullName">Nama Penuh (Syarikat / Individu)</label>
              <input type="text" class="form-control" value="{{ auth()->user()->full_name }}" disabled />
              <small style="color:#64748b; font-size:0.75rem;" data-i18n="profLabelFullNameHint">Sila hubungi pentadbir MBS jika terdapat perubahan nama berdaftar.</small>
            </div>

            <div class="input-row">
              <div class="form-group">
                <label for="email" data-i18n="profLabelEmail">Alamat Emel</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required />
                @error('email') <div class="error-msg">{{ $message }}</div> @enderror
              </div>

              <div class="form-group">
                <label for="phone_no" data-i18n="profLabelPhone">No. Telefon Bimbit</label>
                <input type="text" id="phone_no" name="phone_no" class="form-control" value="{{ old('phone_no', auth()->user()->phone_no) }}" placeholder="Contoh: 0123456789" required />
                @error('phone_no') <div class="error-msg">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="form-group">
              <label for="account_current_password" data-i18n="profLabelCurrentPw">Kata Laluan Semasa</label>
              <div class="input-wrapper">
                <input type="password" id="account_current_password" name="current_password" class="form-control" required autocomplete="current-password" />
                <button type="button" class="eye-toggle" onclick="togglePassword('account_current_password', this)">
                  <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>
              <small style="color:#64748b; font-size:0.75rem;" data-i18n="profConfirmIdentityHint">Diperlukan untuk mengesahkan perubahan emel atau no. telefon.</small>
              @error('current_password') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div style="margin-top: 10px;">
              <button type="submit" class="btn-pay" style="width: auto; padding: 10px 24px;" data-i18n="profSaveInfo">Simpan Maklumat Profil</button>
            </div>
          </form>
        </div>
      </div>

      <div>
        <div class="section-label" data-i18n="profSecurity" data-i18n-html>Keselamatan &amp; Kata Laluan</div>
        <div class="profile-card">
          <form action="{{ route('profile.password') }}" method="POST" id="passwordChangeForm">
            @csrf
            @method('PUT')

            <div class="form-group">
              <label for="current_password" data-i18n="profLabelCurrentPw">Kata Laluan Semasa</label>
              <div class="input-wrapper">
                <input type="password" id="current_password" name="current_password" class="form-control" required />
                <button type="button" class="eye-toggle" onclick="togglePassword('current_password', this)">
                  <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>
              @error('current_password') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
              <label for="password" data-i18n="profLabelNewPw">Kata Laluan Baru</label>
              <div class="input-wrapper">
                <input type="password" id="password" name="password" class="form-control" required oninput="validatePassword(this.value); checkPasswordMatch();" />
                <button type="button" class="eye-toggle" onclick="togglePassword('password', this)">
                  <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>
              @error('password') <div class="error-msg">{{ $message }}</div> @enderror

              <!-- Strength bar & badges (mirrors register page) -->
              <div class="strength-container">
                <div class="strength-meter">
                  <div id="meter-bar"></div>
                </div>
                <div class="badge-container">
                  <span id="req-length" class="badge">8+ Aksara</span>
                  <span id="req-upper" class="badge">Huruf Besar</span>
                  <span id="req-number" class="badge">Nombor</span>
                  <span id="req-symbol" class="badge">Simbol</span>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="password_confirmation" data-i18n="profLabelConfirmPw">Sahkan Kata Laluan Baru</label>
              <div class="input-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required oninput="checkPasswordMatch()" />
                <button type="button" class="eye-toggle" onclick="togglePassword('password_confirmation', this)">
                  <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>
              <div id="match-text" class="error-msg"></div>
            </div>

            <div style="margin-top: 24px;">
              <button type="submit" class="btn-pay" style="width: 100%; background: --color-yellow;" data-i18n="profChangePassword">Tukar Kata Laluan</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
// ─── PASSWORD EYE TOGGLE ──────────────────────────────────────────────────────
function togglePassword(inputId, button) {
  const input = document.getElementById(inputId);
  const icon = button.querySelector('.eye-icon');
  if (input.type === 'password') {
    input.type = 'text';
    icon.style.stroke = '#F5C518';
    icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
  } else {
    input.type = 'password';
    icon.style.stroke = 'currentColor';
    icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
  }
}

// ─── PASSWORD STRENGTH METER ──────────────────────────────────────────────────
// Mirrors the register page's meter and rule set — kept in sync with
// ProfileController::updatePassword(): min(8)->letters()->mixedCase()
// ->numbers()->symbols().
function validatePassword(password) {
  const bar = document.getElementById('meter-bar');
  const checks = {
    length: password.length >= 8,
    upper: /[A-Z]/.test(password),
    number: /[0-9]/.test(password),
    symbol: /[^A-Za-z0-9]/.test(password)
  };

  Object.keys(checks).forEach(key => {
    const el = document.getElementById(`req-${key}`);
    checks[key] ? el.classList.add('valid') : el.classList.remove('valid');
  });

  const score = Object.values(checks).filter(Boolean).length;
  bar.className = '';
  if (score > 0 && score <= 2) bar.classList.add('weak');
  else if (score === 3) bar.classList.add('medium');
  else if (score === 4) bar.classList.add('strong');
}

// ─── PASSWORD MATCH CHECK ──────────────────────────────────────────────────────
// Mirrors the register page's checkMatch() so the user sees immediately
// whether "Sahkan Kata Laluan Baru" matches "Kata Laluan Baru" — no need to
// wait for the server's password.confirmed error after submitting.
function checkPasswordMatch() {
  const p1 = document.getElementById('password').value;
  const p2 = document.getElementById('password_confirmation').value;
  const matchText = document.getElementById('match-text');

  if (p2.length > 0) {
    matchText.style.display = 'block';
    if (p1 === p2) {
      matchText.innerText = 'Kata laluan sepadan';
      matchText.style.color = '#27ae60';
    } else {
      matchText.innerText = 'Kata laluan tidak sepadan';
      matchText.style.color = '#C0392B';
    }
  } else {
    matchText.style.display = 'none';
  }
}

// ─── CONFIRM BEFORE PASSWORD CHANGE ────────────────────────────────────────────
// Changing the password invalidates the old one immediately and (per the
// controller fix) cannot be undone from this page. Confirm with the user
// before submitting so a slipped click doesn't lock them out unexpectedly.
const passwordChangeFormEl = document.getElementById('passwordChangeForm');
if (passwordChangeFormEl && typeof mbsConfirm === 'function') {
  passwordChangeFormEl.addEventListener('submit', function (e) {
    e.preventDefault();
    mbsConfirm({
      intent: 'warning',
      icon: 'lock',
      title: window.MBS_I18N ? window.MBS_I18N.t('confirmPasswordTitle') : 'Tukar kata laluan?',
      message: window.MBS_I18N ? window.MBS_I18N.t('confirmPasswordMsg') : 'Kata laluan semasa anda akan digantikan serta-merta. Pastikan kata laluan baru telah disimpan di tempat yang selamat.',
      confirmText: window.MBS_I18N ? window.MBS_I18N.t('profChangePassword') : 'Tukar Kata Laluan',
      cancelText: window.MBS_I18N ? window.MBS_I18N.t('confirmSubmitCancel') : 'Batal',
      onConfirm: function () { passwordChangeFormEl.submit(); },
    });
  });
}
</script>
</body>
</html>