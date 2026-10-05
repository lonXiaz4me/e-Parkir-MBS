<?php
// Application.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $table      = 'application';
    protected $primaryKey = 'app_id';

    public $timestamps = true;

    protected $fillable = [
        'app_no',
        'user_id',
        'company_name',
        'ssm_no',
        // FIX #9: Added company_email and company_phone — these were collected
        // on the application form and validated client-side, but never added
        // to $fillable, so Application::create() silently dropped them even
        // if the controller tried to pass them.
        'company_email',
        'company_phone',
        'ssm_img',
        'category',
        'type_of_business',
        'location',
        'location_img',
        'location_coords',
        'total_parking',
        'ic_img',
        'licence_img',
        'app_status',
        'app_status_msg',
        'not_approved_reason',
        'painted_lot_img',
        'set_date_painted',
        'end_date_painted',
        'total_amount',
    ];

    protected $casts = [
        'set_date_painted' => 'date',
        'end_date_painted' => 'date',
        'total_parking'    => 'integer',
        // FIX #22: total_amount previously had no cast, so its PHP type
        // depended entirely on the underlying DB driver/column type — every
        // call site had to remember to (float) cast it themselves
        // (PaymentController did; payment_blade.php's number_format() calls
        // happened to work regardless, since number_format() coerces). A
        // decimal:2 cast makes Eloquent always return a string scaled to
        // exactly 2 decimal places (e.g. "378.00"), which avoids silent
        // floating-point drift across repeated reads/writes and removes the
        // need for ad-hoc casting at every usage site. Existing call sites
        // (number_format(), (float) casts, arithmetic with total_parking)
        // all work unchanged with a numeric string.
        'total_amount'     => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Accessors ────────────────────────────────────────────────────────────
    public function getSsmUrlAttribute()      { return asset('storage/' . $this->ssm_img); }
    public function getLocationUrlAttribute() { return asset('storage/' . $this->location_img); }
    public function getIcUrlAttribute()       { return asset('storage/' . $this->ic_img); }
    public function getLicenceUrlAttribute()  { return asset('storage/' . $this->licence_img); }
    public function getPaintedLotUrlAttribute() { return $this->painted_lot_img ? asset('storage/' . $this->painted_lot_img) : null; }

    // ── Helper Methods ───────────────────────────────────────────────────────
    public function isApproved(): bool
    {
        return $this->app_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->app_status === 'not_approved';
    }

    // FIX #24: Mirrors the rate table in application.blade.php's
    // calculatePayment() JS function exactly. Previously
    // ApplicationController::store() trusted $request->input('grand_total')
    // — a hidden form field calculated entirely client-side — meaning anyone
    // could open devtools, edit that field's value before submitting, and
    // get approved for a custom (or zero) monthly rate. The server now
    // recomputes the price itself from the validated location + lot count
    // and ignores whatever the client sent for the total.
    //
    // Returns null for an unrecognized location, same as the JS leaving
    // finalRate at 0 for a location that doesn't match any case.
    public static function calculateMonthlyRate(string $location, int $totalLots): ?float
    {
        if ($totalLots < 1) {
            return null;
        }

        $rate = match ($location) {
            'Seremban – Hadapan (Kategori A)' => match (true) {
                $totalLots >= 6 => 90.00,
                $totalLots >= 4 => 108.00,
                $totalLots >= 2 => 126.00,
                default          => 180.00,
            },
            'Seremban – Hadapan (Kategori B)'     => 180.00,
            'Seremban'                              => 65.00,
            'Nilai – Hadapan / Lorong / Belakang' => 80.00,
            'Seremban – Kawasan belum warta'       => 90.00,
            default                                  => null,
        };

        return $rate === null ? null : round($rate * $totalLots, 2);
    }
}