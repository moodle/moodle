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

        if (has_capability('moodle/course:update', context_course::instance($courseid))) {
            $tabs[] = new tabobject(
                'completion',
                new moodle_url('/course/completion.php', ['id' => $courseid]),
                get_string('coursecompletion', 'completion')
            );

            $tabs[] = new tabobject(
                'defaultcompletion',
                new moodle_url('/course/defaultcompletion.php', ['id' => $courseid]),
                get_string('defaultcompletion', 'completion')
            );
        }

        if (core_completion\manager::can_edit_bulk_completion($courseid)) {
            $tabs[] = new tabobject(
                'bulkcompletion',
                new moodle_url('/course/bulkcompletion.php', ['id' => $courseid]),
                get_string('bulkactivitycompletion', 'completion')
            );
        }

        if (count($tabs) > 1) {
            return $this->tabtree($tabs, $page);
        } else {
            return '';
        }
    }


    public function bulkcompletion($data) {
        return parent::render_from_template('core_course/bulkactivitycompletion', $data);
    }

    public function defaultcompletion($data) {
        return parent::render_from_template('core_course/defaultactivitycompletion', $data);
    }

    /**
     * Renders the form for bulk editing activities completion
     *
     * @param moodleform $form
     * @param array $activities
     * @return string
     */
    public function edit_bulk_completion($form, $activities) {
        ob_start();
        $form->display();
        $formhtml = ob_get_contents();
        ob_end_clean();

        $data = (object)[
            'form' => $formhtml,
            'activities' => array_values($activities),
            'activitiescount' => count($activities),
        ];
        return parent::render_from_template('core_course/editbulkactivitycompletion', $data);
    }

    /**
     * Renders the form for editing default completion
     *
     * @param moodleform $form
     * @param array $modules
     * @return string
     */
    public function edit_default_completion($form, $modules) {
        ob_start();
        $form->display();
        $formhtml = ob_get_contents();
        ob_end_clean();

        $data = (object)[
            'form' => $formhtml,
            'modules' => array_values($modules),
            'modulescount' => count($modules),
        ];
        return parent::render_from_template('core_course/editdefaultcompletion', $data);
    }
}
