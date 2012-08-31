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
 * Category enrolment plugin.
 *
 * @package    enrol_category
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * category enrolment plugin implementation.
 * @author  Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_category_plugin extends enrol_plugin {

   /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function instance_deleteable($instance) {
        global $DB;

        if (!enrol_is_enabled('category')) {
            return true;
        }
        // Allow delete only when no synced users here.
        return !$DB->record_exists('user_enrolments', array('enrolid'=>$instance->id));
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        // Instances are added automatically as necessary.
        return null;
    }

    /**
     * Called for all enabled enrol plugins that returned true from is_cron_required().
     * @return void
     */
    public function cron() {
        global $CFG;

        if (!enrol_is_enabled('category')) {
            return;
        }

        require_once("$CFG->dirroot/enrol/category/locallib.php");
        enrol_category_sync_full();
    }

    /**
     * Called after updating/inserting course.
     *
     * @param bool $inserted true if course just inserted
     * @param stdClass $course
     * @param stdClass $data form data
     * @return void
     */
    public function course_updated($inserted, $course, $data) {
        global $CFG;

        if (!enrol_is_enabled('category')) {
            return;
        }

        // Sync category enrols.
        require_once("$CFG->dirroot/enrol/category/locallib.php");
        enrol_category_sync_course($course);
    }

    /**
     * Automatic enrol sync executed during restore.
     * Useful for automatic sync by course->idnumber or course category.
     * @param stdClass $course course record
     */
    public function restore_sync_course($course) {
        global $CFG;
        require_once("$CFG->dirroot/enrol/category/locallib.php");
        enrol_category_sync_course($course);
    }
}
