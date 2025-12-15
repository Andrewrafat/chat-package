<?php

namespace Andrew\ChatPackage\Http\Controllers;

use Andrew\ChatPackage\Events\MessageStarToggled;
use Andrew\ChatPackage\Models\Message;
use Andrew\ChatPackage\Services\StarMessageService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MessageStarController extends Controller
{
    public function __construct(
        protected StarMessageService $service
    ) {}

    /**
     * POST /chat/messages/{message}/star
     */
    public function store(Request $request, $message)
    {

        $message = Message::query()->findOrFail($message);
        $userId = (int) $request->user()->id;
        $this->authorizeStarAction($message, $userId);

        $this->service->star($message->id, $userId);

        event(new MessageStarToggled(
            chatKey: $message->conversation->chat_key,
            payload: [
                'message_id'  => $message->id,
                'user_id'     => $userId,
                'starred'     => true,
                'created_at'  => now()->toISOString(),
            ]
        ));

        return response()->json([
            'message_id' => $message->id,
            'starred'    => true,
        ]);
    }

    /**
     * DELETE /chat/messages/{message}/star
     */
    public function destroy(Request $request, $message)
    {
        $message = Message::query()->findOrFail($message);
        $userId = (int) $request->user()->id;

        $this->authorizeStarAction($message, $userId);

        $this->service->unstar($message->id, $userId);

        event(new MessageStarToggled(
            chatKey: $message->conversation->chat_key,
            payload: [
                'message_id'  => $message->id,
                'user_id'     => $userId,
                'starred'     => false,
                'created_at'  => now()->toISOString(),
            ]
        ));

        return response()->json([
            'message_id' => $message->id,
            'starred'    => false,
        ]);
    }

    /**
     * GET /chat/messages/starred?chat_key=...&limit=50
     */
    public function index(Request $request)
    {
        $userId = (int) $request->user()->id;
        $limit  = (int) ($request->get('limit', 50));
        $chatKey = $request->get('chat_key');

        $messages = $this->service->listStarredForUser($userId, $limit, $chatKey);

        // Optional: return minimal payload
        return response()->json([
            'data' => $messages->map(function (Message $m) use ($userId) {
                return [
                    'id'         => $m->id,
                    'content'    => $m->content,
                    'type'       => $m->type ?? 'text',
                    'chat_key'   => $m->conversation?->chat_key,
                    'starred'    => true,
                    'created_at' => $m->created_at?->toISOString(),
                ];
            })->values(),
        ]);
    }

    /**
     * ✅ Ensure the user is a participant in the conversation that owns this message.
     * (No dev needs to add policies.)
     */
    protected function authorizeStarAction(Message $message, int $userId): void
    {
        $message->loadMissing('conversation.participants');

        $conversation = $message->conversation;

        abort_unless($conversation, 404, 'Conversation not found for this message.');

        abort_unless(
            $conversation->participants->contains('user_id', $userId),
            403,
            'You are not a participant of this conversation.'
        );
    }
}
