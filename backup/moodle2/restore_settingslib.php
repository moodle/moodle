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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// TODO: Reduce these to the minimum because ui/dependencies are 100% separated

// Root restore settings

/**
 * root generic setting to store different things without dependencies
 */
class restore_generic_setting extends root_backup_setting {}

/**
 * root setting to control if restore will create user information
 * A lot of other settings are dependent of this (module's user info,
 * grades user info, messages, blogs...
 */
class restore_users_setting extends restore_generic_setting {}

/**
 * root setting to control if restore will create role assignments
 * or no (any level), depends of @restore_users_setting
 */
class restore_role_assignments_setting extends root_backup_setting {}

/**
 * root setting to control if restore will create activities
 * A lot of other settings (_included at activity levels)
 * are dependent of this setting
 */
class restore_activities_setting extends restore_generic_setting {}

/**
 * root setting to control if restore will create
 * comments or no, depends of @restore_users_setting
 * exactly in the same way than @restore_role_assignments_setting so we extend from it
 */
class restore_comments_setting extends restore_role_assignments_setting {}

/**
 * root setting to control if restore will create
 * completion info or no, depends of @restore_users_setting
 * exactly in the same way than @restore_role_assignments_setting so we extend from it
 */
class restore_userscompletion_setting extends restore_role_assignments_setting {}

/**
 * root setting to control if restore will create
 * logs or no, depends of @restore_users_setting
 * exactly in the same way than @restore_role_assignments_setting so we extend from it
 */
class restore_logs_setting extends restore_role_assignments_setting {}

/**
 * root setting to control if restore will create
 * grade_histories or no, depends of @restore_users_setting
 * exactly in the same way than @restore_role_assignments_setting so we extend from it
 */
class restore_grade_histories_setting extends restore_role_assignments_setting {}


// Course restore settings

/**
 * generic course setting to pass various settings between tasks and steps
 */
class restore_course_generic_setting extends course_backup_setting {}

/**
 * Setting to define is we are going to overwrite course configuration
 */
class restore_course_overwrite_conf_setting extends restore_course_generic_setting {}


class restore_course_generic_text_setting extends restore_course_generic_setting {

    public function __construct($name, $vtype, $value = null, $visibility = self::VISIBLE, $status = self::NOT_LOCKED) {
        parent::__construct($name, $vtype, $value, $visibility, $status);
        $this->set_ui(new backup_setting_ui_text($this, $name));
    }

}

// Section restore settings

/**
 * generic section setting to pass various settings between tasks and steps
 */
class restore_section_generic_setting extends section_backup_setting {}

/**
 * Setting to define if one section is included or no. Activities _included
 * settings depend of them if available
 */
class restore_section_included_setting extends restore_section_generic_setting {}

/**
 * section backup setting to control if section will include
 * user information or no, depends of @restore_users_setting
 */
class restore_section_userinfo_setting extends restore_section_generic_setting {}


// Activity backup settings

/**
 * generic activity setting to pass various settings between tasks and steps
 */
class restore_activity_generic_setting extends activity_backup_setting {}

/**
 * activity backup setting to control if activity will
 * be included or no, depends of @restore_activities_setting and
 * optionally parent section included setting
 */
class restore_activity_included_setting extends restore_activity_generic_setting {}

/**
 * activity backup setting to control if activity will include
 * user information or no, depends of @restore_users_setting
 */
class restore_activity_userinfo_setting extends restore_activity_generic_setting {}
