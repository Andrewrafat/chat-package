<?php

use Illuminate\Support\Facades\Route;
use Andrew\ChatPackage\Http\Controllers\ConversationController;
use Andrew\ChatPackage\Http\Controllers\ConversationInviteController;
use Andrew\ChatPackage\Http\Controllers\ConversationLeaveController;
use Andrew\ChatPackage\Http\Controllers\ConversationListController;
use Andrew\ChatPackage\Http\Controllers\MessageController;
use Andrew\ChatPackage\Http\Controllers\ConversationTypingController;
use Andrew\ChatPackage\Http\Controllers\ConversationReadController;
use Andrew\ChatPackage\Http\Controllers\MessageStarController;

Route::middleware(['auth:sanctum'])->group(function () {

  /*
    |--------------------------------------------------------------------------
    | Conversations
    |--------------------------------------------------------------------------
    */

  // Create conversation (private / group)
  Route::post('/chat/conversations', [ConversationController::class, 'store']);
  Route::get('/chat/conversations', [ConversationController::class, 'index']);

  // List user conversations
  Route::get('/chat/users', [ConversationListController::class, 'index']);

  // Invite user (admin only)
  Route::post(
    '/chat/conversations/{chat_key}/invite',
    [ConversationInviteController::class, 'store']
  );

  // Leave conversation
  Route::post(
    '/chat/conversations/{chat_key}/leave',
    [ConversationLeaveController::class, 'store']
  );

  // Mark conversation as read
  Route::post(
    '/chat/conversations/{chat_key}/read',
    [ConversationReadController::class, 'store']
  );

  // Typing indicator (real-time only)
  Route::post(
    '/chat/conversations/{chat_key}/typing',
    [ConversationTypingController::class, 'store']
  );

  /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

  // Fetch messages by chat_key (recommended)
  Route::get(
    '/chat/conversations/{chat_key}/messages',
    [MessageController::class, 'index']
  );

  // (Optional – legacy support)
  Route::get(
    '/chat/conversations/{id}/messages',
    [MessageController::class, 'indexById']
  );

  // Send message
  Route::post('/chat/messages', [MessageController::class, 'store']);







  /*
|--------------------------------------------------------------------------
| Message Stars
|--------------------------------------------------------------------------
*/

Route::post('/chat/messages/{message}/star', [MessageStarController::class, 'store']);
Route::delete('/chat/messages/{message}/star', [MessageStarController::class, 'destroy']);

// List starred messages (optional: filter by chat_key)
Route::get(
    '/chat/messages/starred',
    [\Andrew\ChatPackage\Http\Controllers\MessageStarController::class, 'index']
);
});
