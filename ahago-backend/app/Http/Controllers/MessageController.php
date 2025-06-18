<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // List all messages between two users (optional sender_id and receiver_id in query)
    public function index(Request $request)
    {
        $senderId = $request->query('sender_id');
        $receiverId = $request->query('receiver_id');

        $query = Message::query();

        if ($senderId && $receiverId) {
            $query->where(function ($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
            });
        }

        $messages = $query->orderBy('sent_at')->get();

        return response()->json($messages);
    }

    // Store a new message
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'is_read' => 'sometimes|boolean',
        ]);

        // sent_at will default to current time automatically in DB, so no need to set here

        $message = Message::create($validated);

        return response()->json($message, 201);
    }

    // Mark a message as read (optional)
    public function markAsRead($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        $message->is_read = true;
        $message->save();

        return response()->json(['message' => 'Message marked as read']);
    }
}
