<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtpCode;
use App\Models\Actor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class OtpCodeController extends Controller
{
    /**
     * 1. ENVOYER OTP
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'type' => 'required|in:email,phone'
        ]);

        // vérifier utilisateur
        $user = $request->type === 'email'
            ? Actor::where('email', $request->identifier)->first()
            : Actor::where('phone', $request->identifier)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable'
            ], 404);
        }

        // générer code
        $code = rand(100000, 999999);

        OtpCode::updateOrCreate(
            ['identifier' => $request->identifier],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(5),
                'verified' => false
            ]
        );

        // envoyer OTP
        if ($request->type === 'email') {
            app(MessagesController::class) ->SendMailTo(new Request([
                'email' => $request->identifier,
                'subject' => "Code de vérification",
                'message' => "Votre code OTP est : $code"
            ]));
        }

        if ($request->type === 'phone') {
            app(MessagesController::class)->whatsapp(new Request([
                'phone_number' => $request->identifier,
                'message' => "Votre code OTP est : $code"
            ]));
        }

        return response()->json([
            'message' => 'OTP envoyé avec succès'
        ]);
    }

    /**
     * 2. VÉRIFIER OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'code' => 'required'
        ]);

        $otp = OtpCode::where('identifier', $request->identifier)
            ->where('code', $request->code)
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Code invalide'], 400);
        }

        if (Carbon::now()->isAfter($otp->expires_at)) {
            return response()->json(['message' => 'Code expiré'], 400);
        }

        $otp->update([
            'verified' => true
        ]);

        return response()->json([
            'message' => 'OTP vérifié avec succès'
        ]);
    }

    /**
     * 3. RESET PASSWORD
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password' => 'required|min:6'
        ]);

        $otp = OtpCode::where('identifier', $request->identifier)
            ->where('verified', true)
            ->first();

        if (!$otp) {
            return response()->json([
                'message' => 'OTP non vérifié'
            ], 400);
        }

        $user = Actor::where('email', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable'
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // supprimer OTP après usage
        $otp->delete();

        return response()->json([
            'message' => 'Mot de passe mis à jour avec succès'
        ]);
    }
}