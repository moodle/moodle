<?php
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class local_seriousgame_external extends external_api {

    public static function get_activities_parameters() {
        return new external_function_parameters([]);
    }

    public static function get_activities() {
        global $DB;
        $activities = $DB->get_records('wordcards_terms', []);
        $results = [];
        foreach ($activities as $activity) {
            $results[] = [
                'id' => $activity->id,
                'term' => $activity->term,
                'definition' => $activity->definition,
            ];
        }
        return $results;
    }

    public static function get_activities_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Activity ID'),
                'term' => new external_value(PARAM_TEXT, 'Term'),
                'definition' => new external_value(PARAM_TEXT, 'Definition'),
            ])
        );
    }
}
