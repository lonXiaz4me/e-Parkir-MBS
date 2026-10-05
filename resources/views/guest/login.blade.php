<!DOCTYPE html>
<html lang="ms">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Log Masuk — Sistem Petak Sewa MBS</title>
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
</head>

<body>

  <div class="login-shell">

    <!-- LEFT PANEL -->
    <div class="left-panel">
      <div class="brand-row">
        <div class="brand-logo">
          <img src="{{ asset('500px-Majlis_Bandaraya_Seremban.svg.png') }}" alt="MBS"
            style="width:50px;height:50px;object-fit:contain;"
            onerror="this.style.display='none';this.parentNode.innerHTML='MBS'" />
        </div>
        <div class="brand-txt">
          <div class="b-name">Majlis Bandaraya<br>Seremban</div>
          <div class="b-sub">e-Parkir MBS</div>
        </div>
      </div>

      <div class="hero-title">Sistem Pengurusan<br><span>Sewa Petak Parkir</span></div>
      <div class="hero-desc">Platform digital bersepadu untuk memudahkan proses permohonan, semakan dan pembayaran sewa
        petak parkir MBS.</div>

      <div class="feature-list">
        <div class="feat">
          <div class="feat-icon">
            <svg viewBox="0 0 24 24">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
              <polyline points="14 2 14 8 20 8" />
              <line x1="9" y1="13" x2="15" y2="13" />
            </svg>
          </div>
          <div class="feat-text">
            <div class="ft-title">Permohonan Digital</div>
            <div class="ft-desc">Hantar permohonan dalam talian dengan mudah</div>
          </div>
        </div>
        <div class="feat">
          <div class="feat-icon">
            <svg viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
          </div>
          <div class="feat-text">
            <div class="ft-title">Pemantauan Masa Nyata</div>
            <div class="ft-desc">Jejaki status permohonan secara langsung</div>
          </div>
        </div>
        <div class="feat">
          <div class="feat-icon">
            <svg viewBox="0 0 24 24">
              <rect x="1" y="4" width="22" height="16" rx="2" />
              <line x1="1" y1="10" x2="23" y2="10" />
            </svg>
          </div>
          <div class="feat-text">
            <div class="ft-title">Pembayaran Online</div>
            <div class="ft-desc">Bayar bil & dapatkan resit digital serta-merta</div>
          </div>
        </div>
      </div>

      <div class="left-footer">
        © 2025 Majlis Bandaraya Seremban · <a href="#">Dasar Privasi</a> · <a href="#">Hubungi Kami</a>
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
      <div class="login-title">Log Masuk</div>
      <div class="login-sub">Sila log masuk menggunakan akaun MBS Click atau e-mel anda.</div>

      <!-- SSO BUTTONS -->
      <div class="sso-block">
        <div class="sso-label">Log Masuk Melalui</div>
        <button class="sso-btn">
          <div class="sso-btn-icon blue">
            <svg viewBox="0 0 24 24">
              <path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2z" />
              <path d="M12 8v8M8 12h8" />
            </svg>
          </div>
          MBS Click (SSO)
          <span style="font-size:10px;color:#aaa;font-weight:400;margin-left:4px">— Portal Rasmi MBS</span>
          <div class="sso-chevron"><svg viewBox="0 0 24 24">
              <polyline points="9 18 15 12 9 6" />
            </svg></div>
        </button>
        <button class="sso-btn">
          <div class="sso-btn-icon red">
            <svg viewBox="0 0 24 24">
              <path
                d="M20.317 4.37a19.79 19.79 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.74 19.74 0 0 0 3.677 4.37" />
              <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
            </svg>
          </div>
          eHasil MBS
          <span style="font-size:10px;color:#aaa;font-weight:400;margin-left:4px">— Pentadbir &amp; Pegawai</span>
          <div class="sso-chevron"><svg viewBox="0 0 24 24">
              <polyline points="9 18 15 12 9 6" />
            </svg></div>
        </button>
      </div>

      <div class="divider"><span>atau log masuk dengan e-mel</span></div>

      <!-- LOGIN FORM -->
      <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="form-field">
          <label class="f-label">Alamat E-mel</label>
          <input type="email" name="email" class="f-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
            value="{{ old('email') }}" placeholder="contoh@emel.com">
        </div>

        <div class="form-field">
          <div class="password-header">
            <div class="f-label">Kata Laluan</div>
            <a href="{{ route('password.otp.request') }}" class="forgot-link">Lupa kata laluan?</a>
          </div>
          <div class="input-wrapper">
            <input type="password" id="password" name="password"
              class="f-input {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="••••••••">
            <button type="button" class="eye-toggle" onclick="togglePassword('password', this)">
              <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        @if ($errors->any())
          <div class="error-alert">
            <ul class="error-list">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <button type="submit" class="btn-login">Log Masuk</button>
      </form>

      <div class="otp-hint">
        <div class="otp-dot"></div>
        <div class="otp-text">Pengesahan OTP akan dihantar ke nombor telefon bimbit anda semasa log masuk. Pastikan
          nombor telefon anda adalah terkini.</div>
      </div>

      <div class="auth-link-row">
        Belum ada akaun? <a href="{{ route('register') }}">Daftar Sekarang →</a>
      </div>

      <div class="right-footer">
        Versi 1.0.0 · Dibangunkan oleh Oval Success Sdn. Bhd.
      </div>
    </div>

  </div>

  <script>
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
  </script>

</body>

</html>