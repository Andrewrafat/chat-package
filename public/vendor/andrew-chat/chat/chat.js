document.addEventListener('DOMContentLoaded', () => {

    /* =====================================================
     * ELEMENTS
     * ===================================================== */
    const form = document.getElementById('send-message-form');
    if (!form) return;

    const input        = form.querySelector('input[name="content"]');
    const fileInput    = form.querySelector('input[type="file"]');
    const previewBox   = document.querySelector('.attachment-preview');
    const messagesBox  = document.querySelector('.chat-messages');
    const csrfToken    = document.querySelector('meta[name="csrf-token"]')?.content;

    /* =====================================================
     * HELPERS
     * ===================================================== */
    function scrollToBottom() {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function resetForm() {
        input.value = '';
        fileInput.value = '';
        previewBox.innerHTML = '';
        previewBox.style.display = 'none';
    }

    function buildAttachmentPreview(file, index) {
        // IMAGE
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'image-preview';
                div.innerHTML = `
                    <img src="${e.target.result}">
                    <span class="preview-remove" data-index="${index}">×</span>
                `;
                previewBox.appendChild(div);
            };

            reader.readAsDataURL(file);
        }
        // FILE
        else {
            const div = document.createElement('div');
            div.className = 'file-preview';
            div.innerHTML = `
                📎 <span title="${file.name}">${file.name}</span>
                <span class="preview-remove" data-index="${index}">×</span>
            `;
            previewBox.appendChild(div);
        }
    }

    function buildMessageHTML(message) {
        const attachmentsHTML = message.attachments && message.attachments.length
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

        return `
            <div class="message-row me">
                <div class="message-bubble me">
                    <div class="message-sender">You</div>
                    ${message.content ? `<div class="message-text">${message.content}</div>` : ''}
                    ${attachmentsHTML}
                    <div class="message-footer">
                        <span class="message-time">${message.created_at_formatted ?? 'now'}</span>
                        <span class="message-status sent">✓</span>
                    </div>
                </div>
            </div>
        `;
    }

    /* =====================================================
     * FILE PREVIEW HANDLING
     * ===================================================== */
    fileInput.addEventListener('change', () => {
        previewBox.innerHTML = '';

        if (fileInput.files.length === 0) {
            previewBox.style.display = 'none';
            return;
        }

        previewBox.style.display = 'flex';

        Array.from(fileInput.files).forEach((file, index) => {
            buildAttachmentPreview(file, index);
        });
    });

    previewBox.addEventListener('click', (e) => {
        if (!e.target.classList.contains('preview-remove')) return;

        const removeIndex = parseInt(e.target.dataset.index, 10);
        const dt = new DataTransfer();

        Array.from(fileInput.files).forEach((file, index) => {
            if (index !== removeIndex) {
                dt.items.add(file);
            }
        });

        fileInput.files = dt.files;
        fileInput.dispatchEvent(new Event('change'));
    });

    /* =====================================================
     * SEND MESSAGE
     * ===================================================== */
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!input.value.trim() && fileInput.files.length === 0) return;

        const formData = new FormData(form);

        try {
            const response = await fetch('/chat/messages', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!response.ok) {
                console.error('Failed to send message');
                return;
            }

            const result = await response.json();
            const message = result.data;

            messagesBox.insertAdjacentHTML(
                'beforeend',
                buildMessageHTML(message)
            );

            resetForm();
            scrollToBottom();

        } catch (error) {
            console.error('Chat error:', error);
        }
    });

    /* =====================================================
     * INIT
     * ===================================================== */
    scrollToBottom();

});
