<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form page.
     */
    public function edit()
    {
        return view('auth.profile'); // Adjust view directory path as necessary
    }

    /**
     * Update the generic profile details (Phone & Email).
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // FIX: The users table's primary key column is 'user_id', not 'id'
        // (see User::$primaryKey and the create_users_table migration).
        // The unique rule's "ignore" clause needs the actual PK column name
        // passed explicitly as the 4th segment — otherwise Laravel defaults
        // to looking for a column called 'id', which doesn't exist on this
        // table and throws "Unknown column 'id' in 'where clause'".
        // Also use $user->user_id instead of $user->id, since the model has
        // no 'id' attribute (that's why the previous query had an empty
        // value after "id <>").
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->user_id . ',user_id'],
            // FIX: was missing a unique check. The users table has a unique
            // constraint on phone_no, so trying to change to a number already
            // used by another account previously skipped Laravel validation
            // entirely and hit the database constraint instead — causing an
            // uncaught QueryException (500 error) rather than a friendly
            // red validation message under the field.
            'phone_no' => ['required', 'string', 'min:9', 'max:15', 'unique:users,phone_no,' . $user->user_id . ',user_id'],
            // FIX #20: Require re-entering the current password before email
            // or phone can change. Email in particular is the destination
            // for the OTP password-reset flow (UserController::sendOtp), so
            // without this check, anyone who gets hold of an active session
            // (a stolen cookie, an unlocked unattended device) could quietly
            // redirect password resets to an email they control — turning a
            // session hijack into permanent account takeover. Requiring the
            // password again closes that gap the same way changing the
            // password itself already requires it.
            'current_password' => ['required', 'string'],
        ], [
            'email.unique' => 'Alamat emel ini telah digunakan oleh akaun lain.',
            'email.required' => 'Ruangan emel wajib diisi.',
            'phone_no.required' => 'Ruangan no. telefon wajib diisi.',
            'phone_no.unique' => 'No. telefon ini telah digunakan oleh akaun lain.',
            'current_password.required' => 'Sila isi kata laluan semasa untuk mengesahkan perubahan ini.',
        ]);

        // FIX #20: Verify the password BEFORE writing any changes — same
        // check used by updatePassword() below.
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors([
                'current_password' => 'Kata laluan semasa yang anda masukkan adalah salah.'
            ])->withInput($request->except('current_password'));
        }

        $user->update([
            'email'    => $validated['email'],
            'phone_no' => $validated['phone_no'],
        ]);

        return redirect()->route('profile.edit')->with('success', 'Maklumat profil anda berjaya dikemaskini.');
    }

    /**
     * Cryptographically check current credentials and update user's account password.
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            // FIX: tightened to match the register page's password policy
            // (mixedCase + symbols added) so the strength badges shown on the
            // profile page — 8+ Aksara, Huruf Besar, Nombor, Simbol — reflect
            // a rule the backend actually enforces.
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'current_password.required' => 'Sila isi kata laluan semasa anda.',
            'password.required' => 'Sila isi kata laluan baru.',
            'password.confirmed' => 'Pengesahan kata laluan baru tidak sepadan.',
            'password.min' => 'Kata laluan baru mestilah sekurang-kurangnya 8 aksara panjang dan mengandungi kombinasi huruf besar, huruf kecil, nombor dan simbol.',
        ]);

        // Verify old password hash integrity matches the database record
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors([
                'current_password' => 'Kata laluan semasa yang anda masukkan adalah salah.'
            ]);
        }

        // FIX #19: Previously this only updated the password hash, leaving
        // any other active session (a different browser, another device, or
        // a session hijacked via a stolen cookie) fully logged in and able
        // to keep using the account under the OLD password's session — the
        // very scenario a user changing their password is usually trying to
        // protect against. Auth::logoutOtherDevices() re-derives the
        // session hash from the NEW password and invalidates every other
        // session's remember/auth cookies, while leaving this current
        // session (the one performing the change) intact.
        //
        // FIX #11 still applies here: the User model casts 'password' =>
        // 'hashed', which hashes automatically on save. We deliberately do
        // NOT call Hash::make() ourselves (that would double-hash) — same
        // pattern as UserController::resetPassword(). logoutOtherDevices()
        // needs the plaintext new password to do its own internal re-hash
        // for session comparison, so we pass $validated['password'] as-is.
        Auth::logoutOtherDevices($validated['password']);

        $user->update([
            'password' => $validated['password'],
        ]);

        return redirect()->route('profile.edit')->with('success', 'Kata laluan anda berjaya ditukar. Sesi log masuk pada peranti lain telah ditamatkan.');
    }
}