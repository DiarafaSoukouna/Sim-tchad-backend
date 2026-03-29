<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\ActorType;

class ActorTypeController extends Controller
{
    public function store(Request $request) : jsonResponse
    {
        $validation = Validator::make($request->all(), [
            'code'          => 'required|string|unique:actors,code',
            'name'     => 'required|string|max:255',
            'description'    => 'nullable|string'
        ]);
    if ($validation->fails()) {
      return response()->json([
        'message' => 'Erreur de validation.',
        'errors' => $validation->errors(),
      ], 422);
    };
    $actor_types= ActorType::create($request->all());

    return response()->json(['Message'=> 'Type acteur crée avec succès', 'data' => $actor_types ], 201);
    
    }
    public function index(): JsonResponse
    {
        $actor_types = ActorType::all();
        return response()->json([
            'Message' => 'Liste des types acteurs recupérés avec succés', 'data'=> $actor_types
        ], 201);
    }
    public function show($id) : JsonResponse
    {
        $actor_type = ActorType::find($id);

        if (!$actor_type) {
            return response()->json([
                'Message'=>'Type acteur non trouvé'
            ], 404);
        };
        return response()->json([
            'Message'=> 'Type acteur trouvé avec succès',
            'data'=>$actor_type
        ], 200);
    }

    public function update(Request $request, $id): JsonResponse
{
    $actor_type = ActorType::find($id);

    if (!$actor_type) {
        return response()->json([
            'message' => 'Type acteur non trouvé'
        ], 404);
    }

    $validation = Validator::make($request->all(), [
        'code'        => 'required|string|unique:actor_types,code,' . $id,
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string'
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation.',
            'errors' => $validation->errors(),
        ], 422);
    }

    $actor_type->update($request->all());

    return response()->json([
        'message' => 'Type acteur mis à jour avec succès',
        'data' => $actor_type
    ], 200);
}
public function destroy($id): JsonResponse
{
    $actor_type = ActorType::find($id);

    if (!$actor_type) {
        return response()->json([
            'message' => 'Type acteur non trouvé'
        ], 404);
    }

    $actor_type->delete();

    return response()->json([
        'message' => 'Type acteur supprimé avec succès'
    ], 200);
}

    
}
