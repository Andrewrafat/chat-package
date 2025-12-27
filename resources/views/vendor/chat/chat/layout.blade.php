<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Andrew Chat</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Chat Styles --}}
    <link rel="stylesheet" href="{{ asset('vendor/andrew-chat/chat/chat.css') }}">
</head>

<body>

    <div class="chat-app">
        <aside class="chat-sidebar">
            @yield('sidebar')
        </aside>

        <main class="chat-content">
            @yield('content')
        </main>
    </div>

    {{-- Chat Script --}}
    <script src="{{ asset('vendor/andrew-chat/chat/chat.js') }}" defer></script>

</body>

</html>
