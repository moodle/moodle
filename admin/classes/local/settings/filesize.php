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
 * File size admin setting.
 *
 * @package    core_admin
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_admin\local\settings;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * An admin setting to support entering and displaying of file sizes in Bytes, KB, MB or GB.
 *
 * @copyright   2019 Shamim Rezaie <shamim@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filesize extends \admin_setting {

    /** @var int The byte unit. Number of bytes in a byte */
    const UNIT_B = 1;

    /** @var int The kilobyte unit (number of bytes in a kilobyte) */
    const UNIT_KB = 1024;

    /** @var int The megabyte unit (number of bytes in a megabyte) */
    const UNIT_MB = 1048576;

    /** @var int The gigabyte unit (number of bytes in a gigabyte) */
    const UNIT_GB = 1073741824;

    /** @var int default size unit */
    protected $defaultunit;

    /**
     * Constructor
     *
     * @param string    $name           unique ascii name, either 'mysetting' for settings that in config,
     *                                  or 'myplugin/mysetting' for ones in config_plugins.
     * @param string    $visiblename    localised name
     * @param string    $description    localised long description
     * @param int|null  $defaultvalue   Value of the settings in bytes
     * @param int|null  $defaultunit    GB, MB, etc. (in bytes)
     */
    public function __construct(string $name, string $visiblename, string $description,
            int $defaultvalue = null, int $defaultunit = null) {

        $defaultsetting = self::parse_bytes($defaultvalue);

        if ($defaultunit && array_key_exists($defaultunit, self::get_units())) {
            $this->defaultunit = $defaultunit;
        } else {
            $this->defaultunit = self::UNIT_MB;
        }
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Returns selectable units.
     *
     * @return  array
     */
    protected static function get_units(): array {
        return [
            self::UNIT_GB => get_string('sizegb'),
            self::UNIT_MB => get_string('sizemb'),
            self::UNIT_KB => get_string('sizekb'),
            self::UNIT_B  => get_string('sizeb'),
        ];
    }

    /**
     * Converts bytes to some more user friendly string.
     *
     * @param   int     $bytes  The number of bytes we want to convert from
     * @return  string
     */
    protected static function get_size_text(int $bytes): string {
        if (empty($bytes)) {
            return get_string('none');
        }
        return display_size($bytes);
    }

    /**
     * Finds suitable units for given file size.
     *
     * @param   int     $bytes  The number of bytes
     * @return  array           Parsed file size in the format of ['v' => value, 'u' => unit]
     */
    protected static function parse_bytes(int $bytes): array {
        foreach (self::get_units() as $unit => $unused) {
            if ($bytes % $unit === 0) {
                return ['v' => (int)($bytes / $unit), 'u' => $unit];
            }
        }
        return ['v' => (int)$bytes, 'u' => self::UNIT_B];
    }

    /**
     * Get the selected file size as array.
     *
     * @return  array|null  An array containing 'v' => xx, 'u' => xx, or null if not set
     */
    public function get_setting(): ?array {
        $bytes = $this->config_read($this->name);
        if (is_null($bytes)) {
            return null;
        }

        return self::parse_bytes($bytes);
    }

    /**
     * Store the file size as bytes.
     *
     * @param   array   $data   Must be form 'h' => xx, 'm' => xx
     * @return  string          The error string if any
     */
    public function write_setting($data): string {
        if (!is_array($data)) {
            return '';
        }

        if (!is_numeric($data['v']) || $data['v'] < 0) {
            return get_string('errorsetting', 'admin');
        }

        $bytes = $data['v'] * $data['u'];

        $result = $this->config_write($this->name, $bytes);
        return ($result ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns file size text+select fields.
     *
     * @param   array   $data   The current setting value. Must be form 'v' => xx, 'u' => xx.
     * @param   string  $query  Admin search query to be highlighted.
     * @return  string          File size text+select fields and wrapping div(s).
     */
    public function output_html($data, $query = ''): string {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        if (is_number($default)) {
            $defaultinfo = self::get_size_text($default);
        } else if (is_array($default)) {
            $defaultinfo = self::get_size_text($default['v'] * $default['u']);
        } else {
            $defaultinfo = null;
        }

        $inputid = $this->get_id() . 'v';
        $units = self::get_units();
        $defaultunit = $this->defaultunit;

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data['v'],
            'readonly' => $this->is_readonly(),
            'options' => array_map(function($unit, $title) use ($data, $defaultunit) {
                return [
                    'value' => $unit,
                    'name' => $title,
                    'selected' => ($data['v'] == 0 && $unit == $defaultunit) || $unit == $data['u']
                ];
            }, array_keys($units), $units)
        ];

        $element = $OUTPUT->render_from_template('core_admin/setting_configfilesize', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, $inputid, '', $defaultinfo, $query);
    }
}
