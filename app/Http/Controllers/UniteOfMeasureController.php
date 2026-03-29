<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UniteOfMeasure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UniteOfMeasureController extends Controller
{
    public function index() : JsonResponse
    {
        $unites = UniteOfMeasure::all();
        return response()->json(['Message' => 'Unites de mesure recuperees avec succes', 'data' => $unites], 200);
    }
    public function show($id): JsonResponse
    {
        $unite = UniteOfMeasure::find($id);
        if (!$unite) {
            return response()->json(['Message' => 'Aucune unite de mesure trouvee avec cet ID'], 404);
        }
        return response()->json(['Message' => 'Unite de mesure recuepee avec succes', 'data' => $unite], 200);
    }
    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:500|unique:unite_of_measures,code',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validation->errors(),
            ], 422);
        }

        $unite = UniteOfMeasure::create($request->all());
        return response()->json(['Message' => 'Unite de mesure creee avec succes', 'data' => $unite], 201);
    }
    public function update(Request $request, $id): JsonResponse
    {
        $unite = UniteOfMeasure::find($id);
        if (!$unite) {
            return response()->json(['Message' => 'Aucune unite de mesure trouvee avec cet ID'], 404);
        }

        $validation = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:500|unique:unite_of_measures,code,' . $id,
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validation->errors(),
            ], 422);
        }

        $unite->update($request->all());
        return response()->json(['Message' => 'Unite de mesure mise a jour avec succes', 'data' => $unite], 200);
    }
    public function destroy($id): JsonResponse
    {
        $unite = UniteOfMeasure::find($id);
        if (!$unite) {
            return response()->json(['Message' => 'Aucune unite de mesure trouvee avec cet ID'], 404);
        }   
        $unite->delete();
        return response()->json(['Message' => 'Unite de mesure supprimee avec succes'], 200);
    }
}
