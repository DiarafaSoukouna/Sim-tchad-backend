<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Speculation;


class SpeculationController extends Controller
{
    public function index()
    {
        $speculations = Speculation::all();
        return response()->json(['Message' => 'Speculations récupérées avec succès', 'data' => $speculations], 200);
    }
    public function show($id)
    {
        $speculation = Speculation::find($id);
        if (!$speculation) {
            return response()->json(['Message' => 'Spéculation non trouvée'], 404);
        }
        return response()->json(['Message' => 'Spéculation récupérée avec succès', 'data' => $speculation], 200);
    }
    public function store(Request $request)
    {
    $validation = Validator::make($request->all(), [
    'name'        => 'required|string|max:255',
    'description' => 'nullable|string',
    'code'        => 'required|string|max:255|unique:speculations,code',
    'photo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    'category_id' => 'required|exists:categories,id'
]);

if ($validation->fails()) {
    return response()->json([
        'message' => 'Erreur de validation',
        'errors'  => $validation->errors(),
    ], 422);
}

    $data = $request->except(['photo']);

    if ($request->hasFile('photo')) {
        $data['photo'] = $request->file('photo')->store('speculations', 'public');
    }

    $speculation = Speculation::create($data);

    return response()->json(['Message' => 'Spéculation créée avec succès', 'data' => $speculation], 201);

    }
    public function update(Request $request, $id)
    {
        $speculation = Speculation::find($id);
        if (!$speculation) {
            return response()->json(['Message' => 'Spéculation non trouvée'], 404);
        }

        $validation = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'code'        => 'sometimes|required|string|max:255|unique:speculations,code,' . $id,
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'sometimes|required|exists:categories,id'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $data = $request->except(['photo']);

        if ($request->hasFile('photo')) {

            if ($speculation->photo && \Storage::disk('public')->exists($speculation->photo)) {
                \Storage::disk('public')->delete($speculation->photo);
            }

            $data['photo'] = $request->file('photo')->store('speculations', 'public');
        }

        $speculation->update($data);

        return response()->json(['Message' => 'Spéculation mise à jour avec succès', 'data' => $speculation], 200);
    }
    public function destroy($id)
    {
        $speculation = Speculation::find($id);
        if (!$speculation) {
            return response()->json(['Message' => 'Spéculation non trouvée'], 404);
        }
        $speculation->delete();
        return response()->json(['Message' => 'Spéculation supprimée avec succès'], 200);
    }

}
