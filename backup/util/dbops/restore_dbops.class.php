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
 * @package    moodlecore
 * @subpackage backup-dbops
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Base abstract class for all the helper classes providing DB operations
 *
 * TODO: Finish phpdocs
 */
abstract class restore_dbops {

    /**
     * Return all the inforef.xml files to be loaded into the temp_ids table
     * We do that by loading the controller from DB, then iterating over all the
     * included tasks and calculating all the inforef files for them
     */
    public static function get_needed_inforef_files($restoreid) {
        $rc = restore_controller_dbops::load_controller($restoreid);
        $tasks = $rc->get_plan()->get_tasks();
        $files = array();
        foreach ($tasks as $task) {
            // Calculate if the task is being included
            $included = false;
            // blocks, based in blocks setting and parent activity/course
            if ($task instanceof restore_block_task) {
                if (!$task->get_setting('blocks')) { // Blocks not included, continue
                    continue;
                }
                $parent = basename(dirname(dirname($task->get_taskbasepath())));
                if ($parent == 'course') { // Parent is course, always included if present
                    $included = true;

                } else { // Look for activity_included setting
                    $included = $task->get_setting_value($parent . '_included');
                }

            // ativities, based on included setting
            } else if ($task instanceof restore_activity_task) {
                $included = $task->get_setting_value('included');

            // sections, based on included setting
            } else if ($task instanceof restore_section_task) {
                $included = $task->get_setting_value('included');

            // course always included if present
            } else if ($task instanceof restore_course_task) {
                $included = true;
            }

            // If included and file exists, add it to results
            if ($included) {
                $inforefpath = $task->get_taskbasepath() . '/inforef.xml';
                if (file_exists($inforefpath)) {
                    $files[] = $inforefpath;
                }
            }
        }
        return $files;
    }

    /**
     * Load one inforef.xml file to backup_ids table for future reference
     */
    public static function load_inforef_to_tempids($restoreid, $inforeffile) {

        if (!file_exists($inforeffile)) { // Shouldn't happen ever, but...
            throw new backup_helper_exception('missing_inforef_xml_file', $inforeffile);
        }
        // Let's parse, custom processor will do its work, sending info to DB
        $xmlparser = new progressive_parser();
        $xmlparser->set_file($inforeffile);
        $xmlprocessor = new restore_inforef_parser_processor($restoreid);
        $xmlparser->set_processor($xmlprocessor);
        $xmlparser->process();
    }

    /**
     * Load the needed role.xml file to backup_ids table for future reference
     */
    public static function load_roles_to_tempids($restoreid, $rolesfile) {

        if (!file_exists($rolesfile)) { // Shouldn't happen ever, but...
            throw new backup_helper_exception('missing_roles_xml_file', $rolesfile);
        }
        // Let's parse, custom processor will do its work, sending info to DB
        $xmlparser = new progressive_parser();
        $xmlparser->set_file($rolesfile);
        $xmlprocessor = new restore_roles_parser_processor($restoreid);
        $xmlparser->set_processor($xmlprocessor);
        $xmlparser->process();
    }

    /**
     * Precheck the loaded roles, return empty array if everything is ok, and
     * array with 'errors', 'warnings' elements (suitable to be used by restore_prechecks)
     * with any problem found
     */
    public static function precheck_included_roles($restoreid, $courseid, $userid, $samesite) {
        debugging('implement the roles mapping/skip here. returns errors/warnings array', DEBUG_DEVELOPER);
        return array();
    }

    /**
     * Process the loaded roles, looking for their best mapping or skipping
     * Any error will cause exception. Note this is one wrapper over
     * precheck_included_roles, that contains all the logic, but returns
     * errors/warnings instead and is executed as part of the restore prechecks
     */
     public static function process_included_roles($restoreid, $courseid, $userid, $samesite) {
        global $DB;

        // Just let precheck_included_roles() to do all the hard work
        $problems = self::precheck_included_roles($restoreid, $courseid, $userid, $samesite);

        // With problems of type error, throw exception, shouldn't happen if prechecks executed
        if (array_key_exists('errors', $problems)) {
            throw new restore_dbops_exception('restore_problems_processing_roles', null, implode(', ', $problems['errors']));
        }
    }

