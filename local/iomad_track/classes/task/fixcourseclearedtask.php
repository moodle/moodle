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
 * An adhoc task for local Iomad track
 *
 * @package    local_iomad_track
 * @copyright  2020 E-Learn Design https://www.e-learndesign.co.uk
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_iomad_track\task;

defined('MOODLE_INTERNAL') || die();

use core\task\adhoc_task;

class fixcourseclearedtask extends adhoc_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('fixcourseclearedtask', 'local_iomad_track');
    }

    /**
     * Run fixtracklicensestask
     */
    public function execute() {
        global $DB;

        // Deal with the local_iomad_track entries
        $allentries = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                            WHERE timecompleted > 0
                                            AND coursecleared = 0", array());
        foreach ($allentries as $entry) {
            if ($DB->count_records_sql("SELECT COUNT(id) FROM {local_iomad_track}
                                        WHERE userid = :userid
                                        AND courseid = :courseid
                                        AND (
                                         timeenrolled > :timecompleted
                                         OR timeenrolled is null
                                        )",
                                        array('userid' => $entry->userid,
                                              'courseid' => $entry->courseid,
                                              'timecompleted' => $entry->timecompleted)) > 0) {
                $DB->set_field('local_iomad_track', 'coursecleared', 1, array('id' => $entry->id));
                $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $entry->id));
            } else if (!empty($entry->licenseid) && !empty($entry->licenseallocated)) {
                if ($DB->get_record_sql("SELECT id FROM {companylicense_users}
                                         WHERE licenseid = :licenseid
                                         AND licensecourseid = :courseid
                                         AND issuedate = :licenseallocated
                                         AND timecompleted > 0",
                                         array('licenseid' => $entry->licenseid,
                                               'courseid' => $entry->courseid,
                                               'licenseallocated' => $entry->licenseallocated))) {
                    $DB->set_field('local_iomad_track', 'coursecleared', 1, array('id' => $entry->id));
                    $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $entry->id));
                } else if ($licenserec = $DB->get_record_sql("SELECT id FROM {companylicense_users}
                                                              WHERE id = :licenseid
                                                              AND licensecourseid = :courseid
                                                              AND issuedate = :licenseallocated
                                                              AND timecompleted > 0",
                                                              array('licenseid' => $entry->licenseid,
                                                                    'courseid' => $entry->courseid,
                                                                    'licenseallocated' => $entry->licenseallocated))) {
                    $DB->set_field('local_iomad_track', 'coursecleared', 1, array('id' => $entry->id));
                    $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $entry->id));
                    $DB->set_field('local_iomad_track', 'licenseid', $licenserec->licenseid, array('id' => $entry->id));
                }
            }
        }
    }

    /**
     * Queues the task.
     *
     */
    public static function queue_task() {

        // Let's set up the adhoc task.
        $task = new \local_iomad_track\task\fixcourseclearedtask();
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
