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
 * Course Hider Tool
 *
 * @package   block_course_hider
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_course_hider\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/course_hider/lib.php');
require_login();

class course_hider_view implements renderable, templatable {

    public $courses;
    /**
     * Constructor.
     *
     * @param array
     */
    public function __construct($courses) {
        
        $this->courses = $courses;
    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output, $results = array()): array {
        global $CFG;
        unset($this->courses["lockme"]);
        unset($this->courses["hideme"]);

        error_log("\n\n------------------------------------\n\n");
        error_log("\nExporting for TEMPLATE\n\n");
        $courses = array();
        if (isset($this->courses)) {
            // Convert to array objects for the template.
            foreach ($this->courses as $course) {
                // Use the more obvious hidden / visible versus 0 / 1.
                $course->visible = $course->visible == 1 ? 'visible' : 'hidden';
                $course->locked = $course->locked == 1 ? 'locked' : 'Open';
                $courses[] = json_decode(json_encode($course), true);
            }
        }

        if ($cc = count($courses)) {
            $updateddata['show_count'] = true;
            $updateddata['course_count'] = $cc;
        } else {
            $updateddata['show_count'] = false;
        }

        $updateddata['churl'] = $CFG->wwwroot;
        $updateddata['courses'] = $courses;

        return $updateddata;
    }
}
