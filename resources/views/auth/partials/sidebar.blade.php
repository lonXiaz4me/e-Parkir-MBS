{{-- ============================================================
     resources/views/partials/sidebar.blade.php
     Requires: tokens.css, sidebar.css (loaded in the layout)
     ============================================================ --}}

{{-- Shared language switcher — included here so every authenticated page
     (Dashboard, Application, Payment, Profile, Settings) gets it for free,
     since this partial is @include'd on all of them. --}}
<script src="{{ asset('js/i18n.js') }}"></script>

<script>
(function() {
    function applyGlobalTheme() {
        try {
            const settings = JSON.parse(localStorage.getItem('mbs_settings')) || {};
            let theme = settings.theme || 'light';
            if (settings.systemTheme) {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (theme === 'dark') {
                document.documentElement.classList.add('theme-dark');
            } else {
                document.documentElement.classList.remove('theme-dark');
            }
        } catch (e) {
            console.error('Error loading theme:', e);
        }
    }

    // Apply theme immediately on load to prevent flash of light theme
    applyGlobalTheme();

    // Listen for storage events (updates theme across other tabs instantly)
    window.addEventListener('storage', function(e) {
        if (e.key === 'mbs_settings') {
            applyGlobalTheme();
        }
    });

    // Listen for system theme preferences changes if "Follow System" is checked
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
        applyGlobalTheme();
    });
})();
</script>



{{-- ═══════════════════════════════════════════════════════════
     MOBILE HEADER  (visible only on ≤768px)
═══════════════════════════════════════════════════════════ --}}
<header class="mobile-header" id="mobileHeader">
    <div class="mobile-header-brand">
        <div class="mobile-header-logo">
            <img src="{{ asset('500px-Majlis_Bandaraya_Seremban.svg.png') }}"
                 alt="MBS"
                 onerror="this.style.display='none';this.parentNode.innerHTML='MBS'"/>
        </div>
        <div class="mobile-header-text">
            <div class="mobile-header-name">Majlis Bandaraya Seremban</div>
            <div class="mobile-header-sub" data-i18n="brandSub">e-Parkir MBS</div>
        </div>
    </div>

    <div class="mobile-header-right" id="mobileHeaderRight">
        {{-- Bell button --}}
        <div class="bell-btn" id="mobileBellBtn" onclick="toggleMobileNotifPanel()">
            <svg viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <div class="bell-dot" id="mobileBellDot"></div>
        </div>

        {{-- Notification panel (drops below mobile header) --}}
        <div class="notif-panel" id="mobileNotifPanel">
            <div class="np-header">
                <span class="np-title">
                    <span data-i18n="notifTitle">Pemberitahuan</span>
                    <span class="np-badge" id="mobileUnreadCount"></span>
                </span>
                <button class="np-read-all" data-i18n="notifReadAll" onclick="mobileMarkAllRead()">Baca Semua</button>
            </div>
            <div class="np-tabs">
                <div class="np-tab active" data-i18n="notifTabAll" onclick="switchMobileTab('all', this)">Semua</div>
                <div class="np-tab" data-i18n="notifTabUnread" onclick="switchMobileTab('unread', this)">Belum Baca</div>
                <div class="np-tab" data-i18n="notifTabStatus" onclick="switchMobileTab('status', this)">Status</div>
                <div class="np-tab" data-i18n="notifTabPayment" onclick="switchMobileTab('payment', this)">Bayaran</div>
            </div>
            <div class="np-list" id="mobileNotifList">
                <div class="np-empty">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <p data-i18n="notifLoading">Memuatkan...</p>
                </div>
            </div>
            <div class="np-footer">
                <a href="#" data-i18n="notifViewAll">Lihat semua pemberitahuan →</a>
            </div>
        </div>
    </div>
</header>


{{-- ═══════════════════════════════════════════════════════════
     DESKTOP SIDEBAR  (hidden on ≤768px)
═══════════════════════════════════════════════════════════ --}}
<aside class="sidebar" id="mobileSidebar">

    {{-- Brand --}}
    <div class="brand">
        <div class="brand-box">
            <img src="{{ asset('500px-Majlis_Bandaraya_Seremban.svg.png') }}"
                 alt="MBS"
                 onerror="this.style.display='none';this.parentNode.innerHTML='MBS'"/>
        </div>
        <div class="brand-text">
            <div class="brand-name" data-i18n-html data-i18n="brandCouncil">Majlis Bandaraya<br>Seremban</div>
        </div>
    </div>

    {{-- User block --}}
    <div class="user-block">
        <div class="user-av">
            {{ collect(explode(' ', auth()->user()->full_name))
                ->map(fn($n) => Str::upper($n[0]))
                ->take(2)
                ->join('') }}
        </div>
        <div>
            <div class="user-name">{{ auth()->user()->full_name }}</div>
            <div class="user-role" data-i18n="userRoleApplicant">Pemohon</div>
        </div>
    </div>

    {{-- Main nav --}}
    <div class="nav-section-label" data-i18n="navSectionMain">Menu Utama</div>

    <a href="{{ route('dashboard') }}" class="nav-link">
        <div class="nav-item {{ Request::is('*dashboard*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span data-i18n="navDashboard">Papan Pemuka</span>
        </div>
    </a>

    <a href="{{ route('application') }}" class="nav-link">
        <div class="nav-item {{ Request::is('*application*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span data-i18n="navApplication">Permohonan Baru</span>
        </div>
    </a>

    <a href="{{ route('payment.index') }}" class="nav-link">
        <div class="nav-item {{ Request::is('*payment*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span data-i18n-html data-i18n="navPayment">Resit &amp; Bayaran</span>
        </div>
    </a>

    {{-- Account nav --}}
    <div class="nav-section-label" data-i18n="navSectionAccount">Akaun</div>

    <a href="{{ route('profile.edit') }}" class="nav-link">
        <div class="nav-item {{ Request::is('*profile*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span data-i18n="navProfile">Profil Saya</span>
        </div>
    </a>

    <a href="{{ route('settings') }}" class="nav-link">
        <div class="nav-item {{ Request::is('*settings*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span data-i18n="navSettings">Tetapan</span>
        </div>
    </a>

    {{-- Logout --}}
    <div class="sb-footer">
        <a href="{{ route('logout') }}"
           class="nav-link"
           onclick="event.preventDefault(); mbsLogoutConfirm('logout-form');">
            <div class="nav-item nav-logout">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                    <path d="M6 2H3a1 1 0 00-1 1v10a1 1 0 001 1h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11 11l3-3-3-3M14 8H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span data-i18n="navLogout">Log Keluar</span>
            </div>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>

</aside>


{{-- ═══════════════════════════════════════════════════════════
     MOBILE BOTTOM NAV  (visible only on ≤768px)
═══════════════════════════════════════════════════════════ --}}
<nav class="mobile-bottom-nav" id="mobileBottomNav">

    {{-- Papan Pemuka --}}
    <a href="{{ route('dashboard') }}"
       class="mob-nav-item {{ Request::is('*dashboard*') ? 'active' : '' }}">
        <div class="mob-nav-icon">
            <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
        </div>
        <span class="mob-nav-label" data-i18n="mobNavDashboard">Dashboard</span>
    </a>

    {{-- Permohonan Baru --}}
    <a href="{{ route('application') }}"
       class="mob-nav-item {{ Request::is('*application*') ? 'active' : '' }}">
        <div class="mob-nav-icon">
            <svg viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <span class="mob-nav-label" data-i18n="mobNavApplication">Permohonan</span>
    </a>

    {{-- Resit & Bayaran --}}
    <a href="{{ route('payment.index') }}"
       class="mob-nav-item {{ Request::is('*payment*') ? 'active' : '' }}">
        <div class="mob-nav-icon">
            <svg viewBox="0 0 24 24">
                <rect x="1" y="4" width="22" height="16" rx="2"/>
                <line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            {{-- Unread badge — driven by JS --}}
            <span class="mob-nav-badge" id="mobPayBadge"></span>
        </div>
        <span class="mob-nav-label" data-i18n="mobNavPayment">Bayaran</span>
    </a>

    {{-- Profil --}}
    <a href="{{ route('profile.edit') }}"
       class="mob-nav-item {{ Request::is('*profile*') ? 'active' : '' }}">
        <div class="mob-nav-icon">
            <svg viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <span class="mob-nav-label" data-i18n="mobNavProfile">Profil</span>
    </a>

    {{-- Tetapan — swapped in for Log Keluar. Logout is a rare, one-way
         action and doesn't need a permanently reserved slot in the 5-item
         bottom nav; it's now reached from inside the Settings page itself
         (see settings.blade.php's "Akaun" card), the same page this tab
         links to. --}}
    <a href="{{ route('settings') }}"
       class="mob-nav-item {{ Request::is('*settings*') ? 'active' : '' }}">
        <div class="mob-nav-icon">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
        </div>
        <span class="mob-nav-label" data-i18n="mobNavSettings">Tetapan</span>
    </a>

</nav>


<script>
/* ═══════════════════════════════════════════════════════════
   MOBILE NOTIFICATION PANEL (shared across all auth pages)
═══════════════════════════════════════════════════════════ */
const MOBILE_CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// Notification type display metadata now comes from the shared i18n module
// (window.MBS_I18N.notifTypeMeta) so every page's panel stays in sync with
// the active language without duplicating the translated map.
function mobileNotiMeta(type) {
    return window.MBS_I18N ? window.MBS_I18N.notifTypeMeta(type) : { title: type, iconColor: '#888', bgColor: '#f0ede6' };
}

const mobileStatusTypes  = ['app_submitted', 'app_approved', 'app_rejected'];
const mobilePaymentTypes = ['payment_due', 'payment_received', 'payment_reminder'];

const mobileBellSvg = `<svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:none;stroke-width:1.8;stroke:currentColor">
  <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
  <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
</svg>`;

let mobileNotifData  = [];
let mobileCurrentTab = 'all';
let mobilePanelOpen  = false;

// FIX #13: pass active tab to server for filtering instead of fetching a
// fixed 20-item window and filtering client-side.
async function loadMobileNotifications(tab) {
    tab = tab || mobileCurrentTab;
    try {
        const res  = await fetch('/notifications?tab=' + encodeURIComponent(tab));
        const data = await res.json();
        mobileNotifData = data.notifications;
        updateMobileBadge(data.unread_count);
        renderMobileNotifList();
    } catch (e) {
        const el = document.getElementById('mobileNotifList');
        if (el) el.innerHTML = '<div class="np-empty"><p>' + (window.MBS_I18N ? window.MBS_I18N.t('notifFailed') : 'Gagal memuatkan pemberitahuan.') + '</p></div>';
    }
}

// FIX #13: server already filtered by tab — just render
function renderMobileNotifList() {
    const items = mobileNotifData;

    const list = document.getElementById('mobileNotifList');
    if (!list) return;

    if (!items.length) {
        const emptyText = window.MBS_I18N ? window.MBS_I18N.t('notifEmpty') : 'Tiada pemberitahuan';
        list.innerHTML = `<div class="np-empty">
            <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <p>${emptyText}</p>
        </div>`;
        return;
    }

    list.innerHTML = items.map(n => {
        const meta = mobileNotiMeta(n.type);
        return `<div class="notif-item ${n.unread ? 'unread' : ''}" onclick="mobileMarkRead(${n.id}, this)">
            <div class="notif-icon" style="background:${meta.bgColor};color:${meta.iconColor}">${mobileBellSvg}</div>
            <div class="notif-body">
                <div class="notif-title">${meta.title}</div>
                <div class="notif-msg">${n.message}</div>
                <div class="notif-time">${n.time}</div>
            </div>
            ${n.unread ? '<div class="unread-dot"></div>' : ''}
        </div>`;
    }).join('');
}

function updateMobileBadge(count) {
    const badge    = document.getElementById('mobileUnreadCount');
    const dot      = document.getElementById('mobileBellDot');
    const mobBadge = document.getElementById('mobPayBadge');

    if (badge) { badge.textContent = count; badge.style.display = count > 0 ? 'inline-block' : 'none'; }
    if (dot)   dot.style.display   = count > 0 ? 'block' : 'none';
    if (mobBadge) {
        mobBadge.textContent = count > 0 ? count : '';
        mobBadge.classList.toggle('visible', count > 0);
    }
}

// FIX #13: re-fetch from server instead of just re-filtering in-memory data
function switchMobileTab(tab, el) {
    mobileCurrentTab = tab;
    document.querySelectorAll('#mobileNotifPanel .np-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    loadMobileNotifications(tab);
}

async function mobileMarkRead(id, el) {
    await fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': MOBILE_CSRF }
    });
    const n = mobileNotifData.find(n => n.id === id);
    if (n) n.unread = false;
    el.classList.remove('unread');
    el.querySelector('.unread-dot')?.remove();
    updateMobileBadge(mobileNotifData.filter(n => n.unread).length);
}

async function mobileMarkAllRead() {
    await fetch('/notifications/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': MOBILE_CSRF }
    });
    mobileNotifData.forEach(n => n.unread = false);
    renderMobileNotifList(mobileCurrentTab);
    updateMobileBadge(0);
}

