{{-- ============================================================
     resources/views/auth/partials/confirm-modal.blade.php
     Shared confirmation popup — @include this once per page.

     Requires: tokens.css (for --color-yellow, --color-red, etc.)

     Usage from any page's <script>:
       mbsConfirm({
         intent:      'warning' | 'danger' | 'success' | 'info',
         icon:        'question' | 'trash' | 'logout' | 'lock' | 'card' | 'check',
         title:       'Hantar permohonan ini?',
         message:     'Sila semak semua maklumat sebelum menghantar.',
         detail:      [{ k: 'No. Permohonan', v: 'MBS-20260630-0007' }], // optional
         confirmText: 'Hantar',
         cancelText:  'Batal',
         onConfirm:   function () { ... },
         onCancel:    function () { ... } // optional
       });
     ============================================================ --}}

<div class="mbs-modal-overlay" id="mbsModalOverlay" onclick="if(event.target===this) mbsCloseModal()">
    <div class="mbs-modal" id="mbsModal">
        <div class="mbs-modal-accent"></div>
        <div class="mbs-modal-body">
            <div class="mbs-modal-icon" id="mbsModalIcon"></div>
            <div class="mbs-modal-title" id="mbsModalTitle"></div>
            <div class="mbs-modal-message" id="mbsModalMessage"></div>
            <div class="mbs-modal-detail" id="mbsModalDetail"></div>
        </div>
        <div class="mbs-modal-actions">
            <button type="button" class="mbs-btn-cancel" id="mbsModalCancelBtn" onclick="mbsCloseModal()"></button>
            <button type="button" class="mbs-btn-confirm" id="mbsModalConfirmBtn"></button>
        </div>
    </div>
</div>

<style>
.mbs-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(20, 18, 10, 0.45);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    padding: 20px;
}
.mbs-modal-overlay.open {
    display: flex;
    animation: mbsFadeIn 0.15s ease;
}
@keyframes mbsFadeIn { from { opacity: 0; } to { opacity: 1; } }

