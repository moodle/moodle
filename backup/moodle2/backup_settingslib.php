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

// Root backup settings

/**
 * root generic setting to store different things without dependencies
 */
class backup_generic_setting extends root_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Nothing to do, no dependencies
    }
}

/**
 * root setting to handle backup file names (no dependencies nor anything else)
 */
class backup_filename_setting extends backup_generic_setting {

    public function  __construct($name, $vtype, $value = null, $visibility = self::VISIBLE, $status = self::NOT_LOCKED) {
        parent::__construct($name, $vtype, $value, $visibility, $status);
    }

    public function set_ui($label, $value, array $options = null) {
        parent::make_ui(self::UI_HTML_TEXTFIELD, $label, null, $options);
        $this->set_value($value);
    }
}

/**
 * root setting to control if backup will include user information
 * A lot of other settings are dependant of this (module's user info,
 * grades user info, messages, blogs...
 */
class backup_users_setting extends backup_generic_setting {
}

/**
 * root setting to control if backup will include activities or no.
 * A lot of other settings (_included at activity levels)
 * are dependent of this setting
 */
class backup_activities_setting extends backup_generic_setting {
}

/**
 * root setting to control if backup will generate anonymized
 * user info or no, depends of @backup_users_setting so only is
 * availabe if the former is enabled (apart from security
 * that can change it
 */
class backup_anonymize_setting extends root_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // If change detected in backup_users_setting, proceed
        if ($setting instanceof backup_users_setting) {
            switch ($ctype) {
                case self::CHANGED_VALUE: // backup_users = false, this too, and locked
                    if (!$setting->get_value()) {
                        $this->set_value(false);
                        $this->set_status(self::LOCKED_BY_HIERARCHY);
                    }
                    break;
                case self::CHANGED_VISIBILITY: // backup_users not visible, this too
                    if (!$setting->get_visibility()) {
                        $this->set_visibility(false);
                    }
                    break;
                case self::CHANGED_STATUS: // backup_users unlocked, this too
                    if ($setting->get_status() == self::NOT_LOCKED) {
                        $this->set_status(self::NOT_LOCKED);
                    }
                    break;
            }
        }
    }
}

/**
 * root setting to control if backup will include
 * user files or no (images, local storage), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_user_files_setting extends backup_anonymize_setting {
    // Nothing to do. All the logic is in backup_anonymize_setting
}

/**
 * root setting to control if backup will include
 * role assignments or no (any level), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_role_assignments_setting extends backup_anonymize_setting {
    // Nothing to do. All the logic is in backup_anonymize_setting
}

/**
 * root setting to control if backup will include
 * logs or no (any level), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_logs_setting extends backup_anonymize_setting {
    // Nothing to do. All the logic is in backup_anonymize_setting
}

/**
 * root setting to control if backup will include
 * comments or no (any level), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_comments_setting extends backup_anonymize_setting {
    // Nothing to do. All the logic is in backup_anonymize_setting
}

/**
 * root setting to control if backup will include
 * users completion data or no (any level), depends of @backup_users_setting
 * exactly in the same way than @backup_anonymize_setting so we extend from it
 */
class backup_userscompletion_setting extends backup_anonymize_setting {
    // Nothing to do. All the logic is in backup_anonymize_setting
}

// Section backup settings

/**
 * generic section setting to pass various settings between tasks and steps
 */
class backup_section_generic_setting extends section_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Nothing to do, no dependencies
    }
}

/**
 * Setting to define if one section is included or no. Activities _included
 * settings depend of them if available
 */
class backup_section_included_setting extends section_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Nothing to do, no dependencies
    }
}

/**
 * section backup setting to control if section will include
 * user information or no, depends of @backup_users_setting
 */
class backup_section_userinfo_setting extends section_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // If change detected in backup_users_setting
        if ($setting instanceof backup_users_setting) {
            switch ($ctype) {
                case self::CHANGED_VALUE: // backup_users = false, this too, and locked
                    if (!$setting->get_value()) {
                        $this->set_value(false);
                        $this->set_status(self::LOCKED_BY_HIERARCHY);
                    }
                    break;
                case self::CHANGED_VISIBILITY: // backup_users not visible, this too
                    if (!$setting->get_visibility()) {
                        $this->set_visibility(false);
                    }
                    break;
                case self::CHANGED_STATUS: // backup_users unlocked, this too
                    if ($setting->get_status() == self::NOT_LOCKED) {
                        $this->set_status(self::NOT_LOCKED);
                    }
                    break;
            }
        }
    }
}


// Activity backup settings

/**
 * generic activity setting to pass various settings between tasks and steps
 */
class backup_activity_generic_setting extends activity_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Nothing to do, no dependencies
    }
}

/**
 * activity backup setting to control if activity will
 * be included or no, depends of @backup_activities_setting and
 * optionally parent section included setting
 */
class backup_activity_included_setting extends activity_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // If change detected in backup_activities_setting or backup_section_included_setting
        if ($setting instanceof backup_activities_setting ||
            $setting instanceof backup_section_included_setting) {
            switch ($ctype) {
                case self::CHANGED_VALUE: // backup_users = false, this too, and locked
                    if (!$setting->get_value()) {
                        $this->set_value(false);
                        $this->set_status(self::LOCKED_BY_HIERARCHY);
                    }
                    break;
                case self::CHANGED_VISIBILITY: // backup_users not visible, this too
                    if (!$setting->get_visibility()) {
                        $this->set_visibility(false);
                    }
                    break;
                case self::CHANGED_STATUS: // backup_users unlocked, this too
                    if ($setting->get_status() == self::NOT_LOCKED) {
                        $this->set_status(self::NOT_LOCKED);
                    }
                    break;
            }
        }
    }
}

/**
 * activity backup setting to control if activity will include
 * user information or no, depends of @backup_users_setting
 */
class backup_activity_userinfo_setting extends activity_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // If change detected in backup_users_setting or backup_section_userinfo_setting
        if ($setting instanceof backup_users_setting ||
            $setting instanceof backup_section_userinfo_setting) {
            switch ($ctype) {
                case self::CHANGED_VALUE: // backup_users = false, this too, and locked
                    if (!$setting->get_value()) {
                        $this->set_value(false);
                        $this->set_status(self::LOCKED_BY_HIERARCHY);
                    }
                    break;
                case self::CHANGED_VISIBILITY: // backup_users not visible, this too
                    if (!$setting->get_visibility()) {
                        $this->set_visibility(false);
                    }
                    break;
                case self::CHANGED_STATUS: // backup_users unlocked, this too
                    if ($setting->get_status() == self::NOT_LOCKED) {
                        $this->set_status(self::NOT_LOCKED);
                    }
                    break;
            }
        }
    }
}
