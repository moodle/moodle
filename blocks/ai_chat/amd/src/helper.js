/**
 * Copy ai reply to clipboard.
 * @param {*} element
 */
export const copyToClipboard = (element) => {

    // Find the adjacent text container.
    const textElement = element.nextElementSibling;

    // Get the text content.
    const textToCopy = textElement.innerText || textElement.textContent;

    // Copy to clipboard using the Clipboard API.
    navigator.clipboard.writeText(textToCopy);

    // Briefly show toast.
    const toast = element.previousElementSibling;
    toast.style.visibility = 'visible';
    setTimeout(() => {
       toast.style.visibility = 'hidden';
    }, 750);

};

/**
 * Attach copy listener to all elements.
 */
export const attachCopyListenerLast = () => {
    const elements = document.querySelectorAll(".block_ai_chat_modal .copy");
    const last = elements[elements.length - 1];
    last.addEventListener('click', function() {
        copyToClipboard(last);
    });
};


/**
 * Focus textarea.
 */
export const focustextarea = () => {
    const textarea = document.getElementById('block_ai_chat-input-id');
    textarea.focus();
};


/**
 * Scroll to bottom of modal body.
 */
export const scrollToBottom = () => {
    const modalContent = document.querySelector('.block_ai_chat_modal .modal-body .block_ai_chat-output-wrapper');
    modalContent.scrollTop = modalContent.scrollHeight;
};


/**
 * Escape html.
 * @param {*} str
 */
export const escapeHTML = (str) => {
    if (str === null || str === undefined) {
        return '';
    }
    const escapeMap = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
        '`': '&#x60;',
        '/': '&#x2F;',
    };

    return String(str).replace(/[&<>"'`/]/g, function(match) {
        return escapeMap[match];
    });
};

/**
 * Hash function to get a hash of a string.
 *
 * @param {string} stringToHash the string to hash
 * @returns {Promise<string>} the promise containing a hex representation of the string encoded by SHA-256
 */
export const hash = async(stringToHash) => {
    const encoder = new TextEncoder();
    const data = encoder.encode(stringToHash);
    const hashAsArrayBuffer = await window.crypto.subtle.digest("SHA-256", data);
    const uint8ViewOfHash = new Uint8Array(hashAsArrayBuffer);
    return Array.from(uint8ViewOfHash)
        .map((b) => b.toString(16).padStart(2, "0"))
        .join("");
};
