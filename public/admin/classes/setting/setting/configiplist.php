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
 * Used to validate a textarea used for ip addresses
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2011 Petr Skoda (http://skodak.org)
 */
namespace core_admin\setting\setting;

class configiplist extends \admin_setting_configtextarea {

    /**
     * Validate the contents of the textarea as IP addresses
     *
     * Used to validate a new line separated list of IP addresses collected from
     * a textarea control
     *
     * @param string $data A list of IP Addresses separated by new lines
     * @return mixed bool true for success or string:error on failure
     */
    public function validate($data) {
        if(!empty($data)) {
            $lines = explode("\n", $data);
        } else {
            return true;
        }
        $result = true;
        $badips = array();
        foreach ($lines as $line) {
            $tokens = explode('#', $line);
            $ip = trim($tokens[0]);
            if (empty($ip)) {
                continue;
            }
            if (preg_match('#^(\d{1,3})(\.\d{1,3}){0,3}$#', $ip, $match) ||
                preg_match('#^(\d{1,3})(\.\d{1,3}){0,3}(\/\d{1,2})$#', $ip, $match) ||
                preg_match('#^(\d{1,3})(\.\d{1,3}){3}(-\d{1,3})$#', $ip, $match)) {
            } else {
                $result = false;
                $badips[] = $ip;
            }
        }
        if($result) {
            return true;
        } else {
            return get_string('validateiperror', 'admin', join(', ', $badips));
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configiplist::class, \admin_setting_configiplist::class);
