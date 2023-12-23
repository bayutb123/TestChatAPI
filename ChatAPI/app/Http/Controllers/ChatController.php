<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;

class ChatController extends Controller
{
    protected $chat;
    protected $message;

    public function __construct(Chat $chat, Message $message)
    {
        $this->chat = $chat;
        $this->message = $message;
    }

    public function getChats(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $chats = $this->chat->where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo'])
            ->get();

        return response()->json([
            'chats' => $chats,
        ]);
    }

    public function getMessages(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $messages = $this->message->where('chat_id', $request->chat_id)
            ->with(['sender'])
            ->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $message = $this->message->create([
            'chat_id' => $request->chat_id,
            'sender_id' => $user->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => $message,
        ]);
    }

    public function createChat(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $chat = $this->chat->create([
            'user_one_id' => $user->id,
            'user_two_id' => $request->user_two_id,
        ]);

        return response()->json([
            'chat' => $chat,
        ]);
    }

    public function deleteChat(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $chat = $this->chat->where('id', $request->chat_id)
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->first();

        if (!$chat) {
            return response()->json([
                'message' => 'Chat not found.',
            ], 404);
        }

        $chat->delete();

        return response()->json([
            'message' => 'Chat deleted.',
        ]);
    }

    public function markRead(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $message = $this->message->where('id', $request->message_id)
            ->where('chat_id', $request->chat_id)
            ->where('sender_id', '!=', $user->id)
            ->first();

        if (!$message) {
            return response()->json([
                'message' => 'Message not found.',
            ], 404);
        }

        $message->read = true;
        $message->save();

        return response()->json([
            'message' => 'Message marked as read.',
        ]);
    }

}
