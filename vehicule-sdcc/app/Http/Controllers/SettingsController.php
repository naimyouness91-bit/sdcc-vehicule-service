<?php

namespace App\Http\Controllers;

use App\Notifications\SystemUpdateNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Throwable;

class SettingsController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }
    public function show()
    {
        $user = Auth::user();

        return view('settings.show', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $this->notificationService->notifyUser(
            $user,
            new SystemUpdateNotification(
                title: 'Profil mis à jour',
                message: 'Les informations de votre profil (nom/email) ont été modifiées avec succès.',
                url: route('settings.show')
            ),
            async: true
        );

        return redirect()->route('settings.show')->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => $validated['password'],
        ]);

        $this->notificationService->notifyUser(
            $user,
            new SystemUpdateNotification(
                title: 'Mot de passe mis à jour',
                message: 'Le mot de passe de votre compte a été modifié avec succès.',
                url: route('settings.show')
            ),
            async: true
        );

        return redirect()->route('settings.show')->with('success', 'Mot de passe mis à jour avec succès.');
    }
}
