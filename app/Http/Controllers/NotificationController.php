<?php

namespace App\Http\Controllers;
use App\Models\Notification;


use Illuminate\Http\Request;


class NotificationController extends Controller
{
   public function index($actorId)
   {
       $notifications = Notification::where('actor_id', $actorId)->get();
       return response()->json(['Message' => 'Notifications retrieved successfully', 'data' => $notifications], 200);
   }

   public function store(Request $request)
   {
       $validatedData = $request->validate([
           'title' => 'nullable|string|max:255',
           'message' => 'required|string',
           'actor_id' => 'required|exists:actors,id',
           'type' => 'nullable|string|max:255',
       ]);

       $notification = Notification::create($validatedData);
       return response()->json(['Message' => 'Notification created successfully', 'data' => $notification], 201);
   }
   public function destroy($id)
   {
       $notification = Notification::find($id);
       if (!$notification) {
           return response()->json(['Message' => 'Notification not found'], 404);
       }

       $notification->delete();
       return response()->json(['Message' => 'Notification deleted successfully'], 200);
   }
   public function markAsRead($id)
   {
       $notification = Notification::find($id);
       if (!$notification) {
           return response()->json(['Message' => 'Notification not found'], 404);
       }

       $notification->update(['is_read' => true]);
       return response()->json(['Message' => 'Notification marked as read successfully'], 200);
   }

}
