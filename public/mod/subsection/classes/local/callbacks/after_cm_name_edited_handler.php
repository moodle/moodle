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

namespace mod_subsection\local\callbacks;

use core_courseformat\hook\after_cm_name_edited;
use core_courseformat\formatactions;
use mod_subsection\manager;

/**
 * Class after activity renaming hook handler.
 *
 * @package    mod_subsection
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class after_cm_name_edited_handler {
    /**
     * Handle the activity name change.
     *
     * @param after_cm_name_edited $hook
     */
    public static function callback(after_cm_name_edited $hook): void {
        $cm = $hook->get_cm();

        if ($cm->modname !== manager::MODULE) {
            return;
        }

        $section = get_fast_modinfo($cm->course)->get_section_info_by_component(manager::PLUGINNAME, $cm->instance);
        if ($section) {
            formatactions::section($cm->course)->update($section, ['name' => $hook->get_newname()]);
        }
    }
}
