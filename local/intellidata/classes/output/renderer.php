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
 * Renderer file.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\output;

use plugin_renderer_base;

/**
 * Standard HTML output renderer for intellidata
 */
class renderer extends plugin_renderer_base {
    /**
     * Return content of "View Lti" page
     *
     * @param lti_view $page
     * @return string HTML string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function render_lti_view(\local_intellidata\output\lti_view $page) {
        return $this->render_from_template(
            'local_intellidata/lti_view', $page->export_for_template($this)
        );
    }

    /**
     * Return content of "Launch Lti" page
     *
     * @param lti_launch $page
     * @return string HTML string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function render_lti_launch(\local_intellidata\output\lti_launch $page) {
        return $this->render_from_template(
            'local_intellidata/lti_launch', $page->export_for_template($this)
        );
    }

    /**
     * Render from template but with validation.
     *
     * @param $params
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_from_template_with_validation($template, $templatepath, $context) {
        global $CFG;

        if (!file_exists($CFG->dirroot . $templatepath . '.mustache')) {
            return '';
        }

        return $this->render_from_template($template, $context);
    }

    /**
     * Return content for "Help" page.
     *
     * @param help $page
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_help(\local_intellidata\output\help $page) {
        return $this->render_from_template(
            'local_intellidata/help', $page->export_for_template($this)
        );
    }
}