function toggleMobileNotifPanel() {
    mobilePanelOpen = !mobilePanelOpen;
    const panel = document.getElementById('mobileNotifPanel');
    if (panel) panel.classList.toggle('open', mobilePanelOpen);
    if (mobilePanelOpen) loadMobileNotifications();
}

/* Close mobile notif panel on outside click */
document.addEventListener('click', function (e) {
    const btn   = document.getElementById('mobileBellBtn');
    const panel = document.getElementById('mobileNotifPanel');
    if (btn && panel && !btn.contains(e.target) && !panel.contains(e.target)) {
        panel.classList.remove('open');
        mobilePanelOpen = false;
    }
});

/* FIX #10: Pause polling when the tab is hidden/backgrounded, resume + refresh
   immediately when it becomes visible again. The sidebar partial is included
   on every authenticated page, so this single fix covers mobile polling
   across the whole app. */
let mobileNotifPollTimer = null;

function startMobileNotifPolling() {
    if (mobileNotifPollTimer) return;
    mobileNotifPollTimer = setInterval(loadMobileNotifications, 60000);
}

function stopMobileNotifPolling() {
    clearInterval(mobileNotifPollTimer);
    mobileNotifPollTimer = null;
}

document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        stopMobileNotifPolling();
    } else {
        loadMobileNotifications();
        startMobileNotifPolling();
    }
});

