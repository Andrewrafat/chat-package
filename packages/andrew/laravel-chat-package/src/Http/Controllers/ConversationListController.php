<?php

namespace Andrew\ChatPackage\Http\Controllers;

use Andrew\ChatPackage\Services\ConversationQueryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Andrew\ChatPackage\Services\ConversationService;

class ConversationListController extends Controller
{
    public function __construct(
        protected ConversationQueryService $conversationService
    ) {}

    public function index(Request $request)
    {
        $data = $this->conversationService->listForUser(
            userId: $request->user()->getAuthIdentifier()
        );

        return response()->json([
            'data' => $data
        ]);
    }
}
