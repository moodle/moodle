<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Privacy class for requesting user data.
 *
 * @package    gradingform_rubric
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_rubric\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;

/**
 * Privacy class for requesting user data.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_grading\privacy\gradingform_provider_v2 {

    /**
     * Returns meta data about this system.
     *
     * @param  collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('gradingform_rubric_fillings', [
            'instanceid' => 'privacy:metadata:instanceid',
            'criterionid' => 'privacy:metadata:criterionid',
            'levelid' => 'privacy:metadata:levelid',
            'remark' => 'privacy:metadata:remark'
        ], 'privacy:metadata:fillingssummary');
        return $collection;
    }

    /**
     * Export user data relating to an instance ID.
     *
     * @param  \context $context Context to use with the export writer.
     * @param  int $instanceid The instance ID to export data for.
     * @param  array $subcontext The directory to export this data to.
     */
    public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext) {
        global $DB;
        // Get records from the provided params.
        $params = ['instanceid' => $instanceid];
        $sql = "SELECT rc.description, rl.definition, rl.score, rf.remark
                  FROM {gradingform_rubric_fillings} rf
                  JOIN {gradingform_rubric_criteria} rc ON rc.id = rf.criterionid
                  JOIN {gradingform_rubric_levels} rl ON rf.levelid = rl.id
                 WHERE rf.instanceid = :instanceid";
        $records = $DB->get_records_sql($sql, $params);
        if ($records) {
            $subcontext = array_merge($subcontext, [get_string('rubric', 'gradingform_rubric'), $instanceid]);
            \core_privacy\local\request\writer::with_context($context)->export_data($subcontext, (object) $records);
        }
    }

    /**
     * Deletes all user data related to the provided instance IDs.
     *
     * @param  array  $instanceids The instance IDs to delete information from.
     */
    public static function delete_gradingform_for_instances(array $instanceids) {
        global $DB;
        $DB->delete_records_list('gradingform_rubric_fillings', 'instanceid', $instanceids);
    }
}
