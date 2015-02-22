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
 * Renderer class for learning plans
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

/**
 * Renderer class for learning plans
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param manage_competency_frameworks_page $page
     *
     * @return string html for the page
     */
    public function render_manage_competency_frameworks_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/manage_competency_frameworks_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param manage_competencies_page $page
     *
     * @return string html for the page
     */
    public function render_manage_competencies_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/manage_competencies_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param course_competencies_page $page
     *
     * @return string html for the page
     */
    public function render_course_competencies_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/course_competencies_page', $data);
    }

    /**
     * Defer to template.
     *
     * @param manage_templates_page $page
     *
     * @return string html for the page
     */
    public function render_manage_templates_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_lp/manage_templates_page', $data);
    }

}
