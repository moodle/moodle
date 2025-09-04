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

namespace mod_bigbluebuttonbn\output;

use core\notification;
use core\output\inplace_editable;
use html_table;
use html_writer;
use mod_bigbluebuttonbn\instance;
use plugin_renderer_base;

/**
 * Renderer for the mod_bigbluebuttonbn plugin.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Darko Miletic  (darko.miletic [at] gmail [dt] com)
 */
class renderer extends plugin_renderer_base {

    /**
     * Render the index table.
     *
     * @param  index $index
     * @return string
     */
    protected function render_index(index $index): string {
        $this->page->requires->js_call_amd('mod_bigbluebuttonbn/index', 'init');

        return html_writer::table($index->get_table($this));
    }

    /**
     * Render the groups selector.
     *
     * @param instance $instance
     * @return string
     */
    public function render_groups_selector(instance $instance): string {
        $groupmode = groups_get_activity_groupmode($instance->get_cm());
        if ($groupmode == NOGROUPS) {
            return '';
        }

        // Separate or visible group mode.
        $groups = groups_get_activity_allowed_groups($instance->get_cm());
        if (empty($groups)) {
            // No groups in this course.
            notification::add(get_string('view_groups_nogroups_warning', 'bigbluebuttonbn'), notification::INFO);
            return '';
        }

        // Assign group default values.
        if (count($groups) == 0) {
            // Only the All participants group exists.
            notification::add(get_string('view_groups_notenrolled_warning', 'bigbluebuttonbn'), notification::INFO);
            return '';
        }

        if (count($groups) > 1) {
            notification::add(get_string('view_groups_selection_warning', 'bigbluebuttonbn'), notification::INFO);
        }

        $groupsmenu = groups_print_activity_menu(
            $instance->get_cm(),
            $instance->get_view_url(),
            true
        );

        return $groupsmenu . '<br><br>';
    }

    /**
     * Render inplace editable
     *
     * @param inplace_editable $e
     * @return bool|string
     */
    public function render_inplace_editable(inplace_editable $e) {
        return $this->render_from_template('core/inplace_editable', $e->export_for_template($this));
    }
}
