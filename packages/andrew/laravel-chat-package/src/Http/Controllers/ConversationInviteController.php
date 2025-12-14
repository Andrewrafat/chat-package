<?php

namespace Andrew\ChatPackage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Andrew\ChatPackage\Services\ConversationInviteService;

class ConversationInviteController extends Controller
{
    public function __construct(
        protected ConversationInviteService $service
    ) {}

    public function store(Request $request, string $chatKey)
    {
        $data = $request->validate([
            'user_id' => 'required|integer',
        ]);

        $result = $this->service->invite(
            chatKey: $chatKey,
            inviterId: $request->user()->getAuthIdentifier(),
            invitedUserId: $data['user_id']
        );

        return response()->json([
            'data'    => $result,
            'message' => 'User invited successfully.',
        ], 201);
    }
}
