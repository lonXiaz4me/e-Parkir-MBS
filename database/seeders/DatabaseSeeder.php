<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * This seeder intentionally goes through the SAME Eloquent models (and,
     * where practical, the same helper logic) that the real controllers use
     * — User::create(), Application::create(), Payment::create(),
     * Notification::send() — instead of raw DB::table()->insert() calls.
     *
     * FIX #27 (app_id ↔ chronology ↔ notification id ordering):
     *   $applicationDefs is processed in array order, and Application::
     *   create() assigns app_id via plain auto-increment in that same
     *   order. Each application's notifications are created immediately
     *   after it (see FIX #26), so noti_id ordering always follows
     *   application (app_id) ordering.
     *
     *   The definitions below are ordered:
     *     1. not_approved  (Zainol Trading Co)
     *     2. completed     (Zainol Hardware Trading)
     *     3. approved      (Zainol Auto Parts)
     *     4. pending        (Zainol Enterprise)
     *
     *   'submitted_at' for each entry is also set in strictly increasing
     *   order matching this same sequence, so the seeded data is
     *   internally consistent: app_id order, notification id order, and
     *   the actual submitted_at timestamps all agree with each other and
     *   with this intended status progression — not_approved app_id=1 is
     *   the earliest submission, completed app_id=2 next, approved
     *   app_id=3 next, and the still-pending app_id=4 is the most recent.
     */
    public function run(): void
    {
        // ── STEP 1: "Register" the user ──────────────────────────────────────
        // Mirrors UserController::store(): plain password in, hashed cast
        // handles the rest. Auth::login() isn't relevant here (no session in
        // a seeder), but everything else matches a real signup.
        $user = User::create([
            'full_name' => 'Muhammad Zainol Izudin Bin Ahmad',
            'ic_no' => '020723-10-1365',
            'email' => 'zenfrost23@gmail.com',
            'phone_no' => '0182894347',
            'password' => '@Password123',
        ]);

        // ── STEP 2: Define each application AND every event that happens to
        //    it, in the exact order they should end up in the database:
        //    not_approved → completed → approved → pending. Each entry is
        //    processed fully (application → submitted notification →
        //    decision notification → payment + notification, as applicable)
        //    before moving to the next entry. ─────────────────────────────────
        $applicationDefs = [
            // #1 — not_approved. Submitted first, then rejected. A failed
            // payment attempt was also made against it (no notification for
            // a failed attempt, matching PaymentController::store(), which
            // only sends a notification on successful payment).
            [
                'app_no' => 'MBS-20260610-000001',
                'company_name' => 'Zainol Trading Co',
                'company_email' => 'zenfrost23@gmail.com',
                'company_phone' => '0182894347',
                'ssm_no' => '202301004444',
                'category' => 'Kategori B',
                'type_of_business' => 'Premis perniagaan hardware',
                'location' => 'Nilai – Hadapan / Lorong / Belakang',
                'location_coords' => '2.8123,101.9777',
                'total_parking' => 3,
                'submitted_at' => Carbon::create(2026, 6, 10, 9, 12),
                'app_status' => 'not_approved',
                'app_status_msg' => 'Permohonan anda tidak berjaya. Sila semak sebab penolakan dan hantar semula.',
                'not_approved_reason' => 'Dokumen SSM tidak jelas dan susah dibaca. Sila muat naik semula salinan yang lebih jelas.',
                'decision' => 'app_rejected',
                'decided_at' => Carbon::create(2026, 6, 12, 14, 40),
                'payment' => null,
            ],
            // #2 — completed. Submitted next, approved, then paid in full.
            [
                'app_no' => 'MBS-20260615-000002',
                'company_name' => 'Zainol Hardware Trading',
                'company_email' => 'zenfrost23@gmail.com',
                'company_phone' => '0182894347',
                'ssm_no' => '202301003333',
                'category' => 'Kategori B',
                'type_of_business' => 'Premis perniagaan hardware',
                'location' => 'Seremban – Kawasan belum warta',
                'location_coords' => '2.7260,101.9450',
                'total_parking' => 8,
                'submitted_at' => Carbon::create(2026, 6, 15, 10, 5),
                'app_status' => 'completed',
                'app_status_msg' => 'Permohonan anda telah diluluskan dan pembayaran telah diterima.',
                'not_approved_reason' => null,
                'set_date_painted' => '2026-06-20',
                'end_date_painted' => '2027-06-19',
                'decision' => 'app_approved',
                'decided_at' => Carbon::create(2026, 6, 16, 9, 0),
                'payment' => [
                    'status' => 'paid',
                    'type' => 'online_transfer',
                    'bank' => 'Maybank2u',
                    'paid_at' => Carbon::create(2026, 6, 18, 16, 22),
                ],
            ],
            // #3 — approved. Submitted next, approved, still awaiting
            // payment (no payment event for this one yet).
            [
                'app_no' => 'MBS-20260620-000003',
                'company_name' => 'Zainol Auto Parts',
                'company_email' => 'zenfrost23@gmail.com',
                'company_phone' => '0182894347',
                'ssm_no' => '202301002222',
                'category' => 'Kategori A',
                'type_of_business' => 'Premis perniagaan menjual tayar / menjual bateri / bengkel kenderaan',
                'location' => 'Seremban',
                'location_coords' => '2.7350,101.9501',
                'total_parking' => 4,
                'submitted_at' => Carbon::create(2026, 6, 20, 11, 30),
                'app_status' => 'approved',
                'app_status_msg' => 'Tahniah! Permohonan anda telah diluluskan. Sila buat pembayaran untuk mengaktifkan permit.',
                'not_approved_reason' => null,
                'decision' => 'app_approved',
                'decided_at' => Carbon::create(2026, 6, 22, 9, 15),
                'payment' => null,
            ],
            // #4 — pending. Freshly submitted, still awaiting officer review
            // (no decision yet).
            [
                'app_no' => 'MBS-20260722-000004',
                'company_name' => 'Zainol Enterprise',
                'company_email' => 'zenfrost23@gmail.com',
                'company_phone' => '0182894347',
                'ssm_no' => '202301001111',
                'category' => 'Kategori B',
                'type_of_business' => 'Bank / kedai pajak gadai / syarikat kewangan',
                'location' => 'Seremban – Hadapan (Kategori B)',
                'location_coords' => '2.7297,101.9422',
                'total_parking' => 2,
                'submitted_at' => Carbon::now(),
                'app_status' => 'pending',
                'app_status_msg' => 'Permohonan telah diterima dan sedang menunggu semakan pegawai.',
                'not_approved_reason' => null,
                'decision' => null,
                'decided_at' => null,
                'payment' => null,
            ],
        ];

        // NOTE: intentionally NOT sorted/reordered here — $applicationDefs
        // above is already written in the exact order the applications
        // should be created in (not_approved → completed → approved →
        // pending), and 'submitted_at' on each entry increases in that same
        // order, so array order and chronological order already agree.

        foreach ($applicationDefs as $def) {
            // Same calculation ApplicationController::store() performs
            // server-side before ever writing the row — see FIX #24.
            $totalAmount = Application::calculateMonthlyRate(
                $def['location'],
                (int) $def['total_parking']
            );

            // ── 2a. "Submit" the application ─────────────────────────────────
            $application = Application::create([
                'app_no' => $def['app_no'],
                'user_id' => $user->user_id,
                'company_name' => $def['company_name'],
                'company_email' => $def['company_email'],
                'company_phone' => $def['company_phone'],
                'ssm_no' => $def['ssm_no'],
                // No real files exist in this seeded environment, so these
                // stay empty rather than pointing at a nonexistent blob —
                // the dashboard already renders "Tiada Fail" correctly for
                // empty has_ssm/has_ic/etc. flags derived from these.
                'ssm_img' => '',
                'category' => $def['category'],
                'type_of_business' => $def['type_of_business'],
                'location' => $def['location'],
                'location_img' => '',
                'location_coords' => $def['location_coords'],
                'total_parking' => $def['total_parking'],
                'ic_img' => '',
                'licence_img' => '',
                'app_status' => $def['app_status'],
                'app_status_msg' => $def['app_status_msg'],
                'not_approved_reason' => $def['not_approved_reason'],
                'painted_lot_img' => null,
                'set_date_painted' => $def['set_date_painted'] ?? null,
                'end_date_painted' => $def['end_date_painted'] ?? null,
                'total_amount' => $totalAmount,
            ]);

            // created_at/updated_at aren't mass-assignable via the normal
            // flow (Eloquent stamps them automatically on create), so we
            // backdate them the same way a real historical backfill would:
            // update the timestamps after the row exists.
            $application->forceFill([
                'created_at' => $def['submitted_at'],
                'updated_at' => $def['decided_at'] ?? $def['submitted_at'],
            ])->save();

            // Same Notification::send() call ApplicationController::store()
            // fires immediately after a successful submission — created
            // right here, right after the application it belongs to, so its
            // noti_id stays grouped with application #N before #N+1 exists.
            // Because the applications loop follows the intended
            // not_approved → completed → approved → pending order (FIX
            // #27), notification ids follow that exact same order too.
            $submittedNotification = Notification::send(
                $user->user_id,
                $application->app_id,
                'app_submitted',
                "app_submitted:{$application->app_no}"
            );
            $submittedNotification->forceFill(['created_at' => $def['submitted_at']])->save();

            // ── 2b. Officer decision (if one has happened for this app) ──────
            if ($def['decision'] !== null) {
                $decisionNotification = Notification::send(
                    $user->user_id,
                    $application->app_id,
                    $def['decision'],
                    "{$def['decision']}:{$application->app_no}"
                );
                $decisionNotification->forceFill(['created_at' => $def['decided_at']])->save();
            }

            // ── 2c. Payment attempt (if one has happened for this app) ───────
            // Mirrors PaymentController::store(): total_amt comes from the
            // application record (never client input), invoice_no follows
            // the same INV-{uppercased id} shape the controller generates,
            // and a payment_received notification only fires on success —
            // a failed attempt (see the "Zainol Trading Co" entry) gets no
            // notification, exactly like the real controller.
            if ($def['payment'] !== null) {
                $pay = $def['payment'];

                $payment = Payment::create([
                    'user_id' => $user->user_id,
                    'app_no' => $application->app_no,
                    'invoice_no' => 'INV-' . strtoupper(substr(md5($application->app_no), 0, 13)),
                    'payment_type' => $pay['type'],
                    'bank_name' => $pay['bank'],
                    'total_amt' => $totalAmount,
                    'payment_status' => $pay['status'],
                ]);
                $payment->forceFill(['created_at' => $pay['paid_at']])->save();

                if ($pay['status'] === 'paid') {
                    $paymentNotification = Notification::send(
                        $user->user_id,
                        $application->app_id,
                        'payment_received',
                        "payment_received:{$payment->invoice_no}:RM " . number_format((float) $payment->total_amt, 2)
                    );
                    $paymentNotification->forceFill(['created_at' => $pay['paid_at']->copy()->addMinutes(3)])->save();
                }
            }
        }
    }
}