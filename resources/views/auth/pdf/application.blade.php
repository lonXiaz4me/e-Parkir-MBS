<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8"/>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }

  /* Force every page to the exact size of the real form (US Letter, 612x792pt)
     with zero PDF-level margin — all spacing below is handled by each page's
     own CSS so the form backgrounds line up pixel-for-pixel with the overlay text. */
  @page { margin: 0; size: letter; }

  .page { padding: 36px 44px; }
  .page-break { page-break-before: always; }

  /* ── FORM PAGES (page 1 & 2 — literal borang background + overlay) ── */
  .form-page {
    position: relative;
    width: 612pt;
    height: 792pt;
    overflow: hidden;
  }
  .form-page .bg-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 612pt;
    height: 792pt;
  }
  .form-page .field-value {
    position: absolute;
    left: 226pt;
    max-width: 324pt;
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 11pt;
    line-height: 1.15;
    color: #1a1a1a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  /* Lokasi Petak di Mohon is the only field with 2 dotted lines reserved on
     the real form — let it wrap instead of truncating. */
  .form-page .field-value.wrap-2 {
    white-space: normal;
    max-width: 330pt;
  }

  /* ── DOCUMENT IMAGES (page 3) — styled to match the plain official
     letter look of pages 1-2, not a colored dashboard card ── */
  .doc-header {
    display: table;
    width: 100%;
    border-bottom: 1pt solid #1a1a1a;
    padding-bottom: 8pt;
    margin-bottom: 20pt;
  }
  .doc-header-left  { display: table-cell; vertical-align: bottom; }
  .doc-header-right { display: table-cell; text-align: right; vertical-align: bottom; }
  .doc-org  { font-size: 11pt; font-weight: 700; text-transform: uppercase; color: #1a1a1a; }
  .doc-ref  { font-size: 9pt; color: #555; margin-top: 3pt; }
  .doc-page { font-size: 9pt; color: #555; }

  .doc-section-title {
    font-size: 11pt;
    font-weight: 700;
    text-transform: uppercase;
    color: #1a1a1a;
    margin-bottom: 14pt;
  }

  .img-grid { display: table; width: 100%; border-collapse: collapse; }
  .img-cell {
    display: table-cell;
    width: 50%;
    padding: 10pt;
    vertical-align: top;
    border: 1pt solid #999;
  }
  .img-cell:nth-child(odd) { border-right: none; }

  .img-label {
    font-size: 9pt;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 7pt;
  }
  .img-wrap {
    border: 1pt solid #ccc;
    overflow: hidden;
    text-align: center;
    min-height: 160px;
  }
  .img-wrap img {
    max-width: 100%;
    max-height: 200px;
    display: block;
    margin: 0 auto;
    object-fit: contain;
  }
  .img-missing {
    padding: 40px 10px;
    color: #999;
    font-size: 10pt;
    font-style: italic;
    text-align: center;
  }

  /* ── FOOTER ── */
  .footer {
    border-top: 1pt solid #ccc;
    margin-top: 24px;
    padding-top: 12px;
    text-align: center;
  }
  .footer p { font-size: 9px; color: #888; line-height: 1.8; }
</style>
</head>
<body>

@php
  $generatedAt = now()->format('d/m/Y H:i');

  // ── Official form backgrounds ──────────────────────────────────────────
  // Rasterized straight from the real a.pdf (200dpi), so pages 1-2 are a
  // pixel-faithful copy of the actual borang. Drop the two PNGs here:
  //   public/images/borang-permohonan-bg-p1.png
  //   public/images/borang-permohonan-bg-p2.png
  $bgPath1 = public_path('images/borang-permohonan-bg-p1.png');
  $bgPath2 = public_path('images/borang-permohonan-bg-p2.png');
@endphp

{{-- ══════════════════════════════════════════════
     PAGE 1 — Official Borang (letter + applicant block)
     Coordinates below were measured directly from a.pdf (in pt, matching
     the PDF's own coordinate space 1:1 — no DPI conversion needed).
══════════════════════════════════════════════ --}}
<div class="form-page">
  @if(file_exists($bgPath1))
    <img class="bg-img" src="{{ $bgPath1 }}" alt="">
  @endif

  {{-- Tandatangan : left blank — no captured e-signature in the current data model --}}

  <div class="field-value" style="top:408.8pt;">{{ $user->full_name }}</div>
  <div class="field-value" style="top:440.2pt;">{{ $application->company_name }}</div>

  {{-- No. Tel Pejabat : left blank — only one phone number is currently captured --}}
  <div class="field-value" style="top:488.1pt;">{{ $user->phone_no }}</div>

  <div class="field-value" style="top:519.4pt;">{{ $user->email }}</div>
  <div class="field-value" style="top:550.9pt;">{{ $application->ssm_no }}</div>
  <div class="field-value" style="top:582.3pt; font-size:{{ strlen($application->type_of_business ?? '') > 55 ? '8pt' : (strlen($application->type_of_business ?? '') > 40 ? '9.5pt' : '11pt') }};">{{ $application->type_of_business }}</div>
  <div class="field-value wrap-2" style="top:613.8pt;">{{ $application->location }}</div>
  <div class="field-value" style="top:661.5pt;">{{ $application->total_parking }} Petak</div>
  <div class="field-value" style="top:693.0pt;">{{ $application->created_at?->format('d/m/Y') ?? '' }}</div>
</div>

{{-- ══════════════════════════════════════════════
     PAGE 2 — Syarat-Syarat Permohonan (fully static, no data fields)
══════════════════════════════════════════════ --}}
<div class="form-page page-break">
  @if(file_exists($bgPath2))
    <img class="bg-img" src="{{ $bgPath2 }}" alt="">
  @endif
</div>

{{-- ══════════════════════════════════════════════
     PAGE 3 — Supporting Document Images
══════════════════════════════════════════════ --}}
<div class="page page-break">

  {{-- Header — plain letterhead style, matching pages 1-2 --}}
  <div class="doc-header">
    <div class="doc-header-left">
      <div class="doc-org">Majlis Bandaraya Seremban</div>
      <div class="doc-ref">Salinan Dokumen Sokongan · {{ $application->app_no }}</div>
    </div>
    <div class="doc-header-right">
      <div class="doc-page">Halaman 3 / 3</div>
    </div>
  </div>

  <div class="doc-section-title">Dokumen Sokongan Yang Dimuat Naik</div>

  {{-- Row 1: SSM + IC --}}
  <table class="img-grid">
    <tr>
      <td class="img-cell">
        <div class="img-label">Salinan SSM Syarikat</div>
        <div class="img-wrap">
          @if($images['ssm'])
            <img src="{{ $images['ssm'] }}" alt="SSM">
          @else
            <div class="img-missing">Tiada imej</div>
          @endif
        </div>
      </td>
      <td class="img-cell">
        <div class="img-label">Salinan IC Pemohon</div>
        <div class="img-wrap">
          @if($images['ic'])
            <img src="{{ $images['ic'] }}" alt="IC">
          @else
            <div class="img-missing">Tiada imej</div>
          @endif
        </div>
      </td>
    </tr>
  </table>

  {{-- Row 2: Licence + Location --}}
  <table class="img-grid" style="margin-top:0;border-top:none;">
    <tr>
      <td class="img-cell">
        <div class="img-label">Salinan Lesen Perniagaan</div>
        <div class="img-wrap">
          @if($images['licence'])
            <img src="{{ $images['licence'] }}" alt="Lesen Perniagaan">
          @else
            <div class="img-missing">Tiada imej</div>
          @endif
        </div>
      </td>
      <td class="img-cell">
        <div class="img-label">Gambar Lokasi Yang Disewa</div>
        <div class="img-wrap">
          @if($images['location'])
            <img src="{{ $images['location'] }}" alt="Gambar Lokasi">
          @else
            <div class="img-missing">Tiada imej</div>
          @endif
        </div>
        @if($application->location_coords)
          <div style="margin-top:5pt;font-size:9pt;color:#555;">
            GPS: {{ $application->location_coords }}
          </div>
        @endif
      </td>
    </tr>
  </table>

  {{-- Footer --}}
  <div class="footer" style="margin-top:16px">
    <p>
      Salinan dokumen sokongan bagi permohonan {{ $application->app_no }}.<br>
      Dijana oleh Sistem e-Parkir MBS pada {{ $generatedAt }}.
    </p>
  </div>

</div>

</body>
</html>