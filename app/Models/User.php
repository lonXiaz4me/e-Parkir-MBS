<?php
// User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table      = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'full_name',
        'ic_no',
        'email',
        'phone_no',
        'password',
    ];

    // FIX #21: reset_token, reset_expires, otp, and otp_expires were
    // previously in $fillable. Nothing in the codebase actually
    // mass-assigns these — the real password-reset OTP flow
    // (UserController::sendOtp / verifyOtp / resetPassword) stores OTP
    // data entirely in Cache::put('pwd_reset_' . $email, [...]), never on
    // the User model. Leaving these in $fillable was a dormant risk: if
    // any future controller ever did User::create($request->all()) or
    // $user->update($request->all()) instead of a validated array (an easy
    // mistake), a malicious request could set its own otp or reset_token
    // directly on the row. They're removed from $fillable below; if these
    // columns are still genuinely needed later, set them via direct
    // attribute assignment ($user->otp = ...; $user->save();) rather than
    // mass assignment.
    //
    // $hidden and $casts below are left untouched — those are read-side
    // protections (serialization safety, datetime casting) and stay
    // useful regardless of whether the columns are ever written to.

    protected $hidden = [
        'password',
        'remember_token',
        'reset_token', // ADDED: hide sensitive reset token from serialisation
        'otp',         // ADDED: hide OTP from serialisation
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'reset_expires'     => 'datetime', // ADDED: cast for reset token expiry
        'otp_expires'       => 'datetime', // ADDED: cast for OTP expiry
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function applications()
    {
        return $this->hasMany(Application::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }
}