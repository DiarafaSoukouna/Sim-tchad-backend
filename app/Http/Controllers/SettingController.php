<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
   public function index() : JsonResponse
   {
       $settings = Setting::all();
       return response()->json(['Message' => 'Settings recupérés avec succès', 'data' => $settings], 200);
   }
   public function show($id) : JsonResponse
   {
       $setting = Setting::find($id);
       if (!$setting) {
           return response()->json(['Message' => 'Setting non trouvé'], 404);
       }
       return response()->json(['Message' => 'Setting récupéré avec succès', 'data' => $setting], 200);
   }
   public function store(Request $request) : JsonResponse
   {
    $validation = Validator::make($request->all(), [
    'organization_acronym'       => 'required|string|max:100',
    'organization_name'          => 'required|string|max:255',
    'system_acronym'             => 'required|string|max:100',
    'system_name'                => 'required|string|max:255',
    'system_description'         => 'nullable|string',
    'system_slogan'              => 'nullable|string|max:255',
    'system_logo'                => 'nullable|string|max:255',
    'organization_address'       => 'nullable|string|max:255',
    'organization_email'         => 'nullable|email|max:255',
    'organization_phone'         => 'nullable|string|max:20',
    'organization_whatsapp'      => 'nullable|string|max:20',
    'organization_level_code'    => 'nullable|string|max:100',
    'organization_locality'      => 'nullable|string|max:255',
]);

if ($validation->fails()) {
    return response()->json([
        'message' => 'Erreur de validation',
        'errors'  => $validation->errors(),
    ], 422);
}
    $setting = Setting::create($request->all());
    return response()->json(['Message' => 'Setting créé avec succès', 'data' => $setting], 201);
   }
   public function update(Request $request, $id) : JsonResponse
   {
       $setting = Setting::find($id);
       if (!$setting) {
           return response()->json(['Message' => 'Setting non trouvé'], 404);
       }

       $validation = Validator::make($request->all(), [
        'organization_acronym'       => 'sometimes|required|string|max:100',
        'organization_name'          => 'sometimes|required|string|max:255',
        'system_acronym'             => 'sometimes|required|string|max:100',
        'system_name'                => 'sometimes|required|string|max:255',
        'system_description'         => 'nullable|string',
        'system_slogan'              => 'nullable|string|max:255',
        'system_logo'                => 'nullable|string|max:255',
        'organization_address'       => 'nullable|string|max:255',
        'organization_email'         => 'nullable|email|max:255',
        'organization_phone'         => 'nullable|string|max:20',
        'organization_whatsapp'      => 'nullable|string|max:20',
        'organization_level_code'    => 'nullable|string|max:100',
        'organization_locality'      => 'nullable|string|max:255',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation',
            'errors'  => $validation->errors(),
        ], 422);
    }

       $setting->update($request->all());
       return response()->json(['Message' => 'Setting mis à jour avec succès', 'data' => $setting], 200);
   }
   public function destroy($id) : JsonResponse
   {
       $setting = Setting::find($id);
       if (!$setting) {
           return response()->json(['Message' => 'Setting non trouvé'], 404);
       }
       $setting->delete();
       return response()->json(['Message' => 'Setting supprimé avec succès'], 200);
   }
}
