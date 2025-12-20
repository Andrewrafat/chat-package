@extends('chat::chat.layout')

@section('sidebar')

<div class="sidebar-header">
    Andrew Chat
</div>

@foreach($conversations as $chat)
    <a href="{{ url('/chat/'.$chat->chat_key) }}" class="chat-item">
        <div class="chat-avatar">
            {{ strtoupper(substr($chat->title ?? 'C', 0, 1)) }}
        </div>

        <div class="chat-info">
            <div class="chat-title">
                {{ $chat->title ?? 'Conversation' }}
            </div>
            <div class="chat-last">
                {{ optional($chat->messages->first())->content ?? 'No messages yet' }}
            </div>
        </div>

        <div class="chat-time">
            {{ $chat->updated_at->format('H:i') }}
        </div>
    </a>
@endforeach

@endsection

@section('content')
<div style="
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#777;
    font-size:16px;
">
    Select a conversation to start chatting
</div>
@endsection
