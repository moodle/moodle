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
 * Empty setting used to allow flags (advanced) on settings that can have no sensible default.
 *
 * Note: Only advanced makes sense right now - locked does not.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configempty extends \core_admin\setting\setting\configtext {
    /**
     * Constructor for the empty setting.
     *
     * @param string $name A unique ascii name for the setting.
     * @param string $visiblename Localised setting name
     * @param string $description Setting description
     */
    public function __construct($name, $visiblename, $description) {
        parent::__construct($name, $visiblename, $description, '', PARAM_RAW);
    }

    /**
     * Returns an XHTML string for the hidden field
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configempty', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', get_string('none'), $query);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configempty::class, \admin_setting_configempty::class);
