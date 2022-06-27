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
 * Plugin library.
 *
 * @package auth_iomadoidc
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   auth_oidc by James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Initialize custom icon.
 *
 * @param $filefullname
 * @return false|void
 */
function auth_iomadoidc_initialize_customicon($filefullname) {
    global $CFG;

    // IOMAD
    require_once($CFG->dirroot . '/local/iomad/lib/company.php');
    $companyid = iomad::get_my_companyid(context_system::instance(), false);
    if (!empty($companyid)) {
        $postfix = "_$companyid";
    } else {
        $postfix = "";
    }

    $file = get_config('auth_iomadoidc' . $postfix, 'customicon');
    $systemcontext = \context_system::instance();
    $fullpath = "/{$systemcontext->id}/auth_iomadoidc/customicon/0{$file}";

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    $pixpluginsdir = 'pix_plugins/auth/iomadoidc/0';
    $pixpluginsdirparts = explode('/', $pixpluginsdir);
    $curdir = $CFG->dataroot;
    foreach ($pixpluginsdirparts as $dir) {
        $curdir .= '/' . $dir;
        if (!file_exists($curdir)) {
            mkdir($curdir);
        }
    }

    if (file_exists($CFG->dataroot . '/pix_plugins/auth/iomadoidc/0')) {
        $file->copy_content_to($CFG->dataroot . '/pix_plugins/auth/iomadoidc/0/customicon.jpg');
        theme_reset_all_caches();
    }
}

/**
 * Check for connection abilities.
 *
 * @param int $userid Moodle user id to check permissions for.
 * @param string $mode Mode to check
 *                     'connect' to check for connect specific capability
 *                     'disconnect' to check for disconnect capability.
 *                     'both' to check for disconnect and connect capability.
 * @param boolean $require Use require_capability rather than has_capability.
 *
 * @return boolean True if has capability.
 */
function auth_iomadoidc_connectioncapability($userid, $mode = 'connect', $require = false) {
    $check = 'has_capability';
    if ($require) {
        // If requiring the capability and user has manageconnection than checking connect and disconnect is not needed.
        $check = 'require_capability';
        if (has_capability('auth/iomadoidc:manageconnection', \context_user::instance($userid), $userid)) {
            return true;
        }
    } else if ($check('auth/iomadoidc:manageconnection', \context_user::instance($userid), $userid)) {
        return true;
    }

    $result = false;
    switch ($mode) {
        case "connect":
            $result = $check('auth/iomadoidc:manageconnectionconnect', \context_user::instance($userid), $userid);
            break;
        case "disconnect":
            $result = $check('auth/iomadoidc:manageconnectiondisconnect', \context_user::instance($userid), $userid);
            break;
        case "both":
            $result = $check('auth/iomadoidc:manageconnectionconnect', \context_user::instance($userid), $userid);
            $result = $result && $check('auth/iomadoidc:manageconnectiondisconnect', \context_user::instance($userid), $userid);
    }
    if ($require) {
        return true;
    }

    return $result;
}

/**
 * Determine if local_o365 plugins is installed.
 *
 * @return bool
 */
function auth_iomadoidc_is_local_365_installed() {
    global $CFG, $DB;

    $dbmanager = $DB->get_manager();

    return file_exists($CFG->dirroot . '/local/o365/version.php') &&
        $DB->record_exists('config_plugins', ['plugin' => 'local_o365', 'name' => 'version']) &&
        $dbmanager->table_exists('local_o365_objects') &&
        $dbmanager->table_exists('local_o365_connections');
}

/**
 * Return details of all auth_iomadoidc tokens having empty Moodle user IDs.
 *
 * @return array
 */
function auth_iomadoidc_get_tokens_with_empty_ids() {
    global $DB;

    $emptyuseridtokens = [];

    $records = $DB->get_records('auth_iomadoidc_token', ['userid' => '0']);

    foreach ($records as $record) {
        $item = new stdClass();
        $item->id = $record->id;
        $item->iomadoidcusername = $record->iomadoidcusername;
        $item->moodleusername = $record->username;
        $item->userid = 0;
        $item->iomadoidcuniqueid = $record->iomadoidcuniqid;
        $item->matchingstatus = get_string('unmatched', 'auth_iomadoidc');
        $item->details = get_string('na', 'auth_iomadoidc');
        $deletetokenurl = new moodle_url('/auth/iomadoidc/cleanupiomadoidctokens.php', ['id' => $record->id]);
        $item->action = html_writer::link($deletetokenurl, get_string('delete_token', 'auth_iomadoidc'));

        $emptyuseridtokens[$record->id] = $item;
    }

    return $emptyuseridtokens;
}

