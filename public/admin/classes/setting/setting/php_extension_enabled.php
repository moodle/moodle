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
 * Admin setting to show if a php extension is enabled or not.
 *
 * @copyright 2013 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class php_extension_enabled extends \admin_setting {

    /** @var string The name of the extension to check for */
    private $extension;

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct($name, $visiblename, $description, $extension) {
        $this->extension = $extension;
        $this->nosave = true;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Outputs the html for this setting.
     * @return string Returns an XHTML string
     */
    public function output_html($data, $query='') {
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
