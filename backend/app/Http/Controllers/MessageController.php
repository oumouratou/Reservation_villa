<?php

namespace App\Http\Controllers;

use App\Events\NewMessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Get all conversations for current user.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Get unique conversation partners with last message
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender:id,name,avatar', 'receiver:id,name,avatar', 'reservation.villa:id,title'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                $partnerId = $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
                return $msg->reservation_id . '_' . $partnerId;
            })
            ->map(function ($messages) use ($userId) {
                $last = $messages->first();
                $partner = $last->sender_id === $userId ? $last->receiver : $last->sender;
                return [
                    'partner'        => $partner,
                    'reservation'    => $last->reservation,
                    'last_message'   => $last->body,
                    'last_at'        => $last->created_at,
                    'unread_count'   => $messages->where('receiver_id', $userId)->where('is_read', false)->count(),
                ];
            })
            ->values();

        return response()->json($conversations);
    }

    /**
     * Get messages for a specific conversation.
     */
    public function conversation(Request $request): JsonResponse
    {
        $request->validate([
            'partner_id'     => 'required|exists:users,id',
            'reservation_id' => 'nullable|exists:reservations,id',
        ]);

        $userId    = $request->user()->id;
        $partnerId = $request->partner_id;

        $messages = Message::where(function ($q) use ($userId, $partnerId) {
                $q->where('sender_id', $userId)->where('receiver_id', $partnerId);
            })
            ->orWhere(function ($q) use ($userId, $partnerId) {
                $q->where('sender_id', $partnerId)->where('receiver_id', $userId);
            })
            ->when($request->reservation_id, fn($q) => $q->where('reservation_id', $request->reservation_id))
            ->with(['sender:id,name,avatar'])
            ->orderBy('created_at')
            ->get();

        // Mark received messages as read
        Message::where('sender_id', $partnerId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Send a message.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'receiver_id'    => 'required|exists:users,id|different:' . $request->user()->id,
            'reservation_id' => 'nullable|exists:reservations,id',
            'body'           => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'sender_id'      => $request->user()->id,
            'receiver_id'    => $data['receiver_id'],
            'reservation_id' => $data['reservation_id'] ?? null,
            'body'           => $data['body'],
        ]);

        $message->load('sender:id,name,avatar');

        // Broadcast via WebSocket
        broadcast(new NewMessageSent($message))->toOthers();

        return response()->json(['message' => 'Message envoyé.', 'data' => $message], 201);
    }

    /**
     * Mark message as read.
     */
    public function markRead(Request $request, Message $message): JsonResponse
    {
        abort_unless($message->receiver_id === $request->user()->id, 403);
        $message->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['message' => 'Message marqué comme lu.']);
    }

    /**
     * Unread count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread' => $count]);
    }
}