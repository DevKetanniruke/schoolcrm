<?php

namespace App\Http\Controllers\Auth;

use App\Core\Services\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger)
    {
    }

    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        return view('auth.login', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            $this->auditLogger->log(
                action: 'user.failed_login',
                payload: ['email' => $request->input('email')]
            );
            throw $e;
        }

        $user = Auth::user();

        // Check if account is suspended or inactive
        if (isset($user->status) && in_array($user->status, ['inactive', 'suspended'])) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $this->auditLogger->log(
                action: 'user.login_blocked',
                payload: ['user_id' => $user->id, 'status' => $user->status]
            );

            throw ValidationException::withMessages([
                'email' => ['Your account has been ' . $user->status . '. Please contact the School Administrator.'],
            ]);
        }

        // Update last login timestamp
        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        $request->session()->regenerate();

        $this->auditLogger->log(
            action: 'user.login',
            payload: ['user_id' => $user->id, 'email' => $user->email]
        );

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        if ($userId) {
            $this->auditLogger->log(
                action: 'user.logout',
                payload: ['user_id' => $userId]
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Logged out successfully.');
    }
}
