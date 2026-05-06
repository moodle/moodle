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
 * Used to validate a textarea used for port numbers.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 Jake Dallimore (jrhdallimore@gmail.com)
 */
namespace core_admin\setting\setting;

class configportlist extends \core_admin\setting\setting\configtextarea {

    /**
     * Validate the contents of the textarea as port numbers.
     * Used to validate a new line separated list of ports collected from a textarea control.
     *
     * @param string $data A list of ports separated by new lines
     * @return mixed bool true for success or string:error on failure
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }
        $ports = explode("\n", $data);
        $badentries = [];
        foreach ($ports as $port) {
            $port = trim($port);
            if (empty($port)) {
                return get_string('validateemptylineerror', 'admin');
            }

            // Is the string a valid integer number?
            if (strval(intval($port)) !== $port || intval($port) <= 0) {
                $badentries[] = $port;
            }
        }
        if (count($badentries) > 0) {
            $badentries = implode(get_string('listsep', 'core_langconfig') . ' ', $badentries);
            return get_string('validateerrorlist', 'admin', $badentries);
        }
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configportlist::class, \admin_setting_configportlist::class);