/**
 * Return details of all auth_iomadoidc tokens with matching Moodle user IDs, but mismatched usernames.
 *
 * @return array
 */
function auth_iomadoidc_get_tokens_with_mismatched_usernames() {
    global $DB;

    $mismatchedtokens = [];

    $sql = 'SELECT tok.id AS id, tok.userid AS tokenuserid, tok.username AS tokenusername, tok.iomadoidcusername AS iomadoidcusername,
                   tok.iomadoidcuniqid as iomadoidcuniqid, u.id AS muserid, u.username AS musername
              FROM {auth_iomadoidc_token} tok
              JOIN {user} u ON u.id = tok.userid
             WHERE tok.userid != 0
               AND u.username != tok.username';
    $records = $DB->get_recordset_sql($sql);
    foreach ($records as $record) {
        $item = new stdClass();
        $item->id = $record->id;
        $item->iomadoidcusername = $record->iomadoidcusername;
        $item->userid = $record->muserid;
        $item->iomadoidcuniqueid = $record->iomadoidcuniqid;
        $item->matchingstatus = get_string('mismatched', 'auth_iomadoidc');
        $item->details = get_string('mismatched_details', 'auth_iomadoidc',
            ['tokenusername' => $record->tokenusername, 'moodleusername' => $record->musername]);
        $deletetokenurl = new moodle_url('/auth/iomadoidc/cleanupiomadoidctokens.php', ['id' => $record->id]);
        $item->action = html_writer::link($deletetokenurl, get_string('delete_token_and_reference', 'auth_iomadoidc'));

        $mismatchedtokens[$record->id] = $item;
    }

    return $mismatchedtokens;
}

/**
 * Delete the auth_iomadoidc token with the ID.
 *
 * @param int $tokenid
 */
function auth_iomadoidc_delete_token(int $tokenid) {
    global $DB;

    if (auth_iomadoidc_is_local_365_installed()) {
        $sql = 'SELECT obj.id, obj.objectid, tok.token, u.id AS userid, u.email
                  FROM {local_o365_objects} obj
                  JOIN {auth_iomadoidc_token} tok ON obj.o365name = tok.username
                  JOIN {user} u ON obj.moodleid = u.id
                 WHERE type = :type AND tok.id = :tokenid';
        if ($objectrecord = $DB->get_record_sql($sql, ['type' => 'user', 'tokenid' => $tokenid], IGNORE_MULTIPLE)) {
            // Delete record from local_o365_objects.
            $DB->get_records('local_o365_objects', ['id' => $objectrecord->id]);

            // Delete record from local_o365_token.
            $DB->delete_records('local_o365_token', ['user_id' => $objectrecord->userid]);

            // Delete record from local_o365_connections.
            $DB->delete_records_select('local_o365_connections', 'muserid = :userid OR LOWER(aadupn) = :email',
                ['userid' => $objectrecord->userid, 'email' => $objectrecord->email]);
        }
    }

    $DB->delete_records('auth_iomadoidc_token', ['id' => $tokenid]);
}

/**
 * Return the list of remote field options in field mapping.
 *
 * @return array
 */
