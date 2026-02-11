<?php
defined('MOODLE_INTERNAL') || die();

function local_aiemotion_before_footer() {
    global $PAGE, $DB;

    /*
     * ======================================================
     * ASSIGNMENT VIEW / SUBMISSION PAGES → feedback.js
     * ======================================================
     */
    if (
        $PAGE->pagetype === 'mod-assign-view' ||
        $PAGE->pagetype === 'mod-assign-submission' ||
        $PAGE->pagetype === 'mod-assign-editsubmission'
    ) {
        error_log('AIEMOTION: Feedback page detected → ' . $PAGE->pagetype);

        $config = [
            'enableaifeedback' => 0,
            'aifeedbackprompt' => ''
        ];

        // cmid always comes as ?id=
        $cmid = optional_param('id', 0, PARAM_INT);
        error_log('AIEMOTION: editsubmission cmid = ' . $cmid);

        if ($cmid) {
            $cm = get_coursemodule_from_id('assign', $cmid, 0, false, IGNORE_MISSING);

            if ($cm) {
                error_log('AIEMOTION: assignmentid = ' . $cm->instance);

                $record = $DB->get_record(
                    'local_aiemotion_assign',
                    ['assignmentid' => $cm->instance],
                    'enableaifeedback, aifeedbackprompt',
                    IGNORE_MISSING
                );

                if ($record) {
                    $config['enableaifeedback'] = (int)$record->enableaifeedback;
                    $config['aifeedbackprompt'] = $record->aifeedbackprompt ?? '';
                    error_log('AIEMOTION: FEEDBACK CONFIG FOUND → ' . json_encode($config));
                } else {
                    error_log('AIEMOTION: FEEDBACK CONFIG NOT FOUND');
                }
            }
        }

        // Load JS
        $PAGE->requires->js(
            new moodle_url('/local/aiemotion/js/feedback.js')
        );

        // ✅ PASS CONFIG JUST LIKE aibutton.js
        $PAGE->requires->js_init_call(
            'M.local_aiemotion_feedback_init',
            [$config]
        );

        error_log('AIEMOTION: feedback.js config sent');
        return;
    }


    /*
     * ======================================================
     * ASSIGNMENT EDIT PAGE → aibutton.js (unchanged)
     * ======================================================
     */
    if ($PAGE->pagetype === 'mod-assign-mod') {
        error_log('AIEMOTION: Assignment edit page detected → ' . $PAGE->pagetype);

        $config = [
            'enableaifeedback' => 0,
            'aifeedbackprompt' => ''
        ];

        $cmid = optional_param('update', 0, PARAM_INT);

        if ($cmid) {
            $cm = get_coursemodule_from_id('assign', $cmid, 0, false, IGNORE_MISSING);

            if ($cm) {
                $assignmentid = $cm->instance;

                $record = $DB->get_record(
                    'local_aiemotion_assign',
                    ['assignmentid' => $assignmentid],
                    'enableaifeedback, aifeedbackprompt',
                    IGNORE_MISSING
                );

                if ($record) {
                    $config['enableaifeedback'] = (int)$record->enableaifeedback;
                    $config['aifeedbackprompt'] = $record->aifeedbackprompt ?? '';
                }
            }
        }

        $PAGE->requires->js(
            new moodle_url('/local/aiemotion/js/aibutton.js')
        );

        $PAGE->requires->js_init_call(
            'M.local_aiemotion_init',
            [$config]
        );
    }
}
