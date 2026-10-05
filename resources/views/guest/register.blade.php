<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar Akaun — Sistem Petak Sewa MBS</title>
    <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
</head>

<body>

    <div class="login-shell">

        <!-- LEFT PANEL -->
        <div class="left-panel">
            <div class="brand-row">
                <div class="brand-logo">
                    <img src="{{ asset('500px-Majlis_Bandaraya_Seremban.svg.png') }}" alt="MBS"
                        onerror="this.style.display='none';this.parentNode.innerHTML='MBS'" />
                </div>
                <div class="brand-txt">
                    <div class="b-name">Majlis Bandaraya<br>Seremban</div>
                    <div class="b-sub">e-Parkir MBS</div>
                </div>
            </div>

            <div class="hero-title">Sistem Pengurusan<br><span>Sewa Petak Parkir</span></div>
            <div class="hero-desc">Platform digital bersepadu untuk memudahkan proses permohonan, semakan dan pembayaran
                sewa petak parkir MBS.</div>

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
            <div class="reg-title">Pendaftaran Akaun</div>
            <div class="reg-sub">Lengkapkan maklumat di bawah untuk mendaftar.</div>

            <!-- REGISTER FORM -->
            <form action="{{ route('register.store') }}" method="POST">
                @csrf
                <div class="form-grid">

                    <div class="form-field full-width">
                        <label class="f-label">Nama Penuh (Seperti MyKad)</label>
                        <input type="text" name="full_name"
                            class="f-input {{ $errors->has('full_name') ? 'is-invalid' : '' }}"
                            value="{{ old('full_name') }}" placeholder="MUHAMMAD ALI BIN ABU">
                        @error('full_name') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-field">
                        <label class="f-label">No. MyKad</label>
                        <input type="text" name="ic_no" id="ic_no" class="f-input {{ $errors->has('ic_no') ? 'is-invalid' : '' }}"
                            value="{{ old('ic_no') }}" placeholder="000000-00-0000" maxlength="14"
                            oninput="formatIcNumber(this)" inputmode="numeric">
                        @error('ic_no') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-field">
                        <label class="f-label">No. Telefon</label>
                        <input type="text" name="phone_no"
                            class="f-input {{ $errors->has('phone_no') ? 'is-invalid' : '' }}"
                            value="{{ old('phone_no') }}" placeholder="0123456789">
                        @error('phone_no') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-field full-width">
                        <label class="f-label">Alamat E-mel</label>
                        <input type="email" name="email" class="f-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}" placeholder="contoh@emel.com">
                        @error('email') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-field">
                        <label class="f-label">Kata Laluan</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="f-input" placeholder="••••••••"
                                oninput="validatePassword(this.value)">
                            <button type="button" class="eye-toggle" onclick="togglePassword('password', this)">
                                <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <div id="match-text" class="error-text"></div>
                    </div>

                    <div class="form-field">
                        <label class="f-label">Sahkan Kata Laluan</label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="f-input" placeholder="••••••••" oninput="checkMatch()">
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

                </div>

                <!-- Strength bar & badges -->
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

                <button type="submit" class="btn-reg">Daftar Akaun</button>
            </form>

            <div class="auth-link-row">
                Sudah mempunyai akaun? <a href="{{ route('welcome') }}">Log Masuk DiSini →</a>
            </div>

            <div class="right-footer">Versi 1.0.0 · Dibangunkan oleh Oval Success Sdn. Bhd.</div>
        </div>

    </div>

    <script>
        // FIX #12: Live-format the IC number as the user types, so input
        // like "970101145523" automatically becomes "970101-14-5523".
        // This matches the placeholder format and matches what
        // MalaysianIcNumber validation rule expects server-side.
        function formatIcNumber(input) {
            // Strip everything except digits
            let digits = input.value.replace(/\D/g, '').slice(0, 12);

            let formatted = digits;
            if (digits.length > 6) {
                formatted = digits.slice(0, 6) + '-' + digits.slice(6);
            }
            if (digits.length > 8) {
                formatted = digits.slice(0, 6) + '-' + digits.slice(6, 8) + '-' + digits.slice(8);
            }

            input.value = formatted;
        }

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

            checkMatch();
        }

        function checkMatch() {
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
    </script>

</body>

</html>