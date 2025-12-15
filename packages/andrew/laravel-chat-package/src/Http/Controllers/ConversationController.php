<?php

namespace Andrew\ChatPackage\Http\Controllers;

use Andrew\ChatPackage\Services\ConversationCreateService;
use Andrew\ChatPackage\Services\ConversationListService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConversationController extends Controller
{
    public function __construct(
        protected ConversationCreateService $createService,
        protected ConversationListService $listService
    ) {}

    /**
     * GET /chat/conversations
     */
    public function index(Request $request)
    {
        $userId = (int) $request->user()->id;
        $limit  = (int) $request->get('limit', 20);

        $conversations = $this->listService->listForUser($userId, $limit);
        
        return response()->json([
            'data' => $conversations->getCollection()->map(function ($conversation) use ($userId) {

                $lastMessage = $conversation->messages->first();

                return [
                    'chat_key'           => $conversation->chat_key,
                    'type'               => $conversation->type,
                    'title'              => $conversation->title,
                    'participants_count' => $conversation->participants->count(),
                    'unread_count'       => $conversation->unreadCountFor($userId),
                    'last_message'       => $lastMessage ? [
                        'id'         => $lastMessage->id,
                        'content'    => $lastMessage->content,
                        'sender_id'  => $lastMessage->sender_id,
                        'created_at' => $lastMessage->created_at->toISOString(),
                    ] : null,
                    'updated_at' => optional($conversation->last_message_at)
                        ? $conversation->last_message_at->toISOString()
                        : $conversation->updated_at->toISOString(),
                ];
            })->values(),

            'meta' => [
                'current_page' => $conversations->currentPage(),
                'last_page'    => $conversations->lastPage(),
                'total'        => $conversations->total(),
            ]
        ]);
    }

    /**
     * POST /chat/conversations
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type'           => 'nullable|in:private,group',
            'title'          => 'nullable|string|max:255',
            'participants'   => 'required|array|min:1',
            'participants.*' => 'integer',
        ]);

        $conversation = $this->createService->create(
            creatorId: $request->user()->getAuthIdentifier(),
            participants: $data['participants'],
            type: $data['type'] ?? 'private',
            title: $data['title'] ?? null
        );

        return response()->json([
            'data' => $conversation,
        ], 201);
    }
}
