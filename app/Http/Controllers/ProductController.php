<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\AttributeValue;
use App\Models\NameInOthersLanguages;


class ProductController extends Controller

{
public function index(): JsonResponse
{
   $products = Product::with([
    'speculation.attributes',
    'attributeValues',
    'speculation.categorie'
])->get();

$data = $products->map(function ($product) {

    $valuesByAttribute = $product->attributeValues->keyBy('attribute_id');

    $attributes = optional($product->speculation->attributes ?? collect())->map(function ($attr) use ($valuesByAttribute) {
        return [
            'attribute_id' => $attr->id,
            'name' => $attr->name,
            'value' => $valuesByAttribute[$attr->id]->value ?? '',
        ];
    });

    $categorie = $product->speculation->categorie ?? null;

    return [
        'id' => $product->id,
        'name' => $product->name,
        'code' => $product->code,
        'description' => $product->description,
        'product_type_id' => null,
        'speculation_id' => $product->speculation_id,
        'categorie_id' => $categorie->id ?? null,
        'categorie_name' => $categorie->name ?? null,
        'unit_of_measure_id'=> $product->unit_of_measure_id,
        'production_area_id'=> $product->production_area_id,
        'actor_id'=> $product->actor_id,
        'store_id'=> $product->store_id,
        'quantity'=> $product->quantity,
        'price'=> $product->price,
        'origin'=> $product->origin,
        'shape'=> $product->shape,
        'currency_id'=> $product->currency_id,
        'measure_used'=> $product->measure_used,
        'production_date'=> $product->production_date,
        'photo'=> $product->photo,
        'updated_by'=> $product->updated_by,
        'attributes'=> $attributes,
        'is_active'=> $product->is_active,
    ];
});

return response()->json([
    'Message' => 'Produits récupérés avec tous les attributs',
    'data' => $data
], 200);
}

