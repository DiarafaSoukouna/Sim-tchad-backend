<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sector;
use Illuminate\Support\Facades\Validator;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = Sector::all();
        return response()->json(['Message' => 'Sectors retrouvés avec succès', 'data' => $sectors], 200);
    }
    public function show($id)
    {
        $sector = Sector::find($id);
        if ($sector) {
            return response()->json(['Message' => 'Secteur retrouvé avec succès', 'data' => $sector], 200);
        } else {
            return response()->json(['Message' => 'Secteur non trouvé'], 404);
        }
    }
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:sectors,code',
            'description' => 'nullable|string',

        ]);
        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors' => $validation->errors(),
            ], 422);
        }
        $sector = Sector::create($request->all());

        return response()->json(['Message' => 'Secteur créé avec succès', 'data' => $sector], 201);
    }
    public function update(Request $request, $id)
    {
        $sector = Sector::find($id);
        if (!$sector) {
            return response()->json(['Message' => 'Secteur non trouvé'], 404);
        }

        $validation = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:50|unique:sectors,code,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors' => $validation->errors(),
            ], 422);
        }

        $sector->update($request->all());

        return response()->json(['Message' => 'Secteur mis à jour avec succès', 'data' => $sector], 200);
    }
    public function destroy($id)
    {
        $sector = Sector::find($id);
        if (!$sector) {
            return response()->json(['Message' => 'Secteur non trouvé'], 404);
        }

        $sector->delete();

        return response()->json(['Message' => 'Secteur supprimé avec succès'], 200);
    }
}
