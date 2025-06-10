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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsuxe\output;

use plugin_renderer_base;

class renderer extends plugin_renderer_base {
    /**
     * Defer to template.
     *
     * @param mappings_create $page
     *
     * @return string html for the page
     */
    public function render_mappings_view($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_lsuxe/mappings_view', $data);
    }

    /**
     * Defer to template.
     *
     * @param mappings_create $page
     *
     * @return string html for the page
     */
    public function render_mappings_create($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_lsuxe/mappings_create', $data);
    }

    /**
     * Defer to template.
     *
     * @param mappings_create $page
     *
     * @return string html for the page
     */
    public function render_moodles_view($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_lsuxe/moodles_view', $data);
    }

    /**
     * Defer to template.
     *
     * @param mappings_create $page
     *
     * @return string html for the page
     */
    public function render_moodles_create($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_lsuxe/moodles_create', $data);
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
        return parent::render_from_template('block_lsuxe/dashboard', $data);
    }
}
