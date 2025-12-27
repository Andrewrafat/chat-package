<?php

namespace Andrew\ChatPackage\Services;

use Andrew\ChatPackage\Models\Conversation;
use Andrew\ChatPackage\Models\Message;
use Andrew\ChatPackage\Models\MessageAttachment;
use Andrew\ChatPackage\Events\MessageSent;
use Andrew\ChatPackage\Support\Broadcast;
use Illuminate\Http\UploadedFile;

class MessageService
{
    /**
     * Send a message (text / attachments / mixed)
     */
    public function send(
        string $chatKey,
        int $userId,
        ?string $content = null,
        array $attachments = []
    ): array {

        // --------------------------------------------------
        // 1️⃣ Ensure user is a participant in conversation
        // --------------------------------------------------
        $conversation = Conversation::query()
            ->where('chat_key', $chatKey)
            ->whereHas('participants', fn($q) => $q->where('user_id', $userId))
            ->firstOrFail();

        // --------------------------------------------------
        // 2️⃣ Prevent empty messages
        // --------------------------------------------------
        abort_if(
            empty($content) && empty($attachments),
            422,
            'Message must contain text or attachment.'
        );

        // --------------------------------------------------
        // 3️⃣ Determine message type
        // --------------------------------------------------
        $type = !empty($attachments) ? 'attachment' : 'text';

        // --------------------------------------------------
        // 4️⃣ Create message
        // --------------------------------------------------
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $userId,
            'content'         => $content,
            'type'            => $type,
        ]);

        // --------------------------------------------------
        // 5️⃣ Handle attachments (if any)
        // --------------------------------------------------
        $attachmentsPayload = [];

        /** @var UploadedFile $file */
        foreach ($attachments as $file) {

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('chat-attachments', 'public');

            $attachment = MessageAttachment::create([
                'message_id'    => $message->id,
                'disk'          => 'public',
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
            ]);

            $attachmentsPayload[] = [
                'id'   => $attachment->id,
                'name' => $attachment->original_name,
                'mime' => $attachment->mime_type,
                'size' => $attachment->size,
                'url'  => null, // 🔐 served via secure endpoint later
            ];
        }

        // --------------------------------------------------
        // 6️⃣ Build response payload
        // --------------------------------------------------
        $payload = [
            'id'          => $message->id,
            'type'        => $message->type,
            'content'     => $message->content,
            'attachments' => $attachmentsPayload,
            'sender'      => [
                'id' => $userId,
            ],
            'created_at'  => $message->created_at->toISOString(),
        ];

        // --------------------------------------------------
        // 7️⃣ Optional realtime broadcast (driver-agnostic)
        // --------------------------------------------------
        $message->load(['sender', 'attachments']); // حسب موديلاتك

        Broadcast::dispatch(new MessageSent(
            chatKey: $conversation->chat_key,
            message: [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender' => [
                    'id' => $message->sender?->id,
                    'name' => $message->sender?->name,
                ],
                'content' => $message->content,
                'created_at_formatted' => optional($message->created_at)->format('H:i'),
                'attachments' => collect($message->attachments ?? [])->map(function ($a) {
                    return [
                        'name' => $a->name ?? null,
                        'mime' => $a->mime_type ?? null,
                        // مهم: url جاهز للاستخدام في الـ JS
                        'url'  => isset($a->path) ? asset('storage/' . $a->path) : ($a->url ?? null),
                    ];
                })->values()->toArray(),
            ]
        ));
        return $payload;
    }

    /**
     * Star a message
     */
    public function star(int $messageId, int $userId): void
    {
        Message::query()
            ->findOrFail($messageId)
            ->stars()
            ->syncWithoutDetaching([$userId]);
    }

    /**
     * Unstar a message
     */
    public function unstar(int $messageId, int $userId): void
    {
        Message::query()
            ->findOrFail($messageId)
            ->stars()
            ->detach($userId);
    }
}
