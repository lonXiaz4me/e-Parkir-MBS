{{-- ============================================================
     resources/views/auth/partials/notif-panel.blade.php
     Shared desktop topbar notification bell + panel.
     @include('auth.partials.notif-panel') inside every .topbar-right.

     Requires:
       - tokens.css, sidebar.css (already loaded by the page)
       - i18n.js (loaded by sidebar partial)
       - meta[name="csrf-token"] on the page

     Exposes these JS globals (called by the host page's own JS if needed):
       loadNotifications(tab?)   — fetch & render
       updateNotifBadge(count)   — update bell dot + badge number
     ============================================================ --}}

{{-- ── Bell button ── --}}
<div class="bell-btn" id="bellBtn" onclick="toggleNotifPanel()">
    <svg viewBox="0 0 24 24">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>
    <div class="bell-dot" id="bellDot"></div>
</div>

{{-- ── Panel ── --}}
<div class="notif-panel" id="notifPanel">
    <div class="np-header">
        <span class="np-title">
            <span data-i18n="notifTitle">Pemberitahuan</span>
            <span class="np-badge" id="unreadCount"></span>
        </span>
        <button class="np-read-all" data-i18n="notifReadAll" onclick="markAllRead()">Baca Semua</button>
    </div>
    <div class="np-tabs">
        <div class="np-tab active"  data-i18n="notifTabAll"     onclick="switchNotifTab('all',     this)">Semua</div>
        <div class="np-tab"         data-i18n="notifTabUnread"  onclick="switchNotifTab('unread',  this)">Belum Baca</div>
        <div class="np-tab"         data-i18n="notifTabStatus"  onclick="switchNotifTab('status',  this)">Status</div>
        <div class="np-tab"         data-i18n="notifTabPayment" onclick="switchNotifTab('payment', this)">Bayaran</div>
    </div>
    <div class="np-list" id="notifList">
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

{{-- ── Shared notification JS ─────────────────────────────────────────────
     All pages share one copy of this logic.
     Pages that need to hook into language-change (e.g. dashboard's
     rerenderCardButtons) listen to window 'mbs:languagechange' themselves;
     this partial only handles the notification panel.
──────────────────────────────────────────────────────────────────────── --}}
<script>
(function () {
    // ── State ────────────────────────────────────────────────────────────────
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const bellSvg = `<svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:none;stroke-width:1.8;stroke:currentColor">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>`;

    let notifData      = [];
    let currentTab     = 'all';
    let panelOpen      = false;
    let pollTimer      = null;

    // ── Fetch ────────────────────────────────────────────────────────────────
    async function loadNotifications(tab) {
        tab = tab || currentTab;
        try {
            const res  = await fetch('/notifications?tab=' + encodeURIComponent(tab));
            const data = await res.json();
            notifData  = data.notifications;
            updateNotifBadge(data.unread_count);
            renderNotifList();
        } catch (e) {
            const list = document.getElementById('notifList');
            if (list) list.innerHTML = '<div class="np-empty"><p>'
                + (window.MBS_I18N ? window.MBS_I18N.t('notifFailed') : 'Gagal memuatkan pemberitahuan.')
                + '</p></div>';
        }
    }

    // ── Render ───────────────────────────────────────────────────────────────
    function renderNotifList() {
        const list = document.getElementById('notifList');
        if (!list) return;

        if (!notifData.length) {
            list.innerHTML = `<div class="np-empty">
                <svg viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <p>${window.MBS_I18N ? window.MBS_I18N.t('notifEmpty') : 'Tiada pemberitahuan'}</p>
            </div>`;
            return;
        }

        list.innerHTML = notifData.map(function (n) {
            const meta = window.MBS_I18N
                ? window.MBS_I18N.notifTypeMeta(n.type)
                : { title: n.type, iconColor: '#888', bgColor: '#f0ede6' };
            const msg = window.MBS_I18N
                ? window.MBS_I18N.resolveNotifMessage(n)
                : n.message;
            return `<div class="notif-item ${n.unread ? 'unread' : ''}" onclick="markRead(${n.id}, this)">
                <div class="notif-icon" style="background:${meta.bgColor};color:${meta.iconColor}">${bellSvg}</div>
                <div class="notif-body">
                    <div class="notif-title">${meta.title}</div>
                    <div class="notif-msg">${msg}</div>
                    <div class="notif-time">${n.time}</div>
                </div>
                ${n.unread ? '<div class="unread-dot"></div>' : ''}
            </div>`;
        }).join('');
    }

    // ── Badge ────────────────────────────────────────────────────────────────
    function updateNotifBadge(count) {
        const badge = document.getElementById('unreadCount');
        const dot   = document.getElementById('bellDot');
        // Also sync the mobile badge in the sidebar partial if present
        const mobBadge = document.getElementById('mobPayBadge');
        if (badge) { badge.textContent = count; badge.style.display = count > 0 ? 'inline-block' : 'none'; }
        if (dot)     dot.style.display  = count > 0 ? 'block' : 'none';
        if (mobBadge) {
            mobBadge.textContent = count > 0 ? count : '';
            mobBadge.classList.toggle('visible', count > 0);
        }
    }

    // ── Tab switch ───────────────────────────────────────────────────────────
    function switchNotifTab(tab, el) {
        currentTab = tab;
        document.querySelectorAll('#notifPanel .np-tab').forEach(function (t) { t.classList.remove('active'); });
        el.classList.add('active');
        loadNotifications(tab);
    }

    // ── Mark read ────────────────────────────────────────────────────────────
    async function markRead(id, el) {
        await fetch('/notifications/' + id + '/read', {
            method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }
        });
        const n = notifData.find(function (n) { return n.id === id; });
        if (n) n.unread = false;
        el.classList.remove('unread');
        el.querySelector('.unread-dot')?.remove();
        updateNotifBadge(notifData.filter(function (n) { return n.unread; }).length);
    }

    async function markAllRead() {
        await fetch('/notifications/read-all', {
            method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }
        });
        notifData.forEach(function (n) { n.unread = false; });
        renderNotifList();
        updateNotifBadge(0);
    }

    // ── Toggle panel ─────────────────────────────────────────────────────────
    function toggleNotifPanel() {
        panelOpen = !panelOpen;
        document.getElementById('notifPanel').classList.toggle('open', panelOpen);
        if (panelOpen) loadNotifications();
    }

    // Close on outside click
    document.addEventListener('click', function (e) {
        const bell  = document.getElementById('bellBtn');
        const panel = document.getElementById('notifPanel');
        if (bell && panel && !bell.contains(e.target) && !panel.contains(e.target)) {
            panel.classList.remove('open');
            panelOpen = false;
        }
    });

    // ── Polling (paused when tab hidden) ─────────────────────────────────────
    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(loadNotifications, 60000);
    }

    function stopPolling() {
        clearInterval(pollTimer);
        pollTimer = null;
    }

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) { stopPolling(); }
        else { loadNotifications(); startPolling(); }
    });

    // ── Language change — re-render with new translations ────────────────────
    window.addEventListener('mbs:languagechange', function () { renderNotifList(); });
    window.addEventListener('storage', function (e) {
        if (e.key === 'mbs_settings') renderNotifList();
    });

    // ── Boot ─────────────────────────────────────────────────────────────────
    loadNotifications();
    startPolling();

    // Expose to global scope so host pages can call them if needed
    // (e.g. a page that sends a notification and wants to refresh the panel)
    window.loadNotifications   = loadNotifications;
    window.updateNotifBadge    = updateNotifBadge;
    window.switchNotifTab      = switchNotifTab;
    window.markRead            = markRead;
    window.markAllRead         = markAllRead;
    window.toggleNotifPanel    = toggleNotifPanel;
})();
</script>