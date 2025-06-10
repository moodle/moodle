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
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

interface enrollment_factory {
    // Returns a semester_processor.
    public function semester_source();

    // Returns a semester_processor.
    public function semester_source2();

    // Returns a course_processor.
    public function course_source();

    // Returns a teacher_processor.
    public function teacher_source();

    // Retunrs a student_processor.
    public function student_source();

    // Returns teacher enrollment information for a given department.
    public function teacher_department_source();

    // Returns student enrollment information for a given department.
    public function student_department_source();
}

abstract class enrollment_provider implements enrollment_factory {
    // Simple settings array.
    public $settings = array();

    public function get_setting($key, $default=false) {
        $attempt = get_config($this->plugin_key(), $key);

        if (isset($this->settings[$key])) {
            $def = empty($this->settings[$key]) ? $default : $this->settings[$key];
        } else {
            $def = $default;
        }

        return empty($attempt) ? $def : $attempt;
    }

    // Override for special behavior hooks.
    public function preprocess($enrol = null) {
        return true;
    }

    public function postprocess($enrol = null) {
        return true;
    }

    public function supports_reverse_lookups() {
        $source = $this->teacher_info_source();
        return !empty($source);
    }

    public function supports_section_lookups() {
        return !(is_null($this->student_source()) or is_null($this->teacher_source()));
    }

    public function supports_department_lookups() {
        return !(is_null($this->teacher_source()) or is_null($this->teacher_department_source()));
    }

    // Optionally return a source for reverse lookups.
    public function teacher_info_source() {
        return null;
    }

    public function teacher_source() {
        return null;
    }

    public function teacher_department_source() {
        return null;
    }

    public function student_source() {
        return null;
    }

    public function student_department_source() {
        return null;
    }

    protected function simple_settings($settings) {
        global $CFG;

        $pluginkey = $this->plugin_key();

        $s = ues::gen_str($pluginkey);
        foreach ($this->settings as $key => $default) {
            $settings->add(new admin_setting_configtext("$pluginkey/$key",
                $s($key), $s("{$key}_desc", $CFG), $default));
        }
    }

    // Override this function for displaying settings on the UES page as well.
    public function settings($settings) {

        if (!empty($this->settings)) {
            $settings->add(new admin_setting_heading('provider_heading',
                $this->get_name(), ''));

            $this->simple_settings($settings);
        }
    }

    // Display name.
    public static function get_name() {
        $class = get_called_class();
        return get_string('pluginname', $class::plugin_key());
    }

    public static function translate_error($code) {
        $class = get_called_class();
        return get_string($code, $class::plugin_key());
    }

    // Returns the Moodle plugin key for this provider.
    public static function plugin_key() {
        return "enrol_ues";
    }
}
