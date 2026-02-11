(function () {

    console.log('AIEMOTION: aibutton.js loaded');

    window.M = window.M || {};

    M.local_aiemotion_init = function (Y, config) {

        console.log('AIEMOTION: INIT CALLED WITH CONFIG →', config);

        let attempts = 0;

        function waitForFormAndInsert() {

            const form = document.querySelector('form.mform');
            const competencies = document.getElementById('id_competenciessection');

            if (!form || !competencies) {
                attempts++;
                if (attempts < 20) {
                    return setTimeout(waitForFormAndInsert, 300);
                }
                console.error('AIEMOTION: Form or competencies section not found');
                return;
            }

            if (document.getElementById('id_aifeedbacksection')) {
                console.warn('AIEMOTION: AI section already exists');
                return;
            }

            console.log('AIEMOTION: Inserting AI collapsible fieldset');

            const fieldset = document.createElement('fieldset');
            fieldset.id = 'id_aifeedbacksection';
            fieldset.className = 'clearfix collapsible collapsed';

            fieldset.innerHTML = `
<legend class="sr-only">AI Feedback</legend>

<div class="d-flex align-items-center mb-2">
    <div class="position-relative d-flex ftoggler align-items-center mr-1">
        <a data-toggle="collapse"
           href="#id_aifeedbacksectioncontainer"
           role="button"
           aria-expanded="false"
           aria-controls="id_aifeedbacksectioncontainer"
           class="btn btn-icon mr-1 icons-collapse-expand stretched-link fheader collapsed">

            <span class="expanded-icon icon-no-margin p-2" title="Collapse">
                <i class="icon fa fa-chevron-down fa-fw"></i>
            </span>

            <span class="collapsed-icon icon-no-margin p-2" title="Expand">
                <span class="dir-rtl-hide">
                    <i class="icon fa fa-chevron-right fa-fw"></i>
                </span>
                <span class="dir-ltr-hide">
                    <i class="icon fa fa-chevron-left fa-fw"></i>
                </span>
            </span>

            <span class="sr-only">AI Feedback</span>
        </a>

        <h3 class="d-flex align-self-stretch align-items-center mb-0" aria-hidden="true">
            AI Feedback
        </h3>
    </div>
</div>

<div id="id_aifeedbacksectioncontainer" class="fcontainer collapseable collapse">

    <div class="form-group row fitem">
        <div class="col-md-3 col-form-label">
            <label for="id_enableaifeedback">
                Enable AI feedback
            </label>
        </div>

        <div class="col-md-9 checkbox">
            <div class="form-check d-flex">
                <input type="hidden" name="enableaifeedback" value="0">
                <input type="checkbox"
                       class="form-check-input"
                       name="enableaifeedback"
                       id="id_enableaifeedback"
                       value="1">
            </div>
        </div>
    </div>

    <div class="form-group row fitem" id="ai_prompt_row">
        <div class="col-md-3 col-form-label">
            <label for="id_aifeedbackprompt">
                AI prompt
            </label>
        </div>

        <div class="col-md-9">
            <textarea
                name="aifeedbackprompt"
                id="id_aifeedbackprompt"
                class="form-control"
                rows="4"
                placeholder="Enter prompt to guide AI feedback..."></textarea>

            Default prompt:
            Analyze the following student assignment and give short, empathetic feedback. Detect emotional, academic, and motivational tone.
            Avoid grading.
        </div>
    </div>

</div>
`;

            competencies.insertAdjacentElement('afterend', fieldset);

            const checkbox = document.getElementById('id_enableaifeedback');
            const prompt = document.getElementById('id_aifeedbackprompt');
            const promptRow = document.getElementById('ai_prompt_row');

            console.log('AIEMOTION: Applying DB values');

            checkbox.checked = Number(config.enableaifeedback) === 1;
            prompt.value = config.aifeedbackprompt || '';

            console.log('AIEMOTION: checkbox →', checkbox.checked);
            console.log('AIEMOTION: prompt →', prompt.value);

            promptRow.style.display = checkbox.checked ? '' : 'none';
            prompt.disabled = !checkbox.checked;

            checkbox.addEventListener('change', () => {
                promptRow.style.display = checkbox.checked ? '' : 'none';
                prompt.disabled = !checkbox.checked;
            });

            console.log('AIEMOTION: UI READY (native Moodle style)');
        }

        waitForFormAndInsert();
    };

})();
