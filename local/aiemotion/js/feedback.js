(function () {

    console.log('AIEMOTION: feedback.js loaded');

    window.M = window.M || {};

    // ✅ MUST match js_init_call signature
    M.local_aiemotion_feedback_init = function (Y, config) {

        console.log('AIEMOTION: feedback js_init_call executed');
        console.log('AIEMOTION: config received →', config);

        window.AIEMOTION_FEEDBACK_CONFIG = config;
    };
    
    function editorText() {
        let text = '';

        if (window.tinyMCE && tinyMCE.activeEditor) {
            text = tinyMCE.activeEditor.getContent({ format: 'text' }).trim();
            console.log('AIEMOTION: Text from TinyMCE →', text);
            return text;
        }

        const editor = document.getElementById('id_onlinetext_editoreditable');
        if (editor) {
            text = editor.innerText.replace(/\u00A0/g, ' ').trim();
            console.log('AIEMOTION: Text from contenteditable →', text);
            return text;
        }

        console.warn('AIEMOTION: No editor found, empty text returned');
        return '';
    }

    function hasSubmission() {
        return editorText().length > 0 ||
               document.querySelector('.fp-hascontextmenu');
    }
    function showModal(content) {
        require(['core/modal_factory'], function(ModalFactory) {
            ModalFactory.create({
                title: 'AI Feedback',
                body: `<div style="white-space: pre-wrap;">${content}</div>`
            }).then(function(modal) {
                modal.show();
            });
        });
    }

    function injectButton() {

        if (!window.AIEMOTION_FEEDBACK_CONFIG) {
            console.log('AIEMOTION: config NOT available yet');
            return;
        }

        console.log(
            'AIEMOTION: enableaifeedback value =',
            window.AIEMOTION_FEEDBACK_CONFIG.enableaifeedback
        );

        if (Number(window.AIEMOTION_FEEDBACK_CONFIG.enableaifeedback) !== 1) {
            const existing = document.getElementById('aiemotion-btn');
            if (existing) existing.remove();
            console.log('AIEMOTION: feedback disabled → button hidden');
            return;
        }

        if (document.getElementById('aiemotion-btn')) {
            return;
        }

        if (!hasSubmission()) {
            console.log('AIEMOTION: no submission yet');
            return;
        }

        const page = document.getElementById('page-content');
        if (!page) return;

        const btn = document.createElement('button');
        btn.id = 'aiemotion-btn';
        btn.className = 'btn btn-primary';
        btn.innerText = 'AI Feedback';
        btn.style.marginTop = '15px';

        page.appendChild(btn);

        btn.onclick = () => {
        btn.disabled = true;
        btn.innerText = 'Analyzing...';
        const liveText = editorText();
        console.log('AIEMOTION: FINAL text being sent →', liveText);

        console.log('AIEMOTION: LIVE editor text →', liveText);

        fetch(M.cfg.wwwroot + '/local/aiemotion/ajax/feedback.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
            text: liveText,
            cmid: M.cfg.contextid,
            prompt:AIEMOTION_FEEDBACK_CONFIG.aifeedbackprompt,
            sesskey: M.cfg.sesskey  
        })
        })
        .then(r => r.json())
        .then(d => {
            console.log('AI RAW RESPONSE:', d.raw);  
            showModal(d.feedback);
        })
        .catch(() => {
            showModal('Error generating feedback.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = 'AI Feedback';
        });
        };

        console.log('AIEMOTION: feedback button injected');
    }

    const observer = new MutationObserver(injectButton);

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

})();
