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
 * Contains renderers for the recommendations page.
 *
 * @package core_course
 * @copyright 2020 Adrian Greeve
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\output\recommendations;

/**
 * Main renderer for the recommendations page.
 *
 * @package core_course
 * @copyright 2020 Adrian Greeve
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render a list of activities to recommend.
     *
     * @param  \core_course\output\recommendations\activity_list $page activity list renderable
     * @return string html for displaying.
     */
    public function render_activity_list(\core_course\output\recommendations\activity_list $page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('core_course/activity_list', $data);
    }
}
