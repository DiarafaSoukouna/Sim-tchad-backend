<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AlertController extends Controller
{
    public function index() : JsonResponse
    {
        $alerts = Alert::all();
        return response()->json([
            'Message' => 'Liste des alertes récupérées avec succès',
            'data' => $alerts
        ], 200);
    }
    public function show($id) : JsonResponse
    {
        $alert = Alert::find($id);
        if (!$alert) {
            return response()->json([
                'Message' => 'Alerte non trouvée'
            ], 404);
        }
        return response()->json([
            'Message' => 'Alerte récupérée avec succès',
            'data' => $alert
        ], 200);
    }
public function store(Request $request) : JsonResponse
{
    $validator = Validator::make($request->all(), [
        'actor_id' => 'required|exists:actors,id',
        'message' => 'required|string',
        'type' => 'required|in:alertes,conseils',
        'media_type' => 'nullable|in:audio,video,photos',
        'media_url' => 'nullable|file|mimes:jpeg,jpg,png,mp4,mp3,wav|max:20480'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Erreur de validation',
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $validator->validated();

    // ⚠️ FIX 1: sécuriser media_type automatiquement si fichier envoyé
    if ($request->hasFile('media_url')) {

        $file = $request->file('media_url');

        $path = $file->store('alerts', 'public');
        $data['media_url'] = $path;

        // auto-détection du type si non fourni
        if (empty($data['media_type'])) {
            $mime = $file->getMimeType();

            if (str_contains($mime, 'image')) {
                $data['media_type'] = 'photos';
            } elseif (str_contains($mime, 'video')) {
                $data['media_type'] = 'video';
            } elseif (str_contains($mime, 'audio')) {
                $data['media_type'] = 'audio';
            }
        }
    }

    // ⚠️ FIX 2: éviter erreur si media_type est null mais fichier existe
    if (!$request->hasFile('media_url')) {
        $data['media_type'] = null;
    }

    $alert = Alert::create($data);

    return response()->json([
        'message' => 'Alerte créée avec succès',
        'data' => $alert
    ], 201);
}
public function update(Request $request, $id) : JsonResponse
{
    $alert = Alert::find($id);

    if (!$alert) {
        return response()->json([
            'message' => 'Alerte non trouvée'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'actor_id' => 'sometimes|required|exists:actors,id',
        'message' => 'sometimes|required|string',
        'type' => 'sometimes|required|in:alertes,conseils',
        'media_type' => 'nullable|in:audio,video,photos',
        'media_url' => 'nullable|file|mimes:jpeg,jpg,png,mp4,mp3,wav|max:20480'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Erreur de validation',
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $validator->validated();

    // Handle file upload replacement
    if ($request->hasFile('media_url')) {

        // delete old file if exists
        if ($alert->media_url && Storage::disk('public')->exists($alert->media_url)) {
            Storage::disk('public')->delete($alert->media_url);
        }

        $file = $request->file('media_url');
        $path = $file->store('alerts', 'public');
        $data['media_url'] = $path;

        // auto-detect media type if not provided
        if (empty($data['media_type'])) {
            $mime = $file->getMimeType();

            if (str_contains($mime, 'image')) {
                $data['media_type'] = 'photos';
            } elseif (str_contains($mime, 'video')) {
                $data['media_type'] = 'video';
            } elseif (str_contains($mime, 'audio')) {
                $data['media_type'] = 'audio';
            }
        }
    }

    $alert->update($data);

    return response()->json([
        'message' => 'Alerte mise à jour avec succès',
        'data' => $alert
    ], 200);
}
public function destroy($id) : JsonResponse
{
    $alert = Alert::find($id);

    if (!$alert) {
        return response()->json([
            'message' => 'Alerte non trouvée'
        ], 404);
    }

    // delete associated media file if exists
    if ($alert->media_url && Storage::disk('public')->exists($alert->media_url)) {
        Storage::disk('public')->delete($alert->media_url);
    }

    $alert->delete();

    return response()->json([
        'message' => 'Alerte supprimée avec succès'
    ], 200);
}
}
