<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductionAreaController extends Controller
{
    public function index() : JsonResponse
    {
        $production_areas = ProductionArea::all();
        return response()->json(['Message' => 'Production areas recuperees avec succes', 'data' => $production_areas], 200);
    }
public function show($id) : JsonResponse
    {
        $production_area = ProductionArea::find($id);
        if (!$production_area) {
            return response()->json(['Message' => 'Aucune zone de production trouvee avec cet ID'], 404);
        }
        return response()->json(['Message' => 'Zone de production recuepee avec succes', 'data' => $production_area], 200);
    }
    public function store(Request $request) : JsonResponse
    {
         $validation = Validator::make($request->all(), [
    'name'       => 'required|string|max:255',
    'code'       => 'required|string|max:100|unique:production_areas,code',
    'actor_id'   => 'nullable|exists:actors,id',
    'latitude'   => 'nullable|numeric|between:-90,90',
    'longitude'  => 'nullable|numeric|between:-180,180',
    'address'    => 'nullable|string|max:255',
    'photo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    'updated_by' => 'nullable|string|max:255',
]);

if ($validation->fails()) {
    return response()->json([
        'message' => 'Erreur de validation',
        'errors'  => $validation->errors(),
    ], 422);
}

  $data = $validation->validated();

  if ($request->hasFile('photo')) {
      $data['photo'] = $request->file('photo')->store('production_areas', 'public');
  }

  $production_area = ProductionArea::create($data);
        return response()->json(['Message' => 'Zone de production creee avec succes', 'data' => $production_area], 201);
    }
    public function update(Request $request, $id) : JsonResponse
    {
         $production_area = ProductionArea::find($id);
        if (!$production_area) {
            return response()->json(['Message' => 'Aucune zone de production trouvee avec cet ID'], 404);
        }
        $validation = Validator::make($request->all(), [
    'name'       => 'sometimes|required|string|max:255',
    'code'       => 'sometimes|required|string|max:100|unique:production_areas,code,' . $id,
    'actor_id'   => 'nullable|exists:actors,id',
    'latitude'   => 'nullable|numeric|between:-90,90',      
    'longitude'  => 'nullable|numeric|between:-180,180',
    'address'    => 'nullable|string|max:255',
    'photo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    'updated_by' => 'nullable|string|max:255',
]);

if ($validation->fails()) {
    return response()->json([
        'message' => 'Erreur de validation',
        'errors'  => $validation->errors(),
    ], 422);
}      
  $data = $validation->validated();

  if ($request->hasFile('photo')) {
      $data['photo'] = $request->file('photo')->store('production_areas', 'public');
  }

  $production_area->update($data);
        return response()->json(['Message' => 'Zone de production mise a jour avec succes', 'data' => $production_area], 200);
    }
    public function destroy($id) : JsonResponse
    {
        $production_area = ProductionArea::find($id);
        if (!$production_area) {
            return response()->json(['Message' => 'Aucune zone de production trouvee avec cet ID'], 404);
        }
        $production_area->delete();
        return response()->json(['Message' => 'Zone de production supprimee avec succes'], 200);
    }
}
