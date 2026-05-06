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
 * Special text editor for site description.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_frontpagedesc extends admin_setting_confightmleditor {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('summary', get_string('frontpagedescription'), get_string('frontpagedescriptionhelp'), null,
            PARAM_RAW, 60, 15);
    }

    /**
     * Return the current setting
     * @return string The current setting
     */
    public function get_setting() {
        $site = course_get_format(get_site())->get_course();
        return $site->{$this->name};
    }

    /**
     * Save the new setting
     *
     * @param string $data The new value to save
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $DB, $SITE, $COURSE;
        $record = new stdClass();
        $record->id            = $SITE->id;
        $record->{$this->name} = $data;
        $record->timemodified  = time();

        course_get_format($SITE)->update_course_format_options($record);
        $DB->update_record('course', $record);

        // Reset caches.
        $SITE = $DB->get_record('course', array('id'=>$SITE->id), '*', MUST_EXIST);
        if ($SITE->id == $COURSE->id) {
            $COURSE = $SITE;
        }
        core_courseformat\base::reset_course_cache($SITE->id);

        return '';
    }

    /**
     * admin_setting_special_frontpagedesc is not meant to be overridden in config.php.
     *
     * @return bool
     */
    public function is_forceable(): bool {
        return false;
    }
}
