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

use plugin_renderer_base;

class renderer extends plugin_renderer_base {
    /**
     * Defer to template.
     *
     * @param sample_view $page
     *
     * @return string html for the page
     */
    public function render_sample_view($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_course_hider/course_hider_view', $data);
    }

    /**
     * Defer to template.
     *
     * @param Dashboard $page
     *
     * @return string html for the page
     */
    public function render_dashboard($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_course_hider/dashboard', $data);
    }
}
