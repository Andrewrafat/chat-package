let typingTimeout = null;

export function initTyping(chatKey, userId) {
    const input = document.getElementById('chat-input');
    const indicator = document.getElementById('typing-indicator');

    // Emit typing
    input.addEventListener('input', () => {
        fetch(`/chat/conversations/${chatKey}/typing`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrf,
                'Accept': 'application/json'
            }
        });

        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => {
            indicator.innerText = '';
        }, 2000);
    });

    // Listen realtime
    Echo.private(`chat.${chatKey}`)
        .listen('.user.typing', (e) => {
            if (e.user.id !== userId) {
                indicator.innerText = 'typing...';
                clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    indicator.innerText = '';
                }, 2000);
            }
        });
}
