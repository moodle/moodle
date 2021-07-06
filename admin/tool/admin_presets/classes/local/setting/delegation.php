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

namespace tool_admin_presets\local\setting;

use admin_setting;

/**
 * Cross-class methods
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delegation {

    /**
     * Adds a piece of string to the $type setting
     *
     * @param boolean $value
     * @param string $type Indicates the "extra" setting
     * @return    string
     */
    public function extra_set_visiblevalue(bool $value, string $type): string {
        // Adding the advanced value to the text string if present.
        if ($value) {
            $string = get_string('markedas' . $type, 'tool_admin_presets');
        } else {
            $string = get_string('markedasnon' . $type, 'tool_admin_presets');
        }

        // Adding the advanced state.
        return ', ' . $string;
    }

    public function extra_loadchoices(admin_setting &$adminsetting) {
        $adminsetting->load_choices();
    }
}
