<?php

use Illuminate\Support\Facades\Route;
use Andrew\ChatPackage\Http\Controllers\Ui\ChatUiController;

Route::middleware(['web' ])
    ->prefix('chat')
    ->group(function () {

        // 📱 WhatsApp-like layout (all conversations)
        Route::get('/', [ChatUiController::class, 'index'])
            ->name('chat.ui.index');

        // 💬 Open specific chat
        Route::get('/{chatKey}', [ChatUiController::class, 'show'])
            ->name('chat.ui.show');
    });
