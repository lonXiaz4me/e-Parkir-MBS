<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8"/>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }
  .page { padding: 40px 48px; }

  /* Header */
  .header { display: table; width: 100%; border-bottom: 3px solid #F5C518; padding-bottom: 20px; margin-bottom: 32px; }
  .header-left  { display: table-cell; vertical-align: middle; }
  .header-right { display: table-cell; vertical-align: middle; text-align: right; }
  .org-name { font-size: 20px; font-weight: 700; color: #1a1a1a; }
  .org-sub { font-size: 10px; color: #888; margin-top: 3px; line-height: 1.6; }
  .receipt-label .big { font-size: 22px; font-weight: 700; color: #F5C518; letter-spacing: 1px; }
  .receipt-label .inv { font-size: 11px; color: #888; margin-top: 4px; }

  /* Status badge */
  .status-bar { background: #EAF3DE; border: 1px solid #b2d68a; border-radius: 6px; padding: 10px 16px; margin-bottom: 24px; display: table; width: 100%; }
  .status-dot { width: 10px; height: 10px; border-radius: 50%; background: #3B6D11; display: inline-block; margin-right: 8px; vertical-align: middle; }
  .status-text { font-size: 12px; font-weight: 600; color: #27500A; vertical-align: middle; }

  /* Info grid */
  .info-grid { display: table; width: 100%; margin-bottom: 28px; border-collapse: collapse; }
  .info-row-wrap { display: table-row; }
  .info-block { display: table-cell; width: 50%; padding: 6px 8px 6px 0; vertical-align: top; }
  .info-block .label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #aaa; margin-bottom: 4px; }
  .info-block .value { font-size: 12px; font-weight: 600; color: #1a1a1a; }
  .info-block .value-sub { font-size: 11px; color: #666; margin-top: 2px; }

  /* Table */
  table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  thead tr { background: #1a1a1a; }
  thead th { padding: 10px 14px; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; text-align: left; }
  tbody tr { border-bottom: 1px solid #f0ede6; }
  tbody td { padding: 11px 14px; font-size: 12px; color: #333; }
  tbody tr:nth-child(even) { background: #fafaf8; }
  .td-right { text-align: right; }

  /* Totals */
  .totals { margin-left: auto; width: 280px; }
  .tot-row { display: table; width: 100%; padding: 5px 0; font-size: 12px; color: #555; }
  .tot-key { display: table-cell; }
  .tot-val { display: table-cell; text-align: right; }
  .tot-divider { border-top: 1px dashed #ddd; margin: 6px 0; }
  .tot-grand { display: table; width: 100%; padding: 8px 0 5px; font-size: 14px; font-weight: 700; color: #1a1a1a; }
  .tot-grand .tot-key { display: table-cell; }
  .tot-grand .tot-val { display: table-cell; text-align: right; color: #F5C518; }

  /* Payment method */
  .method-box { background: #f8f8f6; border: 1px solid #e8e7e0; border-radius: 6px; padding: 12px 16px; margin-top: 24px; margin-bottom: 24px; }
  .method-title { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; color: #aaa; margin-bottom: 8px; }
  .method-row { display: table; width: 100%; font-size: 11px; color: #555; margin-bottom: 4px; }
  .method-row .mk { display: table-cell; }
  .method-row .mv { display: table-cell; text-align: right; font-weight: 600; color: #1a1a1a; }

  /* Footer */
  .footer { border-top: 1px solid #eee; margin-top: 32px; padding-top: 16px; text-align: center; }
  .footer p { font-size: 10px; color: #aaa; line-height: 1.8; }
  .footer .thank { font-size: 13px; font-weight: 600; color: #1a1a1a; margin-bottom: 6px; }
  .seal { display: inline-block; border: 2px solid #3B6D11; border-radius: 50%; width: 60px; height: 60px; line-height: 56px; text-align: center; color: #3B6D11; font-size: 9px; font-weight: 700; letter-spacing: 0.5px; margin-top: 16px; }
</style>
</head>
<body>

@php
  /*
   * FIX #7: All hardcoded placeholder values replaced with real data.
   *
   * PREVIOUS PROBLEMS:
   *   - Payment date was empty (two blank <div> tags)
   *   - Owner name used auth()->user()->name which doesn't exist (column is full_name)
   *   - Line items were fake: "Zon B · Terminal 1 · Seremban (3 hari)", RM210/RM10/RM17.60
   *   - Subtotal, discount, and voucher rows were all hardcoded fiction
   *   - Bank name was hardcoded to "BankIslam" for every single receipt
   *   - The receipt total (RM total_amt) contradicted the fake line items above it
   *
   * FIX: Derive all values from the real $payment and $application records
   * passed from PaymentController::receipt().
   */

  // Payment date — use created_at (when the payment row was first inserted)
  $paidAt      = $payment->created_at?->format('d/m/Y') ?? '—';
  $paidAtFull  = $payment->created_at?->format('d M Y, H:i') ?? '—';

  // Owner name — column is full_name, not name
  $ownerName   = $user->full_name ?? 'Pengguna MBS';

  // Real amounts — total_amt is the single source of truth (no fake breakdown)
  $totalAmt    = (float) $payment->total_amt;

  // Location description for the line item
  $locationDesc = $application?->location ?? '—';
  $totalParking = $application?->total_parking ?? '—';

  // Payment method display
  $methodLabel = match(strtolower($payment->payment_type ?? '')) {
    'online_transfer', 'fpx' => 'FPX / Pindahan Bank Dalam Talian',
    'card'                   => 'Kad Kredit / Debit',
    default                  => strtoupper($payment->payment_type ?? '—'),
  };

  // Bank name — stored on payment if available, otherwise show method label
  $bankName = !empty($payment->bank_name) ? $payment->bank_name : $methodLabel;
@endphp

<div class="page">

  <!-- Header -->
  <div class="header">
    <div class="header-left">
      <div class="org-name">Majlis Bandaraya Seremban</div>
      <div class="org-sub">Sistem Pengurusan Petak Sewa — e-Parkir MBS<br>Terminal Bas Seremban, Negeri Sembilan</div>
    </div>
    <div class="header-right">
      <div class="receipt-label">
        <div class="big">RESIT</div>
        <div class="inv">{{ $payment->invoice_no }}</div>
      </div>
    </div>
  </div>

  <!-- Status -->
  <div class="status-bar">
    <span class="status-dot"></span>
    <span class="status-text">Pembayaran Berjaya — Disahkan</span>
  </div>

  <!-- Info grid -->
  {{-- FIX #7: Payment date and owner name now show real values --}}
  <div class="info-grid">
    <div class="info-row-wrap">
      <div class="info-block">
        <div class="label">Nombor Permohonan</div>
        <div class="value">{{ $payment->app_no }}</div>
      </div>
      <div class="info-block">
        <div class="label">Tarikh Pembayaran</div>
        <div class="value">{{ $paidAt }}</div>
        <div class="value-sub">{{ $paidAtFull }}</div>
      </div>
    </div>
    <div class="info-row-wrap">
      <div class="info-block">
        <div class="label">Nama Pemilik</div>
        {{-- FIX #7: was auth()->user()->name (column doesn't exist), now full_name --}}
        <div class="value">{{ $ownerName }}</div>
      </div>
      <div class="info-block">
        <div class="label">ID Pengguna</div>
        <div class="value">USR-{{ str_pad($payment->user_id, 5, '0', STR_PAD_LEFT) }}</div>
      </div>
    </div>
  </div>

  <!-- Items table -->
  {{--
    FIX #7: Replaced three hardcoded fake line items with a single real one.
    The old rows (Sewa Petak RM210, Caj Proses RM10, SST RM17.60) were
    completely fabricated and didn't add up to total_amt anyway.
    The real receipt is simpler: one line item = the actual rental charge.
  --}}
  <table>
    <thead>
      <tr>
        <th>Perkara</th>
        <th>Perihal</th>
        <th class="td-right">Jumlah (RM)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Sewa Petak</td>
        <td>{{ $locationDesc }} · {{ $totalParking }} Petak</td>
        <td class="td-right">{{ number_format($totalAmt, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <!-- Totals -->
  {{--
    FIX #7: Removed the hardcoded fake subtotal (RM237.60), fake discount
    (–RM10.50), and fake voucher row. The grand total now matches the actual
    payment amount on the record.
  --}}
  <div class="totals">
    <div class="tot-divider"></div>
    <div class="tot-grand">
      <span class="tot-key">JUMLAH DIBAYAR</span>
      <span class="tot-val">RM {{ number_format($totalAmt, 2) }}</span>
    </div>
  </div>

  <!-- Payment method -->
  {{-- FIX #7: Bank name was hardcoded to "BankIslam" — now uses real stored value --}}
  <div class="method-box">
    <div class="method-title">Maklumat Pembayaran</div>
    <div class="method-row">
      <span class="mk">Kaedah</span>
      <span class="mv">{{ $methodLabel }}</span>
    </div>
    <div class="method-row">
      <span class="mk">Bank</span>
      <span class="mv">{{ $bankName }}</span>
    </div>
    <div class="method-row">
      <span class="mk">Status</span>
      <span class="mv" style="color:#3B6D11">Berjaya</span>
    </div>
    <div class="method-row">
      <span class="mk">Nombor Rujukan</span>
      <span class="mv">{{ $payment->invoice_no }}</span>
    </div>
    <div class="method-row">
      <span class="mk">Tarikh</span>
      <span class="mv">{{ $paidAtFull }}</span>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div class="thank">Terima Kasih Atas Pembayaran Anda</div>
    <p>
      Resit ini adalah bukti pembayaran yang sah.<br>
      Sila simpan untuk rujukan masa hadapan.<br>
      Sebarang pertanyaan: <strong>info@mbs-parking.gov.my</strong> | Tel: 06-XXX XXXX
    </p>
    <div class="seal">SAH</div>
  </div>

</div>
</body>
</html>