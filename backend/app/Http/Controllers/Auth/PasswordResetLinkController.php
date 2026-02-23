<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->with('status', 'If your email is registered, we have sent you a code.');
        }

        $table = config('auth.passwords.users.table', 'password_reset_tokens');
        $throttleSeconds = (int) config('auth.passwords.users.throttle', 60);

        if ($throttleSeconds > 0) {
            $recent = DB::table($table)
                ->where('email', $request->email)
                ->where('created_at', '>', now()->subSeconds($throttleSeconds))
                ->first();

            if ($recent) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'Please wait a minute before requesting another code.',
                    ]);
            }
        }

        $otp = (string) random_int(100000, 999999);

        DB::table($table)->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp,
                'created_at' => now(),
            ]
        );

        return redirect()
            ->route('password.reset.otp', ['email' => $request->email])
            ->with('status', 'A password reset code has been generated for this email.');
    }
}
