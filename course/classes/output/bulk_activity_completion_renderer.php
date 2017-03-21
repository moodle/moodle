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

    /**
     * Render the navigation tabs for the completion page.
     *
     * @param int|stdClass $courseorid the course object or id.
     * @param String $page the tab to focus.
     * @return string html
     */
    public function navigation($courseorid, $page) {
        $tabs = core_completion\manager::get_available_completion_tabs($courseorid);
        if (count($tabs) > 1) {
            return $this->tabtree($tabs, $page);
        } else {
            return '';
        }
    }

    /**
     * Render the bulk completion tab.
     *
     * @param Array|stdClass $data the context data to pass to the template.
     * @return bool|string
     */
    public function bulkcompletion($data) {
        return parent::render_from_template('core_course/bulkactivitycompletion', $data);
    }

    /**
     * Render the default completion tab.
     *
     * @param Array|stdClass $data the context data to pass to the template.
     * @return bool|string
     */
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
