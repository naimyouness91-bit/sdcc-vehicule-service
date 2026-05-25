<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\PasswordResetController;

// Custom validation rule for @sdcc.ma domain
$validateSdccEmail = function ($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) && str_ends_with($email, '@sdcc.ma');
};

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function () {
        // In testing environment, allow 419 for missing CSRF if explicitly tested
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Validate email domain
        if (!str_ends_with($credentials['email'], '@sdcc.ma')) {
            return back()
                ->withInput(request()->only('email'))
                ->withErrors([
                    'email' => 'Only email addresses with @sdcc.ma are allowed.',
                ]);
        }

        \Illuminate\Support\Facades\Log::debug('Login attempt', ['email' => $credentials['email'], 'testing' => app()->environment('testing')]);

        $loggedIn = false;
        if (app()->environment('testing')) {
            // In testing, only auto-login if the provided password matches the stored hash
            $userByEmail = User::where('email', $credentials['email'])->first();
            if ($userByEmail && \Illuminate\Support\Facades\Hash::check($credentials['password'], $userByEmail->password)) {
                Auth::login($userByEmail);
                $loggedIn = true;
            }
        }

        if (!$loggedIn) {
            $loggedIn = Auth::attempt($credentials);
        }

        \Illuminate\Support\Facades\Log::debug('Login result', ['loggedIn' => $loggedIn, 'user_id' => Auth::id()]);
        if ($loggedIn) {
            $user = Auth::user();
            
            // Check if user account is active
            if (!$user->is_active) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                
                return back()
                    ->withInput(request()->only('email'))
                    ->withErrors([
                        'email' => 'Votre compte a été désactivé. Contactez votre administrateur.',
                    ]);
            }
            
            request()->session()->regenerate();
            // Redirect all authenticated users to dashboard
            return redirect()->route('dashboard')->with('success', 'Connexion réussie! Bienvenue.');
        }

        return back()
            ->withInput(request()->only('email'))
            ->withErrors([
                'email' => 'Les informations d\'identification fournies sont incorrectes.',
            ]);
    })->middleware('throttle:5,1')->name('login.post');

    Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');

    Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

    Route::get('/password/reset/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');

    Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.update');

    // Registration routes removed: system uses login-only authentication
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login')->with('success', 'Déconnexion réussie. À bientôt!');
    })->name('logout');
});

