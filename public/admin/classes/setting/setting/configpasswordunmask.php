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
 * Password field, allows unmasking of password
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class configpasswordunmask extends \core_admin\setting\setting\configtext {

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting default password
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, PARAM_RAW, 30);
    }

    /**
     * Log config changes if necessary.
     * @param string $name
     * @param string $oldvalue
     * @param string $value
     */
    protected function add_to_config_log($name, $oldvalue, $value) {
        if ($value !== '') {
            $value = '********';
        }
        if ($oldvalue !== '' and $oldvalue !== null) {
            $oldvalue = '********';
        }
        parent::add_to_config_log($name, $oldvalue, $value);
    }

    /**
     * Returns HTML for the field.
     *
     * @param   string  $data       Value for the field
     * @param   string  $query      Passed as final argument for format_admin_setting
     * @return  string              Rendered HTML
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'size' => $this->size,
            'value' => $this->is_readonly() ? null : $data,
            'forceltr' => $this->get_force_ltr(),
            'readonly' => $this->is_readonly(),
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configpasswordunmask', $context);
        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', null, $query);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configpasswordunmask::class, \admin_setting_configpasswordunmask::class);