/* Load on mount + poll every 60s (paused while tab is hidden) */
loadMobileNotifications();
startMobileNotifPolling();

/* ── LOGOUT CONFIRMATION ──────────────────────────────────────────────────
   Uses the shared mbsConfirm() popup (auth.partials.confirm-modal) when
   it's available on the page. Falls back to a native confirm() on any
   page that hasn't included that partial, so logout is never silently
   un-confirmed. */
function mbsLogoutConfirm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    if (typeof mbsConfirm === 'function') {
        mbsConfirm({
            intent: 'info',
            icon: 'logout',
            title: window.MBS_I18N ? window.MBS_I18N.t('confirmLogoutTitle') : 'Log keluar dari akaun?',
            message: window.MBS_I18N ? window.MBS_I18N.t('confirmLogoutMsg') : 'Anda perlu log masuk semula untuk mengakses papan pemuka anda.',
            confirmText: window.MBS_I18N ? window.MBS_I18N.t('navLogout') : 'Log Keluar',
            cancelText: window.MBS_I18N ? window.MBS_I18N.t('confirmSubmitCancel') : 'Batal',
            onConfirm: function () { form.submit(); },
        });
    } else {
        const msg = window.MBS_I18N ? window.MBS_I18N.t('confirmLogoutMsg') : 'Anda pasti mahu log keluar?';
        if (confirm(msg)) form.submit();
    }
}

/* ── Desktop sidebar toggle (kept for any existing burger btn) ── */
function toggleSidebar() {
    document.getElementById('mobileSidebar')?.classList.toggle('active');
}

document.addEventListener('click', function (e) {
    const sb     = document.getElementById('mobileSidebar');
    const burger = document.querySelector('.burger-btn');
    if (window.innerWidth <= 900 && sb && burger &&
        !sb.contains(e.target) &&
        !burger.contains(e.target) &&
        sb.classList.contains('active')) {
        sb.classList.remove('active');
    }
});

/* Re-render already-fetched notification data with the new language's
   labels whenever the language changes — data-i18n only covers static
   markup, not list items built dynamically from JSON. */
window.addEventListener('mbs:languagechange', function () {
    renderMobileNotifList();
});
window.addEventListener('storage', function (e) {
    if (e.key === 'mbs_settings') renderMobileNotifList();
});
</script>