<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Andrew Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont;
            background: #f0f2f5;
        }

        .chat-app {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .chat-sidebar {
            width: 340px;
            background: #fff;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 15px;
            background: #075e54;
            color: #fff;
            font-weight: bold;
        }

        .chat-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            text-decoration: none;
            color: #000;
            border-bottom: 1px solid #f0f0f0;
        }

        .chat-item:hover {
            background: #f5f6f6;
        }

        .chat-item.active {
            background: #e9edef;
        }

        .chat-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #cfd8dc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .chat-info {
            flex: 1;
        }

        .chat-title {
            font-weight: 600;
        }

        .chat-last {
            font-size: 13px;
            color: #666;
        }

        .chat-time {
            font-size: 11px;
            color: #999;
            text-align: right;
        }

        /* Content */
        .chat-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .message-status {
            font-size: 12px;
            color: #999;
            /* ✓ و ✓✓ الرمادي */
        }

        .messageq-status.read {
            color: #34b7f1;
            /* ✓✓ الأزرق */
        }
    </style>
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

</body>

</html>
