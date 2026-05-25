<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset request form (forgot password)
     */
    public function showResetForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link to user's email
     */
    public function sendResetLink()
    {
        $request = request();
        
        // Validate the email
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.exists' => 'Cet email n\'existe pas dans notre système.',
        ]);

        // Check if email is from @sdcc.ma domain
        if (!str_ends_with($validated['email'], '@sdcc.ma')) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Only email addresses with @sdcc.ma are allowed.',
                ]);
        }

        // Create a password reset token
        $token = Str::random(64);
        
        // Store the token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // In a production environment, you would send an email here
        // For now, we'll create a direct reset link
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $validated['email']]);

        // For development/testing, we can display the link
        return redirect('/login')->with('success', 
            'Un lien de réinitialisation a été généré. ' .
            'Utilisez ce lien pour réinitialiser votre mot de passe: ' . 
            $resetUrl
        );
    }

    /**
     * Show the password reset form
     */
    public function showReset($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset the user's password
     */
    public function resetPassword()
    {
        $request = request();
        
        // Validate the input
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.exists' => 'Cet email n\'existe pas dans notre système.',
            'token.required' => 'Le token est requis.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        // Check if email is from @sdcc.ma domain
        if (!str_ends_with($validated['email'], '@sdcc.ma')) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Only email addresses with @sdcc.ma are allowed.',
                ]);
        }

        // Find the password reset token
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$resetToken || !Hash::check($validated['token'], $resetToken->token)) {
            return back()
                ->withErrors([
                    'token' => 'Le lien de réinitialisation est invalide ou a expiré.',
                ]);
        }

        // Check if token hasn't expired (1 hour)
        if (Carbon::parse($resetToken->created_at)->addHour()->isPast()) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            return back()
                ->withErrors([
                    'token' => 'Le lien de réinitialisation a expiré. Veuillez demander un nouveau lien.',
                ]);
        }

        // Update user password
        $user = User::where('email', $validated['email'])->first();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect('/login')->with('success', 
            'Mot de passe réinitialisé avec succès ! Vous pouvez maintenant vous connecter.'
        );
    }
}
