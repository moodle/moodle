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
 * @deprecated since Moodle 4.1. Use copy_helper instead
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
 * @deprecated since Moodle 4.1 MDL-74548 - please use copy_helper instead
 * @todo MDL-75022 This class will be deleted in Moodle 4.5
 * @see copy_helper
 */
class copy {

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
        debugging('Class \course_backup\copy\copy is deprecated. Please use the copy_helper class instead.');
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
     * @deprecated since Moodle 4.1 MDL-74548 - please use copy_helper instead.
     * @todo MDL-75023 This method will be deleted in Moodle 4.5
     * @see copy_helper::process_formdata()
     * @see copy_helper::create_copy()
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
     * @deprecated since Moodle 4.1 MDL-74548 - please use copy_helper::get_copies() instead.
     * @todo MDL-75024 This method will be deleted in Moodle 4.5
     * @see copy_helper::get_copies()
     */
    public static function get_copies(int $userid, int $courseid=0): array {
        debugging('The method \core_backup\copy\copy::get_copies() is deprecated.
            Please use copy_helper::get_copies() instead.', DEBUG_DEVELOPER);

        return \copy_helper::get_copies($userid, $coursied);
    }
}
