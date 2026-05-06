<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * Allows to specify comma separated list of known country codes.
 *
 * This is a simple subclass of the plain input text field with added validation so that all the codes are actually
 * known codes.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class countrycodes extends \core_admin\setting\setting\configtext {
    /**
     * Construct the instance of the setting.
     *
     * @param string $name Name of the admin setting such as 'allcountrycodes' or 'myplugin/countries'.
     * @param lang_string|string $visiblename Language string with the field label text.
     * @param lang_string|string $description Language string with the field description text.
     * @param string $defaultsetting Default value of the setting.
     * @param int $size Input text field size.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = '', $size = null) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, '/^(?:\w+(?:,\w+)*)?$/', $size);
    }

    /**
     * Validate the setting value before storing it.
     *
     * The value is first validated through custom regex so that it is a word consisting of letters, numbers or underscore; or
     * a comma separated list of such words.
     *
     * @param string $data Value inserted into the setting field.
     * @return bool|string True if the value is OK, error string otherwise.
     */
    public function validate($data) {

        $parentcheck = parent::validate($data);

        if ($parentcheck !== true) {
            return $parentcheck;
        }

        if ($data === '') {
            return true;
        }

        $allcountries = get_string_manager()->get_list_of_countries(true);

        foreach (explode(',', $data) as $code) {
            if (!isset($allcountries[$code])) {
                return get_string('invalidcountrycode', 'core_error', $code);
            }
        }

        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(countrycodes::class, \admin_setting_countrycodes::class);
