<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function index() : JsonResponse
    {
        $languages = Language::all();
        return response()->json(['Message' => 'Languages recuperés avec succes', 'data' => $languages], 200);
    }
    public function show($id) : JsonResponse
    {
        $language = Language::find($id);
        if (!$language) {
            return response()->json(['Message' => 'Langue non trouvée'], 404);
        }
        return response()->json(['Message' => 'Langue recuperée avec succes', 'data' => $language], 200);
    }
    public function store(Request $request) : JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'locale'        => 'required|string|max:100|unique:languages,locale',
            'is_default'  => 'nullable|boolean',
        ]);
   

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $language = Language::create($validation->validated());

        return response()->json(['Message' => 'Langue créée avec succes', 'data' => $language], 201);
    }
    public function update(Request $request, $id) : JsonResponse
    {
        $language = Language::find($id);
        if (!$language) {
            return response()->json(['Message' => 'Langue non trouvée'], 404);
        }

        $validation = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'locale'        => 'sometimes|required|string|max:100|unique:languages,locale,'.$id,
            'is_default'  => 'nullable|boolean',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $language->update($request->all());

        return response()->json(['Message' => 'Langue mise à jour avec succes', 'data' => $language], 200);
    }
    public function destroy($id) : JsonResponse
    {
        $language = Language::find($id);
        if (!$language) {
            return response()->json(['Message' => 'Langue non trouvée'], 404);  
        }

        $language->delete();

        return response()->json(['Message' => 'Langue supprimée avec succes'], 200);
    }
}