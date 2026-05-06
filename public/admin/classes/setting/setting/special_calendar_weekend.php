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
 * Weekend days selection for the calendar.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class special_calendar_weekend extends \core_admin\setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $name = 'calendar_weekend';
        $visiblename = get_string('calendar_weekend', 'admin');
        $description = get_string('helpweekenddays', 'admin');
        $default = ['0', '6']; // Saturdays and Sundays.
        parent::__construct($name, $visiblename, $description, $default);
    }

    /**
     * Gets the current settings as an array
     *
     * @return mixed Null if none, else array of settings
     */
    public function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return null;
        }
        if ($result === '') {
            return [];
        }
        $settings = [];
        for ($i = 0; $i < 7; $i++) {
            if ($result & (1 << $i)) {
                $settings[] = $i;
            }
        }
        return $settings;
    }

    /**
     * Save the new settings
     *
     * @param array $data Array of new settings
     * @return string error message or empty string on success
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }
        unset($data['xxxxx']);
        $result = 0;
        foreach ($data as $index) {
            $result |= 1 << $index;
        }
        return ($this->config_write($this->name, $result) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Return XHTML to display the control
     *
     * @param array $data array of selected days
     * @param string $query
     * @return string XHTML for display (field + wrapping div(s)
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;

        // The order matters very much because of the implied numeric keys.
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $context = (object) [
            'name' => $this->get_full_name(),
            'id' => $this->get_id(),
            'days' => array_map(function ($index) use ($days, $data) {
                return [
                    'index' => $index,
                    'label' => get_string($days[$index], 'calendar'),
                    'checked' => in_array($index, $data),
                ];
            }, array_keys($days)),
        ];

        $element = $OUTPUT->render_from_template('core_admin/setting_special_calendar_weekend', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', null, $query);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(special_calendar_weekend::class, \admin_setting_special_calendar_weekend::class);
