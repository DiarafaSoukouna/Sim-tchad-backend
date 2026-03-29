<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;


class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $order = Order::all();
        return response()->json(['Message' => 'Commandes récupérées avec succès', 'data' => $order], 200);
    }
   public function store(Request $request): JsonResponse
{
    $validation = Validator::make($request->all(), [
        'buyer_id'    => 'required|exists:actors,id',
        'seller_id'   => 'required|exists:actors,id',
        'status'      => 'integer|in:0,1,2',
        'total_price' => 'required|numeric|min:0',

        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity'   => 'required|integer|min:1',
        'products.*.unit_price' => 'required|numeric|min:0',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation.',
            'errors'  => $validation->errors(),
        ], 422);
    }

    DB::beginTransaction();

    try {

        // créer la commande
        $order = Order::create([
            'buyer_id' => $request->buyer_id,
            'seller_id' => $request->seller_id,
            'status' => $request->status,
            'total_price' => $request->total_price,
        ]);

        $orderPlusItems = [];

        // créer les order items
        foreach ($request->products as $product) {

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
            ]);

            $orderPlusItems[] = $orderItem;
        }

        DB::commit();

        return response()->json([
            'message' => 'Commande créée avec succès',
            'data' => [
                'order' => $order,
                'items' => $orderPlusItems
            ]
        ], 201);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'message' => 'Erreur lors de la création de la commande',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function show($id): JsonResponse
{
    // récupérer la commande avec ses items
    $order = Order::with('orderItems')->find($id);

    if (!$order) {
        return response()->json(['message' => 'Commande non trouvée'], 404);
    }

    // transformer les items pour ne pas inclure les relations inutiles
    $orderItems = $order->orderItems->map(function ($item) {
        return [
            'id' => $item->id,
            'order_id' => $item->order_id,
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => (float) $item->unit_price,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    });

    return response()->json([
        'message' => 'Commande récupérée avec succès',
        'data' => [
            'order' => [
                'id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'seller_id' => $order->seller_id,
                'status' => $order->status,
                'total_price' => (float) $order->total_price,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ],
            'items' => $orderItems,
        ]
    ], 200);
}
public function destroy($id): JsonResponse
{
    $order = Order::find($id);

    if (!$order) {
        return response()->json(['Message' => 'Commande non trouvée'], 404);
    }

    $order->delete();

    return response()->json(['Message' => 'Commande supprimée avec succès'], 200);
}
public function updateStatus(Request $request, $id): JsonResponse
{
    $validation = Validator::make($request->all(), [
        'status' => 'required|integer|in:0,1,2',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation.',
            'errors'  => $validation->errors(),
        ], 422);
    }

    $order = Order::find($id);

    if (!$order) {
        return response()->json(['Message' => 'Commande non trouvée'], 404);
    }

    $order->status = $request->status;
    $order->save();

    return response()->json(['Message' => 'Statut de la commande mis à jour avec succès', 'data' => $order], 200);
}
}