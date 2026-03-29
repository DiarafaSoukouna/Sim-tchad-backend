<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::all();
        return response()->json(['Message' => 'Posts récupérés avec succès', 'data' => $posts], 200);
    }
    public function show($id) : JsonResponse
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['Message' => 'Post non trouvé'], 404);
        }
        return response()->json(['Message' => 'Post récupéré avec succès', 'data' => $post], 200);
    }

public function store(Request $request): JsonResponse
{
    $validation = Validator::make($request->all(), [
        'actor_id' => 'required|exists:actors,id',
        'description' => 'required|string',
        'media_url' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi,mp3,wav|max:20480',
        'media_type' => 'nullable|string|max:50',
        'audience_type_id' => 'nullable|exists:actor_types,id'
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation.',
            'errors'  => $validation->errors(),
        ], 422);
    }

    $data = $request->all();

    if ($request->hasFile('media_url')) {

        $file = $request->file('media_url');

        // 🔥 Détection automatique du type
        $mime = $file->getMimeType();

        if (str_contains($mime, 'image')) {
            $data['media_type'] = 'image';
        } elseif (str_contains($mime, 'video')) {
            $data['media_type'] = 'video';
        } elseif (str_contains($mime, 'audio')) {
            $data['media_type'] = 'audio';
        }

        // Stockage
        $path = $file->store('posts', 'public');

        $data['media_url'] = $path; // on garde ton champ
    }

    $post = Post::create($data);

    return response()->json([
        'Message' => 'Post créé avec succès',
        'data' => $post
    ], 201);
}
  public function update(Request $request, $id): JsonResponse
{
    $post = Post::find($id);

    if (!$post) {
        return response()->json(['Message' => 'Post non trouvé'], 404);
    }

    $validation = Validator::make($request->all(), [
        'description' => 'sometimes|required|string',
        'media_url' => 'sometimes|nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi,mp3,wav|max:20480',
        'media_type' => 'sometimes|nullable|string|max:50',
        'audience_type_id' => 'sometimes|nullable|exists:actor_types,id'
    ]);

    if ($validation->fails()) {
        return response()->json([
            'message' => 'Erreur de validation.',
            'errors'  => $validation->errors(),
        ], 422);
    }

    $data = $request->all();

    if ($request->hasFile('media_url')) {

        // Supprimer ancien fichier
        if ($post->media_url && Storage::disk('public')->exists($post->media_url)) {
            Storage::disk('public')->delete($post->media_url);
        }

        $file = $request->file('media_url');
        $mime = $file->getMimeType();

        if (str_contains($mime, 'image')) {
            $data['media_type'] = 'image';
        } elseif (str_contains($mime, 'video')) {
            $data['media_type'] = 'video';
        } elseif (str_contains($mime, 'audio')) {
            $data['media_type'] = 'audio';
        }

        $path = $file->store('posts', 'public');

        $data['media_url'] = $path; // toujours ton champ
    }

    $post->update($data);

    return response()->json([
        'Message' => 'Post mis à jour avec succès',
        'data' => $post
    ], 200);
}
    public function destroy($id) : JsonResponse
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['Message' => 'Post non trouvé'], 404);
        }

        $post->delete();
        return response()->json(['Message' => 'Post supprimé avec succès'], 200);
    }
}
