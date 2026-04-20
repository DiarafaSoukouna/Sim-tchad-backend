<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Attribute;

class ProductTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $productionTypes= ProductType::all();
        return response()->json(['Message' => 'Product Types recuperés avec succes', 'data' => $productionTypes], 200);
    }
    
    public function indexWithAttributes(): JsonResponse
    {
        $productionTypes = ProductType::all();
        return response()->json(['Message' => 'Product Types with attributes recuperés avec succes', 'data' => $productionTypes], 200);
    }

    public function show($id): JsonResponse
    {
        $productionType= ProductType::find($id);
        if (!$productionType) {
            return response()->json(['Message' => 'Type de produit non trouvé'], 404);
        }
        return response()->json(['Message' => 'Type de produit recuperé avec succes', 'data' => $productionType], 200);
    }
   public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:100|unique:product_types,code',
            'description' => 'nullable|string',
            'updated_by'  => 'nullable|string|max:255',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }

        \DB::beginTransaction();

        try {
            $productionType = ProductType::create($validation->validated());

            \DB::commit();

            return response()->json([
                'Message' => 'Type de produit créé avec succès',
                'data' => $productionType
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de la création du type de produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $productionType = ProductType::find($id);

        if (!$productionType) {
            return response()->json(['Message' => 'Type de produit non trouvé'], 404);
        }

        $validation = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'code'        => 'sometimes|required|string|max:100|unique:product_types,code,' . $id,
            'description' => 'nullable|string',
            'updated_by'  => 'nullable|string|max:255',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }

        \DB::beginTransaction();

        try {
            $productionType->update($validation->validated());

            \DB::commit();

            return response()->json([
                'Message' => 'Type de produit mis à jour avec succès',
                'data' => $productionType
            ], 200);

        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de la mise à jour du type de produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $productionType = ProductType::find($id);
        if (!$productionType) {
            return response()->json(['Message' => 'Type de produit non trouvé'], 404);
        }
        $productionType->delete();
        return response()->json(['Message' => 'Type de produit supprimé avec succès'], 200);
    }
}
