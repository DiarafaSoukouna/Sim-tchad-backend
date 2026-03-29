<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Currency;
use Illuminate\Support\Facades\Validator;


class CurrencyController extends Controller
{
    public function index() : JsonResponse
    {
        $currencies = Currency::all();
        return response()->json(['Message' => 'Currencies recuperés avec succes', 'data' => $currencies], 200);
    }
    public function show($id) : JsonResponse
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return response()->json(['Message' => 'Devise non trouvée'], 404);
        }
        return response()->json(['Message' => 'Devise recuperée avec succes', 'data' => $currency], 200);
    }
    public function store(Request $request) : JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:100|unique:currencies,code',
            'symbol'      => 'nullable|string|max:10',
            'is_default'  => 'nullable|boolean',
        ]);
   

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $currency = Currency::create($validation->validated());

        return response()->json(['Message' => 'Devise créée avec succes', 'data' => $currency], 201);
    }
    public function update(Request $request, $id) : JsonResponse
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return response()->json(['Message' => 'Devise non trouvée'], 404);  
        }
        $validation = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'code'        => 'sometimes|required|string|max:100|unique:currencies,code,'.$id,
            'symbol'      => 'nullable|string|max:10',
            'is_default'  => 'nullable|boolean',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }
        $currency->update($request->all());
        return response()->json(['Message' => 'Devise mise à jour avec succes', 'data' => $currency], 200);
    }
    public function destroy($id) : JsonResponse
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return response()->json(['Message' => 'Devise non trouvée'], 404);
        }
        $currency->delete();
        return response()->json(['Message' => 'Devise supprimée avec succes'], 200);
    }
}
