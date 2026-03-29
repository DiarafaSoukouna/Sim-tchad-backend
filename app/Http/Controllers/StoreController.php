<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Store;

class StoreController extends Controller
{
    public function index() : JsonResponse
    {
        $stores = Store::all();
        return response()->json(['Message' => 'Stores récupérés avec succès', 'data' => $stores], 200);
    }
    public function show($id) : JsonResponse
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['Message' => 'Store non trouvé'], 404);
        }
        return response()->json(['Message' => 'Store récupéré avec succès', 'data' => $store], 200);
    }
    public function store(Request $request) : JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:255|unique:stores,code',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'actor_id'    => 'required|exists:actors,id',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'address'     => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'whatsapp'    => 'nullable|string|max:20',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('stores', 'public');
            $data['photo'] = $photoPath;
        }

        $store = Store::create($data);

        return response()->json([
            'Message' => 'Store créé avec succès',
            'data' => $store,
            'photo_url' => isset($data['photo']) ? asset('storage/' . $data['photo']) : null,
        ], 201);
    }
    public function update(Request $request, $id) : JsonResponse
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['Message' => 'Store non trouvé'], 404);
        }

        $validation = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'code'        => 'sometimes|required|string|max:255|unique:stores,code,' . $id,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'actor_id'    => 'sometimes|required|exists:actors,id',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'address'     => 'sometimes|required|string|max:255',
            'phone'       => 'sometimes|required|string|max:20',
            'whatsapp'    => 'nullable|string|max:20',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            if ($store->photo && Storage::disk('public')->exists($store->photo)) {
                Storage::disk('public')->delete($store->photo);
            }

            $photoPath = $request->file('photo')->store('stores', 'public');
            $data['photo'] = $photoPath;
        }

        $store->update($data);

        return response()->json([
            'Message' => 'Store mis à jour avec succès',
            'data' => $store,
            'photo_url' => $store->photo ? asset('storage/' . $store->photo) : null,
        ], 200);
    }
    public function destroy($id) : JsonResponse
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['Message' => 'Store non trouvé'], 404);    
        }
        $store->delete();
        return response()->json(['Message' => 'Store supprimé avec succès'], 200);
    }
}