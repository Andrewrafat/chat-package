@extends('chat::chat.layout')

{{-- ================= SIDEBAR ================= --}}
@section('sidebar')

    <div class="sidebar-header sidebar-top">
        <span>Andrew Chat</span>

        <button id="openNewChat" class="new-chat-btn">
            ➕
        </button>
    </div>

    {{-- New Chat Modal --}}
    <div id="newChatModal" class="chat-modal is-hidden">
        <div class="chat-modal-content">

            <h4>New Chat</h4>

            {{-- Chat Type --}}
            <div class="chat-type">
                <label>
                    <input type="radio" name="chat_type" value="private" checked>
                    Private
                </label>

                <label>
                    <input type="radio" name="chat_type" value="group">
                    Group
                </label>
            </div>

            {{-- Group Name --}}
            <div id="groupNameWrapper" class="group-name is-hidden">
                <input type="text" id="groupName" placeholder="Group name">
            </div>

            {{-- Users --}}
            <div class="chat-users">
                <p>Select user(s)</p>

                @foreach ($users as $user)
                    <label class="user-option">
                        <input type="checkbox" class="chat-user" value="{{ $user->id }}">
                        {{ $user->name }}
                    </label>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="chat-modal-actions">
                <button id="createChatBtn" class="btn-primary">Create</button>
                <button id="closeNewChat" class="btn-secondary">Cancel</button>
            </div>

        </div>
    </div>

    {{-- Conversations --}}
    @foreach ($conversations as $chat)
        <a href="{{ url('/chat/'.$chat->chat_key) }}"
           class="chat-item {{ isset($activeChat) && $activeChat->id === $chat->id ? 'active' : '' }}">

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


{{-- ================= CONTENT ================= --}}
@section('content')
    <div class="chat-empty">
        Select a conversation to start chatting
    </div>
@endsection
