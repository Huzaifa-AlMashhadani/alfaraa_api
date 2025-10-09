<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $user = auth()->user();

         $conversations = Conversation::where('user_id', $user->id)
             ->orWhere('agent_id', $user->id)
             ->pluck('id');
            $messages = Message::whereIn('conversation_id', $conversations)
             ->with('sender')
             ->get();
            return response()->json($messages);
        
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $sender = $request->user()->id;
        $message = Message::create([
            "conversation_id" => $request->conversation_id,
            "sender_id" => $sender,
            "message" => $request->message,
        ]);
        return response()->json($message, 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
