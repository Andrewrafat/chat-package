@extends('chat::chat.layout')

{{-- ================= SIDEBAR ================= --}}
@section('sidebar')
    <div class="sidebar-header">Andrew Chat</div>

    @foreach ($conversations as $chat)
        <a href="{{ url('/chat/' . $chat->chat_key) }}"
            class="chat-item {{ $activeChat && $activeChat->id === $chat->id ? 'active' : '' }}">

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
    {{-- HEADER --}}
    <div class="chat-header">
        <button class="chat-back-btn" id="chatBackBtn">←</button>
        <span class="chat-title-text">
            {{ $activeChat->title ?? 'Conversation' }}
        </span>
    </div>

    {{-- MESSAGES --}}
    <div class="chat-messages" id="chatMessages">

        @foreach ($activeChat->messages()->oldest()->get() as $message)
            @php
                $authId = auth()->id(); // ✅ مهم
                $isMe = $message->sender_id === $authId;

                $participantsCount = $activeChat->participants->where('user_id', '!=', $authId)->count();

                $deliveredCount = $message->reads->where('user_id', '!=', $authId)->count();

                $readCount = $message->reads->whereNotNull('read_at')->where('user_id', '!=', $authId)->count();

                $status = '';

                if ($isMe) {
                    if ($participantsCount > 0 && $readCount === $participantsCount) {
                        $status = 'read';
                    } elseif ($participantsCount > 0 && $deliveredCount === $participantsCount) {
                        $status = 'delivered';
                    } else {
                        $status = 'sent';
                    }
                }
            @endphp

            <div class="message-row {{ $isMe ? 'me' : 'other' }}">
                <div class="message-bubble {{ $isMe ? 'me' : 'other' }}">

                    {{-- Sender --}}
                    <div class="message-sender">
                        {{ $isMe ? 'You' : $message->sender->name }}
                    </div>

                    {{-- Content --}}
                    @if ($message->content)
                        <div class="message-text">
                            {{ $message->content }}
                        </div>
                    @endif

                    {{-- Attachments --}}
                    @if ($message->attachments && count($message->attachments))
                        <div class="message-attachments">
                            @foreach ($message->attachments as $file)
                                @if (str_starts_with($file->mime_type, 'image/'))
                                    <img src="{{ asset('storage/' . $file->path) }}">
                                @else
                                    <a href="{{ asset('storage/' . $file->path) }}" target="_blank">
                                        📎 {{ $file->name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="message-footer">
                        <span class="message-time">
                            {{ $message->created_at->format('H:i') }}
                        </span>

                        @if ($isMe)
                            <span class="message-status {{ $status }}">
                                {{ $status === 'sent' ? '✓' : '✓✓' }}
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    {{-- Typing Indicator --}}
    <div class="typing-indicator" id="typingIndicator" style="display:none;">
        <span id="typingUserName"></span> is typing...
    </div>

    {{-- FOOTER --}}
    <div class="chat-footer">

        <div class="attachment-preview"></div>

        <form id="send-message-form" enctype="multipart/form-data">
            <input type="hidden" name="chat_key" value="{{ $activeChat->chat_key }}">

            <label class="attach-btn">
                📎
                <input type="file" name="attachments[]" multiple hidden>
            </label>

            <input type="text" name="content" placeholder="Type a message..." autocomplete="off">

            <button type="submit">➤</button>
        </form>
    </div>

    {{-- GLOBAL JS DATA --}}
    <script>
        // window.CURRENT_USER_ID = {{ auth()->id() }};
        window.CURRENT_USER_ID = 1;

        window.CHAT_KEY = "{{ $activeChat->chat_key }}";
    </script>
@endsection