    /**
     * Load the needed users.xml file to backup_ids table for future reference
     */
    public static function load_users_to_tempids($restoreid, $usersfile) {

        if (!file_exists($usersfile)) { // Shouldn't happen ever, but...
            throw new backup_helper_exception('missing_users_xml_file', $usersfile);
        }
        // Let's parse, custom processor will do its work, sending info to DB
        $xmlparser = new progressive_parser();
        $xmlparser->set_file($usersfile);
        $xmlprocessor = new restore_users_parser_processor($restoreid);
        $xmlparser->set_processor($xmlprocessor);
        $xmlparser->process();
    }

    /**
     * Given one component/filearea/context and
     * optionally one source itemname to match itemids
     * put the corresponding files in the pool
     */
    public static function send_files_to_pool($basepath, $restoreid, $component, $filearea, $oldcontextid, $itemname = null) {
        global $DB;

        // Get new context, must exist or this will fail
        if (!$newcontextid = self::get_backup_ids_record($restoreid, 'context', $oldcontextid)->newitemid) {
            throw new restore_dbops_exception('unknown_context_mapping', $oldcontextid);
        }

        // Important: remember how files have been loaded to backup_ids_temp
        //   - info: contains the whole original object (times, names...)
        //   (all them being original ids as loaded from xml)

        // itemname = null, we are going to match only by context, no need to use itemid (all them are 0)
        if ($itemname == null) {
            $sql = 'SELECT contextid, component, filearea, itemid, 0 AS newitemid, info
                      FROM {backup_files_temp}
                     WHERE backupid = ?
                       AND contextid = ?
                       AND component = ?
                       AND filearea  = ?';
            $params = array($restoreid, $oldcontextid, $component, $filearea);

        // itemname not null, going to join with backup_ids to perform the old-new mapping of itemids
        } else {
            $sql = 'SELECT f.contextid, f.component, f.filearea, f.itemid, i.newitemid, f.info
                      FROM {backup_files_temp} f
                      JOIN {backup_ids_temp} i ON i.backupid = f.backupid
                                              AND i.parentitemid = f.contextid
                                              AND i.itemid = f.itemid
                     WHERE f.backupid = ?
                       AND f.contextid = ?
                       AND f.component = ?
                       AND f.filearea = ?
                       AND i.itemname = ?';
            $params = array($restoreid, $oldcontextid, $component, $filearea, $itemname);
        }

        $fs = get_file_storage();         // Get moodle file storage
        $basepath = $basepath . '/files/';// Get backup file pool base
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $rec) {
            $file = (object)unserialize(base64_decode($rec->info));
            // ignore root dirs (they are created automatically)
            if ($file->filepath == '/' && $file->filename == '.') {
                continue;
            }
            // dir found (and not root one), let's create if
            if ($file->filename == '.') {
                $fs->create_directory($newcontextid, $component, $filearea, $rec->newitemid, $file->filepath);
                continue;
            }
            // arrived here, file found
            // Find file in backup pool
            $backuppath = $basepath . backup_file_manager::get_content_file_location($file->contenthash);
            if (!file_exists($backuppath)) {
                throw new restore_dbops_exception('file_not_found_in_pool', $file);
            }
            if (!$fs->file_exists($newcontextid, $component, $filearea, $rec->newitemid, $file->filepath, $file->filename)) {
                $file_record = array(
                    'contextid'   => $newcontextid,
                    'component'   => $component,
                    'filearea'    => $filearea,
                    'itemid'      => $rec->newitemid,
                    'filepath'    => $file->filepath,
                    'filename'    => $file->filename,
                    'timecreated' => $file->timecreated,
                    'timemodified'=> $file->timemodified,
                    'author'      => $file->author,
                    'license'     => $file->license);
                $fs->create_file_from_pathname($file_record, $backuppath);
            }
        }
        $rs->close();
    }

    /**
     * Given one restoreid, create in DB all the users present
     * in backup_ids having newitemid = 0, as far as
     * precheck_included_users() have left them there
     * ready to be created. Also, annotate their newids
     * once created for later reference
     */
    public static function create_included_users($basepath, $restoreid, $userfiles) {
        global $CFG, $DB;

        $authcache = array(); // Cache to get some bits from authentication plugins
        $languages = get_string_manager()->get_list_of_translations(); // Get languages for quick search later
        $themes    = get_list_of_themes(); // Get themes for quick search later

        // Iterate over all the included users with newitemid = 0, have to create them
        $rs = $DB->get_recordset('backup_ids_temp', array('backupid' => $restoreid, 'itemname' => 'user', 'newitemid' => 0), '', 'itemid, parentitemid');
        foreach ($rs as $recuser) {
            $user = (object)self::get_backup_ids_record($restoreid, 'user', $recuser->itemid)->info;

            // if user lang doesn't exist here, use site default
            if (!array_key_exists($user->lang, $languages)) {
                $user->lang = $CFG->lang;
            }

            // if user theme isn't available on target site or they are disabled, reset theme
            if (!empty($user->theme)) {
                if (empty($CFG->allowuserthemes) || !in_array($user->theme, $themes)) {
                    $user->theme = '';
                }
            }

            // if user to be created has mnet auth and its mnethostid is $CFG->mnet_localhost_id
            // that's 100% impossible as own server cannot be accesed over mnet. Change auth to email/manual
            if ($user->auth == 'mnet' && $user->mnethostid == $CFG->mnet_localhost_id) {
                // Respect registerauth
                if ($CFG->registerauth == 'email') {
                    $user->auth = 'email';
                } else {
                    $user->auth = 'manual';
                }
            }
            unset($user->mnethosturl); // Not needed anymore

            // Disable pictures based on global setting
            if (!empty($CFG->disableuserimages)) {
                $user->picture = 0;
            }

            // We need to analyse the AUTH field to recode it:
            //   - if the auth isn't enabled in target site, $CFG->registerauth will decide
            //   - finally, if the auth resulting isn't enabled, default to 'manual'
            if (!is_enabled_auth($user->auth)) {
                if ($CFG->registerauth == 'email') {
                    $user->auth = 'email';
                } else {
                    $user->auth = 'manual';
                }
            }
            if (!is_enabled_auth($user->auth)) { // Final auth check verify, default to manual if not enabled
                $user->auth = 'manual';
            }

            // Now that we know the auth method, for users to be created without pass
            // if password handling is internal and reset password is available
            // we set the password to "restored" (plain text), so the login process
            // will know how to handle that situation in order to allow the user to
            // recover the password. MDL-20846
            if (empty($user->password)) { // Only if restore comes without password
                if (!array_key_exists($user->auth, $authcache)) { // Not in cache
                    $userauth = new stdClass();
                    $authplugin = get_auth_plugin($user->auth);
                    $userauth->preventpassindb = $authplugin->prevent_local_passwords();
                    $userauth->isinternal      = $authplugin->is_internal();
                    $userauth->canresetpwd     = $authplugin->can_reset_password();
                    $authcache[$user->auth] = $userauth;
                } else {
                    $userauth = $authcache[$user->auth]; // Get from cache
                }

                // Most external plugins do not store passwords locally
                if (!empty($userauth->preventpassindb)) {
                    $user->password = 'not cached';

                // If Moodle is responsible for storing/validating pwd and reset functionality is available, mark
                } else if ($userauth->isinternal and $userauth->canresetpwd) {
                    $user->password = 'restored';
                }
            }

            // Creating new user, we must reset the policyagreed always
            $user->policyagreed = 0;

            // Set time created if empty
            if (empty($user->timecreated)) {
                $user->timecreated = time();
            }

            // Done, let's create the user and annotate its id
            $newuserid = $DB->insert_record('user', $user);
            self::set_backup_ids_record($restoreid, 'user', $recuser->itemid, $newuserid);
            // Let's create the user context and annotate it (we need it for sure at least for files)
            $newuserctxid = get_context_instance(CONTEXT_USER, $newuserid)->id;
            self::set_backup_ids_record($restoreid, 'context', $recuser->parentitemid, $newuserctxid);

            // Process custom fields
            if (isset($user->custom_fields)) { // if present in backup
                foreach($user->custom_fields['custom_field'] as $udata) {
                    $udata = (object)$udata;
                    // If the profile field has data and the profile shortname-datatype is defined in server
                    if ($udata->field_data) {
                        if ($field = $DB->get_record('user_info_field', array('shortname'=>$udata->field_name, 'datatype'=>$udata->field_type))) {
                        /// Insert the user_custom_profile_field
                            $rec = new object();
                            $rec->userid  = $newuserid;
                            $rec->fieldid = $field->id;
                            $rec->data    = $udata->field_data;
                            $DB->insert_record('user_info_data', $rec);
                        }
                    }
                }
            }

            // Process tags
            if (!empty($CFG->usetags) && isset($user->tags)) { // if enabled in server and present in backup
                $tags = array();
                foreach($user->tags['tag'] as $usertag) {
                    $usertag = (object)$usertag;
                    $tags[] = $usertag->rawname;
                }
                tag_set('user', $newuserid, $tags);
            }

            // Process preferences
            if (isset($user->preferences)) { // if present in backup
                foreach($user->preferences['preference'] as $preference) {
                    $preference = (object)$preference;
                    // Prepare the record and insert it
                    $preference->userid = $newuserid;
                    $status = $DB->insert_record('user_preferences', $preference);
                }
            }

            // Create user files in pool (profile, icon, private) by context
            restore_dbops::send_files_to_pool($basepath, $restoreid, 'user', 'icon', $recuser->parentitemid);
            restore_dbops::send_files_to_pool($basepath, $restoreid, 'user', 'profile', $recuser->parentitemid);
            if ($userfiles) { // private files only if enabled in settings
                restore_dbops::send_files_to_pool($basepath, $restoreid, 'user', 'private', $recuser->parentitemid);
            }

        }
        $rs->close();
    }

    /**
    * Given one user object (from backup file), perform all the neccesary
    * checks is order to decide how that user will be handled on restore.
    *
    * Note the function requires $user->mnethostid to be already calculated
    * so it's caller responsibility to set it
    *
    * This function is used both by @restore_precheck_users() and
    * @restore_create_users() to get consistent results in both places
    *
    * It returns:
    *   - one user object (from DB), if match has been found and user will be remapped
    *   - boolean true if the user needs to be created
    *   - boolean false if some conflict happened and the user cannot be handled
    *
    * Each test is responsible for returning its results and interrupt
    * execution. At the end, boolean true (user needs to be created) will be
    * returned if no test has interrupted that.
    *
    * Here it's the logic applied, keep it updated:
    *
    *  If restoring users from same site backup:
    *      1A - Normal check: If match by id and username and mnethost  => ok, return target user
    *      1B - Handle users deleted in DB and "alive" in backup file:
    *           If match by id and mnethost and user is deleted in DB and
    *           (match by username LIKE 'backup_email.%' or by non empty email = md5(username)) => ok, return target user
    *      1C - Handle users deleted in backup file and "alive" in DB:
    *           If match by id and mnethost and user is deleted in backup file
    *           and match by email = email_without_time(backup_email) => ok, return target user
    *      1D - Conflict: If match by username and mnethost and doesn't match by id => conflict, return false
    *      1E - None of the above, return true => User needs to be created
    *
    *  if restoring from another site backup (cannot match by id here, replace it by email/firstaccess combination):
    *      2A - Normal check: If match by username and mnethost and (email or non-zero firstaccess) => ok, return target user
    *      2B - Handle users deleted in DB and "alive" in backup file:
    *           2B1 - If match by mnethost and user is deleted in DB and not empty email = md5(username) and
    *                 (username LIKE 'backup_email.%' or non-zero firstaccess) => ok, return target user
    *           2B2 - If match by mnethost and user is deleted in DB and
    *                 username LIKE 'backup_email.%' and non-zero firstaccess) => ok, return target user
    *                 (to cover situations were md5(username) wasn't implemented on delete we requiere both)
    *      2C - Handle users deleted in backup file and "alive" in DB:
    *           If match mnethost and user is deleted in backup file
    *           and by email = email_without_time(backup_email) and non-zero firstaccess=> ok, return target user
    *      2D - Conflict: If match by username and mnethost and not by (email or non-zero firstaccess) => conflict, return false
    *      2E - None of the above, return true => User needs to be created
    *
    * Note: for DB deleted users email is stored in username field, hence we
    *       are looking there for emails. See delete_user()
    * Note: for DB deleted users md5(username) is stored *sometimes* in the email field,
    *       hence we are looking there for usernames if not empty. See delete_user()
    */
    protected static function precheck_user($user, $samesite) {
        global $CFG, $DB;

        // Handle checks from same site backups
        if ($samesite && empty($CFG->forcedifferentsitecheckingusersonrestore)) {

            // 1A - If match by id and username and mnethost => ok, return target user
            if ($rec = $DB->get_record('user', array('id'=>$user->id, 'username'=>$user->username, 'mnethostid'=>$user->mnethostid))) {
                return $rec; // Matching user found, return it
            }

            // 1B - Handle users deleted in DB and "alive" in backup file
            // Note: for DB deleted users email is stored in username field, hence we
            //       are looking there for emails. See delete_user()
            // Note: for DB deleted users md5(username) is stored *sometimes* in the email field,
            //       hence we are looking there for usernames if not empty. See delete_user()
            // If match by id and mnethost and user is deleted in DB and
            // match by username LIKE 'backup_email.%' or by non empty email = md5(username) => ok, return target user
            if ($rec = $DB->get_record_sql("SELECT *
                                              FROM {user} u
                                             WHERE id = ?
                                               AND mnethostid = ?
                                               AND deleted = 1
                                               AND (
                                                       username LIKE ?
                                                    OR (
                                                           ".$DB->sql_isnotempty('user', 'email', false, false)."
                                                       AND email = ?
                                                       )
                                                   )",
                                           array($user->id, $user->mnethostid, $user->email.'.%', md5($user->username)))) {
                return $rec; // Matching user, deleted in DB found, return it
            }

            // 1C - Handle users deleted in backup file and "alive" in DB
            // If match by id and mnethost and user is deleted in backup file
            // and match by email = email_without_time(backup_email) => ok, return target user
            if ($user->deleted) {
                // Note: for DB deleted users email is stored in username field, hence we
                //       are looking there for emails. See delete_user()
                // Trim time() from email
                $trimemail = preg_replace('/(.*?)\.[0-9]+.?$/', '\\1', $user->username);
                if ($rec = $DB->get_record_sql("SELECT *
                                                  FROM {user} u
                                                 WHERE id = ?
                                                   AND mnethostid = ?
                                                   AND email = ?",
                                               array($user->id, $user->mnethostid, $trimemail))) {
                    return $rec; // Matching user, deleted in backup file found, return it
                }
            }

            // 1D - If match by username and mnethost and doesn't match by id => conflict, return false
            if ($rec = $DB->get_record('user', array('username'=>$user->username, 'mnethostid'=>$user->mnethostid))) {
                if ($user->id != $rec->id) {
                    return false; // Conflict, username already exists and belongs to another id
                }
            }

        // Handle checks from different site backups
        } else {

            // 2A - If match by username and mnethost and
            //     (email or non-zero firstaccess) => ok, return target user
            if ($rec = $DB->get_record_sql("SELECT *
                                              FROM {user} u
                                             WHERE username = ?
                                               AND mnethostid = ?
                                               AND (
                                                       email = ?
                                                    OR (
                                                           firstaccess != 0
                                                       AND firstaccess = ?
                                                       )
                                                   )",
                                           array($user->username, $user->mnethostid, $user->email, $user->firstaccess))) {
                return $rec; // Matching user found, return it
            }

            // 2B - Handle users deleted in DB and "alive" in backup file
            // Note: for DB deleted users email is stored in username field, hence we
            //       are looking there for emails. See delete_user()
            // Note: for DB deleted users md5(username) is stored *sometimes* in the email field,
            //       hence we are looking there for usernames if not empty. See delete_user()
            // 2B1 - If match by mnethost and user is deleted in DB and not empty email = md5(username) and
            //       (by username LIKE 'backup_email.%' or non-zero firstaccess) => ok, return target user
            if ($rec = $DB->get_record_sql("SELECT *
                                              FROM {user} u
                                             WHERE mnethostid = ?
                                               AND deleted = 1
                                               AND ".$DB->sql_isnotempty('user', 'email', false, false)."
                                               AND email = ?
                                               AND (
                                                       username LIKE ?
                                                    OR (
                                                           firstaccess != 0
                                                       AND firstaccess = ?
                                                       )
                                                   )",
                                           array($user->mnethostid, md5($user->username), $user->email.'.%', $user->firstaccess))) {
                return $rec; // Matching user found, return it
            }

            // 2B2 - If match by mnethost and user is deleted in DB and
            //       username LIKE 'backup_email.%' and non-zero firstaccess) => ok, return target user
            //       (this covers situations where md5(username) wasn't being stored so we require both
            //        the email & non-zero firstaccess to match)
            if ($rec = $DB->get_record_sql("SELECT *
                                              FROM {user} u
                                             WHERE mnethostid = ?
                                               AND deleted = 1
                                               AND username LIKE ?
                                               AND firstaccess != 0
                                               AND firstaccess = ?",
                                           array($user->mnethostid, $user->email.'.%', $user->firstaccess))) {
                return $rec; // Matching user found, return it
            }

            // 2C - Handle users deleted in backup file and "alive" in DB
            // If match mnethost and user is deleted in backup file
            // and match by email = email_without_time(backup_email) and non-zero firstaccess=> ok, return target user
            if ($user->deleted) {
                // Note: for DB deleted users email is stored in username field, hence we
                //       are looking there for emails. See delete_user()
                // Trim time() from email
                $trimemail = preg_replace('/(.*?)\.[0-9]+.?$/', '\\1', $user->username);
                if ($rec = $DB->get_record_sql("SELECT *
                                                  FROM {user} u
                                                 WHERE mnethostid = ?
                                                   AND email = ?
                                                   AND firstaccess != 0
                                                   AND firstaccess = ?",
                                               array($user->mnethostid, $trimemail, $user->firstaccess))) {
                    return $rec; // Matching user, deleted in backup file found, return it
                }
            }

            // 2D - If match by username and mnethost and not by (email or non-zero firstaccess) => conflict, return false
            if ($rec = $DB->get_record_sql("SELECT *
                                              FROM {user} u
                                             WHERE username = ?
                                               AND mnethostid = ?
                                           AND NOT (
                                                       email = ?
                                                    OR (
                                                           firstaccess != 0
                                                       AND firstaccess = ?
                                                       )
                                                   )",
                                           array($user->username, $user->mnethostid, $user->email, $user->firstaccess))) {
                return false; // Conflict, username/mnethostid already exist and belong to another user (by email/firstaccess)
            }
        }

        // Arrived here, return true as the user will need to be created and no
        // conflicts have been found in the logic above. This covers:
        // 1E - else => user needs to be created, return true
        // 2E - else => user needs to be created, return true
        return true;
    }

    /**
     * Check all the included users, deciding the action to perform
     * for each one (mapping / creation) and returning one array
     * of problems in case something is wrong (lack of permissions,
     * conficts)
     */
    public static function precheck_included_users($restoreid, $courseid, $userid, $samesite) {
        global $CFG, $DB;

        // To return any problem found
        $problems = array();

        // We are going to map mnethostid, so load all the available ones
        $mnethosts = $DB->get_records('mnet_host', array(), 'wwwroot', 'wwwroot, id');

        // Calculate the context we are going to use for capability checking
        $context = get_context_instance(CONTEXT_COURSE, $courseid);

        // Calculate if we have perms to create users, by checking:
        // to 'moodle/restore:createuser' and 'moodle/restore:userinfo'
        // and also observe $CFG->disableusercreationonrestore
        $cancreateuser = false;
        if (has_capability('moodle/restore:createuser', $context, $userid) and
            has_capability('moodle/restore:userinfo', $context, $userid) and
            empty($CFG->disableusercreationonrestore)) { // Can create users

            $cancreateuser = true;
        }

        // Iterate over all the included users
        $rs = $DB->get_recordset('backup_ids_temp', array('backupid' => $restoreid, 'itemname' => 'user'), '', 'itemid');
        foreach ($rs as $recuser) {
            $user = (object)self::get_backup_ids_record($restoreid, 'user', $recuser->itemid)->info;

            // Find the correct mnethostid for user before performing any further check
            if (empty($user->mnethosturl) || $user->mnethosturl === $CFG->wwwroot) {
                $user->mnethostid = $CFG->mnet_localhost_id;
            } else {
                // fast url-to-id lookups
                if (isset($mnethosts[$user->mnethosturl])) {
                    $user->mnethostid = $mnethosts[$user->mnethosturl]->id;
                } else {
                    $user->mnethostid = $CFG->mnet_localhost_id;
                }
            }

            // Now, precheck that user and, based on returned results, annotate action/problem
            $usercheck = self::precheck_user($user, $samesite);

            if (is_object($usercheck)) { // No problem, we have found one user in DB to be mapped to
                // Annotate it, for later process. Set newitemid to mapping user->id
                self::set_backup_ids_record($restoreid, 'user', $recuser->itemid, $usercheck->id);

            } else if ($usercheck === false) { // Found conflict, report it as problem
                 $problems[] = get_string('restoreuserconflict', '', $user->username);

            } else if ($usercheck === true) { // User needs to be created, check if we are able
                if ($cancreateuser) { // Can create user, set newitemid to 0 so will be created later
                    self::set_backup_ids_record($restoreid, 'user', $recuser->itemid, 0, null, (array)$user);

                } else { // Cannot create user, report it as problem
                    $problems[] = get_string('restorecannotcreateuser', '', $user->username);
                }

            } else { // Shouldn't arrive here ever, something is for sure wrong. Exception
                throw new restore_dbops_exception('restore_error_processing_user', $user->username);
            }
        }
        $rs->close();
        return $problems;
    }

    /**
     * Process the needed users in order to create / map them
     *
     * Just wrap over precheck_included_users(), returning
     * exception if any problem is found or performing the
     * required user creations if needed
     */
    public static function process_included_users($restoreid, $courseid, $userid, $samesite) {
        global $DB;

        // Just let precheck_included_users() to do all the hard work
        $problems = self::precheck_included_users($restoreid, $courseid, $userid, $samesite);

        // With problems, throw exception, shouldn't happen if prechecks were originally
        // executed, so be radical here.
        if (!empty($problems)) {
            throw new restore_dbops_exception('restore_problems_processing_users', null, implode(', ', $problems));
        }
    }

    public static function set_backup_files_record($restoreid, $filerec) {
        global $DB;

        $filerec->info     = base64_encode(serialize($filerec)); // Serialize the whole rec in info
        $filerec->backupid = $restoreid;
        $DB->insert_record('backup_files_temp', $filerec);
    }


    public static function set_backup_ids_record($restoreid, $itemname, $itemid, $newitemid = 0, $parentitemid = null, $info = null) {
        global $DB;

        // Build the basic (mandatory) record info
        $record = array(
            'backupid' => $restoreid,
            'itemname' => $itemname,
            'itemid'   => $itemid
        );
        // Build conditionally the extra record info
        $extrarecord = array();
        if ($newitemid != 0) {
            $extrarecord['newitemid'] = $newitemid;
        }
        if ($parentitemid != null) {
            $extrarecord['parentitemid'] = $parentitemid;
        }
        if ($info != null) {
            $extrarecord['info'] = base64_encode(serialize($info));
        }

        // TODO: Analyze if some static (and limited) cache by the 3 params could save us a bunch of get_record() calls
        // Note: Sure it will! And also will improve getter
        if (!$dbrec = $DB->get_record('backup_ids_temp', $record)) { // Need to insert the complete record
            $DB->insert_record('backup_ids_temp', array_merge($record, $extrarecord));

        } else { // Need to update the extra record info if there is something to
            if (!empty($extrarecord)) {
                $extrarecord['id'] = $dbrec->id;
                $DB->update_record('backup_ids_temp', $extrarecord);
            }
        }
    }

    public static function get_backup_ids_record($restoreid, $itemname, $itemid) {
        global $DB;

        // Build the basic (mandatory) record info to look for
        $record = array(
            'backupid' => $restoreid,
            'itemname' => $itemname,
            'itemid'   => $itemid
        );
        // TODO: Analyze if some static (and limited) cache by the 3 params could save us a bunch of get_record() calls
        if ($dbrec = $DB->get_record('backup_ids_temp', $record)) {
            if ($dbrec->info != null) {
                $dbrec->info = unserialize(base64_decode($dbrec->info));
            }
        }
        return $dbrec;
    }
}

/*
 * Exception class used by all the @dbops stuff
 */
class restore_dbops_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, 'error', '', $a, null, $debuginfo);
    }
}
