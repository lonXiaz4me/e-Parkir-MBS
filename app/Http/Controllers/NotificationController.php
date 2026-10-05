<?php
// NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Notification type groupings — kept in one place so the controller and
    // any future code stay in sync with what the frontend tabs expect.
    private const STATUS_TYPES  = ['app_submitted', 'app_approved', 'app_rejected'];
    private const PAYMENT_TYPES = ['payment_due', 'payment_received', 'payment_reminder'];

    // GET /notifications?tab=all|unread|status|payment
    public function index(Request $request)
    {
        // ── FIX #13: Filter server-side by tab instead of always returning a
        //    fixed "latest 20" window and letting the frontend filter that same
        //    slice client-side.
        //
        //    PREVIOUS BUG: If a user had 25 recent "app_submitted" notifications
        //    and 3 older "payment_received" ones, the take(20) window would
        //    only ever contain app_submitted entries. The "Bayaran" tab would
        //    then show empty — not because no payment notifications exist, but
        //    because they fell outside the arbitrary 20-item window before
        //    the client-side filter ever got a chance to see them.
        //
        //    FIX: Apply the type/unread filter in the query itself, before
        //    take(20), so each tab gets its own correctly-scoped latest 20.
        $tab = $request->query('tab', 'all');

        $query = Notification::where('user_id', auth()->id());

        match ($tab) {
            'unread'  => $query->unread(),
            'status'  => $query->whereIn('noti_type', self::STATUS_TYPES),
            'payment' => $query->whereIn('noti_type', self::PAYMENT_TYPES),
            default   => null, // 'all' — no extra filter
        };

        // FIX #26: Always sort newest-first, with a deterministic tiebreaker.
        //
        // PREVIOUS BEHAVIOUR: ->latest('created_at') alone orders by
        // created_at DESC, which is correct in the common case — but if two
        // notifications share the exact same created_at (same second, e.g.
        // several notifications fired back-to-back in one request, or a
        // seeded/backfilled dataset), the order between those tied rows is
        // left to whatever the database happens to return, which isn't
        // guaranteed to be insertion order.
        //
        // FIX: Add noti_id DESC as a secondary sort key. noti_id is an
        // auto-increment primary key, so it's monotonically increasing with
        // insertion order — this guarantees that among any rows with equal
        // timestamps, the one inserted later always appears above the one
        // inserted earlier, so "newest at the top" holds unconditionally.
        $notifications = $query
            ->orderByDesc('created_at')
            ->orderByDesc('noti_id')
            ->take(20)
            ->get()
            ->map(function ($n) {
                // Messages are stored as either:
                //   "msgKey:param1:param2"   — new structured format (translatable)
                //   "Full Malay sentence..."  — legacy format (display as-is)
                $raw    = $n->noti_message ?? '';
                $parts  = explode(':', $raw, 3);
                $knownKeys = [
                    'app_submitted', 'app_approved', 'app_rejected',
                    'payment_received', 'payment_due', 'payment_reminder',
                ];
                if (count($parts) >= 2 && in_array($parts[0], $knownKeys)) {
                    $msgKey    = $parts[0];
                    $msgParams = array_slice($parts, 1);
                } else {
                    // Legacy Malay message — pass through as raw text
                    $msgKey    = null;
                    $msgParams = [];
                }

                return [
                    'id'        => $n->noti_id,
                    'type'      => $n->noti_type,
                    'message'   => $raw,          // kept for fallback / legacy display
                    'msgKey'    => $msgKey,        // null for legacy rows
                    'msgParams' => $msgParams,     // array of substitution values
                    'unread'    => ! $n->is_read,
                    'time'      => $n->created_at?->diffForHumans() ?? '-',
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => Notification::where('user_id', auth()->id())->unread()->count(),
        ]);
    }

    // POST /notifications/{id}/read
    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // POST /notifications/read-all
    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}