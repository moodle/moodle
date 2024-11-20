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

namespace mod_lti\output;

use core\output\notification;
use renderer_base;

/**
 * Course tools page header renderable, containing the data for the page zero state and 'add tool' button.
 *
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_tools_page_header implements \templatable {

    /**
     * Constructor.
     *
     * @param int $courseid the course id.
     * @param int $toolcount the number of tools available in the course.
     * @param bool $canadd whether the user can add tools to the course or not.
     */
    public function __construct(protected int $courseid, protected int $toolcount, protected bool $canadd) {
    }

    /**
     * Export the header's data for template use.
     *
     * @param renderer_base $output
     * @return object the data.
     */
    public function export_for_template(renderer_base $output): \stdClass {

        $context = (object) [];

        if ($this->canadd) {
            $context->addlink = (new \moodle_url('/mod/lti/coursetooledit.php', ['course' => $this->courseid]))->out();
        }

        if ($this->toolcount == 0) {
            $notification = new notification(get_string('nocourseexternaltoolsnotice', 'mod_lti'), notification::NOTIFY_INFO, true);
            $context->notoolsnotice = $notification->export_for_template($output);
        }

        return $context;
    }
}
