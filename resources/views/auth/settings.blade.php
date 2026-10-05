<!DOCTYPE html>
<html lang="ms" id="htmlRoot">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Tetapan — Sistem Petak Sewa MBS</title>
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
  <style>
    /* ── SETTINGS TOGGLE ───────────────────────────────────────────────── */
    .setting-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 0;
      border-bottom: 1px solid #f0ede6;
    }
    .setting-row:last-child { border-bottom: none; }

    .setting-info { display: flex; flex-direction: column; gap: 3px; }
    .setting-label {
      font-size: 0.875rem;
      font-weight: 600;
      color: #334155;
    }
    .setting-desc {
      font-size: 0.78rem;
      color: #94a3b8;
      line-height: 1.4;
    }

    /* Toggle switch */
    .toggle-wrap { position: relative; flex-shrink: 0; }
    .toggle-input {
      position: absolute;
      opacity: 0;
      width: 0;
      height: 0;
    }
    .toggle-track {
      display: flex;
      align-items: center;
      width: 44px;
      height: 24px;
      background: #e2e8f0;
      border-radius: 12px;
      cursor: pointer;
      transition: background 0.2s;
      position: relative;
    }
    .toggle-track::after {
      content: '';
      position: absolute;
      left: 3px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: #fff;
      box-shadow: 0 1px 3px rgba(0,0,0,0.2);
      transition: transform 0.2s;
    }
    .toggle-input:checked + .toggle-track {
      background: #F5C518;
    }
    .toggle-input:checked + .toggle-track::after {
      transform: translateX(20px);
    }

    /* Language option cards */
    .lang-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-top: 4px;
    }
    .lang-card {
      border: 1.5px solid #e2e8f0;
      border-radius: 8px;
      padding: 12px 14px;
      cursor: pointer;
      background: #fff;
      transition: border-color 0.15s, background 0.15s;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .lang-card:hover { border-color: #F5C518; background: #fffdf0; }
    .lang-card.selected {
      border-color: #F5C518;
      background: #fff8d6;
    }
    .lang-flag {
      font-size: 20px;
      flex-shrink: 0;
      line-height: 1;
    }
    .lang-card-name {
      font-size: 13px;
      font-weight: 600;
      color: #334155;
    }
    .lang-card-sub {
      font-size: 11px;
      color: #94a3b8;
      margin-top: 1px;
    }
    .lang-card.selected .lang-card-name { color: #a07000; }

    /* Theme cards */
    .theme-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-top: 4px;
    }
    .theme-card {
      border: 1.5px solid #e2e8f0;
      border-radius: 8px;
      padding: 14px;
      cursor: pointer;
      background: #fff;
      transition: border-color 0.15s, background 0.15s;
      text-align: center;
    }
    .theme-card:hover { border-color: #F5C518; }
    .theme-card.selected { border-color: #F5C518; background: #fff8d6; }

    .theme-preview {
      width: 100%;
      height: 52px;
      border-radius: 5px;
      margin-bottom: 8px;
      position: relative;
      overflow: hidden;
      border: 1px solid #e2e8f0;
    }
    .theme-preview-light {
      background: #f5f0e8;
    }
    .theme-preview-light::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 30%;
      background: #444;
    }
    .theme-preview-light::after {
      content: '';
      position: absolute;
      right: 6px; top: 8px;
      width: 55%;
      height: 8px;
      background: #F5C518;
      border-radius: 3px;
    }
    .theme-preview-dark {
      background: #1a1a1a;
    }
    .theme-preview-dark::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 30%;
      background: #2a2a2a;
      border-right: 1px solid #333;
    }
    .theme-preview-dark::after {
      content: '';
      position: absolute;
      right: 6px; top: 8px;
      width: 55%;
      height: 8px;
      background: #F5C518;
      border-radius: 3px;
      opacity: 0.8;
    }
    .theme-card-name {
      font-size: 12px;
      font-weight: 600;
      color: #334155;
    }
    .theme-card.selected .theme-card-name { color: #a07000; }

    /* Save button */
    .settings-save-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--color-yellow);
      border: none;
      border-radius: 6px;
      padding: 10px 24px;
      font-family: var(--font-ui);
      font-weight: 700;
      font-size: 13px;
      color: #0d0d0d;
      cursor: pointer;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      position: relative;
      margin-top: 4px;
    }
    .settings-save-btn::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 4px;
      background: var(--color-red);
      border-radius: 6px 0 0 6px;
    }
    .settings-save-btn:hover { background: var(--color-yellow-hover); }

    /* Account card — logout button */
    .btn-logout-account {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      background: transparent;
      border: 1.5px solid var(--color-red);
      border-radius: 6px;
      padding: 10px;
      font-family: var(--font-ui);
      font-weight: 700;
      font-size: 13px;
      color: var(--color-red);
      cursor: pointer;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      transition: background 0.15s, color 0.15s;
    }
    .btn-logout-account svg { flex-shrink: 0; }
    .btn-logout-account:hover {
      background: var(--color-red);
      color: #fff;
    }

    /* Toast */
    .settings-toast {
      position: fixed;
      bottom: 80px;
      left: 50%;
      transform: translateX(-50%) translateY(20px);
      background: #1a1a1a;
      color: #fff;
      font-size: 13px;
      font-weight: 500;
      padding: 10px 20px;
      border-radius: 20px;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.25s, transform 0.25s;
      z-index: 9999;
      white-space: nowrap;
      border: 1px solid #333;
    }
    .settings-toast.show {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }

    /* Section icon box (reuse from profile/application) */
    .section-icon-box {
      width: 28px; height: 28px;
      background: #fff8d6;
      border: 1px solid var(--color-yellow);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .section-icon-box svg { width: 14px; height: 14px; stroke: #a07000; fill: none; stroke-width: 1.8; }



    @media (max-width: 768px) {
      .lang-grid, .theme-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
      .settings-toast { bottom: 76px; }

      /* Put "Log Keluar" below "Tetapan Semasa" on mobile only.
         .settings-col-left/-right normally just stack (left column, then
         right column). display:contents removes their own boxes so their
         children — settings-prefs-group, settings-account-group, and the
         summary panel — become siblings directly inside .pay-grid, where
         "order" can place settings-account-group last. Desktop is
         untouched since this whole block only applies ≤768px. */
      .settings-col-left,
      .settings-col-right {
        display: contents;
      }
      .settings-account-group {
        order: 10;
      }
      /* These margins did their job via normal block-flow collapsing
         before; now that the two groups are separate grid items, .pay-grid's
         own gap provides that spacing instead, so drop the duplicates. */
      .settings-prefs-group > .profile-card:last-child {
        margin-bottom: 0;
      }
      .settings-account-group > .section-label {
        margin-top: 0;
      }
    }
  </style>
</head>
<body>

<div class="shell">

  @include('auth.partials.sidebar')

  <div class="topbar">
    <span class="breadcrumb">
      <a href="#" data-i18n="breadcrumbHome">Laman Utama</a><span class="sep">/</span>
      <span class="current" data-i18n="crumbSettings">Tetapan</span>
    </span>
    <div class="topbar-right">

      @include('auth.partials.notif-panel')

    </div>
  </div>

  @include('auth.partials.confirm-modal')

  <div class="main">
    <div class="page-title" id="lbl-page-title">Tetapan</div>
    <div class="page-sub" id="lbl-page-sub">Urus keutamaan paparan dan bahasa aplikasi anda.</div>

    <div class="pay-grid">

      <!-- LEFT: LANGUAGE + THEME -->
      <div class="settings-col-left">

        {{-- Language + Theme stay grouped as one block so the mobile order
             rule below can move settings-account-group past the summary
             panel without splitting these two apart. --}}
        <div class="settings-prefs-group">

        <!-- LANGUAGE CARD -->
        <div class="section-label" id="lbl-lang-section">Bahasa / Language</div>
        <div class="profile-card">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div class="section-icon-box">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <div>
              <div style="font-weight:600;font-size:14px;color:#334155" id="lbl-language-title">Pilih Bahasa Paparan</div>
              <div style="font-size:12px;color:#94a3b8;margin-top:1px" id="lbl-language-sub">Bahasa yang digunakan pada antara muka aplikasi ini.</div>
            </div>
          </div>

          <div class="lang-grid">
            <div class="lang-card" id="lang-ms" onclick="selectLanguage('ms')">
              <div class="lang-flag">🇲🇾</div>
              <div>
                <div class="lang-card-name">Bahasa Melayu</div>
                <div class="lang-card-sub">Lalai</div>
              </div>
            </div>
            <div class="lang-card" id="lang-en" onclick="selectLanguage('en')">
              <div class="lang-flag">🇬🇧</div>
              <div>
                <div class="lang-card-name">English</div>
                <div class="lang-card-sub">Default</div>
              </div>
            </div>
          </div>

          <!-- Live translation preview -->
          <div style="margin-top:14px;padding:12px 14px;background:#fafaf8;border:1px solid #e8e7e0;border-radius:6px;">
            <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#bbb;margin-bottom:6px" id="lbl-preview-title">PRATONTON</div>
            <div style="font-size:12px;color:#555;line-height:1.7" id="lang-preview-text">
              Selamat datang ke Sistem Pengurusan Petak Sewa MBS. Sila semak status permohonan anda di papan pemuka.
            </div>
          </div>
        </div>

        <!-- THEME CARD -->
        <div class="section-label" style="margin-top:20px" id="lbl-theme-section">Tema Paparan</div>
        <div class="profile-card">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div class="section-icon-box">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            </div>
            <div>
              <div style="font-weight:600;font-size:14px;color:#334155" id="lbl-theme-title">Pilih Tema</div>
              <div style="font-size:12px;color:#94a3b8;margin-top:1px" id="lbl-theme-sub">Rupa antara muka yang akan dipaparkan.</div>
            </div>
          </div>

          <div class="theme-grid">
            <div class="theme-card" id="theme-light" onclick="selectTheme('light')">
              <div class="theme-preview theme-preview-light"></div>
              <div class="theme-card-name" id="lbl-theme-light">Cerah</div>
            </div>
            <div class="theme-card" id="theme-dark" onclick="selectTheme('dark')">
              <div class="theme-preview theme-preview-dark"></div>
              <div class="theme-card-name" id="lbl-theme-dark">Gelap</div>
            </div>
          </div>

          <div class="setting-row" style="margin-top:12px;">
            <div class="setting-info">
              <div class="setting-label" id="lbl-auto-theme">Ikut Sistem Peranti</div>
              <div class="setting-desc" id="lbl-auto-theme-sub">Gunakan tema terang atau gelap mengikut tetapan peranti anda secara automatik.</div>
            </div>
            <div class="toggle-wrap">
              <input type="checkbox" class="toggle-input" id="toggle-system-theme" onchange="toggleSystemTheme(this.checked)">
              <label class="toggle-track" for="toggle-system-theme"></label>
            </div>
          </div>
        </div>

        </div> {{-- /.settings-prefs-group --}}

        {{-- ACCOUNT CARD (Logout) — added so logging out is still reachable
             on mobile now that the bottom nav's "Log Keluar" slot was
             swapped for "Tetapan" (see partials/sidebar.blade.php). Reuses
             the same mbsLogoutConfirm() confirmation flow as the desktop
             sidebar. Wrapped in settings-account-group so the mobile order
             rule below can drop it under "Tetapan Semasa" instead of
             leaving it above the summary panel. --}}
        <div class="settings-account-group">
        <div class="section-label" style="margin-top:20px" data-i18n="navSectionAccount">Akaun</div>
        <div class="profile-card">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div class="section-icon-box" style="background:#FCEBEB;border-color:var(--color-red);">
              <svg viewBox="0 0 24 24" style="stroke:var(--color-red);"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </div>
            <div>
              <div style="font-weight:600;font-size:14px;color:#334155" data-i18n="setLogoutTitle">Log Keluar Akaun</div>
              <div style="font-size:12px;color:#94a3b8;margin-top:1px" data-i18n="setLogoutSub">Tamatkan sesi log masuk anda pada peranti ini.</div>
            </div>
          </div>

          <button type="button" class="btn-logout-account" onclick="event.preventDefault(); mbsLogoutConfirm('settings-logout-form');">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span data-i18n="navLogout">Log Keluar</span>
          </button>
        </div>
        <form id="settings-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
          @csrf
        </form>
        </div> {{-- /.settings-account-group --}}

      </div>

      <!-- RIGHT: SUMMARY PANEL -->
      <div class="settings-col-right">
        <div class="summary-panel">
          <div class="sp-header">
            <div class="sp-title" id="lbl-summary-title">Tetapan Semasa</div>
            <div class="sp-sub" id="lbl-summary-sub">Simpan untuk menerapkan perubahan.</div>
          </div>
          <div class="sp-body">

            <div class="sp-row">
              <span class="sp-key" id="lbl-row-lang">Bahasa</span>
              <span class="sp-val" id="summary-language">Bahasa Melayu</span>
            </div>
            <div class="sp-row">
              <span class="sp-key" id="lbl-row-theme">Tema</span>
              <span class="sp-val" id="summary-theme">Cerah</span>
            </div>
            <div class="sp-row">
              <span class="sp-key" id="lbl-row-auto">Ikut Sistem</span>
              <span class="sp-val" id="summary-auto">Tidak</span>
            </div>

            <div class="sp-divider"></div>

            <div style="background:#fff8d6;border:1px solid #e0c060;border-radius:5px;padding:10px 12px;margin-bottom:14px;">
              <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#a07000;margin-bottom:4px" id="lbl-note-title">NOTA</div>
              <div style="font-size:11px;color:#5a4500;line-height:1.6" id="lbl-note-body">Tetapan ini disimpan di peranti ini sahaja dan tidak mempengaruhi akaun anda.</div>
            </div>

            <button type="button" class="settings-save-btn" onclick="saveSettings()" id="saveBtnEl" style="width:100%;justify-content:center;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              <span id="lbl-save-btn">Simpan Tetapan</span>
            </button>

            <button type="button"
              onclick="resetSettings()"
              style="width:100%;margin-top:8px;background:transparent;border:1px solid #d0cfc8;border-radius:6px;padding:9px;font-family:var(--font-ui);font-weight:600;font-size:12px;color:#888;cursor:pointer;letter-spacing:0.04em;text-transform:uppercase;"
              id="resetBtnEl">
              <span id="lbl-reset-btn">Tetapkan Semula</span>
            </button>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Toast -->
<div class="settings-toast" id="settingsToast">✓ Tetapan berjaya disimpan</div>

<script>
// ─── SETTINGS STATE ───────────────────────────────────────────────────────────
// Language strings now live in the shared i18n.js module (window.MBS_I18N) so
// every authenticated page reads from the same dictionary — this page is no
// longer a second source of truth, just the UI that lets the user change it.
const SETTINGS_KEY = 'mbs_settings';

const DEFAULTS = {
  language:    'ms',
  theme:       'light',
  systemTheme: false,
};

// `current` holds an in-progress PREVIEW of the settings (selecting a card
// doesn't persist until "Simpan Tetapan" is pressed) — same UX as before.
let current = Object.assign({}, DEFAULTS, (window.MBS_I18N ? window.MBS_I18N.readSettings() : {}));

// Settings-page-only strings that aren't part of the global dictionary
// (theme card labels, summary panel labels, toast messages, etc.) stay here
// since theme does not apply globally yet — only language does.
const T = {
  ms: {
    langSectionTitle: 'Bahasa / Language',
    langTitle:        'Pilih Bahasa Paparan',
    langSub:          'Bahasa yang digunakan pada antara muka aplikasi ini.',
    previewTitle:     'PRATONTON',
    previewText:      'Selamat datang ke Sistem Pengurusan Petak Sewa MBS. Sila semak status permohonan anda di papan pemuka.',
    themeSection:     'Tema Paparan',
    themeTitle:       'Pilih Tema',
    themeSub:         'Rupa antara muka yang akan dipaparkan.',
    themeLight:       'Cerah',
    themeDark:        'Gelap',
    autoTheme:        'Ikut Sistem Peranti',
    autoThemeSub:     'Gunakan tema terang atau gelap mengikut tetapan peranti anda secara automatik.',
    summaryTitle:     'Tetapan Semasa',
    summarySub:       'Simpan untuk menerapkan perubahan.',
    rowLang:          'Bahasa',
    rowTheme:         'Tema',
    rowAuto:          'Ikut Sistem',
    noteTitle:        'NOTA',
    noteBody:         'Tetapan bahasa akan disimpan dan digunakan pada semua halaman. Tetapan tema disimpan di peranti ini sahaja.',
    saveBtn:          'Simpan Tetapan',
    resetBtn:         'Tetapkan Semula',
    toastSaved:       '✓ Tetapan berjaya disimpan',
    toastReset:       '↺ Tetapan telah ditetapkan semula',
    summaryLight:     'Cerah',
    summaryDark:      'Gelap',
    summaryYes:       'Ya',
    summaryNo:        'Tidak',
    summaryMS:        'Bahasa Melayu',
    summaryEN:        'English',
  },
  en: {
    langSectionTitle: 'Language',
    langTitle:        'Choose Display Language',
    langSub:          'The language used in this application interface.',
    previewTitle:     'PREVIEW',
    previewText:      'Welcome to MBS Parking Space Rental Management System. Please check your application status on the dashboard.',
    themeSection:     'Display Theme',
    themeTitle:       'Choose Theme',
    themeSub:         'The appearance of the interface.',
    themeLight:       'Light',
    themeDark:        'Dark',
    autoTheme:        'Follow System Theme',
    autoThemeSub:     'Automatically use light or dark theme based on your device settings.',
    summaryTitle:     'Current Settings',
    summarySub:       'Save to apply changes.',
    rowLang:          'Language',
    rowTheme:         'Theme',
    rowAuto:          'Follow System',
    noteTitle:        'NOTE',
    noteBody:         'Language settings are saved and applied across every page. Theme is saved on this device only.',
    saveBtn:          'Save Settings',
    resetBtn:         'Reset to Default',
    toastSaved:       '✓ Settings saved successfully',
    toastReset:       '↺ Settings have been reset',
    summaryLight:     'Light',
    summaryDark:      'Dark',
    summaryYes:       'Yes',
    summaryNo:        'No',
    summaryMS:        'Bahasa Melayu',
    summaryEN:        'English',
  }
};

// ─── LOAD SETTINGS ────────────────────────────────────────────────────────────
function loadSettings() {
  current = Object.assign({}, DEFAULTS, (window.MBS_I18N ? window.MBS_I18N.readSettings() : {}));
  applySettings(false);
}

// ─── APPLY SETTINGS (no save) ─────────────────────────────────────────────────
function applySettings(animate) {
  const lang  = current.language;
  const theme = resolveTheme();

  // Apply theme class to <html> (theme remains local to this page for now)
  const html = document.getElementById('htmlRoot');
  html.classList.toggle('theme-dark', theme === 'dark');

  // Highlight selected language card
  document.getElementById('lang-ms').classList.toggle('selected', lang === 'ms');
  document.getElementById('lang-en').classList.toggle('selected', lang === 'en');

  // Highlight selected theme card
  document.getElementById('theme-light').classList.toggle('selected', theme === 'light');
  document.getElementById('theme-dark').classList.toggle('selected', theme === 'dark');

  // System theme toggle state
  document.getElementById('toggle-system-theme').checked = current.systemTheme;

  // Apply translations to the page text (shared keys via MBS_I18N.t, plus
  // settings-only keys via the local T table above)
  applyTranslations(lang);

  // Update summary panel
  updateSummary(lang, theme);
}

function resolveTheme() {
  if (current.systemTheme) {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }
  return current.theme;
}

// ─── APPLY TRANSLATIONS ───────────────────────────────────────────────────────
function applyTranslations(lang) {
  const t = T[lang] || T['ms'];
  const set = (id, text) => { const el = document.getElementById(id); if (el) el.textContent = text; };

  // Page title/sub use the shared dictionary so they match every other page
  if (window.MBS_I18N) {
    set('lbl-page-title', window.MBS_I18N.t('setPageTitle'));
    set('lbl-page-sub',   window.MBS_I18N.t('setPageSub'));
  }

  set('lbl-language-title',   t.langTitle);
  set('lbl-lang-section',     t.langSectionTitle);
  set('lbl-language-sub',     t.langSub);
  set('lbl-preview-title',    t.previewTitle);
  set('lang-preview-text',    t.previewText);
  set('lbl-theme-section',    t.themeSection);
  set('lbl-theme-title',      t.themeTitle);
  set('lbl-theme-sub',        t.themeSub);
  set('lbl-theme-light',      t.themeLight);
  set('lbl-theme-dark',       t.themeDark);
  set('lbl-auto-theme',       t.autoTheme);
  set('lbl-auto-theme-sub',   t.autoThemeSub);
  set('lbl-summary-title',    t.summaryTitle);
  set('lbl-summary-sub',      t.summarySub);
  set('lbl-row-lang',         t.rowLang);
  set('lbl-row-theme',        t.rowTheme);
  set('lbl-row-auto',         t.rowAuto);
  set('lbl-note-title',       t.noteTitle);
  set('lbl-note-body',        t.noteBody);
  set('lbl-save-btn',         t.saveBtn);
  set('lbl-reset-btn',        t.resetBtn);
}

// ─── UPDATE SUMMARY PANEL ─────────────────────────────────────────────────────
function updateSummary(lang, theme) {
  const t = T[lang] || T['ms'];
  const set = (id, text) => { const el = document.getElementById(id); if (el) el.textContent = text; };

  set('summary-language', lang === 'ms' ? t.summaryMS : t.summaryEN);
  set('summary-theme',    theme === 'light' ? t.summaryLight : t.summaryDark);
  set('summary-auto',     current.systemTheme ? t.summaryYes : t.summaryNo);
}

// ─── SELECT LANGUAGE ─────────────────────────────────────────────────────────
// Selecting a card previews the change on THIS page immediately (same as
// before) but does not persist/broadcast until Save is pressed — preserving
// the existing "preview before commit" UX for the settings page itself.
function selectLanguage(lang) {
  current.language = lang;
  applySettings(true);
}

// ─── SELECT THEME ─────────────────────────────────────────────────────────────
function selectTheme(theme) {
  current.theme = theme;
  // If user manually picks, turn off system-follow
  current.systemTheme = false;
  document.getElementById('toggle-system-theme').checked = false;
  applySettings(true);
}

// ─── SYSTEM THEME TOGGLE ─────────────────────────────────────────────────────
function toggleSystemTheme(checked) {
  current.systemTheme = checked;
  applySettings(true);
}

// Listen for OS theme changes while system-follow is on
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
  if (current.systemTheme) applySettings(false);
});

