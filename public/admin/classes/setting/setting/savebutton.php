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
 * Show the save changes button.
 */
namespace core_admin\setting\setting;

class savebutton extends \admin_setting {
    /**
     * Constructor.
     *
     * @param string $name unique ascii name.
     * @param string $visiblename localised name.
     * @param string $description localised long description.
     * @param mixed $defaultsetting string or array depending on implementation.
     */
    public function __construct(string $name, string $visiblename = "", string $description = "", $defaultsetting = "") {
        $this->nosave = true;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Always returns true, does nothing.
     *
     * @return bool Always return true.
     */
    public function get_setting(): bool {
        return true;
    }

    /**
     * Always returns '', does not write anything.
     *
     * @param mixed $data string or array, must not be NULL.
     * @return string Always returns ''.
     */
    public function write_setting($data): string {
        return '';
    }

    /**
     * Return part of form with setting.
     *
     * This function should always be overwritten.
     *
     * @param mixed $data array or string depending on setting.
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = ''): string {
        global $OUTPUT;
        return $OUTPUT->render_from_template('core_admin/setting_savebutton', []);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(savebutton::class, \admin_setting_savebutton::class);
