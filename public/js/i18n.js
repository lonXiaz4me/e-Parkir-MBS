/* ============================================================
   i18n.js — Shared language switcher for MBS e-Parkir
   Load this on every authenticated page (after sidebar partial,
   before any page-specific script that needs translated text).

   How it works:
   - Settings are read from the same 'mbs_settings' localStorage key
     the Tetapan (Settings) page already writes to.
   - Any element with data-i18n="key" gets its text content replaced
     with MBS_I18N.t(key) for the active language.
   - Any element with data-i18n-placeholder="key" gets its
     placeholder attribute translated (for inputs).
   - Call MBS_I18N.apply() after dynamically injecting new HTML
     (e.g. notification list items) if it contains data-i18n nodes.
   - Listens for the 'storage' event so changing language in one tab
     updates all other open tabs immediately. Also exposes
     MBS_I18N.setLanguage(lang) so any page (e.g. Settings) can
     trigger an immediate in-page update + persist + broadcast.
   ============================================================ */

(function (window) {
  const STORAGE_KEY = 'mbs_settings';
  const DEFAULTS = { language: 'ms', theme: 'light', systemTheme: false };

  // ── Shared dictionary ───────────────────────────────────────────────────
  // Keys are grouped by area but all live in one flat namespace so any page
  // can reuse any key (e.g. sidebar nav labels are reused in the mobile nav).
  const DICT = {
    ms: {
      // Sidebar / nav
      brandCouncil:        'Majlis Bandaraya<br>Seremban',
      brandSub:             'e-Parkir MBS',
      userRoleApplicant:    'Pemohon',
      navSectionMain:       'Menu Utama',
      navSectionAccount:    'Akaun',
      navDashboard:         'Papan Pemuka',
      navApplication:       'Permohonan Baru',
      navPayment:           'Resit &amp; Bayaran',
      navProfile:           'Profil Saya',
      navSettings:          'Tetapan',
      navLogout:            'Log Keluar',
      mobNavDashboard:      'Dashboard',
      mobNavApplication:    'Permohonan',
      mobNavPayment:        'Bayaran',
      mobNavProfile:        'Profil',
      mobNavSettings:       'Tetapan',
      mobNavLogout:         'Log Keluar',

      // Topbar / breadcrumb
      breadcrumbHome:       'Laman Utama',
      crumbDashboard:       'Papan Pemuka',
      crumbApplication:     'Permohonan Baru',
      crumbPayment:         'Resit &amp; Bayaran',
      crumbProfile:         'Profil Pengguna',
      crumbSettings:        'Tetapan',

      // Notification panel (shared across all pages)
      notifTitle:           'Pemberitahuan',
      notifReadAll:         'Baca Semua',
      notifTabAll:          'Semua',
      notifTabUnread:       'Belum Baca',
      notifTabStatus:       'Status',
      notifTabPayment:      'Bayaran',
      notifLoading:         'Memuatkan...',
      notifEmpty:           'Tiada pemberitahuan',
      notifFailed:          'Gagal memuatkan pemberitahuan.',
      notifFailedShort:     'Gagal memuatkan.',
      notifViewAll:         'Lihat semua pemberitahuan →',

      // Dashboard
      dashTitle:            'Papan Pemuka',
      dashSub:               'Ringkasan semua permohonan anda di sini.',
      dashTotalApps:        'Jumlah Semua Permohonan',
      statPending:          'Proses',
      statApproved:         'Diluluskan',
      statCompleted:        'Selesai',
      statRejected:         'Ditolak',
      dashStatusTitle:      'Status Permohonan',
      dashStatusSub:        'Semak dan urus semua permohonan anda di sini.',
      searchPlaceholder:    'Cari no. permohonan, jenis…',
      filterAll:            'Semua',
      filterPending:        'Dalam Proses',
      filterApproved:       'Diluluskan',
      filterCompleted:      'Selesai',
      filterRejected:       'Ditolak',
      sortNewest:           'Terbaru',
      sortOldest:           'Tertua',
      btnNewApplication:    '+ Permohonan Baru',
      noApplicationsFound:  'Tiada permohonan dijumpai.',

      // Application form
      appPageTitle:         'Permohonan Petak Letak Kereta',
      appPageSub:           'Sila lengkapkan semua maklumat yang diperlukan. Medan bertanda',
      appPageSubRequired:   'adalah wajib diisi.',
      btnBack:              'Kembali',
      btnSaveDraft:         'Simpan Draf',
      btnSubmitApp:         'Hantar Permohonan',

      // Payment
      payPageTitle:         'Resit &amp; Bayaran',
      payPageSub:           'Urus bil dan lihat semua rekod pembayaran anda.',
      payHistoryTitle:      'Sejarah Pembayaran',
      payNoHistory:         'Tiada pembayaran yang direkod.',
      payNotFound:          'Permohonan tidak dijumpai atau anda tidak mempunyai akses.',

      // Profile
      profPageTitle:        'Profil Pengguna',
      profPageSub:          'Kemaskini maklumat peribadi dan kata laluan akaun anda.',
      profAccountInfo:      'Maklumat Akaun',
      profSecurity:         'Keselamatan &amp; Kata Laluan',
      profSaveInfo:         'Simpan Maklumat Profil',
      profChangePassword:   'Tukar Kata Laluan',

      // Settings (kept here too so Settings page can share the same dict)
      setPageTitle:         'Tetapan',
      setPageSub:           'Urus keutamaan paparan dan bahasa aplikasi anda.',
      setLogoutTitle:       'Log Keluar Akaun',
      setLogoutSub:         'Tamatkan sesi log masuk anda pada peranti ini.',

      // Notification message templates ({0}, {1} are positional params)
      notifMsgAppSubmitted:    'Permohonan {0} telah dihantar dan sedang disemak oleh pegawai kami. Terima kasih!',
      notifMsgAppApproved:     'Permohonan {0} telah diluluskan. Sila buat pembayaran untuk mengaktifkan permit anda.',
      notifMsgAppRejected:     'Permohonan {0} tidak diluluskan. Sila semak sebab penolakan dan hubungi pegawai.',
      notifMsgPaymentReceived: 'Pembayaran {0} berjumlah {1} telah berjaya diterima. Permit petak anda kini aktif.',
      notifMsgPaymentDue:      'Bil bayaran bagi permohonan {0} perlu dijelaskan segera.',
      notifMsgPaymentReminder: 'Peringatan: Pembayaran bagi permohonan {0} masih belum diterima.',

      // Notification type titles (used by every page's notification panel)
      notiTypeAppSubmitted:    'Permohonan Dihantar',
      notiTypeAppApproved:     'Permohonan Diluluskan',
      notiTypeAppRejected:     'Permohonan Ditolak',
      notiTypePaymentDue:      'Bil Bayaran Menunggu',
      notiTypePaymentReceived: 'Pembayaran Berjaya',
      notiTypePaymentReminder: 'Peringatan Pembayaran',
      notiTypeDefault:         'Pemberitahuan',


      // Select placeholder options
      selCategory:          '-- Pilih Kategori --',
      selBusinessType:      '-- Pilih Jenis Perniagaan --',
      selLocation:          '-- Pilih Lokasi --',
      phTotalParking:       'Masukkan jumlah petak',

      // Toast messages
      toastSuccess:         'Berjaya',
      toastError:           'Ralat',
      toastValidationTitle: 'Permohonan Tidak Lengkap',
      toastValidationMsg:   'Sila lengkapkan semua medan bertanda (*) sebelum menghantar.',
      toastFormErrorTitle:  'Permohonan Gagal Dihantar',
      toastFormErrorMsg:    'Sila semak semula maklumat yang ditandakan di bawah sebelum menghantar.',
      errorMissingPrefix:   'Tiada ',
      draftSaving:          'Menyimpan…',
      draftSaved:           'Draf Disimpan',
      draftDiscardConfirm:  'Buang draf yang disimpan? Semua maklumat yang belum dihantar akan dipadamkan.',

      // Dashboard — timeline step labels
      tlSubmitted:          'Permohonan Dihantar',
      tlDone:               'Selesai',
      tlSystem:             'Sistem Auto',
      tlContactOfficer:     'Sila hubungi pegawai untuk maklumat lanjut.',
      tlRejected:           'Permohonan Ditolak',
      tlNotApproved:        'Tidak Diluluskan',
      tlBillPayment:        'Pembayaran Bil',
      tlAwaitingPayment:    'Menunggu Pembayaran',
      tlActivatePermit:     'Sila jelaskan bayaran permit untuk mengaktifkan lesen petak.',
      tlPaymentDone:        'Pembayaran Selesai',
      tlNotYet:             'Belum dilaksanakan',
      tlCompleted:          'Selesai',
      tlLicenceActive:      'Lesen Aktif',
      tlAwaitingApproval:   'Menunggu Kelulusan Pegawai',
      tlUnderReview:        'Sedang dalam semakan...',
      tlNotifProcessing:    'Notifikasi kepada pemohon sedang diproses.',
      tlDocsVerified:       'Dokumen Disahkan',

      // Dashboard — detail panel misc
      dpAppNoPrefix:        'No. Permohonan',
      dpRejectionHeader:    'Sebab Penolakan',
      dpNoRejectionReason:  'Tiada sebab penolakan dinyatakan. Sila hubungi pegawai untuk maklumat lanjut.',
      docNameSsm:           'Dokumen SSM',
      docNameIc:            'Salinan IC',
      docNameLocation:      'Foto Lokasi',
      docNameLicence:       'Salinan Lesen',
      docUploaded:          'Dimuat naik',
      docMissing:           'Tiada Fail',
      alertSelectApp:       'Sila pilih permohonan terlebih dahulu.',
      emailSubject:         'Pertanyaan Permohonan',
      emailGreeting:        'Salam,',
      emailIntro:           'Saya ingin bertanya mengenai permohonan berikut:',
      emailStatusLabel:     'Status Semasa',
      emailQuestion:        'Pertanyaan saya:',
      emailQuestionHint:    'Sila nyatakan pertanyaan anda di sini',
      emailClosing:         'Terima kasih.',

      // Profile — form error
      formErrorFix:         'Sila betulkan ralat borang di bawah.',
      declLabel:            'Perakuan pemohon',

      // Dashboard — detail panel section labels & info row keys
      dpSectionStatus:      'Status Aliran Kerja',
      dpSectionRejection:   'Sebab Penolakan',
      dpSectionCompany:     'Maklumat Syarikat &amp; Perniagaan',
      dpSectionParking:     'Maklumat Lokasi &amp; Parkir',
      dpSectionDocs:        'Muat Naik Dokumen Sokongan',


      // Application card — status badge labels
      statusPending:        'Menunggu Kelulusan',
      statusApproved:       'Diluluskan',
      statusRejected:       'Ditolak',
      statusCompleted:      'Selesai',
      statusUnknown:        'Tidak Diketahui',

      // Application card — progress step labels
      progSubmit:           'Hantar',
      progApprove:          'Lulus',
      progPay:              'Bayar',
      progDone:             'Selesai',

      // Application card — action button labels
      btnViewDocs:          'Lihat Dokumen',
      btnCheckStatus:       'Semak Status',
      btnPayNow:            'Bayar Sekarang',
      btnViewReason:        'Lihat Sebab',
      btnReapply:           'Mohon Semula',
      btnPrintLicence:      'Cetak Lesen',
      btnHelp:              'Bantuan',
      btnContact:           'Hubungi',

      // Misc units & labels
      unitLots:             'Petak',
      dpOfficerNote:        'Nota pegawai',

      dpDownloadPdf:        'Muat Turun PDF',
      dpContactOfficer:     'Hubungi Pegawai',

      // Application form — step bar
      stepPersonalCompany:  'Maklumat Peribadi &amp; Syarikat',
      stepParkingPayment:   'Butiran Petak &amp; Bayaran',
      stepDocuments:        'Muat Naik Dokumentasi',
      stepReviewSubmit:     'Semakan &amp; Hantar',

      // Application form — draft banner
      draftFound:           'Draf ditemui.',
      draftFoundMsg:        'Maklumat sebelum ini telah dipulihkan secara automatik. Sila semak sebelum menghantar.',
      draftDiscard:         'Buang Draf',

      // Application form — info strip
      infoIncomplete:       'Permohonan yang tidak lengkap akan ditolak. Pastikan semua dokumen sokongan dimuat naik sebelum menghantar.',

      // Application form — card titles
      cardTitlePersonal:    'Maklumat Peribadi',
      cardTitleCompany:     'Maklumat Syarikat',
      cardTitleParking:     'Butiran Petak',
      cardTitlePayment:     'Pengiraan Bayaran',
      cardTitleDocs:        'Dokumentasi',

      // Application form — personal info labels
      labelFullName:        'Nama Penuh',
      labelIcNo:            'No. Kad Pengenalan',
      labelPersonalPhone:   'No. Telefon Peribadi',

      // Application form — company info labels
      labelCompanyName:     'Nama Syarikat',
      labelSsmNo:           'No Pendaftaran SSM',
      labelCompanyEmail:    'Email Rasmi Syarikat',
      labelCompanyPhone:    'No. Telefon Syarikat',

      // Application form — company field errors
      errCompanyName:       'Sila masukkan nama syarikat.',
      errSsmNo:             'Sila masukkan no. pendaftaran SSM.',
      errCompanyEmail:      'Sila masukkan alamat email yang sah.',
      errCompanyPhone:      'Sila masukkan no. telefon syarikat.',

      // Application form — parking detail labels
      labelCategory:        'Kategori Perniagaan',
      labelBusinessType:    'Jenis Perniagaan',
      labelLocation:        'Lokasi',
      labelTotalParking:    'Jumlah Petak Dimohon',

      // Application form — parking field errors
      errCategory:          'Sila pilih kategori perniagaan.',
      errBusinessType:      'Sila pilih jenis perniagaan.',
      errLocation:          'Sila pilih lokasi.',
      errTotalParking:      'Sila masukkan jumlah petak (minimum 1).',

      // Application form — payment summary
      payEmptyHint:         'Pilih lokasi dan jumlah petak untuk melihat anggaran bayaran.',
      payRowLocation:       'Lokasi',
      payRowRate:           'Kadar Sewaan (sebulan / petak)',
      payRowLots:           'Jumlah Petak',
      payRowTotal:          'Jumlah Bayaran Anggaran / Bulan',
      payEstimateNote:      '* Anggaran sahaja. Jumlah sebenar tertakluk kepada kelulusan pihak pengurusan.',

      // Application form — document info strip
      docInfoStrip:         'Dokumen SSM, IC, dan Lesen Perniagaan boleh dimuat naik sebagai fail atau diambil menggunakan tab kamera. Gambar Lokasi mesti diambil menggunakan kamera berserta GPS.',

      // Application form — document labels
      labelSsmDoc:          'Salinan SSM Syarikat',
      labelIcDoc:           'Salinan IC Pemohon',
      labelLicenceDoc:      'Salinan Lesen Perniagaan',
      labelLocationDoc:     'Gambar Lokasi Yang Ingin Disewa',

      // Application form — doc field errors
      errSsmImg:            'Sila muat naik salinan SSM syarikat.',
      errIcImg:             'Sila muat naik salinan IC pemohon.',
      errLicenceImg:        'Sila muat naik salinan lesen perniagaan.',
      errLocationImg:       'Sila ambil gambar lokasi yang ingin disewa.',
      errDeclaration:       'Sila tandakan perakuan sebelum menghantar.',

      // Application form — upload zone shared UI
      uploadTabFile:        'Muat Naik',
      uploadTabCamera:      'Kamera',
      uploadFileTitle:      'Pilih Fail',
      uploadFileSub:        'PDF, JPG, PNG atau WEBP · Maks 5MB',
      uploadFileBrowse:     '📂 Semak Imbas',
      uploadCameraOpen:     '📷 Buka Kamera',
      uploadPreviewReady:   '✓ Sedia',

      // Application form — upload zone per-field camera titles & subtitles
      uploadCamTitleSsm:    'Ambil Gambar SSM',
      uploadCamSubSsm:      'Buka kamera untuk mengambil gambar dokumen SSM',
      uploadCamTitleIc:     'Ambil Gambar IC',
      uploadCamSubIc:       'Buka kamera untuk mengambil gambar IC pemohon',
      uploadCamTitleLic:    'Ambil Gambar Lesen',
      uploadCamSubLic:      'Buka kamera untuk mengambil gambar lesen perniagaan',
      uploadCamTitleLoc:    'Gambar Lokasi',
      uploadCamSubLoc:      'Kamera akan mengambil gambar berserta koordinat GPS lokasi sebenar.',

      // Application form — declaration
      declText:             'Saya mengaku bahawa semua maklumat yang diberikan adalah benar dan tepat. Saya memahami bahawa maklumat palsu atau dokumen yang tidak sah boleh menyebabkan permohonan ini ditolak atau permit dibatalkan.',

      // Payment history — past card labels
      payStatusPaid:        'Dibayar',
      payStatusFailed:      'Gagal / Tertunggak',
      payStatusOverdue:     'Tertunggak',
      payParkingRental:     'Petak Sewa',
      payBtnReceipt:        '↓ Resit',
      payBtnPay:            'Bayar',
      payBtnPayNow:         'Bayar Sekarang',
      payDueDate:           'Tarikh akhir',
      payRefPrefix:         'RUJUKAN',

      // Payment summary panel stats
      payStatTotalBills:    'Jumlah Bil',
      payStatPaid:          'Telah Dibayar',
      payStatOverdue:       'Tertunggak',
      payStatPending:       'Menunggu',
      payStatTotalPaid:     'Jumlah Dibayar',
      payStatBalance:       'Baki Perlu Dibayar',

      // Payment page
      payCurrentBill:       'Bil Semasa',
      payAmountLabel:       'Jumlah Bayaran',
      payMethodLabel:       'Kaedah Pembayaran',
      payMethodFpx:         'FPX / Bank',
      payMethodFpxDesc:     'Pindahan bank dalam talian',
      payMethodCard:        'Kad Kredit / Debit',
      payMethodCardDesc:    'Visa, Mastercard',
      payBankSelect:        '-- Pilih Bank Anda --',
      paySecureNote:        'Transaksi dilindungi dengan penyulitan 256-bit SSL yang selamat',
      payFinancialSummary:  'Ringkasan Kewangan',
      payAlertBill:         'Anda mempunyai 1 bil yang perlu dibayar sebelum permit dikeluarkan. Sila buat pembayaran segera untuk mengelakkan kelewatan.',

      // Profile page — form labels
      profLabelFullName:    'Nama Penuh (Syarikat / Individu)',
      profLabelFullNameHint:'Sila hubungi pentadbir MBS jika terdapat perubahan nama berdaftar.',
      profLabelEmail:       'Alamat Emel',
      profLabelPhone:       'No. Telefon Bimbit',
      profLabelCurrentPw:   'Kata Laluan Semasa',
      profConfirmIdentityHint: 'Diperlukan untuk mengesahkan perubahan emel atau no. telefon.',
      profLabelNewPw:       'Kata Laluan Baru',
      profLabelConfirmPw:   'Sahkan Kata Laluan Baru',

      // Confirm modal — shared across application/payment/profile/sidebar
      confirmSubmitTitle:      'Hantar permohonan ini?',
      confirmSubmitMsg:        'Sila pastikan semua maklumat dan dokumen yang dimuat naik adalah betul. Permohonan tidak boleh diubah selepas dihantar.',
      confirmSubmitOk:         'Hantar',
      confirmSubmitCancel:     'Batal',
      confirmDiscardDraftTitle:'Buang draf yang disimpan?',
      confirmKeep:             'Kekalkan',
      confirmPayTitle:         'Sahkan pembayaran?',
      confirmPayMsg:           'Pembayaran akan diproses serta-merta dan permohonan akan ditandakan selesai.',
      confirmLogoutTitle:      'Log keluar dari akaun?',
      confirmLogoutMsg:        'Anda perlu log masuk semula untuk mengakses papan pemuka anda.',
      confirmPasswordTitle:    'Tukar kata laluan?',
      confirmPasswordMsg:      'Kata laluan semasa anda akan digantikan serta-merta. Pastikan kata laluan baru telah disimpan di tempat yang selamat.',
    },
    en: {
      brandCouncil:         'Majlis Bandaraya<br>Seremban',
      brandSub:              'e-Parkir MBS',
      userRoleApplicant:    'Applicant',
      navSectionMain:       'Main Menu',
      navSectionAccount:    'Account',
      navDashboard:         'Dashboard',
      navApplication:       'New Application',
      navPayment:           'Receipts &amp; Payment',
      navProfile:           'My Profile',
      navSettings:          'Settings',
      navLogout:            'Log Out',
      mobNavDashboard:      'Dashboard',
      mobNavApplication:    'Application',
      mobNavPayment:        'Payment',
      mobNavProfile:        'Profile',
      mobNavSettings:       'Settings',
      mobNavLogout:         'Log Out',

      breadcrumbHome:       'Home',
      crumbDashboard:       'Dashboard',
      crumbApplication:     'New Application',
      crumbPayment:         'Receipts &amp; Payment',
      crumbProfile:         'User Profile',
      crumbSettings:        'Settings',

      notifTitle:           'Notifications',
      notifReadAll:         'Mark All Read',
      notifTabAll:          'All',
      notifTabUnread:       'Unread',
      notifTabStatus:       'Status',
      notifTabPayment:      'Payment',
      notifLoading:         'Loading...',
      notifEmpty:           'No notifications',
      notifFailed:          'Failed to load notifications.',
      notifFailedShort:     'Failed to load.',
      notifViewAll:         'View all notifications →',

      dashTitle:            'Dashboard',
      dashSub:               'A summary of all your applications.',
      dashTotalApps:        'Total Applications',
      statPending:          'In Progress',
      statApproved:         'Approved',
      statCompleted:        'Completed',
      statRejected:         'Rejected',
      dashStatusTitle:      'Application Status',
      dashStatusSub:        'Review and manage all your applications here.',
      searchPlaceholder:    'Search application no., type…',
      filterAll:            'All',
      filterPending:        'In Progress',
      filterApproved:       'Approved',
      filterCompleted:      'Completed',
      filterRejected:       'Rejected',
      sortNewest:           'Newest',
      sortOldest:           'Oldest',
      btnNewApplication:    '+ New Application',
      noApplicationsFound:  'No applications found.',

      appPageTitle:         'Parking Lot Application',
      appPageSub:           'Please complete all required information. Fields marked',
      appPageSubRequired:   'are mandatory.',
      btnBack:              'Back',
      btnSaveDraft:         'Save Draft',
      btnSubmitApp:         'Submit Application',

      payPageTitle:         'Receipts &amp; Payment',
      payPageSub:           'Manage your bills and view all your payment records.',
      payHistoryTitle:      'Payment History',
      payNoHistory:         'No payments recorded.',
      payNotFound:          'Application not found or you do not have access.',

      profPageTitle:        'User Profile',
      profPageSub:          'Update your personal information and account password.',
      profAccountInfo:      'Account Information',
      profSecurity:         'Security &amp; Password',
      profSaveInfo:         'Save Profile Information',
      profChangePassword:   'Change Password',

      setPageTitle:         'Settings',
      setPageSub:           'Manage your display preferences and app language.',
      setLogoutTitle:       'Log Out of Account',
      setLogoutSub:         'End your login session on this device.',

      // Notification message templates ({0}, {1} are positional params)
      notifMsgAppSubmitted:    'Application {0} has been submitted and is being reviewed by our officer. Thank you!',
      notifMsgAppApproved:     'Application {0} has been approved. Please make payment to activate your permit.',
      notifMsgAppRejected:     'Application {0} was not approved. Please check the rejection reason and contact an officer.',
      notifMsgPaymentReceived: 'Payment {0} totalling {1} has been successfully received. Your parking lot permit is now active.',
      notifMsgPaymentDue:      'The payment bill for application {0} must be settled promptly.',
      notifMsgPaymentReminder: 'Reminder: Payment for application {0} has not yet been received.',

      notiTypeAppSubmitted:    'Application Submitted',
      notiTypeAppApproved:     'Application Approved',
      notiTypeAppRejected:     'Application Rejected',
      notiTypePaymentDue:      'Payment Due',
      notiTypePaymentReceived: 'Payment Received',
      notiTypePaymentReminder: 'Payment Reminder',
      notiTypeDefault:         'Notification',


      // Select placeholder options
      selCategory:          '-- Pilih Kategori --',
      selBusinessType:      '-- Pilih Jenis Perniagaan --',
      selLocation:          '-- Pilih Lokasi --',
      phTotalParking:       'Masukkan jumlah petak',

      // Toast messages
      toastSuccess:         'Berjaya',
      toastError:           'Ralat',
      toastValidationTitle: 'Permohonan Tidak Lengkap',
      toastValidationMsg:   'Sila lengkapkan semua medan bertanda (*) sebelum menghantar.',
      toastFormErrorTitle:  'Permohonan Gagal Dihantar',
      toastFormErrorMsg:    'Sila semak semula maklumat yang ditandakan di bawah sebelum menghantar.',
      errorMissingPrefix:   'Tiada ',
      draftSaving:          'Menyimpan…',
      draftSaved:           'Draf Disimpan',
      draftDiscardConfirm:  'Buang draf yang disimpan? Semua maklumat yang belum dihantar akan dipadamkan.',

      // Dashboard — timeline step labels
      tlSubmitted:          'Permohonan Dihantar',
      tlDone:               'Selesai',
      tlSystem:             'Sistem Auto',
      tlContactOfficer:     'Sila hubungi pegawai untuk maklumat lanjut.',
      tlRejected:           'Permohonan Ditolak',
      tlNotApproved:        'Tidak Diluluskan',
      tlBillPayment:        'Pembayaran Bil',
      tlAwaitingPayment:    'Menunggu Pembayaran',
      tlActivatePermit:     'Sila jelaskan bayaran permit untuk mengaktifkan lesen petak.',
      tlPaymentDone:        'Pembayaran Selesai',
      tlNotYet:             'Belum dilaksanakan',
      tlCompleted:          'Selesai',
      tlLicenceActive:      'Lesen Aktif',
      tlAwaitingApproval:   'Menunggu Kelulusan Pegawai',
      tlUnderReview:        'Sedang dalam semakan...',
      tlNotifProcessing:    'Notifikasi kepada pemohon sedang diproses.',
      tlDocsVerified:       'Dokumen Disahkan',

      // Dashboard — detail panel misc
      dpAppNoPrefix:        'No. Permohonan',
      dpRejectionHeader:    'Sebab Penolakan',
      dpNoRejectionReason:  'Tiada sebab penolakan dinyatakan. Sila hubungi pegawai untuk maklumat lanjut.',
      docNameSsm:           'Dokumen SSM',
      docNameIc:            'Salinan IC',
      docNameLocation:      'Foto Lokasi',
      docNameLicence:       'Salinan Lesen',
      docUploaded:          'Dimuat naik',
      docMissing:           'Tiada Fail',
      alertSelectApp:       'Sila pilih permohonan terlebih dahulu.',
      emailSubject:         'Pertanyaan Permohonan',
      emailGreeting:        'Salam,',
      emailIntro:           'Saya ingin bertanya mengenai permohonan berikut:',
      emailStatusLabel:     'Status Semasa',
      emailQuestion:        'Pertanyaan saya:',
      emailQuestionHint:    'Sila nyatakan pertanyaan anda di sini',
      emailClosing:         'Terima kasih.',

      // Profile — form error
      formErrorFix:         'Sila betulkan ralat borang di bawah.',
      declLabel:            'Perakuan pemohon',


      // Select placeholder options
      selCategory:          '-- Select Category --',
      selBusinessType:      '-- Select Business Type --',
      selLocation:          '-- Select Location --',
      phTotalParking:       'Enter number of lots',

      // Toast messages
      toastSuccess:         'Success',
      toastError:           'Error',
      toastValidationTitle: 'Incomplete Application',
      toastValidationMsg:   'Please complete all fields marked (*) before submitting.',
      toastFormErrorTitle:  'Application Submission Failed',
      toastFormErrorMsg:    'Please review the highlighted fields below before submitting.',
      errorMissingPrefix:   'Missing ',
      draftSaving:          'Saving…',
      draftSaved:           'Draft Saved',
      draftDiscardConfirm:  'Discard saved draft? All unsent information will be deleted.',

      // Dashboard — timeline step labels
      tlSubmitted:          'Application Submitted',
      tlDone:               'Completed',
      tlSystem:             'Auto System',
      tlContactOfficer:     'Please contact the officer for further information.',
      tlRejected:           'Application Rejected',
      tlNotApproved:        'Not Approved',
      tlBillPayment:        'Bill Payment',
      tlAwaitingPayment:    'Awaiting Payment',
      tlActivatePermit:     'Please settle the permit payment to activate the parking lot licence.',
      tlPaymentDone:        'Payment Completed',
      tlNotYet:             'Not yet carried out',
      tlCompleted:          'Completed',
      tlLicenceActive:      'Licence Active',
      tlAwaitingApproval:   'Awaiting Officer Approval',
      tlUnderReview:        'Currently under review...',
      tlNotifProcessing:    'Notification to applicant is being processed.',
      tlDocsVerified:       'Documents Verified',

      // Dashboard — detail panel misc
      dpAppNoPrefix:        'Application No.',
      dpRejectionHeader:    'Rejection Reason',
      dpNoRejectionReason:  'No rejection reason stated. Please contact the officer for further information.',
      docNameSsm:           'SSM Document',
      docNameIc:            'IC Copy',
      docNameLocation:      'Location Photo',
      docNameLicence:       'Licence Copy',
      docUploaded:          'Uploaded',
      docMissing:           'No File',
      alertSelectApp:       'Please select an application first.',
      emailSubject:         'Application Enquiry',
      emailGreeting:        'Dear Sir/Madam,',
      emailIntro:           'I would like to enquire about the following application:',
      emailStatusLabel:     'Current Status',
      emailQuestion:        'My enquiry:',
      emailQuestionHint:    'Please state your enquiry here',
      emailClosing:         'Thank you.',

      // Profile — form error
      formErrorFix:         'Please correct the errors in the form below.',
      declLabel:            'Applicant Declaration',

      // Dashboard — detail panel section labels & info row keys
      dpSectionStatus:      'Application Workflow',
      dpSectionRejection:   'Rejection Reason',
      dpSectionCompany:     'Company &amp; Business Info',
      dpSectionParking:     'Location &amp; Parking Info',
      dpSectionDocs:        'Supporting Documents Uploaded',


      // Application card — status badge labels
      statusPending:        'Pending Approval',
      statusApproved:       'Approved',
      statusRejected:       'Rejected',
      statusCompleted:      'Completed',
      statusUnknown:        'Unknown',

      // Application card — progress step labels
      progSubmit:           'Submit',
      progApprove:          'Approve',
      progPay:              'Pay',
      progDone:             'Done',

      // Application card — action button labels
      btnViewDocs:          'View Documents',
      btnCheckStatus:       'Check Status',
      btnPayNow:            'Pay Now',
      btnViewReason:        'View Reason',
      btnReapply:           'Reapply',
      btnPrintLicence:      'Print Licence',
      btnHelp:              'Help',
      btnContact:           'Contact',

      // Misc units & labels
      unitLots:             'Lots',
      dpOfficerNote:        'Officer note',

      dpDownloadPdf:        'Download PDF',
      dpContactOfficer:     'Contact Officer',

      // Application form — step bar
      stepPersonalCompany:  'Personal &amp; Company Info',
      stepParkingPayment:   'Parking Details &amp; Payment',
      stepDocuments:        'Upload Documents',
      stepReviewSubmit:     'Review &amp; Submit',

      // Application form — draft banner
      draftFound:           'Draft found.',
      draftFoundMsg:        'Your previous information has been automatically restored. Please review before submitting.',
      draftDiscard:         'Discard Draft',

      // Application form — info strip
      infoIncomplete:       'Incomplete applications will be rejected. Ensure all supporting documents are uploaded before submitting.',

      // Application form — card titles
      cardTitlePersonal:    'Personal Information',
      cardTitleCompany:     'Company Information',
      cardTitleParking:     'Parking Details',
      cardTitlePayment:     'Payment Calculation',
      cardTitleDocs:        'Documentation',

      // Application form — personal info labels
      labelFullName:        'Full Name',
      labelIcNo:            'IC / MyKad Number',
      labelPersonalPhone:   'Personal Phone Number',

      // Application form — company info labels
      labelCompanyName:     'Company Name',
      labelSsmNo:           'SSM Registration No.',
      labelCompanyEmail:    'Official Company Email',
      labelCompanyPhone:    'Company Phone Number',

      // Application form — company field errors
      errCompanyName:       'Please enter the company name.',
      errSsmNo:             'Please enter the SSM registration number.',
      errCompanyEmail:      'Please enter a valid email address.',
      errCompanyPhone:      'Please enter the company phone number.',

      // Application form — parking detail labels
      labelCategory:        'Business Category',
      labelBusinessType:    'Type of Business',
      labelLocation:        'Location',
      labelTotalParking:    'Number of Lots Requested',

      // Application form — parking field errors
      errCategory:          'Please select a business category.',
      errBusinessType:      'Please select a type of business.',
      errLocation:          'Please select a location.',
      errTotalParking:      'Please enter the number of lots (minimum 1).',

      // Application form — payment summary
      payEmptyHint:         'Select a location and number of lots to see the estimated payment.',
      payRowLocation:       'Location',
      payRowRate:           'Rental Rate (per month / lot)',
      payRowLots:           'Total Lots',
      payRowTotal:          'Estimated Total Payment / Month',
      payEstimateNote:      '* Estimate only. Actual amount is subject to management approval.',

      // Application form — document info strip
      docInfoStrip:         'SSM, IC, and Business Licence documents can be uploaded as files or captured using the camera tab. Location Photo must be taken with the camera along with GPS coordinates.',

      // Application form — document labels
      labelSsmDoc:          'SSM Company Copy',
      labelIcDoc:           'Applicant IC Copy',
      labelLicenceDoc:      'Business Licence Copy',
      labelLocationDoc:     'Photo of Location to Rent',

      // Application form — doc field errors
      errSsmImg:            'Please upload a copy of the company SSM.',
      errIcImg:             'Please upload a copy of the applicant\'s IC.',
      errLicenceImg:        'Please upload a copy of the business licence.',
      errLocationImg:       'Please take a photo of the location to rent.',
      errDeclaration:       'Please tick the declaration before submitting.',

      // Application form — upload zone shared UI
      uploadTabFile:        'Upload',
      uploadTabCamera:      'Camera',
      uploadFileTitle:      'Choose File',
      uploadFileSub:        'PDF, JPG, PNG or WEBP · Max 5MB',
      uploadFileBrowse:     '📂 Browse',
      uploadCameraOpen:     '📷 Open Camera',
      uploadPreviewReady:   '✓ Ready',

      // Application form — upload zone per-field camera titles & subtitles
      uploadCamTitleSsm:    'Capture SSM Photo',
      uploadCamSubSsm:      'Open camera to take a photo of the SSM document',
      uploadCamTitleIc:     'Capture IC Photo',
      uploadCamSubIc:       'Open camera to take a photo of the applicant\'s IC',
      uploadCamTitleLic:    'Capture Licence Photo',
      uploadCamSubLic:      'Open camera to take a photo of the business licence',
      uploadCamTitleLoc:    'Location Photo',
      uploadCamSubLoc:      'Camera will capture a photo along with the actual GPS coordinates.',

      // Application form — declaration
      declText:             'I declare that all information provided is true and accurate. I understand that false information or invalid documents may result in this application being rejected or the permit being cancelled.',

      // Payment history — past card labels
      payStatusPaid:        'Dibayar',
      payStatusFailed:      'Gagal / Tertunggak',
      payStatusOverdue:     'Tertunggak',
      payParkingRental:     'Petak Sewa',
      payBtnReceipt:        '↓ Resit',
      payBtnPay:            'Bayar',
      payBtnPayNow:         'Bayar Sekarang',
      payDueDate:           'Tarikh akhir',
      payRefPrefix:         'RUJUKAN',

      // Payment summary panel stats
      payStatTotalBills:    'Jumlah Bil',
      payStatPaid:          'Telah Dibayar',
      payStatOverdue:       'Tertunggak',
      payStatPending:       'Menunggu',
      payStatTotalPaid:     'Jumlah Dibayar',
      payStatBalance:       'Baki Perlu Dibayar',

      // Payment history — past card labels
      payStatusPaid:        'Paid',
      payStatusFailed:      'Failed / Overdue',
      payStatusOverdue:     'Overdue',
      payParkingRental:     'Parking Rental',
      payBtnReceipt:        '↓ Receipt',
      payBtnPay:            'Pay',
      payBtnPayNow:         'Pay Now',
      payDueDate:           'Due date',
      payRefPrefix:         'REFERENCE',

      // Payment summary panel stats
      payStatTotalBills:    'Total Bills',
      payStatPaid:          'Paid',
      payStatOverdue:       'Overdue',
      payStatPending:       'Pending',
      payStatTotalPaid:     'Total Paid',
      payStatBalance:       'Balance Due',

      // Payment page
      payCurrentBill:       'Current Bill',
      payAmountLabel:       'Payment Amount',
      payMethodLabel:       'Payment Method',
      payMethodFpx:         'FPX / Bank',
      payMethodFpxDesc:     'Online bank transfer',
      payMethodCard:        'Credit / Debit Card',
      payMethodCardDesc:    'Visa, Mastercard',
      payBankSelect:        '-- Select Your Bank --',
      paySecureNote:        'Transaction secured with 256-bit SSL encryption',
      payFinancialSummary:  'Financial Summary',
      payAlertBill:         'You have 1 outstanding bill to pay before your permit is issued. Please make payment promptly to avoid delays.',

      // Profile page — form labels
      profLabelFullName:    'Full Name (Company / Individual)',
      profLabelFullNameHint:'Please contact MBS administration if there are changes to the registered name.',
      profLabelEmail:       'Email Address',
      profLabelPhone:       'Mobile Phone Number',
      profLabelCurrentPw:   'Current Password',
      profConfirmIdentityHint: 'Required to confirm changes to your email or phone number.',
      profLabelNewPw:       'New Password',
      profLabelConfirmPw:   'Confirm New Password',

      // Confirm modal — shared across application/payment/profile/sidebar
      confirmSubmitTitle:      'Submit this application?',
      confirmSubmitMsg:        'Make sure all information and uploaded documents are correct. The application cannot be changed after submission.',
      confirmSubmitOk:         'Submit',
      confirmSubmitCancel:     'Cancel',
      confirmDiscardDraftTitle:'Discard the saved draft?',
      confirmKeep:             'Keep it',
      confirmPayTitle:         'Confirm payment?',
      confirmPayMsg:           'Payment will be processed immediately and the application will be marked complete.',
      confirmLogoutTitle:      'Log out of your account?',
      confirmLogoutMsg:        'You\'ll need to log in again to access your dashboard.',
      confirmPasswordTitle:    'Change your password?',
      confirmPasswordMsg:      'Your current password will be replaced immediately. Make sure you\'ve saved the new one somewhere safe.',
    },
  };

  function readSettings() {
    try {
      const saved = localStorage.getItem(STORAGE_KEY);
      if (saved) return Object.assign({}, DEFAULTS, JSON.parse(saved));
    } catch (e) {}
    return Object.assign({}, DEFAULTS);
  }

  function writeSettings(settings) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(settings)); } catch (e) {}
  }

  function currentLang() {
    const lang = readSettings().language;
    return DICT[lang] ? lang : 'ms';
  }

  function t(key) {
    const lang = currentLang();
    return (DICT[lang] && DICT[lang][key] !== undefined) ? DICT[lang][key] : (DICT.ms[key] !== undefined ? DICT.ms[key] : key);
  }

  // ── Notification type display metadata ──────────────────────────────────
  // Shared across every page's notification panel so the title is translated
  // consistently without each page keeping its own copy of the map.
  const NOTI_TYPE_META = {
    app_submitted:    { key: 'notiTypeAppSubmitted',    iconColor: '#a07000', bgColor: '#FFF8D6' },
    app_approved:     { key: 'notiTypeAppApproved',     iconColor: '#3B6D11', bgColor: '#EAF3DE' },
    app_rejected:     { key: 'notiTypeAppRejected',     iconColor: '#C0392B', bgColor: '#FCEBEB' },
    payment_due:      { key: 'notiTypePaymentDue',      iconColor: '#185FA5', bgColor: '#E6F1FB' },
    payment_received: { key: 'notiTypePaymentReceived', iconColor: '#0F6E56', bgColor: '#E1F5EE' },
    payment_reminder: { key: 'notiTypePaymentReminder', iconColor: '#854F0B', bgColor: '#FAEEDA' },
  };

  function notifTypeMeta(type) {
    const entry = NOTI_TYPE_META[type];
    if (!entry) return { title: t('notiTypeDefault'), iconColor: '#888', bgColor: '#f0ede6' };
    return { title: t(entry.key), iconColor: entry.iconColor, bgColor: entry.bgColor };
  }

  // Map from noti_type / msgKey → i18n template key
  const NOTIF_MSG_KEY_MAP = {
    app_submitted:    'notifMsgAppSubmitted',
    app_approved:     'notifMsgAppApproved',
    app_rejected:     'notifMsgAppRejected',
    payment_received: 'notifMsgPaymentReceived',
    payment_due:      'notifMsgPaymentDue',
    payment_reminder: 'notifMsgPaymentReminder',
  };

  /**
   * Resolve a notification's display message.
   *
   * @param {object} n  - notification item from the API
   *   n.msgKey    {string|null}   - structured key, e.g. 'app_submitted'
   *   n.msgParams {string[]}      - positional params, e.g. ['MBS-20260624-0001']
   *   n.message   {string}        - raw/legacy Malay fallback text
   */
  function resolveNotifMessage(n) {
    if (n.msgKey && NOTIF_MSG_KEY_MAP[n.msgKey]) {
      let template = t(NOTIF_MSG_KEY_MAP[n.msgKey]);
      // Replace {0}, {1}, … with actual param values
      (n.msgParams || []).forEach(function(param, i) {
        template = template.replace('{' + i + '}', param);
      });
      return template;
    }
    // Legacy row — raw Malay text stored in DB, display as-is
    return n.message || '';
  }

  // Apply translations to every element on the current page carrying
  // data-i18n / data-i18n-placeholder / data-i18n-html attributes.
  function apply(root) {
    const scope = root || document;

    scope.querySelectorAll('[data-i18n]').forEach(function (el) {
      const key = el.getAttribute('data-i18n');
      const val = t(key);
      // Allow explicit opt-in to innerHTML for strings containing &amp; etc.
      if (el.hasAttribute('data-i18n-html')) {
        el.innerHTML = val;
      } else {
        el.textContent = val;
      }
    });

    scope.querySelectorAll('[data-i18n-placeholder]').forEach(function (el) {
      const key = el.getAttribute('data-i18n-placeholder');
      el.setAttribute('placeholder', t(key));
    });

    document.documentElement.setAttribute('lang', currentLang() === 'en' ? 'en' : 'ms');
  }

  function setLanguage(lang) {
    if (!DICT[lang]) return;
    const settings = readSettings();
    settings.language = lang;
    writeSettings(settings);
    apply();
    // Let other open tabs/pages know immediately (storage event only fires
    // in *other* tabs natively, so we also dispatch a same-tab custom event
    // for any page that wants to react, e.g. re-render dynamic lists).
    window.dispatchEvent(new CustomEvent('mbs:languagechange', { detail: { language: lang } }));
  }

  // React to changes made from another tab/page.
  window.addEventListener('storage', function (e) {
    if (e.key === STORAGE_KEY) apply();
  });

  window.MBS_I18N = {
    t: t,
    apply: apply,
    setLanguage: setLanguage,
    currentLang: currentLang,
    readSettings: readSettings,
    writeSettings: writeSettings,
    notifTypeMeta: notifTypeMeta,
    resolveNotifMessage: resolveNotifMessage,
  };

  // Apply as soon as the DOM is interactive, so there's no flash of the
  // default-language text before JS kicks in.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { apply(); });
  } else {
    apply();
  }
})(window);