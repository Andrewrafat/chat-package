/**
 * ======================================================
 * Andrew Chat - chat.js (FULL)
 * ======================================================
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ======================================================
     * Helpers
     * ====================================================== */
    const $ = (s, c = document) => c.querySelector(s);
    const $$ = (s, c = document) => [...c.querySelectorAll(s)];

    const csrfToken = $('meta[name="csrf-token"]')?.content;
    const chatKey   = $('meta[name="chat-key"]')?.content;
    const authId    = Number($('meta[name="auth-id"]')?.content || 0);

    /* ======================================================
     * Mobile UX (Sidebar + Back)
     * ====================================================== */
    const backBtn   = $('#chatBackBtn');
    const chatLinks = $$('.chat-item');

    chatLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                document.body.classList.add('chat-mobile-open');
            }
        });
    });

    backBtn?.addEventListener('click', () => {
        document.body.classList.remove('chat-mobile-open');
    });

    /* ======================================================
     * Message Form
     * ====================================================== */
    const form        = $('#send-message-form');
    if (!form) return;

    const textInput  = $('input[name="content"]', form);
    const fileInput  = $('input[type="file"]', form);
    const previewBox = $('.attachment-preview');
    const messagesBox = $('.chat-messages');

    /* ======================================================
     * Attachments Preview
     * ====================================================== */
    fileInput.addEventListener('change', () => {
        previewBox.innerHTML = '';

        if (!fileInput.files.length) {
            previewBox.style.display = 'none';
            return;
        }

        previewBox.style.display = 'flex';

        Array.from(fileInput.files).forEach((file, index) => {

            // Image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    previewBox.insertAdjacentHTML('beforeend', `
                        <div class="image-preview">
                            <img src="${e.target.result}">
                            <span class="preview-remove" data-index="${index}">×</span>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            }
            // File
            else {
                previewBox.insertAdjacentHTML('beforeend', `
                    <div class="file-preview">
                        📎 <span title="${file.name}">${file.name}</span>
                        <span class="preview-remove" data-index="${index}">×</span>
                    </div>
                `);
            }
        });
    });

    previewBox.addEventListener('click', e => {
        if (!e.target.classList.contains('preview-remove')) return;

        const removeIndex = Number(e.target.dataset.index);
        const dt = new DataTransfer();

        Array.from(fileInput.files).forEach((file, i) => {
            if (i !== removeIndex) dt.items.add(file);
        });

        fileInput.files = dt.files;
        fileInput.dispatchEvent(new Event('change'));
    });

    /* ======================================================
     * Send Message
     * ====================================================== */
    form.addEventListener('submit', async e => {
        e.preventDefault();

        if (!textInput.value.trim() && !fileInput.files.length) return;

        const formData = new FormData(form);

        try {
            const res = await fetch('/chat/messages', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!res.ok) throw new Error('Send failed');

            const { data } = await res.json();
            appendMessage(data);

            // Reset
            textInput.value = '';
            fileInput.value = '';
            previewBox.innerHTML = '';
            previewBox.style.display = 'none';
            messagesBox.scrollTop = messagesBox.scrollHeight;

        } catch (err) {
            console.error(err);
            alert('Failed to send message');
        }
    });

    /* ======================================================
     * Typing Indicator (SEND)
     * ====================================================== */
    let typingTimer = null;
    let isTyping = false;

    textInput.addEventListener('input', () => {
        if (!chatKey) return;

        if (!isTyping) {
            isTyping = true;
            sendTyping();
        }

        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            isTyping = false;
        }, 1200);
    });

    function sendTyping() {
        fetch(`/chat/conversations/${chatKey}/typing`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        }).catch(() => {});
    }

    /* ======================================================
     * Typing Indicator (UI)
     * ====================================================== */
    const typingIndicator = $('#typingIndicator');
    const typingUserSpan = typingIndicator?.querySelector('.typing-user');

    let typingHideTimer = null;

    function showTyping(username) {
        if (!typingIndicator) return;

        typingUserSpan.textContent = username;
        typingIndicator.style.display = 'block';

        clearTimeout(typingHideTimer);
        typingHideTimer = setTimeout(() => {
            typingIndicator.style.display = 'none';
        }, 1500);
    }

    /* ======================================================
     * Realtime (Laravel Echo)
     * ====================================================== */
    if (window.Echo && chatKey) {

        window.Echo.private(`chat.${chatKey}`)

            .listen('.user.typing', e => {
                if (!e?.user) return;
                if (Number(e.user.id) === authId) return;
                showTyping(e.user.name || 'Someone');
            })

            .listen('.message.sent', e => {
                if (Number(e.message.sender_id) === authId) return;
                appendMessage(e.message);
                messagesBox.scrollTop = messagesBox.scrollHeight;
            });
    }

    /* ======================================================
     * Append Message (Frontend)
     * ====================================================== */
    function appendMessage(message) {

        const attachments = message.attachments?.length
            ? `
                <div class="message-attachments">
                    ${message.attachments.map(file =>
                        file.mime?.startsWith('image/')
                            ? `<img src="${file.url}">`
                            : `<a href="${file.url}" target="_blank">📎 ${file.name}</a>`
                    ).join('')}
                </div>
              `
            : '';

        messagesBox.insertAdjacentHTML('beforeend', `
            <div class="message-row me">
                <div class="message-bubble me">
                    <div class="message-sender">You</div>
                    ${message.content ? `<div class="message-text">${message.content}</div>` : ''}
                    ${attachments}
                    <div class="message-footer">
                        <span class="message-time">${message.created_at_formatted ?? 'now'}</span>
                        <span class="message-status sent">✓</span>
                    </div>
                </div>
            </div>
        `);
    }

});
