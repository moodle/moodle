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
 * Setting to store JSON codified settings for this plugin.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\settings;

use admin_setting_configtextarea;
use coding_exception;
use tool_mergeusers\local\jsonizer;

/**
 * Setting to store JSON codified settings for this plugin.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class json_setting extends admin_setting_configtextarea {
    /**
     * Setting to store content in valid JSON format.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param string $defaultsetting
     * @param string $cols
     * @param string $rows
     */
    public function __construct(
        string $name,
        string $visiblename,
        string $description,
        string $defaultsetting,
        string $cols = '60',
        string $rows = '8',
    ) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, PARAM_RAW_TRIMMED, $cols, $rows);
    }

    /**
     * Check whether the content is a valid JSON content.
     *
     * @param $data
     * @return bool|string string with the error message; true on valid content.
     * @throws coding_exception
     */
    public function validate($data): bool|string {
        if (empty($data)) {
            // Allow administrators save this setting empty, to reset it without "Syntax error"s.
            return true;
        }
        $result = jsonizer::from_json($data);
        if (json_last_error() != JSON_ERROR_NONE) {
            return json_last_error_msg();
        }
        if ($result === null || $result == 'null') {
            return get_string('setting:invalidjson', 'tool_mergeusers');
        }
        return true;
    }

    /**
     * Writes the JSON content in human-readable content.
     *
     * @throws coding_exception
     */
    public function write_setting($data) {
        // Based on core code.
        // Check if content is a valid JSON string.
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }

        // Ensure that content is always human-readable.
        if (empty($data) || $data == '{}') {
            // Allow administrators save this setting empty, to reset it without "Syntax error"s.
            $data = '{}';
        } else {
            $data = jsonizer::format($data);
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }
}
