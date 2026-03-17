(function () {

    console.log('WELLBEING: metrics.js loaded');

    window.M = window.M || {};

    M.local_wellbeing_metrics_init = function (Y, config) {

        console.log('WELLBEING CONFIG →', config);

        let attempts = 0;

        function waitForForm() {

            const competencies =
    document.getElementById('id_competenciessection') ||
    document.querySelector('.mform fieldset:last-of-type');
     console.log('WELLBEING: competencies section →', competencies);

            if (!competencies) {
                attempts++;
                if (attempts < 20) {
                    return setTimeout(waitForForm, 300);
                }
                console.error('WELLBEING: competencies section not found');
                return;
            }

            if (document.getElementById('id_wellbeingmetricssection')) {
                return;
            }

            console.log('WELLBEING: Creating metrics section');

            const metrics = config.metrics
            ? config.metrics
                .replace(/<\/?p[^>]*>/g, '')   // remove <p> tags
                .replace(/&nbsp;/g, ' ')       // remove html spaces
                .split(',')
            : [];

            let metricsHTML = '';

            metrics.forEach(metric => {
                const cleanText = metric.replace(/<[^>]*>/g, '').trim();
                const cleanMetric = metric.trim();
                metricsHTML += `
                <div class="form-check mb-2 wellbeing-metric-item">
                    <label class="form-check-label">
                        <input type="checkbox"
                            class="form-check-input wellbeing-metric-checkbox"
                            name="wellbeing_metrics[]"
                            value="${cleanText}">
                        ${cleanText}
                    </label>
                </div>
                `;
            });

            const fieldset = document.createElement('fieldset');

            fieldset.id = 'id_wellbeingmetricssection';
            fieldset.className = 'clearfix collapsible collapsed';

            fieldset.innerHTML = `
<legend class="sr-only">Wellbeing Metrics</legend>

<div class="d-flex align-items-center mb-2">
<div class="position-relative d-flex ftoggler align-items-center mr-1">

<a data-toggle="collapse"
   href="#id_wellbeingmetricscontainer"
   role="button"
   aria-expanded="false"
   aria-controls="id_wellbeingmetricscontainer"
   class="btn btn-icon mr-1 icons-collapse-expand stretched-link fheader collapsed">

<span class="expanded-icon icon-no-margin p-2">
<i class="icon fa fa-chevron-down fa-fw"></i>
</span>

<span class="collapsed-icon icon-no-margin p-2">
<i class="icon fa fa-chevron-right fa-fw"></i>
</span>

<span class="sr-only">Wellbeing Metrics</span>

</a>

<h3 class="d-flex align-self-stretch align-items-center mb-0">
Wellbeing Metrics
</h3>

</div>
</div>

<div id="id_wellbeingmetricscontainer" class="fcontainer collapseable collapse">

${metricsHTML}

</div>
`;

            competencies.insertAdjacentElement('afterend', fieldset);

            // console.log('WELLBEING: Metrics UI ready');
            // Restore previously selected metrics
            const selected = config.selectedmetrics || [];

                if (selected.length > 0) {

                    document.querySelectorAll('.wellbeing-metric-checkbox').forEach(cb => {

                        const value = cb.value.trim();

                        if (selected.includes(value)) {
                            cb.checked = true;
                        }
                        // console.log("ALL CHECKBOX VALUES:",
                    // [...document.querySelectorAll('.wellbeing-metric-checkbox')].map(c=>c.value));

                    // console.log("SELECTED METRICS:", selected);

                    });

                    // console.log("WELLBEING: Restored selected metrics", selected);
                }

        }

        waitForForm();

    };

})();