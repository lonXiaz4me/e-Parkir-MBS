<?php
// Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $table      = 'payment';
    protected $primaryKey = 'payment_id';

    // CHANGED: Enabled — table now has created_at column
    public $timestamps = true;

    // ADDED: Table only has created_at, not updated_at — tell Eloquent to skip updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'app_no',
        'invoice_no',
        'payment_type',
        'bank_name',   // ADDED
        'total_amt',
        'payment_status',
    ];
    
    // FIX #22: same reasoning as Application::total_amount — a decimal:2
    // cast keeps this money field consistently scaled wherever it's read
    // (receipt.blade.php's number_format() and (float) cast, the dashboard
    // stat sums in DashboardController, payment.blade.php's summary panel)
    // instead of depending on each call site to format it correctly.
    protected $casts = [
        'total_amt' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}