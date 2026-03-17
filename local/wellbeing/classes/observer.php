<?php
namespace local_wellbeing;

defined('MOODLE_INTERNAL') || die();

class observer
{

    /**
     * Phase 1:
     * Track courses where wellbeing is enabled
     */
   public static function course_updated(\core\event\course_updated $event)
{
    global $DB;

    $courseid = $event->objectid;

    //debugging("Wellbeing: course_updated triggered for course {$courseid}", DEBUG_DEVELOPER);

    if (!manager::is_enabled($courseid)) {
        //debugging("Wellbeing: NOT enabled for course {$courseid}", DEBUG_DEVELOPER);
        return;
    }

    //debugging("Wellbeing: ENABLED for course {$courseid}", DEBUG_DEVELOPER);

    /*
     * Fetch custom field values
     */

    $sql = "
        SELECT f.shortname, d.value
        FROM {customfield_data} d
        JOIN {customfield_field} f ON f.id = d.fieldid
        WHERE d.instanceid = :courseid
        AND f.shortname IN ('metrics_name_json','metrics_prompt')
    ";

    $fields = $DB->get_records_sql($sql, ['courseid' => $courseid]);

    $metricsjson = '';
    $prompt = '';

    foreach ($fields as $field) {

        //debugging("Wellbeing: Custom field {$field->shortname} = {$field->value}", DEBUG_DEVELOPER);

        if ($field->shortname === 'metrics_name_json') {
            $metricsjson = $field->value;
        }

        if ($field->shortname === 'metrics_prompt') {
            $prompt = $field->value;
        }
    }

    //debugging("Wellbeing: Metrics JSON captured = {$metricsjson}", DEBUG_DEVELOPER);
    //debugging("Wellbeing: Prompt captured = {$prompt}", DEBUG_DEVELOPER);

    /*
     * Insert or update local table
     */

    if ($DB->record_exists('local_wellbeing_courses', ['courseid' => $courseid])) {

        //debugging("Wellbeing: Updating existing wellbeing course record", DEBUG_DEVELOPER);

        $record = $DB->get_record('local_wellbeing_courses', ['courseid' => $courseid]);

        $record->metrics_name_json = $metricsjson;
        $record->metrics_prompt = $prompt;
        $record->timemodified = time();

        $DB->update_record('local_wellbeing_courses', $record);

    } else {

        //debugging("Wellbeing: Inserting new wellbeing course record", DEBUG_DEVELOPER);

        $record = new \stdClass();
        $record->courseid = $courseid;
        $record->isenabled = 1;
        $record->metrics_name_json = $metricsjson;
        $record->metrics_prompt = $prompt;
        $record->timecreated = time();
        $record->timemodified = time();

        $DB->insert_record('local_wellbeing_courses', $record);
    }

    //debugging("Wellbeing: Metrics sync completed", DEBUG_DEVELOPER);
}

    /**
     * Phase 2:
     * Capture assignment submissions and generate metrics
     */
    public static function submission_created(
        \mod_assign\event\submission_created $event
    ) {
         global $DB;
        $onlinetextid = $event->objectid;

    //debugging("WB: onlinetext.id = {$onlinetextid}", DEBUG_DEVELOPER);

    // Fetch parent submission
    $record = $DB->get_record(
        'assignsubmission_onlinetext',
        ['id' => $onlinetextid],
        'submission',
        IGNORE_MISSING
    );

    if (!$record) {
        //debugging("WB ERROR: onlinetext record not found for id {$onlinetextid}", DEBUG_DEVELOPER);
        return;
    }

    $submissionid = (int)$record->submission;
        //debugging("WB: Observer → submission_created", DEBUG_DEVELOPER);
        service\analysis_service::process_submission($submissionid);
    }
    

public static function onlinetext_updated(
    \assignsubmission_onlinetext\event\submission_updated $event
) {
    global $DB;

    //debugging("====================================", DEBUG_DEVELOPER);
    // debugging("WB: Online text updated triggered", DEBUG_DEVELOPER);

    $onlinetextid = $event->objectid;

    //debugging("WB: onlinetext.id = {$onlinetextid}", DEBUG_DEVELOPER);

    // Fetch parent submission
    $record = $DB->get_record(
        'assignsubmission_onlinetext',
        ['id' => $onlinetextid],
        'submission',
        IGNORE_MISSING
    );

    if (!$record) {
        //debugging("WB ERROR: onlinetext record not found for id {$onlinetextid}", DEBUG_DEVELOPER);
        return;
    }

    $submissionid = (int)$record->submission;

    //debugging("WB: assign_submission.id = {$submissionid}", DEBUG_DEVELOPER);

    // Call processing logic
    \local_wellbeing\service\analysis_service::process_submission($submissionid);

    //debugging("WB: Observer DONE ✔", DEBUG_DEVELOPER);
    //debugging("====================================", DEBUG_DEVELOPER);
}

public static function debug_all_events(\core\event\base $event) {

    //debugging("WB EVENT FIRED: " . get_class($event), DEBUG_DEVELOPER);

    $data = $event->get_data();
    //debugging("WB EVENT DATA: " . print_r($data, true), DEBUG_DEVELOPER);
}

public static function submission_removed(
    \mod_assign\event\submission_removed $event
) {
    global $DB;

    //debugging("WB: submission_removed observer triggered", DEBUG_DEVELOPER);

    $data = $event->get_data();
    //debugging("WB: Event data = " . print_r($data, true), DEBUG_DEVELOPER);

    // submissionid comes from event->other
    $submissionid = $data['other']['submissionid'] ?? null;

    if (!$submissionid) {
        //debugging("WB: submissionid missing — aborting delete", DEBUG_DEVELOPER);
        return;
    }

    //debugging("WB: Deleting metrics for submissionid = {$submissionid}", DEBUG_DEVELOPER);

    $before = $DB->count_records('local_wellbeing_metrics', [
        'submissionid' => $submissionid
    ]);

    //debugging("WB: Metrics before delete = {$before}", DEBUG_DEVELOPER);

    $DB->delete_records('local_wellbeing_metrics', [
        'submissionid' => $submissionid
    ]);

    $after = $DB->count_records('local_wellbeing_metrics', [
        'submissionid' => $submissionid
    ]);

    //debugging("WB: Metrics after delete = {$after}", DEBUG_DEVELOPER);
}
}
