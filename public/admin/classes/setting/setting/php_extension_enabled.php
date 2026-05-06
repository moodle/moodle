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
 * Admin setting to show if a php extension is enabled or not.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class php_extension_enabled extends \core_admin\setting {
    /** @var string The name of the extension to check for */
    private $extension;

    /**
     * Calls parent::__construct with specific arguments
     *
     * @param string $name The name of the setting
     * @param string $visiblename The visible name of the setting
     * @param string $description The description of the setting
     * @param string $extension The name of the extension to check for
     */
    public function __construct($name, $visiblename, $description, $extension) {
        $this->extension = $extension;
        $this->nosave = true;
        parent::__construct($name, $visiblename, $description, '');
    }

    #[\Override]
    public function get_setting() {
        return true;
    }

    #[\Override]
    public function get_defaultsetting() {
        return true;
    }

    #[\Override]
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $OUTPUT;

        $o = '';
        if (!extension_loaded($this->extension)) {
            $warning = $OUTPUT->pix_icon('i/warning', '') . ' ' . $this->description;

            $o .= format_admin_setting($this, $this->visiblename, $warning);
        }
        return $o;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(php_extension_enabled::class, \admin_setting_php_extension_enabled::class);
