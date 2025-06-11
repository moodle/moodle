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
 * Renderer for the lsu_people block.
 *
 * @package    block_lsu_people
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Required as it's not a visitable page.
defined('MOODLE_INTERNAL') || die();

class block_lsu_people_renderer extends plugin_renderer_base {

    /**
     * Render the main lsu_people renderable.
     *
     * @param \block_lsu_people\output\lsu_people $renderable
     * @return string HTML to output
     */
    public function render_lsu_people(\block_lsu_people\output\lsu_people $renderable): string {
        $data = $renderable->export_for_template($this);
        return $data->tablehtml;
    }
}

