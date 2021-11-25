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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_iomad_microlearning_renderer extends plugin_renderer_base {

    /**
     * Back to list of roles button
     */
    public function threads_buttons($link) {
        $out = '<p><a class="btn btn-primary" href="'.$link.'">' . get_string('add') . '</a></p>';

        return $out;
    }

    /**
     * Back to list of roles button
     */
    public function threads_list_buttons($link, $link2, $link3, $link4) {
        $out = '<p><a class="btn btn-primary" href="'.$link.'">' . get_string('add') . '</a>';
        if (iomad::has_capability('block/iomad_microlearning:import_threads', context_system::instance())) {
            $out .= '&nbsp<a class="btn btn-primary" href="'.$link2.'">' . get_string('import') . '</a>';
        }
        if (iomad::has_capability('block/iomad_microlearning:manage_groups', context_system::instance())) {
            $out .= '&nbsp<a class="btn btn-primary" href="'.$link3.'">' . get_string('learninggroups', 'block_iomad_microlearning') . '</a>';
            if (iomad::has_capability('block/iomad_microlearning:importgroupfromcsv', context_system::instance())) {
                $out .= '&nbsp<a class="btn btn-primary" href="'.$link4.'">' . get_string('bulkassigngroups', 'block_iomad_microlearning') . '</a>';
            }
        }
        $out .= '</p>';

        return $out;
    }
}
