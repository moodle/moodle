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

require_once('configBase.php');

/**
 * extend this class to use your local values,
 * replacing the array elements with your own
 */
class UesConfigBase extends ConfigBase {

    // Enrol/ues settings.
    private $config = array(
        array('course_form_replace',       'default', 'enrol_ues'),
        array('course_fullname',           'default', 'enrol_ues'),
        array('course_restricted_fields',  'default', 'enrol_ues'),
        array('course_shortname',          'default', 'enrol_ues'),
        array('editingteacher_role',       'default', 'enrol_ues'),
        array('email_report',              'default', 'enrol_ues'),
        array('enrollment_provider',       'default', 'enrol_ues'),
        array('error_threshold',           'default', 'enrol_ues'),
        array('grace_period',              'default', 'enrol_ues'),
        array('process_by_department',     'default', 'enrol_ues'),
        array('recover_grades',            'default', 'enrol_ues'),
        array('running',                   'default', 'enrol_ues'),
        array('student_role',              'default', 'enrol_ues'),
        array('sub_days',                  'default', 'enrol_ues'),
        array('teacher_role',              'default', 'enrol_ues'),
        array('user_auth',                 'default', 'enrol_ues'),
        array('user_city',                 'default', 'enrol_ues'),
        array('user_confirm',              'default', 'enrol_ues'),
        array('user_country',              'default', 'enrol_ues'),
        array('user_email',                'default', 'enrol_ues'),
        array('user_lang',                 'default', 'enrol_ues'),
        array('version',                   'default', 'enrol_ues'),
    );

    public function getUesConfigs() {
        return $this->config;
    }

    public function setUesConfigs() {
        foreach ($this->config as $conf) {
            set_config(implode(',', $conf));
        }
    }
}