.mbs-modal {
    background: #fff;
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 400px;
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.28);
    overflow: hidden;
    animation: mbsPopIn 0.18s cubic-bezier(0.2, 0.8, 0.3, 1);
}
@keyframes mbsPopIn {
    from { opacity: 0; transform: translateY(8px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.mbs-modal-accent { height: 4px; background: var(--color-yellow); }
.mbs-modal.intent-danger  .mbs-modal-accent { background: var(--color-red); }
.mbs-modal.intent-success .mbs-modal-accent { background: #3B6D11; }
.mbs-modal.intent-info    .mbs-modal-accent { background: #185FA5; }

.mbs-modal-body { padding: 28px 28px 24px; text-align: center; }

.mbs-modal-icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff8d6;
    border: 1.5px solid var(--color-yellow);
}
.mbs-modal.intent-danger  .mbs-modal-icon { background: #FCEBEB; border-color: var(--color-red); }
.mbs-modal.intent-success .mbs-modal-icon { background: #EAF3DE; border-color: #3B6D11; }
.mbs-modal.intent-info    .mbs-modal-icon { background: #E6F1FB; border-color: #185FA5; }

.mbs-modal-icon svg {
    width: 24px; height: 24px;
    stroke: #a07000; fill: none; stroke-width: 1.8;
}
.mbs-modal.intent-danger  .mbs-modal-icon svg { stroke: var(--color-red); }
.mbs-modal.intent-success .mbs-modal-icon svg { stroke: #3B6D11; }
.mbs-modal.intent-info    .mbs-modal-icon svg { stroke: #185FA5; }

.mbs-modal-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 8px;
    line-height: 1.4;
}

.mbs-modal-message {
    font-size: 13px;
    color: var(--color-text-faint);
    line-height: 1.6;
    margin-bottom: 4px;
}

.mbs-modal-detail {
    margin-top: 16px;
    background: #faf9f6;
    border: 1px solid var(--color-border-light);
    border-radius: var(--radius-md);
    padding: 12px 14px;
    text-align: left;
    display: none;
}
.mbs-modal-detail.visible { display: block; }
.mbs-modal-detail-row {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    padding: 3px 0;
}
.mbs-modal-detail-row .k { color: var(--color-text-muted); }
.mbs-modal-detail-row .v { color: var(--color-text-primary); font-weight: 600; text-align: right; margin-left: 12px; }

.mbs-modal-actions {
    display: flex;
    gap: 10px;
    padding: 0 28px 28px;
}
.mbs-modal-actions button {
    flex: 1;
    border: none;
    border-radius: var(--radius-sm);
    padding: 11px;
    font-family: var(--font-ui);
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    transition: background 0.15s;
}
.mbs-btn-cancel {
    background: #fff;
    border: 1px solid var(--color-border-light) !important;
    color: var(--color-text-faint);
}
.mbs-btn-cancel:hover { background: #f4f3f0; }

.mbs-btn-confirm {
    background: var(--color-yellow);
    color: #0d0d0d;
    position: relative;
}
.mbs-btn-confirm::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--color-red);
    border-radius: var(--radius-sm) 0 0 var(--radius-sm);
}
.mbs-btn-confirm:hover { background: var(--color-yellow-hover); }

.mbs-modal.intent-danger .mbs-btn-confirm {
    background: var(--color-red);
    color: #fff;
}
.mbs-modal.intent-danger .mbs-btn-confirm::before { background: #791F1F; }
.mbs-modal.intent-danger .mbs-btn-confirm:hover { background: #a93226; }

@media (max-width: 480px) {
    .mbs-modal-body { padding: 24px 20px 20px; }
    .mbs-modal-actions { padding: 0 20px 20px; flex-direction: column-reverse; }
}
</style>

<script>
const MBS_MODAL_ICONS = {
    question: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3.5"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    trash:    '<svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
    logout:   '<svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
    lock:     '<svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
    card:     '<svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
    check:    '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
};

function mbsConfirm(opts) {
    const o = Object.assign({
        intent: 'warning',
        icon: 'question',
        title: 'Sahkan tindakan',
        message: '',
        detail: null,
        confirmText: 'Sahkan',
        cancelText: 'Batal',
        onConfirm: function () {},
        onCancel: function () {},
    }, opts);

    const modal = document.getElementById('mbsModal');
    modal.className = 'mbs-modal intent-' + o.intent;

    document.getElementById('mbsModalIcon').innerHTML    = MBS_MODAL_ICONS[o.icon] || MBS_MODAL_ICONS.question;
    document.getElementById('mbsModalTitle').textContent   = o.title;
    document.getElementById('mbsModalMessage').textContent = o.message;

    const detailBox = document.getElementById('mbsModalDetail');
    if (o.detail && o.detail.length) {
        detailBox.innerHTML = o.detail.map(function (row) {
            return '<div class="mbs-modal-detail-row"><span class="k">' + row.k + '</span><span class="v">' + row.v + '</span></div>';
        }).join('');
        detailBox.classList.add('visible');
    } else {
        detailBox.classList.remove('visible');
        detailBox.innerHTML = '';
    }

    const cancelBtn  = document.getElementById('mbsModalCancelBtn');
    const confirmBtn = document.getElementById('mbsModalConfirmBtn');
    cancelBtn.textContent  = o.cancelText;
    confirmBtn.textContent = o.confirmText;

    cancelBtn.onclick = function () { mbsCloseModal(); o.onCancel(); };
    confirmBtn.onclick = function () { mbsCloseModal(); o.onConfirm(); };

    document.getElementById('mbsModalOverlay').classList.add('open');
    document.addEventListener('keydown', mbsModalEscHandler);
}

function mbsCloseModal() {
    document.getElementById('mbsModalOverlay').classList.remove('open');
    document.removeEventListener('keydown', mbsModalEscHandler);
}

function mbsModalEscHandler(e) {
    if (e.key === 'Escape') mbsCloseModal();
}
</script>