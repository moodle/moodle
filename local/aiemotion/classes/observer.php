<?php
namespace local_aiemotion;

defined('MOODLE_INTERNAL') || die();

class observer {

    public static function assign_updated(\core\event\course_module_updated $event) {
        global $DB;

        error_log('AIEMOTION: course_module_updated EVENT TRIGGERED');

        // Ensure POST data exists
        if (empty($_POST['instance'])) {
            error_log('AIEMOTION: No instance in POST');
            return;
        }

        // Get CM
        $cmid = $event->contextinstanceid;
        $cm = get_coursemodule_from_id('assign', $cmid, 0, false, MUST_EXIST);

        if ($cm->modname !== 'assign') {
            return;
        }

        $assignmentid = (int)$_POST['instance'];
        $enable = !empty($_POST['enableaifeedback']) ? 1 : 0;
        $prompt = $_POST['aifeedbackprompt'] ?? null;
        $time = time();

        error_log("AIEMOTION: assignmentid=$assignmentid enable=$enable prompt=$prompt");

        // Check existing record
        $record = $DB->get_record('local_aiemotion_assign', [
            'assignmentid' => $assignmentid
        ]);

        if ($record) {
            error_log('AIEMOTION: Updating record');

            $record->enableaifeedback = $enable;
            $record->aifeedbackprompt = $prompt;
            $record->timemodified = $time;

            $DB->update_record('local_aiemotion_assign', $record);
        } else {
            error_log('AIEMOTION: Inserting record');

            $record = (object)[
                'assignmentid'     => $assignmentid,
                'enableaifeedback' => $enable,
                'aifeedbackprompt' => $prompt,
                'timecreated'      => $time,
                'timemodified'     => $time,
            ];

            $DB->insert_record('local_aiemotion_assign', $record);
        }

        error_log('AIEMOTION: DB SAVE COMPLETE');
    }
}
