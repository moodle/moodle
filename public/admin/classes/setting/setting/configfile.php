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
 * Path to directory
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configfile extends admin_setting_configtext {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultdirectory default directory location
     */
    public function __construct($name, $visiblename, $description, $defaultdirectory) {
        parent::__construct($name, $visiblename, $description, $defaultdirectory, PARAM_RAW, 50);
    }

    /**
     * Returns XHTML for the field
     *
     * Returns XHTML for the field and also checks whether the file
     * specified in $data exists using file_exists()
     *
     * @param string $data File name and path to use in value attr
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query='') {
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
            $this->visiblename .= '<div class="alert alert-info">'.get_string('execpathnotallowed', 'admin').'</div>';
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configfile', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }

    /**
     * Checks if execpatch has been disabled in config.php
     */
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