// ─── SAVE ─────────────────────────────────────────────────────────────────────
// Persists language through the shared MBS_I18N module (which also broadcasts
// the change to every other open tab/page via the storage event + a same-tab
// custom event), and persists theme locally as before.
function saveSettings() {
  if (window.MBS_I18N) {
    window.MBS_I18N.setLanguage(current.language); // persists + broadcasts + re-applies this page
  }
  try { localStorage.setItem(SETTINGS_KEY, JSON.stringify(current)); } catch(e) {}
  const t = T[current.language] || T['ms'];
  showToast(t.toastSaved);
}

// ─── RESET ────────────────────────────────────────────────────────────────────
function resetSettings() {
  current = Object.assign({}, DEFAULTS);
  if (window.MBS_I18N) {
    window.MBS_I18N.setLanguage(DEFAULTS.language);
  }
  try { localStorage.setItem(SETTINGS_KEY, JSON.stringify(current)); } catch(e) {}
  applySettings(true);
  const t = T['ms']; // default language after reset is ms
  showToast(t.toastReset);
}

// ─── TOAST ────────────────────────────────────────────────────────────────────
let toastTimer = null;
function showToast(msg) {
  const toast = document.getElementById('settingsToast');
  toast.textContent = msg;
  toast.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(function() { toast.classList.remove('show'); }, 2800);
}

// ─── INIT ─────────────────────────────────────────────────────────────────────
loadSettings();

// Keep this page's preview state in sync if the language is changed from
// another tab/page while this page is open.
window.addEventListener('storage', function (e) {
  if (e.key === SETTINGS_KEY) loadSettings();
});
</script>
</body>
</html>