function auth_iomadoidc_get_remote_fields() {
    if (auth_iomadoidc_is_local_365_installed()) {
        $remotefields = [
            '' => get_string('settings_fieldmap_feild_not_mapped', 'auth_iomadoidc'),
            'objectId' => get_string('settings_fieldmap_field_objectId', 'auth_iomadoidc'),
            'userPrincipalName' => get_string('settings_fieldmap_field_userPrincipalName', 'auth_iomadoidc'),
            'displayName' => get_string('settings_fieldmap_field_displayName', 'auth_iomadoidc'),
            'givenName' => get_string('settings_fieldmap_field_givenName', 'auth_iomadoidc'),
            'surname' => get_string('settings_fieldmap_field_surname', 'auth_iomadoidc'),
            'mail' => get_string('settings_fieldmap_field_mail', 'auth_iomadoidc'),
            'streetAddress' => get_string('settings_fieldmap_field_streetAddress', 'auth_iomadoidc'),
            'city' => get_string('settings_fieldmap_field_city', 'auth_iomadoidc'),
            'postalCode' => get_string('settings_fieldmap_field_postalCode', 'auth_iomadoidc'),
            'state' => get_string('settings_fieldmap_field_state', 'auth_iomadoidc'),
            'country' => get_string('settings_fieldmap_field_country', 'auth_iomadoidc'),
            'jobTitle' => get_string('settings_fieldmap_field_jobTitle', 'auth_iomadoidc'),
            'department' => get_string('settings_fieldmap_field_department', 'auth_iomadoidc'),
            'companyName' => get_string('settings_fieldmap_field_companyName', 'auth_iomadoidc'),
            'preferredLanguage' => get_string('settings_fieldmap_field_preferredLanguage', 'auth_iomadoidc'),
            'employeeId' => get_string('settings_fieldmap_field_employeeId', 'auth_iomadoidc'),
            'businessPhones' => get_string('settings_fieldmap_field_businessPhones', 'auth_iomadoidc'),
            'faxNumber' => get_string('settings_fieldmap_field_faxNumber', 'auth_iomadoidc'),
            'mobilePhone' => get_string('settings_fieldmap_field_mobilePhone', 'auth_iomadoidc'),
            'officeLocation' => get_string('settings_fieldmap_field_officeLocation', 'auth_iomadoidc'),
            'preferredName' => get_string('settings_fieldmap_field_preferredName', 'auth_iomadoidc'),
            'manager' => get_string('settings_fieldmap_field_manager', 'auth_iomadoidc'),
            'teams' => get_string('settings_fieldmap_field_teams', 'auth_iomadoidc'),
            'groups' => get_string('settings_fieldmap_field_groups', 'auth_iomadoidc'),
            'roles' => get_string('settings_fieldmap_field_roles', 'auth_iomadoidc'),
        ];

        $order = 0;
        while ($order++ < 15) {
            $remotefields['extensionAttribute' . $order] = get_string('settings_fieldmap_field_extensionattribute', 'auth_iomadoidc',
                $order);
        }

        // SDS profile sync.
        [$sdsprofilesyncenabled, $schoolid, $schoolname] = local_o365\feature\sds\utils::get_profile_sync_status_with_id_name();

        if ($sdsprofilesyncenabled) {
            $remotefields['sds_school_id'] = get_string('settings_fieldmap_field_sds_school_id', 'auth_iomadoidc',
                get_config('local_o365', 'sdsprofilesync', $schoolid));
            $remotefields['sds_school_name'] = get_string('settings_fieldmap_field_sds_school_name', 'auth_iomadoidc', $schoolname);
            $remotefields['sds_school_role'] = get_string('settings_fieldmap_field_sds_school_role', 'auth_iomadoidc');
            $remotefields['sds_student_externalId'] = get_string('settings_fieldmap_field_sds_student_externalId', 'auth_iomadoidc');
            $remotefields['sds_student_birthDate'] = get_string('settings_fieldmap_field_sds_student_birthDate', 'auth_iomadoidc');
            $remotefields['sds_student_grade'] = get_string('settings_fieldmap_field_sds_student_grade', 'auth_iomadoidc');
            $remotefields['sds_student_graduationYear'] = get_string('settings_fieldmap_field_sds_student_graduationYear',
                'auth_iomadoidc');
            $remotefields['sds_student_studentNumber'] = get_string('settings_fieldmap_field_sds_student_studentNumber',
                'auth_iomadoidc');
            $remotefields['sds_teacher_externalId'] = get_string('settings_fieldmap_field_sds_teacher_externalId', 'auth_iomadoidc');
            $remotefields['sds_teacher_teacherNumber'] = get_string('settings_fieldmap_field_sds_teacher_teacherNumber',
                'auth_iomadoidc');
        }
    } else {
        $remotefields = [
            '' => get_string('settings_fieldmap_feild_not_mapped', 'auth_iomadoidc'),
            'objectId' => get_string('settings_fieldmap_field_objectId', 'auth_iomadoidc'),
            'userPrincipalName' => get_string('settings_fieldmap_field_userPrincipalName', 'auth_iomadoidc'),
            'givenName' => get_string('settings_fieldmap_field_givenName', 'auth_iomadoidc'),
            'surname' => get_string('settings_fieldmap_field_surname', 'auth_iomadoidc'),
            'mail' => get_string('settings_fieldmap_field_mail', 'auth_iomadoidc'),
        ];
    }

    return $remotefields;
}

/**
 * Return the list of available remote fields to map email field.
 *
 * @return array
 */
function auth_iomadoidc_get_email_remote_fields() {
    $remotefields = [
        'mail' => get_string('settings_fieldmap_field_mail', 'auth_iomadoidc'),
        'userPrincipalName' => get_string('settings_fieldmap_field_userPrincipalName', 'auth_iomadoidc'),
    ];

    return $remotefields;
}

