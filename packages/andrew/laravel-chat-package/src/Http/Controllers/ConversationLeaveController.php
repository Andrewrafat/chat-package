<?php

namespace Andrew\ChatPackage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Andrew\ChatPackage\Services\ConversationLeaveService;

class ConversationLeaveController extends Controller
{
    public function __construct(
        protected ConversationLeaveService $service
    ) {}

    public function store(Request $request, string $chatKey)
    {
        $result = $this->service->leave(
            chatKey: $chatKey,
            userId: $request->user()->getAuthIdentifier()
        );

        return response()->json([
            'data' => $result,
            'message' => 'You left the conversation successfully.',
        ]);
    }
}
