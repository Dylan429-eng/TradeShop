<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        try {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('status', 'verification-link-sent');
        } catch (Exception $e) {
            Log::error('Erreur envoi email de vérification: ' . $e->getMessage());
            
            // Fallback : utiliser le driver log
            config(['mail.default' => 'log']);
            
            try {
                $request->user()->sendEmailVerificationNotification();
                return back()->with('status', 'verification-link-sent-log');
            } catch (Exception $fallbackException) {
                Log::error('Erreur fallback email: ' . $fallbackException->getMessage());
                return back()->with('error', 'Impossible d\'envoyer l\'email de vérification. Veuillez réessayer plus tard.');
            }
        }
    }
}
