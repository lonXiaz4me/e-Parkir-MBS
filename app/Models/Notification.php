<?php
// Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table      = 'notification';
    protected $primaryKey = 'noti_id';

    // CHANGED: Table only has created_at, no updated_at — disable auto-management
    public $timestamps = false;

    // ADDED: Manually declare created_at so Eloquent sets it on insert
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'user_id',
        'app_id',
        'noti_type',
        'noti_message',
        'is_read',      // CHANGED: was 'read_at' (datetime), now 'is_read' (boolean)
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'is_read'    => 'boolean', // CHANGED: was 'read_at' => 'datetime', now boolean cast
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'app_id', 'app_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopeUnread($query)
    {
        // CHANGED: was whereNull('read_at'), now filters by boolean false
        return $query->where('is_read', false);
    }

    // ── Static helper ────────────────────────────────────────────────────────
    // FIX: previously declared `: void` and didn't return the created row.
    // Callers that only fire-and-forget (ApplicationController::store(),
    // PaymentController::store(), etc.) never noticed, but DatabaseSeeder
    // needs the created Notification instance back so it can look up its
    // noti_id and backdate its created_at. Returning static::create(...)'s
    // result fixes that without changing behavior for existing callers that
    // ignore the return value.
    public static function send(int $userId, int $appId, string $type, string $message): static
    {
        return static::create([
            'user_id'      => $userId,
            'app_id'       => $appId,
            'noti_type'    => $type,
            'noti_message' => $message,
            'is_read'      => false, // CHANGED: was not needed with read_at; now explicitly set to unread
        ]);
    }

    // ── Instance helper ──────────────────────────────────────────────────────
    public function markAsRead(): void
    {
        // CHANGED: was update(['read_at' => now()]), now sets boolean flag
        $this->update(['is_read' => true]);
    }
}