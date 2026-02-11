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
}
