@extends('chat::chat.layout')

{{-- ================= SIDEBAR ================= --}}
@section('sidebar')
    <div class="sidebar-header">
        Andrew Chat
    </div>

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
        {{ $activeChat->title ?? 'Conversation' }}
    </div>

    {{-- MESSAGES --}}
    <div class="chat-messages">

        @foreach ($activeChat->messages()->oldest()->get() as $message)
            @php
                $authId = 1; // مؤقتًا – بعدين نخليه auth()->id()
                $isMe = $message->sender_id === $authId;

                // عدد المشاركين غيري
                $participantsCount = $activeChat->participants->where('user_id', '!=', $authId)->count();

                // اللي استلموا الرسالة (records موجودة)
                $deliveredCount = $message->reads->where('user_id', '!=', $authId)->count();

                // اللي قرا الرسالة
                $readCount = $message->reads->whereNotNull('read_at')->where('user_id', '!=', $authId)->count();

                // تحديد الحالة
                $status = '';
                if ($isMe) {
                    if ($participantsCount > 0 && $readCount === $participantsCount) {
                        $status = 'read'; // ✓✓ أزرق
                    } elseif ($participantsCount > 0 && $deliveredCount === $participantsCount) {
                        $status = 'delivered'; // ✓✓ رمادي
                    } else {
                        $status = 'sent'; // ✓
                    }
                }
            @endphp

            <div class="message-row {{ $isMe ? 'me' : 'other' }}">
                <div class="message-bubble {{ $isMe ? 'me' : 'other' }}">

                    {{-- الاسم --}}
                    <div class="message-sender">
                        {{ $isMe ? 'You' : $message->sender->name }}
                    </div>

                    {{-- المحتوى --}}
                    <div class="message-text">
                        {{ $message->content }}
                    </div>

                    {{-- الوقت + الحالة --}}
                    <div class="message-footer">
                        <span class="message-time">
                            {{ $message->created_at->format('H:i') }}
                        </span>

                        @if ($isMe)
                            <span class="message-status {{ $status }}">
                                @if ($status === 'sent')
                                    ✓
                                @elseif($status === 'delivered')
                                    ✓✓
                                @elseif($status === 'read')
                                    ✓✓
                                @endif
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        @endforeach


    </div>

    {{-- FOOTER --}}
    <div class="chat-footer">
        <form id="send-message-form">
            <input type="hidden" name="chat_key" value="{{ $activeChat->chat_key }}">

            <input type="text" name="content" placeholder="Type a message..." autocomplete="off">

            <button type="submit">➤</button>
        </form>
    </div>
@endsection


{{-- ================= STYLES ================= --}}
<style>
    .chat-header {
        padding: 15px;
        background: #075e54;
        color: #fff;
        font-weight: bold;
    }

    .chat-messages {
        flex: 1;
        padding: 20px;
        background: #e5ddd5;
        overflow-y: auto;
    }

    /* ROW */
    .message-row {
        display: flex;
        margin-bottom: 12px;
    }

    .message-row.me {
        justify-content: flex-end;
    }

    .message-row.other {
        justify-content: flex-start;
    }

    /* BUBBLE */
    .message-bubble {
        max-width: 60%;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 14px;
    }

    .message-bubble.me {
        background: #dcf8c6;
        border-top-right-radius: 0;
    }

    .message-bubble.other {
        background: #fff;
        border-top-left-radius: 0;
    }

    /* NAME */
    .message-sender {
        font-size: 12px;
        font-weight: bold;
        color: #075e54;
        margin-bottom: 4px;
    }

    /* TIME */
    .message-time {
        text-align: right;
        font-size: 11px;
        color: #666;
        margin-top: 4px;
    }

    /* FOOTER */
    .chat-footer {
        padding: 10px;
        background: #f0f0f0;
    }

    .chat-footer form {
        display: flex;
        gap: 8px;
    }

    .chat-footer input {
        flex: 1;
        padding: 10px;
        border-radius: 20px;
        border: 1px solid #ccc;
    }

    .chat-footer button {
        background: #075e54;
        color: #fff;
        border: none;
        border-radius: 50%;
        padding: 0 18px;
        cursor: pointer;
    }

    .message-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
    }

    .message-status {
        font-size: 12px;
        color: #777;
    }

    .message-status.read {
        color: #34b7f1;
        /* أزرق واتساب */
    }
</style>


{{-- ================= SCRIPT ================= --}}
<script>
    document.getElementById('send-message-form')
        ?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(e.target);

            await fetch("{{ url('/chat/messages') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            location.reload();
        });
</script>
