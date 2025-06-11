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

namespace core_message;

/**
 * Class hook_callbacks
 *
 * @package    core_message
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Add messaging widgets after the main region content.
     *
     * @param \core\hook\output\after_standard_main_region_html_generation $hook
     */
    public static function add_messaging_widget(
        \core\hook\output\after_standard_main_region_html_generation $hook,
    ): void {
        $hook->add_html(\core_message\helper::render_messaging_widget(
            isdrawer: true,
        ));
    }
}