    public function show($id) : JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['Message' => 'Produit non trouvé'], 404);
        }
        return response()->json(['Message' => 'Produit recuperé avec succes', 'data' => $product], 200);
    }
    public function store(Request $request) : JsonResponse
    {
        // Decode JSON fields sent via multipart/form-data
        if ($request->has('attributes') && is_string($request->input('attributes'))) {
            $request->merge([
                'attributes' => json_decode($request->input('attributes'), true),
            ]);
        }

        if ($request->has('name_in_others_languages') && is_string($request->input('name_in_others_languages'))) {
            $request->merge([
                'name_in_others_languages' => json_decode($request->input('name_in_others_languages'), true),
            ]);
        }

        $validation = Validator::make($request->all(), [
            'name'                 => 'required|string|max:255',
            'code'                 => 'required|string|max:100|unique:products,code',
            'description'          => 'nullable|string',

            // Foreign keys
            'product_type_id'      => 'required|exists:product_types,id',
            'speculation_id'       => 'required|exists:speculations,id',
            'unit_of_measure_id'   => 'required|exists:unite_of_measures,id',
            'production_area_id'   => 'required|exists:production_areas,id',
            'actor_id'             => 'required|exists:actors,id',
            'store_id'             => 'required|exists:stores,id',

            'updated_by'           => 'nullable|string|max:255',

            // Champs métier
            'quantity'             => 'nullable|integer|min:0',
            'price'                => 'nullable|integer|min:0',
            'origin'               => 'nullable|string|max:255',
            'shape'                => 'nullable|string|max:100',
            'currency_id'         => 'nullable|exists:currencies,id',
            'measure_used'         => 'nullable|string|max:255',
            'photo'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'production_date'      => 'nullable|date',

            // Attributes (optionnels)
            'attributes'                   => 'nullable|array',
            'attributes.*.attribute_id'    => 'required_with:attributes|exists:attributes,id',
            'attributes.*.value'           => 'required_with:attributes|string|max:255',

            'name_in_others_languages' => 'nullable|array',
            'name_in_others_languages.*.language_id' => 'required_with:name_in_others_languages|exists:languages,id',
            'name_in_others_languages.*.name'        => 'required_with:name_in_others_languages|string|max:255'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }

        \DB::beginTransaction();

        try {
            $photoPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('products', 'public');
            }

            // Création du produit avec données validées uniquement
            $data = $validation->validated();
            $data['photo'] = $photoPath;

            $product = Product::create($data);

            // Création des valeurs d'attributs si présentes
            if ($request->has('attributes')) {
                foreach ($request->input('attributes') as $attributeData) {
                    AttributeValue::create([
                        'product_id'   => $product->id,
                        'attribute_id' => $attributeData['attribute_id'],
                        'value'        => $attributeData['value'],
                    ]);
                }
            }
            if( $request->has('name_in_others_languages')) {
                foreach ($request->input('name_in_others_languages') as $nameData) {
                   NameInOthersLanguages::create([
                        'entity_type' => 'product',
                        'entity_id'   => $product->id,
                        'language_id' => $nameData['language_id'],
                        'name'        => $nameData['name'],
                    ]);
                }
            }

            \DB::commit();

            return response()->json([
                'Message' => 'Produit créé avec succès',
                'data'    => $product->load('attributeValues')
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de la création du produit',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, $id): JsonResponse
{
    // Decode JSON fields sent via multipart/form-data
    if ($request->has('attributes') && is_string($request->input('attributes'))) {
        $request->merge([
            'attributes' => json_decode($request->input('attributes'), true),
        ]);
    }

    if ($request->has('name_in_others_languages') && is_string($request->input('name_in_others_languages'))) {
        $request->merge([
            'name_in_others_languages' => json_decode($request->input('name_in_others_languages'), true),
        ]);
    }

    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'Message' => 'Produit non trouvé'
        ], 404);
    }

    $validation = Validator::make($request->all(), [
        'name'                 => 'sometimes|required|string|max:255',
        'code'                 => 'sometimes|required|string|max:100|unique:products,code,' . $id,
        'description'          => 'nullable|string',

        // Foreign keys
        'product_type_id'      => 'sometimes|required|exists:product_types,id',
        'speculation_id'       => 'sometimes|required|exists:speculations,id',
        'unit_of_measure_id'   => 'sometimes|required|exists:unite_of_measures,id',
        'production_area_id'   => 'sometimes|required|exists:production_areas,id',
        'actor_id'             => 'sometimes|required|exists:actors,id',
        'store_id'             => 'sometimes|required|exists:stores,id',

        'updated_by'           => 'nullable|string|max:255',

        // Champs métier
        'quantity'             => 'nullable|integer|min:0',
        'price'                => 'nullable|integer|min:0',
        'origin'               => 'nullable|string|max:255',
        'shape'                => 'nullable|string|max:100',
        'currency_id'         => 'nullable|exists:currencies,id',
        'measure_used'         => 'nullable|string|max:255',
        'photo'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'production_date'      => 'nullable|date',
        'is_active'            => 'nullable|boolean',

        // Attributes (optionnels)
        'attributes'                   => 'nullable|array',
        'attributes.*.attribute_id'    => 'required_with:attributes|exists:attributes,id',
        'attributes.*.value'           => 'required_with:attributes|string|max:255',

        'name_in_others_languages' => 'nullable|array',
        'name_in_others_languages.*.language_id' => 'required_with:name_in_others_languages|exists:languages,id',
        'name_in_others_languages.*.name'        => 'required_with:name_in_others_languages|string|max:255',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation',
            'errors'  => $validation->errors(),
        ], 422);
    }

    \DB::beginTransaction();

    try {
        $data = $validation->validated();

        if ($request->hasFile('photo')) {

            if ($product->photo && \Storage::disk('public')->exists($product->photo)) {
                \Storage::disk('public')->delete($product->photo);
            }

            $data['photo'] = $request->file('photo')->store('products', 'public');
        }

        // Mise à jour du produit
        $product->update($data);

        // Gestion des attributs SI envoyés
        if ($request->has('attributes')) {

            foreach ($request->input('attributes') as $attributeData) {
                AttributeValue::updateOrCreate(
                    [
                        'product_id'   => $product->id,
                        'attribute_id' => $attributeData['attribute_id'],
                    ],
                    [
                        'value' => $attributeData['value'],
                    ]
                );
            }
        }

        if ($request->has('name_in_others_languages')) {
            foreach ($request->input('name_in_others_languages') as $nameData) {
                NameInOthersLanguages::updateOrCreate(
                    [
                        'entity_type' => 'product',
                        'entity_id'   => $product->id,
                        'language_id' => $nameData['language_id'],
                    ],
                    [
                        'name' => $nameData['name'],
                    ]
                );
            }
        }

        \DB::commit();

        return response()->json([
            'Message' => 'Produit mis à jour avec succès',
            'data'    => $product->load('attributeValues')
        ], 200);

    } catch (\Exception $e) {
        \DB::rollBack();

        return response()->json([
            'message' => 'Erreur lors de la mise à jour du produit',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['Message' => 'Produit non trouvé'], 404);
        }
        $product->delete();
        return response()->json(['Message' => 'Produit supprimé avec succès'], 200);
    }
    public function showWithAttributes($id): JsonResponse
{
    $product = Product::with('attributeValues.attribute')->find($id);

    if (!$product) {
        return response()->json([
            'Message' => 'Produit non trouvé'
        ], 404);
    }

    $data = [
        'id'                => $product->id,
            'name'              => $product->name,
            'code'              => $product->code,
            'description'       => $product->description,
            'product_type_id'   => $product->product_type_id,
            'speculation_id'    => $product->speculation_id,
            'unit_of_measure_id'=> $product->unit_of_measure_id,
            'production_area_id'=> $product->production_area_id,
            'actor_id'          => $product->actor_id,
            'store_id'          => $product->store_id,
            'quantity'          => $product->quantity,
            'price'             => $product->price,
            'origin'            => $product->origin,
            'shape'             => $product->shape,
            'currency_id'       => $product->currency_id,
            'measure_used'      => $product->measure_used,
            'production_date'   => $product->production_date,
            'photo'             => $product->photo,
            'updated_by'        => $product->updated_by,

        'attributes'  => $product->attributeValues->map(function ($item) {
            return [
                'attribute_id'   => $item->attribute_id,
                'attribute_name' => $item->attribute->name ?? null,
                'value'          => $item->value
            ];
        })
    ];

    return response()->json([
        'Message' => 'Produit avec attributs récupéré avec succès',
        'data'    => $data
    ], 200);
}
public function getProductNamesInOtherLanguages($id): JsonResponse
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'message' => 'Produit non trouvé'
        ], 404);
    }

    $names = NameInOthersLanguages::with('language')
        ->where('entity_type', 'product')
        ->where('entity_id', $id)
        ->get()
        ->map(function ($item) {
            return [
                'language_id'   => $item->language_id,
                'language_name' => $item->language->name ?? null,
                'name'          => $item->name,
            ];
        });

    return response()->json([
        'message' => 'Noms du produit dans d’autres langues récupérés avec succès',
        'data'    => $names
    ], 200);
}
public function addLanguagesToProduct(Request $request, $id): JsonResponse
{
    // Récupérer le produit
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'message' => 'Produit non trouvé'
        ], 404);
    }

    // Validation des données
    $validation = Validator::make($request->all(), [
        'name_in_others_languages' => 'required|array|min:1',
        'name_in_others_languages.*.language_id' => 'required|exists:languages,id',
        'name_in_others_languages.*.name'        => 'required|string|max:255',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation',
            'errors'  => $validation->errors(),
        ], 422);
    }

    \DB::beginTransaction();

    try {
        foreach ($request->input('name_in_others_languages') as $nameData) {
            NameInOthersLanguages::updateOrCreate(
                [
                    'entity_type' => 'product',
                    'entity_id'   => $product->id,
                    'language_id' => $nameData['language_id'],
                ],
                [
                    'name' => $nameData['name'],
                ]
            );
        }

        \DB::commit();

        // Retourner le produit avec les noms mis à jour
        $names = NameInOthersLanguages::with('language')
            ->where('entity_type', 'product')
            ->where('entity_id', $product->id)
            ->get()
            ->map(function ($item) {
                return [
                    'language_id'   => $item->language_id,
                    'language_name' => $item->language->name ?? null,
                    'name'          => $item->name,
                ];
            });

        return response()->json([
            'message' => 'Noms dans d’autres langues ajoutés/mis à jour avec succès',
            'data'    => $names
        ], 200);

    } catch (\Exception $e) {
        \DB::rollBack();

        return response()->json([
            'message' => 'Erreur lors de l’ajout des noms dans d’autres langues',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
public function deleteLanguageFromProduct( $id, $languageId): JsonResponse
{
    // Récupérer le produit
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'message' => 'Produit non trouvé'
        ], 404);
    }

    try {
        $deleted = NameInOthersLanguages::where('entity_type', 'product')
            ->where('entity_id', $product->id)
            ->where('language_id', $languageId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'message' => 'Nom pour cette langue non trouvé'
            ], 404);
        }

        // Retourner la liste mise à jour
        $names = NameInOthersLanguages::with('language')
            ->where('entity_type', 'product')
            ->where('entity_id', $product->id)
            ->get()
            ->map(function ($item) {
                return [
                    'language_id'   => $item->language_id,
                    'language_name' => $item->language->name ?? null,
                    'name'          => $item->name,
                ];
            });

        return response()->json([
            'message' => 'Nom supprimé avec succès',
            'data'    => $names
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la suppression du nom',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
public function changeProductStatus($id, Request $request): JsonResponse
{
    $product = Product::find($id);
    
    if (!$product) {
        return response()->json([
            'Message' => 'Produit non trouvé'
        ], 404);
    }

    $validation = Validator::make($request->all(), [
        'is_active' => 'required|boolean',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation',
            'errors'  => $validation->errors(),
        ], 422);
    }

    $product->is_active = $request->input('is_active');
    $product->save();

    return response()->json([
        'Message' => 'Statut du produit mis à jour avec succès',
        'data' => [
            'id' => $product->id,
            'name' => $product->name,
            'is_active' => $product->is_active,
        ]
    ], 200);
}
}