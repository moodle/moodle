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
 * Defines classes used to handle backup settings
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// TODO: Reduce these to the minimum because ui/dependencies are 100% separated

// Root backup settings

/**
 * root generic setting to store different things without dependencies
 */
class backup_generic_setting extends root_backup_setting {}

/**
 * root setting to handle backup file names (no dependencies nor anything else)
 */
class backup_filename_setting extends backup_generic_setting {

    /**
     * Instantiates a setting object
     *
     * @param string $name Name of the setting
     * @param string $vtype Type of the setting, eg {@link base_setting::IS_TEXT}
     * @param mixed $value Value of the setting
     * @param bool $visibility Is the setting visible in the UI, eg {@link base_setting::VISIBLE}
     * @param int $status Status of the setting with regards to the locking, eg {@link base_setting::NOT_LOCKED}
     */
    public function __construct($name, $vtype, $value = null, $visibility = self::VISIBLE, $status = self::NOT_LOCKED) {
        parent::__construct($name, $vtype, $value, $visibility, $status);
    }

    public function set_ui_filename($label, $value, ?array $options = null) {
        $this->make_ui(self::UI_HTML_TEXTFIELD, $label, null, $options);
        $this->set_value($value);
    }
}

/**
 * root setting to control if backup will include user information
 * A lot of other settings are dependent of this (module's user info,
 * grades user info, messages, blogs...
 */
class backup_users_setting extends backup_generic_setting {}

/**
 * root setting to control if backup will include permission information by roles
 */
class backup_permissions_setting extends backup_generic_setting {
}

/**
 * root setting to control if backup will include group information depends on @backup_users_setting
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2014 Matt Sammarco
 */
class backup_groups_setting extends backup_generic_setting {
}

/**
 * root setting to control if backup will include custom field information
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2018 Daniel Neis Araujo
 */
class backup_customfield_setting extends backup_generic_setting {
}

/**
 * root setting to control if backup will include activities or no.
 * A lot of other settings (_included at activity levels)
 * are dependent of this setting
 */
class backup_activities_setting extends backup_generic_setting {}

/**
 * root setting to control if backup will generate anonymized
 * user info or no, depends of @backup_users_setting so only is
 * available if the former is enabled (apart from security
 * that can change it
 */
class backup_anonymize_setting extends root_backup_setting {}

/**
 * root setting to control if backup will include
 * role assignments or no (any level), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_role_assignments_setting extends backup_anonymize_setting {}

/**
 * root setting to control if backup will include
 * logs or no (any level), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_logs_setting extends backup_anonymize_setting {}

/**
 * root setting to control if backup will include
 * comments or no (any level), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_comments_setting extends backup_anonymize_setting {}

/**
 * root setting to control if backup will include badges or not,
 * depends on @backup_activities_setting
 */
class backup_badges_setting extends backup_generic_setting {}

/**
 * root setting to control if backup will include
 * calender events or no (any level), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_calendarevents_setting extends backup_anonymize_setting {}

/**
 * root setting to control if backup will include
 * users completion data or no (any level), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_userscompletion_setting extends backup_anonymize_setting {}

/**
 * root setting to control if backup will include competencies or not.
 */
class backup_competencies_setting extends backup_generic_setting {

    /**
     * backup_competencies_setting constructor.
     */
    public function __construct() {
        $defaultvalue = false;
        $visibility = base_setting::HIDDEN;
        $status = base_setting::LOCKED_BY_CONFIG;
        if (\core_competency\api::is_enabled()) {
            $defaultvalue = true;
            $visibility = base_setting::VISIBLE;
            $status = base_setting::NOT_LOCKED;
        }
        parent::__construct('competencies', base_setting::IS_BOOLEAN, $defaultvalue, $visibility, $status);
    }
}

// Section backup settings

/**
 * generic section setting to pass various settings between tasks and steps
 */
class backup_section_generic_setting extends section_backup_setting {}

/**
 * Setting to define if one section is included or no. Activities _included
 * settings depend of them if available
 */
class backup_section_included_setting extends section_backup_setting {}

/**
 * section backup setting to control if section will include
 * user information or no, depends of @backup_users_setting
 */
class backup_section_userinfo_setting extends section_backup_setting {}

/**
 * Subsection base class (section delegated to a course module).
 */
class subsection_backup_setting extends section_backup_setting {
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
 * generic section setting to pass various settings between tasks and steps
 */
class backup_subsection_generic_setting extends subsection_backup_setting {
}

/**
 * Setting to define if one section is included or no. Activities _included
 * settings depend of them if available
 */
class backup_subsection_included_setting extends subsection_backup_setting {
}

/**
 * section backup setting to control if section will include
 * user information or no, depends of @backup_users_setting
 */
class backup_subsection_userinfo_setting extends subsection_backup_setting {
}


// Activity backup settings

/**
 * generic activity setting to pass various settings between tasks and steps
 */
class backup_activity_generic_setting extends activity_backup_setting {}

/**
 * activity backup setting to control if activity will
 * be included or no, depends of @backup_activities_setting and
 * optionally parent section included setting
 */
class backup_activity_included_setting extends activity_backup_setting {}

/**
 * activity backup setting to control if activity will include
 * user information or no, depends of @backup_users_setting
 */
class backup_activity_userinfo_setting extends activity_backup_setting {}

/**
 * Subactivity base class (activity inside a delegated section).
 */
class subactivity_backup_setting extends activity_backup_setting {
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
 * Generic subactivity (activity inside a delegated section) setting to pass various settings between tasks and steps
 */
class backup_subactivity_generic_setting extends subactivity_backup_setting {
}

/**
 * Subactivity (activity inside a delegated section) backup setting to control if activity will
 * be included or no, depends of @backup_activities_setting and
 * optionally parent section included setting
 */
class backup_subactivity_included_setting extends subactivity_backup_setting {
}

/**
 * Subactivity (activity inside a delegated section) backup setting to control if activity will include
 * user information or no, depends of @backup_users_setting
 */
class backup_subactivity_userinfo_setting extends subactivity_backup_setting {
}

/**
 * Root setting to control if backup will include content bank content or no
 */
class backup_contentbankcontent_setting extends backup_generic_setting {
}

/**
 * Root setting to control if backup will include xAPI state or not.
 */
class backup_xapistate_setting extends backup_generic_setting {
}
