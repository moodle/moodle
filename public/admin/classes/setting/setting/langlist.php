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
 * Special setting for limiting of the list of available languages.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class langlist extends \core_admin\setting\setting\configtext {
    /**
     * Calls parent::__construct with specific arguments.
     *
     * @param string $name Name of the admin setting
     */
    public function __construct($name = 'langlist') {
        parent::__construct($name, get_string($name, 'admin'), get_string('config' . $name, 'admin'), '', PARAM_NOTAGS);
    }

    /**
     * Validate that each language identifier exists on the site
     *
     * @param string $data
     * @return bool|string True if validation successful, otherwise error string
     */
    public function validate($data) {
        $parentcheck = parent::validate($data);
        if ($parentcheck !== true) {
            return $parentcheck;
        }

        if ($data === '') {
            return true;
        }

        // Normalize language identifiers.
        $langcodes = array_map('trim', explode(',', $data));
        foreach ($langcodes as $langcode) {
            // If the langcode contains optional alias, split it out.
            [$langcode, ] = preg_split('/\s*\|\s*/', $langcode, 2);

            if (!get_string_manager()->translation_exists($langcode)) {
                return get_string('invalidlanguagecode', 'error', $langcode);
            }
        }

        return true;
    }

    /**
     * Save the new setting
     *
     * @param string $data The new setting
     * @return string error message or empty string on success
     */
    public function write_setting($data) {
        $return = parent::write_setting($data);
        get_string_manager()->reset_caches();
        return $return;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(langlist::class, \admin_setting_langlist::class);
