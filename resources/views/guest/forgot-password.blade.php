<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tetapan Semula Kata Laluan — Sistem Petak Sewa MBS</title>
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forgot-password.css') }}">
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

            <div class="hero-title">Keselamatan<br><span>Akaun Anda</span></div>
            <div class="hero-desc">Sistem pengesahan pelbagai peringkat memastikan data permohonan petak parkir anda
                sentiasa dilindungi dengan selamat.</div>

            <div class="left-footer">
                © 2026 Majlis Bandaraya Seremban · <a href="#">Dasar Privasi</a>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">

            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-alert">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $step = session('step', 1);
                $savedEmail = session('email', old('email'));
            @endphp

            {{-- STEP 1: Request OTP --}}
            @if ($step == 1)
                <div class="login-title">Lupa Kata Laluan?</div>
                <div class="login-sub">Masukkan e-mel anda untuk menerima kod pengesahan OTP.</div>

                <form action="{{ route('password.otp.send') }}" method="POST">
                    @csrf
                    <div class="form-field">
                        <label class="f-label">Alamat E-mel Akaun</label>
                        <input type="email" name="email" class="f-input" value="{{ old('email') }}"
                            placeholder="contoh@emel.com" required>
                    </div>
                    <button type="submit" class="btn-login">Hantar Kod OTP</button>
                </form>
            @endif

            {{-- STEP 2: Verify OTP --}}
            @if ($step == 2)
                <div class="login-title">Sahkan Kod OTP</div>
                <div class="login-sub">Sila periksa peti masuk e-mel <strong>{{ $savedEmail }}</strong>.</div>

                <form action="{{ route('password.otp.verify') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ $savedEmail }}">
                    <div class="form-field">
                        <label class="f-label">Kod OTP (6 Digit)</label>
                        <input type="text" name="otp" class="f-input otp-input" placeholder="000000" maxlength="6"
                            pattern="\d{6}" required autocomplete="off">
                    </div>
                    <button type="submit" class="btn-login">Sahkan Kod</button>
                </form>
            @endif

            {{-- STEP 3: Reset Password --}}
            @if ($step == 3)
                <div class="login-title">Kata Laluan Baru</div>
                <div class="login-sub">Sila tetapkan kata laluan baru yang selamat untuk akaun anda.</div>

                <form action="{{ route('password.otp.resetSubmit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ $savedEmail }}">

                    <div class="form-field">
                        <label class="f-label">Kata Laluan Baru</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="f-input" placeholder="••••••••"
                                required>
                            <button type="button" class="eye-toggle" onclick="togglePassword('password', this)">
                                <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="f-label">Sahkan Kata Laluan</label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="f-input"
                                placeholder="••••••••" required>
                            <button type="button" class="eye-toggle"
                                onclick="togglePassword('password_confirmation', this)">
                                <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">Kemaskini Kata Laluan</button>
                </form>
            @endif

            <div class="auth-link-row">
                Kembali ke halaman <a href="{{ route('welcome') }}">Log Masuk</a>
            </div>

            <div class="right-footer">Versi 1.0.0 · MBS Petak Sewa</div>
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