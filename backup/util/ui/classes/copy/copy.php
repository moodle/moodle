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
 * Course copy class.
 *
 * Handles procesing data submitted by UI copy form
 * and sets up the course copy process.
 *
 * @package    core_backup
 * @copyright  2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_backup\copy;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Course copy class.
 *
 * Handles procesing data submitted by UI copy form
 * and sets up the course copy process.
 *
 * @package    core_backup
 * @copyright  2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class copy  {

    /**
     * The fields required for copy operations.
     *
     * @var array
     */
    private $copyfields = array(
        'courseid',  // Course id integer.
        'fullname', // Fullname of the destination course.
        'shortname', // Shortname of the destination course.
        'category', // Category integer ID that contains the destination course.
        'visible', // Integer to detrmine of the copied course will be visible.
        'startdate', // Integer timestamp of the start of the destination course.
        'enddate', // Integer timestamp of the end of the destination course.
        'idnumber', // ID of the destination course.
        'userdata', // Integer to determine if the copied course will contain user data.
    );

    /**
     * Data required for course copy operations.
     *
     * @var array
     */
    private $copydata = array();

    /**
     * List of role ids to keep enrolments for in the destination course.
     *
     * @var array
     */
    private $roles = array();

    /**
     * Constructor for the class.
     *
     * @param \stdClass $formdata Data from the validated course copy form.
     */
    public function __construct(\stdClass $formdata) {
        $this->copydata = $this->get_copy_data($formdata);
        $this->roles = $this->get_enrollment_roles($formdata);
    }

    /**
     * Extract the enrolment roles to keep in the copied course
     * from the raw submitted form data.
     *
     * @param \stdClass $formdata Data from the validated course copy form.
     * @return array $keptroles The roles to keep.
     */
    private function get_enrollment_roles(\stdClass $formdata): array {
        $keptroles = array();

        foreach ($formdata as $key => $value) {
            if ((substr($key, 0, 5 ) === 'role_') && ($value != 0)) {
                $keptroles[] = $value;
            }
        }

        return $keptroles;
    }

    /**
     *  Take the validated form data and extract the required information for copy operations.
     *
     * @param \stdClass $formdata Data from the validated course copy form.
     * @return \stdClass $copydata Data required for course copy operations.
     * @throws \moodle_exception If one of the required copy fields is missing
     */
    private function get_copy_data(\stdClass $formdata): \stdClass {
        $copydata = new \stdClass();

        foreach ($this->copyfields as $field) {
            if (isset($formdata->{$field})) {
                $copydata->{$field} = $formdata->{$field};
            } else {
                throw new \moodle_exception('copyfieldnotfound', 'backup', '', null, $field);
            }
        }

        return $copydata;
    }

    /**
     * Creates a course copy.
     * Sets up relevant controllers and adhoc task.
     *
     * @return array $copyids THe backup and restore controller ids.
     */
    public function create_copy(): array {
        debugging('The method \core_backup\copy\copy::create_copy() is deprecated.
            Please use the methods provided by copy_helper instead.', DEBUG_DEVELOPER);
        $copydata = clone($this->copydata);
        $copydata->keptroles = $this->roles;
        return \copy_helper::create_copy($copydata);
    }

    /**
     * Get the in progress course copy operations for a user.
     *
     * @param int $userid User id to get the course copies for.
     * @param int $courseid The optional source course id to get copies for.
     * @return array $copies Details of the inprogress copies.
     */
    static public function get_copies(int $userid, int $courseid=0): array {
        global $DB;
        $copies = array();
        $params = array($userid, \backup::EXECUTION_DELAYED, \backup::MODE_COPY);
        $sql = 'SELECT bc.backupid, bc.itemid, bc.operation, bc.status, bc.timecreated
                  FROM {backup_controllers} bc
            INNER JOIN {course} c ON bc.itemid = c.id
                 WHERE bc.userid = ?
                       AND bc.execution = ?
                       AND bc.purpose = ?
              ORDER BY bc.timecreated DESC';

        $copyrecords = $DB->get_records_sql($sql, $params);

        foreach ($copyrecords as $copyrecord) {
            $copy = new \stdClass();
            $copy->itemid = $copyrecord->itemid;
            $copy->time = $copyrecord->timecreated;
            $copy->operation = $copyrecord->operation;
            $copy->status = $copyrecord->status;
            $copy->backupid = null;
            $copy->restoreid = null;

            if ($copyrecord->operation == \backup::OPERATION_RESTORE) {
                $copy->restoreid = $copyrecord->backupid;
                // If record is complete or complete with errors, it means the backup also completed.
                // It also means there are no controllers. In this case just skip and move on.
                if ($copyrecord->status == \backup::STATUS_FINISHED_OK
                    || $copyrecord->status == \backup::STATUS_FINISHED_ERR) {
                        continue;
                } else if ($copyrecord->status > \backup::STATUS_REQUIRE_CONV) {
                    // If record is a restore and it's in progress (>200), it means the backup is finished.
                    // In this case return the restore.
                    $rc = \restore_controller::load_controller($copyrecord->backupid);
                    $course = get_course($rc->get_copy()->courseid);

                    $copy->source = $course->shortname;
                    $copy->sourceid = $course->id;
                    $copy->destination = $rc->get_copy()->shortname;
                    $copy->backupid = $rc->get_copy()->copyids['backupid'];
                    $rc->destroy();

                } else if ($copyrecord->status == \backup::STATUS_REQUIRE_CONV) {
                    // If record is a restore and it is waiting (=200), load the controller
                    // and check the status of the backup.
                    // If the backup has finished successfully we have and edge case. Process as per in progress restore.
                    // If the backup has any other code it will be handled by backup processing.
                    $rc = \restore_controller::load_controller($copyrecord->backupid);
                    $bcid = $rc->get_copy()->copyids['backupid'];
                    if (empty($copyrecords[$bcid])) {
                        continue;
                    }
                    $backuprecord = $copyrecords[$bcid];
                    $backupstatus = $backuprecord->status;
                    if ($backupstatus == \backup::STATUS_FINISHED_OK) {
                        $course = get_course($rc->get_copy()->courseid);

                        $copy->source = $course->shortname;
                        $copy->sourceid = $course->id;
                        $copy->destination = $rc->get_copy()->shortname;
                        $copy->backupid = $rc->get_copy()->copyids['backupid'];
                    } else {
                        continue;
                    }
                }
            } else { // Record is a backup.
                $copy->backupid = $copyrecord->backupid;
                if ($copyrecord->status == \backup::STATUS_FINISHED_OK
                    || $copyrecord->status == \backup::STATUS_FINISHED_ERR) {
                        // If successfully finished then skip it. Restore procesing will look after it.
                        // If it has errored then we can't go any further.
                        continue;
                } else {
                    // If is in progress then process it.
                    $bc = \backup_controller::load_controller($copyrecord->backupid);
                    $course = get_course($bc->get_courseid());

                    $copy->source = $course->shortname;
                    $copy->sourceid = $course->id;
                    $copy->destination = $bc->get_copy()->shortname;
                    $copy->restoreid = $bc->get_copy()->copyids['restoreid'];
                }
            }

            $copies[] = $copy;
        }

        // Extra processing to filter records for a given course.
        if ($courseid != 0 ) {
            $copies = self::filter_copies_course($copies, $courseid);
        }

        return $copies;
    }
}
