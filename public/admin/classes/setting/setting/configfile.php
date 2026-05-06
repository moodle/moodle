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
 * Path to a file.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configfile extends \core_admin\setting\setting\configtext {
    /**
     * Constructor
     * @param string $name A unique ascii name for the setting.
     *      Either 'mysetting' for core settings, or 'myplugin/mysetting' for those belonging to a plugin.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultdirectory default directory location
     */
    public function __construct($name, $visiblename, $description, $defaultdirectory) {
        parent::__construct($name, $visiblename, $description, $defaultdirectory, PARAM_RAW, 50);
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $CFG, $OUTPUT;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'size' => $this->size,
            'value' => $data,
            'showvalidity' => !empty($data),
            'valid' => $data && file_exists($data),
            'readonly' => !empty($CFG->preventexecpath) || $this->is_readonly(),
            'forceltr' => $this->get_force_ltr(),
        ];

        if ($context->readonly) {
            $this->visiblename .= '<div class="alert alert-info">' . get_string('execpathnotallowed', 'admin') . '</div>';
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configfile', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }

    #[\Override]
    public function write_setting($data) {
        global $CFG;
        if (!empty($CFG->preventexecpath)) {
            if ($this->get_setting() === null) {
                // Use default during installation.
                $data = $this->get_defaultsetting();
                if ($data === null) {
                    $data = '';
                }
            } else {
                return '';
            }
        }
        return parent::write_setting($data);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configfile::class, \admin_setting_configfile::class);
