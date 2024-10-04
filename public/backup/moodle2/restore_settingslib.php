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
 * Defines classes used to handle restore settings
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
 * root setting to control if restore will create override permission information by roles
 */
class restore_permissions_setting extends restore_generic_setting {
}

/**
 * root setting to control if restore will create groups/grouping information. Depends on @restore_users_setting
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2014 Matt Sammarco
 */
class restore_groups_setting extends restore_generic_setting {
}

/**
 * root setting to control if restore will include custom field information
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2018 Daniel Neis Araujo
 */
class restore_customfield_setting extends restore_generic_setting {
}

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
 * root setting to control if restore will create badges or not,
 * depends on @restore_activities_setting
 */
class restore_badges_setting extends restore_generic_setting {}

/**
 * root setting to control if competencies will also be restored.
 */
class restore_competencies_setting extends restore_generic_setting {

    /**
     * restore_competencies_setting constructor.
     * @param bool $hascompetencies Flag whether to set the restore setting as checked and unlocked.
     */
    public function __construct($hascompetencies) {
        $defaultvalue = false;
        $visibility = base_setting::HIDDEN;
        $status = base_setting::LOCKED_BY_CONFIG;
        if (\core_competency\api::is_enabled()) {
            $visibility = base_setting::VISIBLE;
            if ($hascompetencies) {
                $defaultvalue = true;
                $status = base_setting::NOT_LOCKED;
            }
        }
        parent::__construct('competencies', base_setting::IS_BOOLEAN, $defaultvalue, $visibility, $status);
    }
}

/**
 * root setting to control if restore will create
 * events or no, depends of @restore_users_setting
 * exactly in the same way than @restore_role_assignments_setting so we extend from it
 */
class restore_calendarevents_setting extends restore_role_assignments_setting {}

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

/**
 * Setting to switch between current and new course name/startdate
 *
 * @copyright   2017 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_course_defaultcustom_setting extends restore_course_generic_setting {
    /**
     * Validates that the value $value has type $vtype
     * @param int $vtype
     * @param mixed $value
     * @return mixed
     */
    public function validate_value($vtype, $value) {
        if ($value === false) {
            // Value "false" means default and is allowed for this setting type even if it does not match $vtype.
            return $value;
        }
        return parent::validate_value($vtype, $value);
    }

    /**
     * Special method for this element only. When value is "false" returns the default value.
     * @return mixed
     */
    public function get_normalized_value() {
        $value = $this->get_value();
        if ($value === false && $this->get_ui() instanceof backup_setting_ui_defaultcustom) {
            $attributes = $this->get_ui()->get_attributes();
            return $attributes['defaultvalue'];
        }
        return $value;
    }
}


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

/**
 * Subsection base class (delegated section).
 */
class restore_subsection_generic_setting extends restore_section_generic_setting {
    /**
     * Class constructor.
     *
     * @param string $name Name of the setting
     * @param string $vtype Type of the setting, for example base_setting::IS_TEXT
     * @param mixed $value Value of the setting
     * @param bool $visibility Is the setting visible in the UI, for example base_setting::VISIBLE
     * @param int $status Status of the setting with regards to the locking, for example base_setting::NOT_LOCKED
     */
    public function __construct($name, $vtype, $value = null, $visibility = self::VISIBLE, $status = self::NOT_LOCKED) {
        parent::__construct($name, $vtype, $value, $visibility, $status);
        $this->level = self::SUBSECTION_LEVEL;
    }
}

/**
 * Setting to define if one subsection is included or no.
 *
 * Activities _included settings depend of them if available.
 */
class restore_subsection_included_setting extends restore_subsection_generic_setting {
}

/**
 * Subsection backup setting to control if section will include
 * user information or no, depends of @restore_users_setting.
 */
class restore_subsection_userinfo_setting extends restore_subsection_generic_setting {
}

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

/**
 * Generic subactivity setting to pass various settings between tasks and steps
 */
class restore_subactivity_generic_setting extends restore_activity_generic_setting {
    /**
     * Class constructor.
     *
     * @param string $name Name of the setting
     * @param string $vtype Type of the setting, for example base_setting::IS_TEXT
     * @param mixed $value Value of the setting
     * @param bool $visibility Is the setting visible in the UI, for example base_setting::VISIBLE
     * @param int $status Status of the setting with regards to the locking, for example base_setting::NOT_LOCKED
     */
    public function __construct($name, $vtype, $value = null, $visibility = self::VISIBLE, $status = self::NOT_LOCKED) {
        parent::__construct($name, $vtype, $value, $visibility, $status);
        $this->level = self::SUBACTIVITY_LEVEL;
    }
}

/**
 * Subactivity backup setting to control if activity will be included or no.
 *
 * Depends of restore_activities_setting and optionally parent section included setting.
 */
class restore_subactivity_included_setting extends restore_subactivity_generic_setting {
}

/**
 * Subactivity backup setting to control if activity will include user information.
 *
 * Depends of restore_users_setting.
 */
class restore_subactivity_userinfo_setting extends restore_subactivity_generic_setting {
}

/**
 * root setting to control if restore will create content bank content or no
 */
class restore_contentbankcontent_setting extends restore_generic_setting {
}

/**
 * Root setting to control if restore will create xAPI states or not.
 */
class restore_xapistate_setting extends restore_generic_setting {
}
