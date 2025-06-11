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

namespace theme_snap;

/**
 * Class to render input of type url in settings pages.
 * @package theme_snap
 * @author Bryan Cruz <bryan.cruz@openlms.net>
 * @copyright 2024 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configcategoryid extends \admin_setting_configtext {
    /**
     * Config categoryid constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting'
     * for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, PARAM_INT, 10);
    }

    public function output_html($data, $query='') {
        global $DB;

        static $datalistrendered = false;
        $datalist = '';
        if (!$datalistrendered) {
            $sql = "SELECT id, name FROM {course_categories} WHERE id != ?";
            $categories = $DB->get_records_sql($sql, [SITEID]);
            $datalist = '<datalist id="categoryids">';
            foreach ($categories as $category) {
                $datalist .= '<option value="' .$category->id. '">' .s($category->name). '</option>';
            }
            $datalist .= '</datalist>';
            $datalistrendered = true;
        }

        $default = $this->get_defaultsetting();
        return format_admin_setting($this, $this->visiblename,
            '<div class="form-text defaultsnext"><input list="categoryids" size="' .
            $this->size. '" id="' .$this->get_id(). '" name="' .$this->get_full_name().
            '" value="' .s($data). '" />' .$datalist. '</div>',
            $this->description, true, '', $default, $query);
    }
}