/**
 * Return the current field mapping settings in an array.
 *
 * @return array
 */
function auth_iomadoidc_get_field_mappings() {
    global $CFG;

    // IOMAD
    require_once($CFG->dirroot . '/local/iomad/lib/company.php');
    $companyid = iomad::get_my_companyid(context_system::instance(), false);
    if (!empty($companyid)) {
        $postfix = "_$companyid";
    } else {
        $postfix = "";
    }
    $fieldmappings = [];

    $userfields = auth_iomadoidc_get_all_user_fields();

    $authiomadoidcconfig = get_config('auth_iomadoidc' . $postfix);

    foreach ($userfields as $userfield) {
        $fieldmapsettingname = 'field_map_' . $userfield;
        if (property_exists($authiomadoidcconfig, $fieldmapsettingname) && $authiomadoidcconfig->$fieldmapsettingname) {
            $fieldsetting = [];
            $fieldsetting['field_map'] = $authiomadoidcconfig->$fieldmapsettingname;

            $fieldlocksettingname = 'field_lock_' . $userfield;
            if (property_exists($authiomadoidcconfig, $fieldlocksettingname)) {
                $fieldsetting['field_lock'] = $authiomadoidcconfig->$fieldlocksettingname;
            } else {
                $fieldsetting['field_lock'] = 'unlocked';
            }

            $fieldupdatelocksettignname = 'field_updatelocal_' . $userfield;
            if (property_exists($authiomadoidcconfig, $fieldupdatelocksettignname)) {
                $fieldsetting['update_local'] = $authiomadoidcconfig->$fieldupdatelocksettignname;
            } else {
                $fieldsetting['update_local'] = 'always';
            }

            $fieldmappings[$userfield] = $fieldsetting;
        }
    }

    if (!array_key_exists('email', $fieldmappings)) {
        $fieldmappings['email'] = auth_iomadoidc_apply_default_email_mapping();
    }

    return $fieldmappings;
}

/**
 * Apply default email mapping settings.
 *
 * @return array
 */
function auth_iomadoidc_apply_default_email_mapping() {
    global $CFG;

    // IOMAD
    require_once($CFG->dirroot . '/local/iomad/lib/company.php');
    $companyid = iomad::get_my_companyid(context_system::instance(), false);
    if (!empty($companyid)) {
        $postfix = "_$companyid";
    } else {
        $postfix = "";
    }
    set_config('field_map_email', 'mail', 'auth_iomadoidc' . $postfix);

    $authiomadoidcconfig = get_config('auth_iomadoidc' . $postfix);

    $fieldsetting = [];
    $fieldsetting['field_map'] = 'mail';

    if (property_exists($authiomadoidcconfig, 'field_lock_email')) {
        $fieldsetting['field_lock'] = $authiomadoidcconfig->field_lock_email;
    } else {
        $fieldsetting['field_lock'] = 'unlocked';
    }

    if (property_exists($authiomadoidcconfig, 'field_updatelocal_email')) {
        $fieldsetting['update_local'] = $authiomadoidcconfig->field_updatelocal_email;
    } else {
        $fieldsetting['update_local'] = 'always';
    }

    return $fieldsetting;
}

/**
 * Helper function used to print mapping and locking for auth_iomadoidc plugin on admin pages.
 *
 * @param stdclass $settings Moodle admin settings instance
 * @param string $auth authentication plugin shortname
 * @param array $userfields user profile fields
 * @param string $helptext help text to be displayed at top of form
 * @param boolean $mapremotefields Map fields or lock only.
 * @param boolean $updateremotefields Allow remote updates
 * @param array $customfields list of custom profile fields
 */
