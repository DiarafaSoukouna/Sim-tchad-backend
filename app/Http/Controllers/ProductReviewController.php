<?php

namespace App\Http\Controllers;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;


use Illuminate\Http\Request;


class ProductReviewController extends Controller
{
    public function show($productId) : JsonResponse
    {
        $reviews = ProductReview::where('product_id', $productId)->get();

        if ($reviews->isEmpty()) {
            return response()->json(['Message' => 'Aucun avis trouvé pour ce produit'], 404);
        }

        return response()->json(['Message' => 'Avis récupérés avec succès', 'data' => $reviews], 200);
    }
    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quality_rating' => 'required|integer|min:1|max:5',
            'price_rating'   => 'required|integer|min:1|max:5',
            'delivery_rating' => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string',
            'order_id'   => 'required|exists:orders,id'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $review = ProductReview::create($request->all());

        return response()->json(['Message' => 'Avis créé avec succès', 'data' => $review], 201);

    }
    public function update(Request $request, $id): JsonResponse
    {
        $review = ProductReview::find($id);

        if (!$review) {
            return response()->json(['Message' => 'Avis non trouvé'], 404);
        }

        $validation = Validator::make($request->all(), [
            'quality_rating' => 'sometimes|integer|min:1|max:5',
            'price_rating'   => 'sometimes|integer|min:1|max:5',
            'delivery_rating' => 'sometimes|integer|min:1|max:5',
            'comment'    => 'nullable|string',
            'order_id'   => 'sometimes|exists:orders,id'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $review->update($request->all());

        return response()->json(['Message' => 'Avis mis à jour avec succès', 'data' => $review], 200);
    }
    public function destroy($id) : JsonResponse
    {
        $review = ProductReview::find($id);

        if (!$review) {
            return response()->json(['Message' => 'Avis non trouvé'], 404);
        }

        $review->delete();

        return response()->json(['Message' => 'Avis supprimé avec succès'], 200);
    }
}
