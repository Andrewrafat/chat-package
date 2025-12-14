<?php

namespace Andrew\ChatPackage\Http\Controllers;

use Andrew\ChatPackage\Services\ConversationCreateService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Andrew\ChatPackage\Services\ConversationService;

class ConversationController extends Controller
{
    public function __construct(
        protected ConversationCreateService $service
    ) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'           => 'nullable|in:private,group',
            'title'          => 'nullable|string|max:255',
            'participants'   => 'required|array|min:1',
            'participants.*' => 'integer',
        ]);

        $conversation = $this->service->create(
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
