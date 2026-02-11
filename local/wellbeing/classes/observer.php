<?php
namespace local_wellbeing;

defined('MOODLE_INTERNAL') || die();

class observer {

    /**
     * Phase 1:
     * Track courses where wellbeing is enabled
     */
    public static function course_updated(\core\event\course_updated $event) {
        global $DB;

        $courseid = $event->objectid;

        debugging("Wellbeing: course_updated for course {$courseid}", DEBUG_DEVELOPER);

        if (!manager::is_enabled($courseid)) {
            debugging("Wellbeing: NOT enabled for course {$courseid}", DEBUG_DEVELOPER);
            return;
        }

        debugging("Wellbeing: ENABLED for course {$courseid}", DEBUG_DEVELOPER);

        if ($DB->record_exists('local_wellbeing_courses', ['courseid' => $courseid])) {

            debugging("Wellbeing: Course record exists, updating timemodified", DEBUG_DEVELOPER);

            $DB->set_field(
                'local_wellbeing_courses',
                'timemodified',
                time(),
                ['courseid' => $courseid]
            );

        } else {

            debugging("Wellbeing: Inserting new wellbeing course record", DEBUG_DEVELOPER);

            $record = new \stdClass();
            $record->courseid     = $courseid;
            $record->isenabled    = 1;
            $record->timecreated  = time();
            $record->timemodified = time();

            $DB->insert_record('local_wellbeing_courses', $record);
        }
    }

    /**
     * Phase 2:
     * Capture assignment submissions and generate metrics
     */
     public static function submission_created(
        \mod_assign\event\submission_created $event
    ) {
            debugging("WB: Observer → submission_created", DEBUG_DEVELOPER);
            service\analysis_service::process_submission($event->objectid);
        }

        public static function onlinetext_updated(
        \assignsubmission_onlinetext\event\submission_updated $event
    ) {
        debugging("WB: Online text updated triggered", DEBUG_DEVELOPER);

        $onlinetextid = $event->objectid; // This is id from assignsubmission_onlinetext

        global $DB;

        // Get the parent submission id (241 type id)
        $submission = $DB->get_record(
            'assignsubmission_onlinetext',
            ['id' => $onlinetextid],
            'submission',
            MUST_EXIST
        );

        $submissionid = $submission->submission;

        debugging("WB: assign_submission.id (from onlinetext) = {$submissionid}", DEBUG_DEVELOPER);

        \local_wellbeing\service\analysis_service::process_submission($submissionid);
    }
}
