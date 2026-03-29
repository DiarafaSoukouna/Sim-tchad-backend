<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription; 
use App\Models\Actor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request) : JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'followed_id' => 'required|exists:actors,id',
            'follower_id' => 'required|exists:actors,id|different:followed_id',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }
        $subscription = Subscription::create($validation->validated());
        return response()->json(['Message' => 'Abonnement créé avec succes', 'data' => $subscription], 201);
    }
    public function followersActor($id) : JsonResponse 
    {
        $actor = Actor::find($id);
        if(!$actor){
            return response()->json(['Message' => 'Acteur non trouvé'], 404);
        }
        $followers = Actor::find($id)->followersActors;
        return response()->json(['Message' => 'Followers recuperés avec succes', 'data' => $followers], 200);
    }
    public function followingActor($id) : JsonResponse 
    {
        $actor = Actor::find($id);
        if(!$actor){
            return response()->json(['Message' => 'Acteur non trouvé'], 404);
        }
        $following = Actor::find($id)->followingActors;
        return response()->json(['Message' => 'Following recuperés avec succes', 'data' => $following], 200);
    }
    public function unsubscribe(Request $request) : JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'followed_id' => 'required|exists:actors,id',
            'follower_id' => 'required|exists:actors,id|different:followed_id',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors'  => $validation->errors(),
            ], 422);
        }
        $subscription = Subscription::where('followed_id', $request->followed_id)
                                     ->where('follower_id', $request->follower_id)
                                     ->first();
        if (!$subscription) {
            return response()->json(['Message' => 'Abonnement non trouvé'], 404);
        }
        $subscription->delete();
        return response()->json(['Message' => 'Abonnement supprimé avec succes'], 200);
    }

}
