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
 * Contains renderers for the bulk activity completion stuff.
 *
 * @package core_course
 * @copyright 2017 Adrian Greeve
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/renderer.php');

/**
 * Main renderer for the bulk activity completion stuff.
 *
 * @package core_course
 * @copyright 2017 Adrian Greeve
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_course_bulk_activity_completion_renderer extends plugin_renderer_base {

    public function navigation($courseid, $page) {
        $tabs = [];

        $tabs[] = new tabobject(
            'completion',
            new moodle_url('/course/completion.php', ['id' => $courseid]),
            get_string('coursecompletion', 'completion')
        );

        $tabs[] = new tabobject(
            'bulkcompletion',
            new moodle_url('/course/bulkcompletion.php', ['id' => $courseid]),
            get_string('bulkactivitycompletion', 'completion');
        );

        return $this->tabtree($tabs, $page);
    }


    public function bulkcompletion($data) {
        return parent::render_from_template('core_course/bulkactivitycompletion', $data);
    }

}
