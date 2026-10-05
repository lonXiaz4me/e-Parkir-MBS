<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Rules\MalaysianIcNumber; // FIX #12: validates real Malaysian IC format

class UserController extends Controller
{
    // FIX #15: Max wrong OTP guesses allowed per OTP session before it is
    // invalidated entirely, regardless of which IP the guesses come from.
    private const MAX_OTP_ATTEMPTS = 5;

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt login with the 'remember' boolean
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Send them where they intended to go, or the dashboard as fallback
            return redirect('dashboard')->with('success', 'Login Masuk Berjaya!');
        }

        // Always return the email input so they don't have to re-type it
        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function index()
    {
        return view('guest.register');
    }

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'full_name' => 'required|string|max:255',
            // FIX #12: was 'required|string|max:14|unique:users,ic_no' — accepted
            // any string up to 14 chars, including garbage like "abc". Now also
            // validates against the real YYMMDD-PB-#### Malaysian IC format.
            'ic_no'     => ['required', 'string', 'max:14', 'unique:users,ic_no', new MalaysianIcNumber],
            'email'     => 'required|email|unique:users,email',
            'phone_no'  => 'required|string|max:15|unique:users,phone_no', // FIX #16: was missing unique check, inconsistent with email/ic_no which both enforce uniqueness
            'password'  => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()     // Requires at least one letter
                    ->mixedCase()   // Requires at least one uppercase and one lowercase letter
                    ->numbers()     // Requires at least one number
                    ->symbols(),    // Requires at least one symbol
            ],
        ]);

        // 2. Create User
        //
        // FIX #11: The User model casts 'password' => 'hashed', which means
        // Eloquent AUTOMATICALLY hashes this attribute when it's set. Calling
        // Hash::make() here manually was double-hashing the password.
        $user = User::create([
            'full_name' => $request->full_name,
            'ic_no'     => $request->ic_no,
            'email'     => $request->email,
            'phone_no'  => $request->phone_no,
            'password'  => $request->password,
        ]);

        // 3. Auto-login after registration (fix #1)
        Auth::login($user);

        // 4. Redirect to dashboard
        return redirect('/dashboard')->with('success', 'Akaun berjaya dicipta!');
    }

    public function showLinkRequestForm()
    {
        return view('guest.forgot-password');
    }

    /**
     * STEP 1: Generate & Send OTP via Cache
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $email = $request->email;

        // Generate a 6-digit OTP code
        $otp = random_int(100000, 999999);

        // FIX #15: Initialize 'attempts' => 0 alongside the existing payload.
        // This counter tracks wrong guesses for THIS specific OTP, independent
        // of which IP address makes the request — closing the gap where an
        // attacker could distribute brute-force attempts across many IPs to
        // bypass the per-IP throttle middleware added in fix #2.
        //
        // Note: This Hash::make() call on the OTP itself is correct and should
        // NOT be changed — the OTP is stored on Cache, not on the User model,
        // so the 'hashed' cast does not apply here.
        Cache::put('pwd_reset_' . $email, [
            'otp'      => Hash::make($otp),
            'verified' => false,
            'attempts' => 0, // ← NEW: tracks wrong guesses for this OTP session
        ], now()->addMinutes(10));

        // Send Email
        Mail::raw("Kod OTP penetapan semula kata laluan anda adalah: {$otp}. Kod ini sah selama 10 minit.", function ($message) use ($email) {
            $message->to($email)->subject('Kod OTP Tukar Kata Laluan — MBS');
        });

        return redirect()->back()
            ->with('step', 2)
            ->with('email', $email)
            ->with('status', 'Kod OTP telah berjaya dihantar ke e-mel anda.');
    }

    /**
     * STEP 2: Verify OTP from Cache
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|numeric',
        ]);

        $email     = $request->email;
        $cacheData = Cache::get('pwd_reset_' . $email);

        // No OTP session exists at all (expired or never requested)
        if (!$cacheData) {
            return redirect()->back()
                ->with('step', 2)
                ->with('email', $email)
                ->withErrors(['otp' => 'Kod OTP tidak sah atau telah tamat tempoh.']);
        }

        // FIX #15: Reject immediately if the attempt limit has already been
        // reached — this check runs BEFORE comparing the OTP, so even the
        // attempt that would have been correct is rejected once the limit is
        // hit. This is intentional: if an attacker has already burned through
        // 5 guesses on this OTP, the OTP itself should be considered
        // compromised/exhausted regardless of timing.
        if ($cacheData['attempts'] >= self::MAX_OTP_ATTEMPTS) {
            Cache::forget('pwd_reset_' . $email); // force the user to request a fresh OTP
            return redirect()->route('password.otp.request')
                ->withErrors(['email' => 'Terlalu banyak percubaan kod OTP yang salah. Sila mohon kod OTP baru.']);
        }

        // Validate token signature
        if (!Hash::check($request->otp, $cacheData['otp'])) {
            // FIX #15: Increment the attempt counter on every wrong guess,
            // persisted back to Cache so it survives across requests/IPs.
            $cacheData['attempts']++;
            Cache::put('pwd_reset_' . $email, $cacheData, now()->addMinutes(10));

            $remaining = self::MAX_OTP_ATTEMPTS - $cacheData['attempts'];

            return redirect()->back()
                ->with('step', 2)
                ->with('email', $email)
                ->withErrors(['otp' => "Kod OTP tidak sah. Baki percubaan: {$remaining}."]);
        }

        // Mark as verified but preserve the 10-minute validity window
        $cacheData['verified'] = true;
        Cache::put('pwd_reset_' . $email, $cacheData, now()->addMinutes(10));

        return redirect()->back()
            ->with('step', 3)
            ->with('email', $email)
            ->with('status', 'OTP berjaya disahkan. Sila tetapkan kata laluan baru anda.');
    }

    /**
     * STEP 3: Validate Verification State & Update Password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email     = $request->email;
        $cacheData = Cache::get('pwd_reset_' . $email);

        // Security check: Ensure they actually verified the token at step 2
        if (!$cacheData || !$cacheData['verified']) {
            return redirect()->route('password.otp.request')
                ->withErrors(['email' => 'Sesi keselamatan telah tamat tempoh. Sila mula semula.']);
        }

        // Apply new password changes to user profile
        //
        // FIX #11: The 'hashed' cast on User::password handles hashing
        // automatically — do not call Hash::make() here.
        $user           = User::where('email', $email)->first();
        $user->password = $request->password;
        $user->save();

        // Evict key immediately from cache database for security hygiene
        Cache::forget('pwd_reset_' . $email);

        return redirect()->route('welcome')->with('status', 'Kata laluan anda berjaya dikemaskini. Sila log masuk.');
    }
}