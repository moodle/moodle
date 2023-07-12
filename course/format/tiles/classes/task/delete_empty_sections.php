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
 * Ad hoc task to be executed the next time cron runs for component 'format_tiles', to register plugin.
 *
 * @package   format_tiles
 * @copyright 2020 David Watson {@link http://evolutioncode.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_tiles\task;

use format_tiles\course_section_manager;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/lib.php');

/**
 * Class delete_empty_sections
 * @package format_tiles
 * @copyright 2020 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_empty_sections extends \core\task\adhoc_task {

    /**
     * Run the task to delete empty sections in flagged courses.
     * Experimental feature which most instances will never need to use.
     * Only relevant for instances which used the incompatible Moodle 3.7 plugin in Moodle 3.9 so have empty sections created.
     */
    public function execute() {
        global $DB, $CFG;
        $flaggedcourses = $DB->get_fieldset_sql(
            "SELECT name from {config_plugins}
            WHERE plugin = 'format_tiles' AND name LIKE 'delete_empty_sections_%'"
        );

        $countflaggedcourses = count($flaggedcourses);
        $startatsection = 30;

        mtrace(
            "format_tiles deleting empty course sections.  Found $countflaggedcourses flagged courses"
        );
        if ($countflaggedcourses > 0) {
            foreach ($flaggedcourses as $flaggedcourse) {
                $courseid = explode('_', $flaggedcourse)[3];
                mtrace(' - starting course ' . $courseid);

                // Before we start, mark as complete (make sure that we don't try this section again even if something goes wrong).
                course_section_manager::cancel_empty_sec_deletion($courseid);

                $coursecontext = \context_course::instance($courseid);
                $emptysections = course_section_manager::get_empty_sections($courseid, $startatsection);

                $countsections = count($emptysections);
                mtrace(
                    " - found $countsections empty sections to delete. " .
                    "Starting deletions from the first empty section after section $startatsection..."
                );
                $deletedsectioncount = 0;
                if ($countsections >= 0) {

                    foreach ($emptysections as $section) {
                        if ($CFG->debugdeveloper) {
                            mtrace(" - checking section id $section->id | section number $section->section");
                        }
                        $result = course_section_manager::delete_section($section, $coursecontext);
                        if ($result) {
                            $deletedsectioncount++;
                            if ($CFG->debugdeveloper) {
                                mtrace(" - deleted section id $section->id");
                            }
                        } else {
                            mtrace(" - ***FAILED*** deletion section id $section->id");
                        }
                    }
                    mtrace(" - completed course $courseid, deleted $deletedsectioncount sections");
                    if ($deletedsectioncount) {
                        course_section_manager::resolve_section_misnumbering($courseid);
                        mtrace(' - marking course cache for rebuild');
                        rebuild_course_cache($courseid, true);
                    }
                }
            }
        }
    }
}
