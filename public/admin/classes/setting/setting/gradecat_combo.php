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
 * Grade category settings
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradecat_combo extends \core_admin\setting {
    /** @var array Array of choices value=>label. */
    public $choices;

    /**
     * Sets choices and calls parent::__construct with passed arguments.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array depending on implementation
     * @param array $choices An array of choices for the control
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    #[\Override]
    public function get_setting() {
        global $CFG;

        $value = $this->config_read($this->name);
        $flag  = $this->config_read($this->name . '_flag');

        if (is_null($value) || is_null($flag)) {
            return null;
        }

        // Bitwise operation is still required, in cases where unused 'advanced' flag is still set.
        $flag   = (int)$flag;
        $forced = (bool)(1 & $flag); // First bit.

        return ['value' => $value, 'forced' => $forced];
    }

    #[\Override]
    public function write_setting($data) {
        global $CFG;

        $value = $data['value'];
        $forced = empty($data['forced']) ? 0 : 1;

        if (!in_array($value, array_keys($this->choices))) {
            return 'Error setting ';
        }

        $oldvalue = $this->config_read($this->name);
        $oldflag = (int)$this->config_read($this->name . '_flag');
        $oldforced = (1 & $oldflag); // First bit.

        $result1 = $this->config_write($this->name, $value);
        $result2 = $this->config_write($this->name . '_flag', $forced);

        // Force regrade if needed.
        if ($oldforced != $forced || ($forced && $value != $oldvalue)) {
            require_once($CFG->libdir . '/gradelib.php');
            \grade_category::updated_forced_settings();
        }

        if ($result1 && $result2) {
            return '';
        } else {
            return get_string('errorsetting', 'admin');
        }
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $OUTPUT;

        $value  = $data['value'];

        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            $defaultinfo = [];
            if (isset($this->choices[$default['value']])) {
                $defaultinfo[] = $this->choices[$default['value']];
            }
            if (!empty($default['forced'])) {
                $defaultinfo[] = get_string('force');
            }
            $defaultinfo = implode(', ', $defaultinfo);
        } else {
            $defaultinfo = null;
        }

        $options = $this->choices;
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'forced' => !empty($data['forced']),
            'options' => array_map(function ($option) use ($options, $value) {
                return [
                    'value' => $option,
                    'name' => $options[$option],
                    'selected' => $option == $value,
                ];
            }, array_keys($options)),
        ];

        $element = $OUTPUT->render_from_template('core_admin/setting_gradecat_combo', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(gradecat_combo::class, \admin_setting_gradecat_combo::class);