function auth_iomadoidc_display_auth_lock_options($settings, $auth, $userfields, $helptext, $mapremotefields, $updateremotefields,
    $customfields = array()) {
    global $DB;

    // Introductory explanation and help text.
    if ($mapremotefields) {
        $settings->add(new admin_setting_heading($auth.'/data_mapping', new lang_string('auth_data_mapping', 'auth'), $helptext));
    } else {
        $settings->add(new admin_setting_heading($auth.'/auth_fieldlocks', new lang_string('auth_fieldlocks', 'auth'), $helptext));
    }

    // Generate the list of options.
    $lockoptions = [
        'unlocked' => get_string('unlocked', 'auth'),
        'unlockedifempty' => get_string('unlockedifempty', 'auth'),
        'locked' => get_string('locked', 'auth'),
    ];

    if (auth_iomadoidc_is_local_365_installed()) {
        $alwaystext = get_string('update_oncreate_and_onlogin_and_usersync', 'auth_iomadoidc');
        $onlogintext = get_string('update_onlogin_and_usersync', 'auth_iomadoidc');
    } else {
        $alwaystext = get_string('update_oncreate_and_onlogin', 'auth_iomadoidc');
        $onlogintext = get_string('update_onlogin', 'auth');
    }
    $updatelocaloptions = [
        'always' => $alwaystext,
        'oncreate' => get_string('update_oncreate', 'auth'),
        'onlogin' => $onlogintext,
    ];

    $updateextoptions = [
        '0' => get_string('update_never', 'auth'),
        '1' => get_string('update_onupdate', 'auth'),
    ];

    // Generate the list of profile fields to allow updates / lock.
    if (!empty($customfields)) {
        $userfields = array_merge($userfields, $customfields);
        $customfieldname = $DB->get_records('user_info_field', null, '', 'shortname, name');
    }

    $remotefields = auth_iomadoidc_get_remote_fields();
    $emailremotefields = auth_iomadoidc_get_email_remote_fields();

    foreach ($userfields as $field) {
        // Define the fieldname we display to the  user.
        // this includes special handling for some profile fields.
        $fieldname = $field;
        $fieldnametoolong = false;
        if ($fieldname === 'lang') {
            $fieldname = get_string('language');
        } else if (!empty($customfields) && in_array($field, $customfields)) {
            // If custom field then pick name from database.
            $fieldshortname = str_replace('profile_field_', '', $fieldname);
            $fieldname = $customfieldname[$fieldshortname]->name;
            if (core_text::strlen($fieldshortname) > 67) {
                // If custom profile field name is longer than 67 characters we will not be able to store the setting
                // such as 'field_updateremote_profile_field_NOTSOSHORTSHORTNAME' in the database because the character
                // limit for the setting name is 100.
                $fieldnametoolong = true;
            }
        } else if ($fieldname == 'url') {
            $fieldname = get_string('webpage');
        } else {
            $fieldname = get_string($fieldname);
        }

        // Generate the list of fields / mappings.
        if ($fieldnametoolong) {
            // Display a message that the field can not be mapped because it's too long.
            $url = new moodle_url('/user/profile/index.php');
            $a = (object)['fieldname' => s($fieldname), 'shortname' => s($field), 'charlimit' => 67, 'link' => $url->out()];
            $settings->add(new admin_setting_heading($auth.'/field_not_mapped_'.sha1($field), '',
                get_string('cannotmapfield', 'auth', $a)));
        } else if ($mapremotefields) {
            // We are mapping to a remote field here.
            // Mapping.
            if ($field == 'email') {
                $settings->add(new admin_setting_configselect("auth_iomadoidc/field_map_{$field}",
                    get_string('auth_fieldmapping', 'auth', $fieldname), '', null, $emailremotefields));
            } else {
                $settings->add(new admin_setting_configselect("auth_iomadoidc/field_map_{$field}",
                    get_string('auth_fieldmapping', 'auth', $fieldname), '', null, $remotefields));
            }

            // Update local.
            $settings->add(new admin_setting_configselect("auth_{$auth}/field_updatelocal_{$field}",
                get_string('auth_updatelocalfield', 'auth', $fieldname), '', 'always', $updatelocaloptions));

            // Update remote.
            if ($updateremotefields) {
                $settings->add(new admin_setting_configselect("auth_{$auth}/field_updateremote_{$field}",
                    get_string('auth_updateremotefield', 'auth', $fieldname), '', 0, $updateextoptions));
            }

            // Lock fields.
            $settings->add(new admin_setting_configselect("auth_{$auth}/field_lock_{$field}",
                get_string('auth_fieldlockfield', 'auth', $fieldname), '', 'unlocked', $lockoptions));
        } else {
            // Lock fields Only.
            $settings->add(new admin_setting_configselect("auth_{$auth}/field_lock_{$field}",
                get_string('auth_fieldlockfield', 'auth', $fieldname), '', 'unlocked', $lockoptions));
        }
    }
}

/**
 * Return all user profile field names in an array.
 *
 * @return array|string[]|null
 */
function auth_iomadoidc_get_all_user_fields() {
    $authplugin = get_auth_plugin('iomadoidc');
    $userfields = $authplugin->userfields;
    $userfields = array_merge($userfields, $authplugin->get_custom_user_profile_fields());

    return $userfields;
}
