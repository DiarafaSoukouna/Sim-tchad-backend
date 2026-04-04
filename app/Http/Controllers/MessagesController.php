<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Services\WhatsAppService;


class MessagesController extends Controller
{

    public function SendMailTo(Request $request)

    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        // Exemple de données dynamiques
        $recipient = $request->input('email');
        $subject = $request->input('subject');
        $message = $request->input('message');

        // Envoi du mail
        Mail::to($recipient)->send(new SendMail($subject, $message));

        return "Mail envoyé avec succès à $recipient !";
    }
   

    public function Whatsapp(Request $request) : JsonResponse
{
    $validation = Validator::make($request->all(), [
        'phone_number' => 'required|string',
        'message' => 'required|string',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'errors' => $validation->errors()
        ], 422);
    }

    $whatsapp = new WhatsAppService();

    $phone = $request->input('phone_number');
    $message = $request->input('message');

    $phone = $request->input('phone_number');

// nettoyer le numéro
$phone = preg_replace('/[^0-9]/', '', $phone);

// ajouter suffix WhatsApp
$phoneFormatted = $phone . '@c.us';

    try {
        $whatsapp->sendMessage($phoneFormatted, $message);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur envoi WhatsApp',
            'details' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'message' => 'Message envoyé avec succès'
    ], 200);
}
}

