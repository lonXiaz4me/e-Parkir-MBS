<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Resit &amp; Bayaran — Sistem Petak Sewa MBS</title>
  <link rel="stylesheet" href="{{ asset('css/tokens.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
</head>
<body>

<div class="shell">

  @include('auth.partials.sidebar')

  <!-- TOPBAR -->
  <div class="topbar">
    <span class="breadcrumb">
      <a href="#" data-i18n="breadcrumbHome">Laman Utama</a><span class="sep">/</span>
      <span class="current" data-i18n="crumbPayment" data-i18n-html>Resit &amp; Bayaran</span>
    </span>
    <div class="topbar-right">
      @include('auth.partials.notif-panel')
    </div>
  </div>

  @include('auth.partials.confirm-modal')

  <!-- MAIN -->
  <div class="main">
    <div class="page-title" data-i18n="payPageTitle" data-i18n-html>Resit &amp; Bayaran</div>
    <div class="page-sub" data-i18n="payPageSub">Urus bil dan lihat semua rekod pembayaran anda.</div>

    @if(session('success'))
    <div class="success-alert">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
      {{ session('success') }}
    </div>
    @endif

    <div class="alert-strip">
      <div class="alert-icon"><svg viewBox="0 0 24 24"><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/><circle cx="12" cy="12" r="10"/></svg></div>
      <div class="alert-text" data-i18n="payAlertBill">Anda mempunyai 1 bil yang perlu dibayar sebelum permit dikeluarkan. Sila buat pembayaran segera untuk mengelakkan kelewatan.</div>
    </div>

    <div class="pay-grid">

      @php $routeId = request()->route('id'); @endphp

      <!-- LEFT: BILL + PAYMENT FORM -->
      <div>
        @if(!empty($routeId))
          @php $app = auth()->user()->applications()->where('app_no', $routeId)->first(); @endphp
          @if($app)
            <div class="section-label" data-i18n="payCurrentBill">Bil Semasa</div>
            <div class="bil-featured">
              <div class="bil-header">
                <div>
                  <div class="bil-ref"><span data-i18n="payRefPrefix">RUJUKAN</span>: {{ $app->app_no }}</div>
                  <div class="bil-title">{{ $app->company_name }}</div>
                  <div class="bil-loc">
                    <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $app->location }}
                  </div>
                </div>
                <div class="bil-amount-wrap">
                  <div class="bil-amount-label" data-i18n="payAmountLabel">Jumlah Bayaran</div>
                  <div class="bil-amount">RM {{ number_format($app->total_amount, 2) }}</div>
                  <div class="bil-amount-sub"><span data-i18n="payDueDate">Tarikh akhir</span>: 25 Apr 2025</div>
                </div>
              </div>
              <div class="bil-body">
                <form action="{{ route('payment.store') }}" method="POST" id="paymentForm">
                  @csrf
                  <input type="hidden" name="app_no" value="{{ $app->app_no }}">
                  <input type="hidden" name="payment_type" id="payment_type_input" value="online_transfer">
                  <div class="bil-breakdown">
                    <div class="br-row"><span data-i18n="payRowLocation">Lokasi</span><span class="br-val">{{ $app->location }}</span></div>
                    <div class="br-row"><span data-i18n="payRowRate">Kadar Sewaan (sebulan / petak)</span><span class="br-val">RM {{ $app->total_parking > 0 ? number_format($app->total_amount / $app->total_parking, 2) : '0.00' }}</span></div>
                    <div class="br-row"><span data-i18n="payRowLots">Jumlah Petak</span><span class="br-val">{{ $app->total_parking }} pax</span></div>
                    <div class="br-row total"><span data-i18n="payRowTotal">Jumlah Bayaran Anggaran / Bulan</span><span class="br-val">RM {{ number_format($app->total_amount, 2) }}</span></div>
                  </div>
                  <div class="method-section">
                    <div class="method-label" data-i18n="payMethodLabel">Kaedah Pembayaran</div>
                    <div class="method-grid">
                      <div class="method-card selected" data-value="online_transfer">
                        <div class="method-icon">
                          <div class="method-icon-box fpx"><svg viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
                          <span class="method-name" data-i18n="payMethodFpx">FPX / Bank</span>
                        </div>
                        <div class="method-desc" data-i18n="payMethodFpxDesc">Pindahan bank dalam talian</div>
                      </div>
                      <div class="method-card" data-value="card">
                        <div class="method-icon">
                          <div class="method-icon-box visa"><svg viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
                          <span class="method-name" data-i18n="payMethodCard">Kad Kredit / Debit</span>
                        </div>
                        <div class="method-desc" data-i18n="payMethodCardDesc">Visa, Mastercard</div>
                      </div>
                    </div>
                    <div id="bankSelectorContainer">
                      <select class="bank-selector" name="bank_name">
                        <option value="" disabled selected data-i18n="payBankSelect">-- Pilih Bank Anda --</option>
                        <option>Maybank2u</option>
                        <option>CIMB Clicks</option>
                        <option>Public Bank</option>
                        <option>RHB Bank</option>
                        <option>Hong Leong Bank</option>
                        <option>AmBank</option>
                        <option>Bank Islam</option>
                        <option>BSN</option>
                      </select>
                    </div>
                  </div>
                  <button type="submit" class="btn-pay"><span data-i18n="payBtnPayNow">Bayar Sekarang</span> — RM {{ number_format($app->total_amount, 2) }}</button>
                </form>
                <div class="secure-note">
                  <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                  <span data-i18n="paySecureNote">Transaksi dilindungi dengan penyulitan 256-bit SSL yang selamat</span>
                </div>
              </div>
            </div>
          @else
            <p data-i18n="payNotFound">Permohonan tidak dijumpai atau anda tidak mempunyai akses.</p>
          @endif
        @endif

        <!-- PAST BILLS -->
        <div class="section-label" style="margin-top:20px" data-i18n="payHistoryTitle">Sejarah Pembayaran</div>
        <div class="past-list">
          @if(auth()->user()->payments->isEmpty())
            <p data-i18n="payNoHistory">Tiada pembayaran yang direkod.</p>
          @else
            @foreach(auth()->user()->payments as $payment)
              @if($payment->payment_status === 'paid')
                <div class="past-card paid">
                  <div class="past-icon paid"><svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
                  <div class="past-info">
                    <div class="past-ref">{{ $payment->app_no }} · {{ $payment->invoice_no }}</div>
                    <div class="past-type"><span data-i18n="payParkingRental">Petak Sewa</span> — {{ $payment->payment_type }}</div>
                    <div class="past-date"><span data-i18n="payStatusPaid">Dibayar</span> · {{ $payment->updated_at?->format('d M Y') }}</div>
                  </div>
                  <div class="past-actions">
                    <span class="past-badge paid" data-i18n="payStatusPaid">Dibayar</span>
                    <div class="past-amount paid">RM {{ number_format($payment->total_amt, 2) }}</div>
                    <a href="{{ route('payment.receipt', ['id' => $payment->app_no]) }}" class="btn-resit" data-i18n="payBtnReceipt">↓ Resit</a>
                  </div>
                </div>
              @elseif($payment->payment_status === 'failed')
                <div class="past-card overdue">
                  <div class="past-icon overdue"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                  <div class="past-info">
                    <div class="past-ref">{{ $payment->app_no }} · {{ $payment->invoice_no }}</div>
                    <div class="past-type"><span data-i18n="payParkingRental">Petak Sewa</span> — {{ $payment->payment_type }}</div>
                    <div class="past-date" data-i18n="payStatusFailed">Gagal / Tertunggak</div>
                  </div>
                  <div class="past-actions">
                    <span class="past-badge overdue" data-i18n="payStatusOverdue">Tertunggak</span>
                    <div class="past-amount overdue">RM {{ number_format($payment->total_amt, 2) }}</div>
                    <button onclick="window.location='{{ route('payment.index', ['id' => $payment->app_no]) }}'" class="btn-resit" style="border-color:#C0392B;color:#C0392B" data-i18n="payBtnPay">Bayar</button>
                  </div>
                </div>
              @endif
            @endforeach
          @endif
        </div>
      </div>

      <!-- RIGHT: SUMMARY PANEL -->
      <div>
        <div class="summary-panel">
          <div class="sp-header">
            <div class="sp-title" data-i18n="payFinancialSummary">Ringkasan Kewangan</div>
            <div class="sp-sub">{{ auth()->user()->full_name }}</div>
          </div>
          <div class="sp-body">
            @php
              $allPayments  = auth()->user()->payments;
              $totalBil     = $allPayments->count();
              $paidCount    = $allPayments->where('payment_status','paid')->count();
              $failedCount  = $allPayments->where('payment_status','failed')->count();
              $totalPaid    = $allPayments->where('payment_status','paid')->sum('total_amt');
              $totalUnpaid  = $allPayments->where('payment_status','failed')->sum('total_amt');
            @endphp
            <div class="stat-mini-grid">
              <div class="stat-mini">
                <div class="sm-num">{{ $totalBil }}</div><div class="sm-lbl" data-i18n="payStatTotalBills">Jumlah Bil</div>
                <div class="sm-bar"><div class="sm-fill" style="background:var(--color-yellow);width:100%"></div></div>
              </div>
              <div class="stat-mini">
                <div class="sm-num" style="color:var(--color-status-approved-bar)">{{ $paidCount }}</div><div class="sm-lbl" data-i18n="payStatPaid">Telah Dibayar</div>
                <div class="sm-bar"><div class="sm-fill" style="background:var(--color-status-approved-bar);width:{{ $totalBil > 0 ? ($paidCount/$totalBil)*100 : 0 }}%"></div></div>
              </div>
              <div class="stat-mini">
                <div class="sm-num" style="color:var(--color-red)">{{ $failedCount }}</div><div class="sm-lbl" data-i18n="payStatOverdue">Tertunggak</div>
                <div class="sm-bar"><div class="sm-fill" style="background:var(--color-red);width:{{ $totalBil > 0 ? ($failedCount/$totalBil)*100 : 0 }}%"></div></div>
              </div>
              <div class="stat-mini">
                <div class="sm-num" style="color:var(--color-yellow)">{{ $totalBil - $paidCount - $failedCount }}</div><div class="sm-lbl" data-i18n="payStatPending">Menunggu</div>
                <div class="sm-bar"><div class="sm-fill" style="background:var(--color-yellow);width:50%"></div></div>
              </div>
            </div>
            <div class="sp-divider"></div>
            <div class="sp-row"><span class="sp-key" data-i18n="payStatTotalPaid">Jumlah Dibayar</span><span class="sp-val" style="color:var(--color-status-approved-bar)">RM {{ number_format($totalPaid, 2) }}</span></div>
            <div class="sp-row"><span class="sp-key" data-i18n="payStatOverdue">Tertunggak</span><span class="sp-val" style="color:var(--color-red)">RM {{ number_format($totalUnpaid, 2) }}</span></div>
            <div class="sp-divider"></div>
            <div class="sp-total-row">
              <span class="sp-total-key" data-i18n="payStatBalance">Baki Perlu Dibayar</span>
              <span class="sp-total-val">RM {{ number_format($totalUnpaid, 2) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>

// ─── PAYMENT METHOD TOGGLE ────────────────────────────────────────────────────
const methodCards   = document.querySelectorAll('.method-card');
const typeInput     = document.getElementById('payment_type_input');
const bankContainer = document.getElementById('bankSelectorContainer');

methodCards.forEach(card => {
  card.addEventListener('click', function () {
    methodCards.forEach(c => c.classList.remove('selected'));
    this.classList.add('selected');
    typeInput.value = this.getAttribute('data-value');
    bankContainer.style.display = this.getAttribute('data-value') === 'online_transfer' ? 'block' : 'none';
  });
});

// ─── CONFIRM BEFORE CHARGING ──────────────────────────────────────────────────
// Payment is a one-way action — PaymentController::store() immediately marks
// the application as completed and there's no refund flow in this app. Show a
// confirmation popup with the key details before the form actually submits.
@if(!empty($routeId) && isset($app) && $app)
const paymentFormEl = document.getElementById('paymentForm');
if (paymentFormEl) {
  paymentFormEl.addEventListener('submit', function (e) {
    e.preventDefault();

    const bankSelect = paymentFormEl.querySelector('select[name="bank_name"]');
    const methodCard = paymentFormEl.querySelector('.method-card.selected');
    const methodName = methodCard ? methodCard.querySelector('.method-name').textContent : '—';
    const bankValue   = bankSelect && bankSelect.value ? bankSelect.value : null;
    const amountText  = '{{ number_format($app->total_amount, 2) }}';

    mbsConfirm({
      intent: 'success',
      icon: 'card',
      title: window.MBS_I18N ? window.MBS_I18N.t('confirmPayTitle') : 'Sahkan pembayaran?',
      message: window.MBS_I18N ? window.MBS_I18N.t('confirmPayMsg') : 'Pembayaran akan diproses serta-merta dan permohonan akan ditandakan selesai.',
      detail: [
        { k: window.MBS_I18N ? window.MBS_I18N.t('payRefPrefix') : 'Rujukan', v: '{{ $app->app_no }}' },
        { k: window.MBS_I18N ? window.MBS_I18N.t('payMethodLabel') : 'Kaedah', v: methodName + (bankValue ? ' — ' + bankValue : '') },
        { k: window.MBS_I18N ? window.MBS_I18N.t('payAmountLabel') : 'Jumlah', v: 'RM ' + amountText },
      ],
      confirmText: (window.MBS_I18N ? window.MBS_I18N.t('payBtnPayNow') : 'Bayar') + ' RM ' + amountText,
      cancelText: window.MBS_I18N ? window.MBS_I18N.t('confirmSubmitCancel') : 'Batal',
      onConfirm: function () { paymentFormEl.submit(); },
    });
  });
}
@endif
</script>
</body>
</html>