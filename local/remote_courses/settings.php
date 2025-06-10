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
 * Configuration settings for local_remote_courses.
 *
 * @package    local_remote_courses
 * @copyright  2016 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
     $settings = new admin_settingpage('local_remote_courses', get_string('pluginname', 'local_remote_courses'));
     $ADMIN->add('localplugins', $settings);

     $settings->add(new admin_setting_configtext('local_remote_courses/extracttermcode',
        new lang_string('extracttermcode', 'local_remote_courses'),
        new lang_string('extracttermcode_desc', 'local_remote_courses'), '', PARAM_NOTAGS));
}
