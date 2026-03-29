<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Actor;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\MessagesController;



class ActorController extends Controller
{
    
     public function store(Request $request)
{
    $validation = Validator::make($request->all(), [
        'actor'             => 'required|string|max:255',
        'actor_sigle'       => 'required|string|max:100',
        'password'          => 'required|string|min:8',
        'updated_by'        => 'nullable|string',
        'code'              => 'required|string|unique:actors,code',
        'description'       => 'nullable|string',
        'phone'             => 'required|string|max:20|unique:actors,phone',
        'logo'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'headquarter_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'actor_type_ids'    => 'required|array',
        'actor_type_ids.*'  => 'integer|exists:actor_types,id',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation.',
            'errors' => $validation->errors(),
        ], 422);
    }

    $data = $request->except(['logo', 'headquarter_photo', 'actor_type_ids']);
    $data['password'] = bcrypt($request->input('password'));

    if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('actors', 'public');
    }

    if ($request->hasFile('headquarter_photo')) {
        $data['headquarter_photo'] = $request->file('headquarter_photo')->store('actors', 'public');
    }

    $actor = Actor::create($data);

    // Attach multiple actor types using the pivot table
    $actor->types()->sync($request->input('actor_type_ids'));

   $sendMail = new MessagesController();
$sendMail->SendMailTo(new Request([
    'email'   => $actor->email,
    'subject' => 'Bienvenue sur SIM Tchad – Votre compte est en attente de validation',
    'message' => "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;'>
            
            <h2 style='color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;'>
                Bienvenue sur SIM Tchad 🎉
            </h2>

            <p style='color: #555; font-size: 15px;'>
                Bonjour <strong>{$actor->actor}</strong>,
            </p>

            <p style='color: #555; font-size: 15px;'>
                Merci de vous être inscrit sur <strong>SIM Tchad</strong>. Nous sommes ravis de vous compter parmi nos utilisateurs.
            </p>

            <div style='background-color: #fff8e1; border-left: 4px solid #f39c12; padding: 15px; border-radius: 4px; margin: 20px 0;'>
                <p style='color: #856404; font-size: 15px; margin: 0;'>
                    ⏳ <strong>Votre compte est actuellement en attente de validation.</strong><br><br>
                    Dès que l'administrateur aura validé votre compte, vous recevrez une confirmation 
                    et vous pourrez vous connecter à la plateforme.
                </p>
            </div>

            <p style='color: #555; font-size: 15px;'>
                En attendant, n'hésitez pas à nous contacter si vous avez des questions ou besoin d'assistance.
            </p>

            <div style='margin-top: 30px; padding-top: 15px; border-top: 1px solid #e0e0e0; text-align: center;'>
                <p style='color: #888; font-size: 13px;'>
                    Cordialement,<br>
                    <strong style='color: #2c3e50;'>L'équipe SIM Tchad</strong>
                </p>
            </div>

        </div>
    "
]));
    return response()->json([
        'Message' => 'Acteur crée avec succès',
        'data' => $actor->load('types')
    ], 201);
}

public function update(Request $request, $id)
{
    $actor = Actor::find($id);
    if (!$actor) {
        return response()->json(['message' => 'Acteur non trouvé'], 404);
    }

    $validation = Validator::make($request->all(), [
        'actor'             => 'sometimes|required|string|max:255',
        'actor_sigle'       => 'sometimes|required|string|max:100',
        'actor_type_ids'    => 'sometimes|array',
        'actor_type_ids.*'  => 'integer|exists:actor_types,id',
        'updated_by'        => 'nullable|string',
        'code'              => 'sometimes|required|string|unique:actors,code,' . $id,
        'description'       => 'nullable|string',
        'phone'             => 'sometimes|required|string|max:20|unique:actors,phone,' . $id,
        'logo'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'headquarter_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation.',
            'errors'  => $validation->errors(),
        ], 422);
    }

    if ($request->hasFile('logo')) {
        if ($actor->logo && \Storage::disk('public')->exists($actor->logo)) {
            \Storage::disk('public')->delete($actor->logo);
        }
        $actor->logo = $request->file('logo')->store('actors', 'public');
    }

    if ($request->hasFile('headquarter_photo')) {
        if ($actor->headquarter_photo && \Storage::disk('public')->exists($actor->headquarter_photo)) {
            \Storage::disk('public')->delete($actor->headquarter_photo);
        }
        $actor->headquarter_photo = $request->file('headquarter_photo')->store('actors', 'public');
    }

    $actor->update($request->except(['logo', 'headquarter_photo', 'actor_type_ids']));

    // Sync actor types if provided
    if ($request->has('actor_type_ids')) {
        $actor->types()->sync($request->input('actor_type_ids'));
    }

    return response()->json([
        'Message' => 'Acteur mis à jour avec succès',
        'data' => $actor->load('types')
    ], 200);
}

    public function auth(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'phone'     => 'required|string',
            'password'  => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $phone = $request->input('phone');
        $password = $request->input('password');

        $user = Actor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé.'
            ], 404);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Mot de passe incorrect.'
            ], 401);
        }

        // Génération du token (Laravel Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Authentification réussie',
            'user'    => $user,
            'token'   => $token,
            'token_type' => 'Bearer'
        ], 200);
    }
 public function index(): JsonResponse
{
    $actors = Actor::with('types')->get();

    $data = $actors->map(function ($actor) {
        return [
            'id' => $actor->id,
            'actor' => $actor->actor,
            'actor_sigle' => $actor->actor_sigle,
            'email' => $actor->email,
            'phone' => $actor->phone,
            'whatsapp' => $actor->whatsapp,
            'actor_type_id' => $actor->actor_type_id, // (old column, optional to keep)
            'is_active' => $actor->is_active,
            'headquarter_photo' => $actor->headquarter_photo,
            'logo' => $actor->logo,
            'address' => $actor->address,
            'latitude' => $actor->latitude,
            'longitude' => $actor->longitude,
            'updated_by' => $actor->updated_by,
            'code' => $actor->code,
            'description' => $actor->description,
            'created_at' => $actor->created_at,
            'updated_at' => $actor->updated_at,

            // 🔥 THIS is what you wanted
            'types' => $actor->types->pluck('name'),
        ];
    });

    return response()->json([
        'message' => 'Liste des acteurs avec leurs types',
        'data' => $data
    ], 200);
}
    public function show($id): JsonResponse
    {
        $actor = Actor::find($id);
        if (!$actor) {
            return response()->json(['message' => 'Acteur non trouvé'], 404);
        }
        return response()->json(['Message' => 'Détails de l\'acteur', 'data' => $actor], 200);
    }
    public function destroy($id): JsonResponse
    {
        $actor = Actor::find($id);
        if (!$actor) {
            return response()->json(['message' => 'Acteur non trouvé'], 404);
        }
        $actor->delete();
        return response()->json(['Message' => 'Acteur supprimé avec succès'], 200);
    }
  
    public function changePassword(Request $request, $id): JsonResponse
    {
        $actor = Actor::find($id);
        if (!$actor) {
            return response()->json(['message' => 'Acteur non trouvé'], 404);
        }

        $validation = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors'  => $validation->errors(),
            ], 422);
        }

        $hashedPassword = bcrypt($request->input('password'));
        $actor->password = $hashedPassword;
        $actor->save();

        return response()->json(['Message' => 'Mot de passe mis à jour avec succès'], 200);
    } 
}
