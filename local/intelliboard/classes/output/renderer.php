<?php
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

namespace local_intelliboard\output;
defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

/**
 * Renderer file.
 *
 * @package    local_intelliboard
 * @author     Intelliboard
 * @copyright  2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

/**
 * Standard HTML output renderer for intelliboard
 */
class renderer extends plugin_renderer_base {
    /**
     * Constructor method, calls the parent constructor
     *
     * @param \moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
    }

    /**
     * Return content of student navigation
     *
     * @param student_menu $studentmenu
     * @return string HTML string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function render_student_menu(student_menu $studentmenu) {
        return $this->render_from_template(
            'local_intelliboard/student_menu', $studentmenu->export_for_template($this)
        );
    }

    /**
     * Return content of "Setup" page
     *
     * @param setup $setup
     * @return string HTML string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function render_setup(setup $setup) {
        return $this->render_from_template(
            'local_intelliboard/setup', $setup->export_for_template($this)
        );
    }

    /**
     * Return content of "Initial Report" page
     *
     * @param initial_report $report
     * @return string HTML string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function render_initial_report(\local_intelliboard\output\initial_report $report) {
        return $this->render_from_template(
            'local_intelliboard/initial_report', $report->export_for_template($this)
        );
    }

    /**
     * Return content of "Instructor dashboard" page
     *
     * @param instructor_index $report
     * @return string HTML string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function render_instructor_index(\local_intelliboard\output\instructor_index $page) {
        return $this->render_from_template(
            'local_intelliboard/instructor_index', $page->export_for_template($this)
        );
    }
}
