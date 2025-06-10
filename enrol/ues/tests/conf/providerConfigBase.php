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
class ProviderConfigBase extends ConfigBase {

    // Local provider settings.
    private $config = array(
        array('course_form_replace',       'default', 'local_provider'),
        array('course_fullname',           'default', 'local_provider'),
        array('course_restricted_fields',  'default', 'local_provider'),
        array('course_shortname',          'default', 'local_provider'),
        array('editingteacher_role',       'default', 'local_provider'),
        array('email_report',              'default', 'local_provider'),
        array('enrollment_provider',       'default', 'local_provider'),
        array('error_threshold',           'default', 'local_provider'),
        array('grace_period',              'default', 'local_provider'),
        array('process_by_department',     'default', 'local_provider'),
        array('recover_grades',            'default', 'local_provider'),
        array('running',                   'default', 'local_provider'),
        array('student_role',              'default', 'local_provider'),
        array('sub_days',                  'default', 'local_provider'),
        array('teacher_role',              'default', 'local_provider'),
        array('user_auth',                 'default', 'local_provider'),
        array('user_city',                 'default', 'local_provider'),
        array('user_confirm',              'default', 'local_provider'),
        array('user_country',              'default', 'local_provider'),
        array('user_email',                'default', 'local_provider'),
        array('user_lang',                 'default', 'local_provider'),
        array('version',                   'default', 'local_provider'),
    );
}
