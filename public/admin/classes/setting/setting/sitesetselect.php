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
 * Special select for frontpage - stores data in course table
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_sitesetselect extends admin_setting_configselect {
    /**
     * Returns the site name for the selected site
     *
     * @see get_site()
     * @return string The site name of the selected site
     */
    public function get_setting() {
        $site = course_get_format(get_site())->get_course();
        return $site->{$this->name};
    }

    /**
     * Updates the database and save the setting
     *
     * @param string data
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $DB, $SITE, $COURSE;
        if (!in_array($data, array_keys($this->choices))) {
            return get_string('errorsetting', 'admin');
        }
        $record = new stdClass();
        $record->id           = SITEID;
        $temp                 = $this->name;
        $record->$temp        = $data;
        $record->timemodified = time();

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
     * admin_setting_sitesetselect is not meant to be overridden in config.php.
     *
     * @return bool
     */
    public function is_forceable(): bool {
        return false;
    }
}
