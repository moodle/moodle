<?php
namespace local_wellbeing;

defined('MOODLE_INTERNAL') || die();

class manager {

    /**
     * Check if wellbeing is enabled via course custom field
     */
    public static function is_enabled(int $courseid): bool {
        global $DB;

        $sql = "
            SELECT d.value
            FROM {customfield_data} d
            JOIN {customfield_field} f ON f.id = d.fieldid
            WHERE f.shortname = :shortname
              AND d.instanceid = :courseid
        ";

        $value = $DB->get_field_sql($sql, [
            'shortname' => 'wellbeing_enabled',
            'courseid'  => $courseid
        ]);

        return ((int)$value === 1);
    }

    /**
     * Sync course into local table
     */
    public static function sync_course(int $courseid): void {
        global $DB;

        if (self::is_enabled($courseid)) {
            if (!$DB->record_exists('local_wellbeing_courses', ['courseid' => $courseid])) {
                $DB->insert_record('local_wellbeing_courses', (object)[
                    'courseid'    => $courseid,
                    'isenabled'   => 1,
                    'timecreated' => time()
                ]);
            }
        } else {
            $DB->delete_records('local_wellbeing_courses', ['courseid' => $courseid]);
        }
    }

    /**
 * Sync metrics and prompt from course custom fields
 */
public static function sync_metrics(int $courseid): void {
    global $DB;

    debugging("Wellbeing: sync_metrics called for course ID {$courseid}", DEBUG_DEVELOPER);

    // Check if wellbeing enabled
    if (!self::is_enabled($courseid)) {
        debugging("Wellbeing: Course {$courseid} is not enabled for wellbeing", DEBUG_DEVELOPER);
        return;
    }

    debugging("Wellbeing: Course {$courseid} is enabled", DEBUG_DEVELOPER);

    $sql = "
        SELECT f.shortname, d.value
        FROM {customfield_data} d
        JOIN {customfield_field} f ON f.id = d.fieldid
        WHERE d.instanceid = :courseid
        AND f.shortname IN ('metrics_name_json','metrics_prompt')
    ";

    $records = $DB->get_records_sql($sql, ['courseid' => $courseid]);

    debugging("Wellbeing: Found " . count($records) . " custom field records", DEBUG_DEVELOPER);

    $metricsjson = '';
    $prompt = '';

    foreach ($records as $record) {

        debugging("Wellbeing: Field {$record->shortname} value = {$record->value}", DEBUG_DEVELOPER);

        if ($record->shortname === 'metrics_name_json') {
            $metricsjson = $record->value;
        }

        if ($record->shortname === 'metrics_prompt') {
            $prompt = $record->value;
        }
    }

    debugging("Wellbeing: Metrics JSON captured = {$metricsjson}", DEBUG_DEVELOPER);
    debugging("Wellbeing: Prompt captured = {$prompt}", DEBUG_DEVELOPER);

    $existing = $DB->get_record('local_wellbeing_courses', ['courseid' => $courseid]);

    if ($existing) {

        debugging("Wellbeing: Existing course record found. Updating.", DEBUG_DEVELOPER);

        $existing->metrics_name_json = $metricsjson;
        $existing->metrics_prompt = $prompt;

        $DB->update_record('local_wellbeing_courses', $existing);

        debugging("Wellbeing: Record updated successfully", DEBUG_DEVELOPER);

    } else {

        debugging("Wellbeing: No record found. Inserting new record.", DEBUG_DEVELOPER);

        $DB->insert_record('local_wellbeing_courses', (object)[
            'courseid' => $courseid,
            'isenabled' => 1,
            'metrics_name_json' => $metricsjson,
            'metrics_prompt' => $prompt,
            'timecreated' => time()
        ]);

        debugging("Wellbeing: Record inserted successfully", DEBUG_DEVELOPER);
    }
}

}
