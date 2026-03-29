<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index() : JsonResponse
    {
        $categories = Category::all();
        return response()->json(['Message' => 'Categories recuperées avec succès', 'data' => $categories], 200);
    }
    public function show($id) : JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['Message' => 'Catégorie non trouvée'], 404);
        }
        return response()->json(['Message' => 'Catégorie recuperée avec succès', 'data' => $category], 200);
    }
    public function store(Request $request) : JsonResponse
    {
          $validation = Validator::make($request->all(), [
        'name'        => 'required|string|max:255|unique:categories,name',
        'description' => 'nullable|string',
        'code'        => 'required|string|max:100|unique:categories,code',
        'sector_id'   => 'required|exists:sectors,id',
        'icons'      => 'nullable|string|max:255',
    ]);
      if ($validation->fails()) {
      return response()->json([
        'message' => 'Erreur de validation.',
        'errors' => $validation->errors(),
      ], 422);
    }
        $category = Category::create($request->all());
        return response()->json(['Message' => 'Catégorie créée avec succès', 'data' => $category], 201);
    }
public function update(Request $request, $id): JsonResponse
{
    $category = Category::find($id);

    if (!$category) {
        return response()->json(['Message' => 'Catégorie non trouvée'], 404);
    }

    $validation = Validator::make($request->all(), [
        'name'        => 'sometimes|string|max:255|unique:categories,name,' . $category->id,
        'description' => 'nullable|string',
        'code'        => 'sometimes|string|max:100|unique:categories,code,' . $category->id,
        'sector_id'   => 'sometimes|exists:sectors,id',
        'icons'      => 'nullable|string|max:255'
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation.',
            'errors'  => $validation->errors(),
        ], 422);
    }

    $category->update($request->all());

    return response()->json([
        'Message' => 'Catégorie mise à jour avec succès',
        'data'    => $category
    ], 200);
}
    public function destroy($id) : JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['Message' => 'Catégorie non trouvée'], 404);
        }
        $category->delete();
        return response()->json(['Message' => 'Catégorie supprimée avec succès'], 200);
    }

}
