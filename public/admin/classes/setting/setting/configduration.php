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
 * Seconds duration setting.
 *
 * @copyright 2012 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configduration extends admin_setting {

    /** @var int default duration unit */
    protected $defaultunit;
    /** @var callable|null Validation function */
    protected $validatefunction = null;

    /** @var int The minimum allowed value */
    protected int $minduration = 0;

    /** @var null|int The maximum allowed value */
    protected null|int $maxduration = null;

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *                     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param mixed $defaultsetting string or array depending on implementation
     * @param int $defaultunit - day, week, etc. (in seconds)
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $defaultunit = 86400) {
        if (is_number($defaultsetting)) {
            $defaultsetting = self::parse_seconds($defaultsetting);
        }
        $units = self::get_units();
        if (isset($units[$defaultunit])) {
            $this->defaultunit = $defaultunit;
        } else {
            $this->defaultunit = 86400;
        }
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Set the minimum allowed value.
     * This must be at least 0.
     *
     * @param int $duration
     */
    public function set_min_duration(int $duration): void {
        if ($duration < 0) {
            throw new coding_exception('The minimum duration must be at least 0.');
        }

        $this->minduration = $duration;
    }

    /**
     * Set the maximum allowed value.
     *
     * A value of null will disable the maximum duration value.
     *
     * @param int|null $duration
     */
    public function set_max_duration(?int $duration): void {
        $this->maxduration = $duration;
    }

    /**
     * Sets a validate function.
     *
     * The callback will be passed one parameter, the new setting value, and should return either
     * an empty string '' if the value is OK, or an error message if not.
     *
     * @param callable|null $validatefunction Validate function or null to clear
     * @since Moodle 3.10
     */
    public function set_validate_function(?callable $validatefunction = null) {
        $this->validatefunction = $validatefunction;
    }

    /**
     * Validate the setting. This uses the callback function if provided; subclasses could override
     * to carry out validation directly in the class.
     *
     * @param int $data New value being set
     * @return string Empty string if valid, or error message text
     * @since Moodle 3.10
     */
    protected function validate_setting(int $data): string {
        if ($data < $this->minduration) {
            return get_string(
                'configduration_low',
                'admin',
                self::get_duration_text($this->minduration, get_string('numseconds', 'core', 0))
            );
        }

        if ($this->maxduration && $data > $this->maxduration) {
            return get_string('configduration_high', 'admin', self::get_duration_text($this->maxduration));
        }

        // If validation function is specified, call it now.
        if ($this->validatefunction) {
            return call_user_func($this->validatefunction, $data);
        }
        return '';
    }

    /**
     * Returns selectable units.
     * @static
     * @return array
     */
    protected static function get_units() {
        return array(
            604800 => get_string('weeks'),
            86400 => get_string('days'),
            3600 => get_string('hours'),
            60 => get_string('minutes'),
            1 => get_string('seconds'),
        );
    }

    /**
     * Converts seconds to some more user friendly string.
     * @static
     * @param int $seconds
     * @param null|string The value to use when the duration is empty. If not specified, a "None" value is used.
     * @return string
     */
    protected static function get_duration_text(int $seconds, ?string $emptyvalue = null): string {
        if (empty($seconds)) {
            if ($emptyvalue !== null) {
                return $emptyvalue;
            }
            return get_string('none');
        }
        $data = self::parse_seconds($seconds);
        switch ($data['u']) {
            case (60*60*24*7):
                return get_string('numweeks', '', $data['v']);
            case (60*60*24):
                return get_string('numdays', '', $data['v']);
            case (60*60):
                return get_string('numhours', '', $data['v']);
            case (60):
                return get_string('numminutes', '', $data['v']);
            default:
                return get_string('numseconds', '', $data['v']*$data['u']);
        }
    }

    /**
     * Finds suitable units for given duration.
     * @static
     * @param int $seconds
     * @return array
     */
    protected static function parse_seconds($seconds) {
        foreach (self::get_units() as $unit => $unused) {
            if ($seconds % $unit === 0) {
                return array('v'=>(int)($seconds/$unit), 'u'=>$unit);
            }
        }
        return array('v'=>(int)$seconds, 'u'=>1);
    }

    /**
     * Get the selected duration as array.
     *
     * @return mixed An array containing 'v'=>xx, 'u'=>xx, or null if not set
     */
    public function get_setting() {
        $seconds = $this->config_read($this->name);
        if (is_null($seconds)) {
            return null;
        }

        return self::parse_seconds($seconds);
    }

    /**
     * Store the duration as seconds.
     *
     * @param array $data Must be form 'h'=>xx, 'm'=>xx
     * @return string error message or empty string on success
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }

        $unit = (int)$data['u'];
        $value = (int)$data['v'];
        $seconds = $value * $unit;

        // Validate the new setting.
        $error = $this->validate_setting($seconds);
        if ($error) {
            return $error;
        }

        $result = $this->config_write($this->name, $seconds);
        return ($result ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns duration text+select fields.
     *
     * @param array $data Must be form 'v'=>xx, 'u'=>xx
     * @param string $query
     * @return string duration text+select fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        if (is_number($default)) {
            $defaultinfo = self::get_duration_text($default);
        } else if (is_array($default)) {
            $defaultinfo = self::get_duration_text($default['v']*$default['u']);
        } else {
            $defaultinfo = null;
        }

        $inputid = $this->get_id() . 'v';
        $units = array_filter(self::get_units(), function($unit): bool {
            if (!$this->maxduration) {
                // No duration limit. All units are valid.
                return true;
            }

            return $unit <= $this->maxduration;
        }, ARRAY_FILTER_USE_KEY);

        $defaultunit = $this->defaultunit;

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data['v'] ?? '',
            'readonly' => $this->is_readonly(),
            'options' => array_map(function($unit) use ($units, $data, $defaultunit) {
                return [
                    'value' => $unit,
                    'name' => $units[$unit],
                    'selected' => isset($data) && (($data['v'] == 0 && $unit == $defaultunit) || $unit == $data['u'])
                ];
            }, array_keys($units))
        ];

        $element = $OUTPUT->render_from_template('core_admin/setting_configduration', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, $inputid, '', $defaultinfo, $query);
    }
}
