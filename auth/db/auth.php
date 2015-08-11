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
 * Authentication Plugin: External Database Authentication
 *
 * Checks against an external database.
 *
 * @package    auth_db
 * @author     Martin Dougiamas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * External database authentication plugin.
 */
class auth_plugin_db extends auth_plugin_base {

    /**
     * Constructor.
     */
    function __construct() {
        global $CFG;
        require_once($CFG->libdir.'/adodb/adodb.inc.php');

        $this->authtype = 'db';
        $this->config = get_config('auth/db');
        if (empty($this->config->extencoding)) {
            $this->config->extencoding = 'utf-8';
        }
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        global $CFG, $DB;

        if ($this->is_configured() === false) {
            debugging(get_string('auth_notconfigured', 'auth', $this->authtype));
            return false;
        }

        $extusername = core_text::convert($username, 'utf-8', $this->config->extencoding);
        $extpassword = core_text::convert($password, 'utf-8', $this->config->extencoding);

        if ($this->is_internal()) {
            // Lookup username externally, but resolve
            // password locally -- to support backend that
            // don't track passwords.

            if (isset($this->config->removeuser) and $this->config->removeuser == AUTH_REMOVEUSER_KEEP) {
                // No need to connect to external database in this case because users are never removed and we verify password locally.
                if ($user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id, 'auth'=>$this->authtype))) {
                    return validate_internal_user_password($user, $password);
                } else {
                    return false;
                }
            }

            $authdb = $this->db_init();

            $rs = $authdb->Execute("SELECT *
                                      FROM {$this->config->table}
                                     WHERE {$this->config->fielduser} = '".$this->ext_addslashes($extusername)."'");
            if (!$rs) {
                $authdb->Close();
                debugging(get_string('auth_dbcantconnect','auth_db'));
                return false;
            }

            if (!$rs->EOF) {
                $rs->Close();
                $authdb->Close();
                // User exists externally - check username/password internally.
                if ($user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id, 'auth'=>$this->authtype))) {
                    return validate_internal_user_password($user, $password);
                }
            } else {
                $rs->Close();
                $authdb->Close();
                // User does not exist externally.
                return false;
            }

        } else {
            // Normal case: use external db for both usernames and passwords.

            $authdb = $this->db_init();

            $rs = $authdb->Execute("SELECT {$this->config->fieldpass}
                                      FROM {$this->config->table}
                                     WHERE {$this->config->fielduser} = '".$this->ext_addslashes($extusername)."'");
            if (!$rs) {
                $authdb->Close();
                debugging(get_string('auth_dbcantconnect','auth_db'));
                return false;
            }

            if ($rs->EOF) {
                $authdb->Close();
                return false;
            }

            $fields = array_change_key_case($rs->fields, CASE_LOWER);
            $fromdb = $fields[strtolower($this->config->fieldpass)];
            $rs->Close();
            $authdb->Close();

            if ($this->config->passtype === 'plaintext') {
                return ($fromdb == $extpassword);
            } else if ($this->config->passtype === 'md5') {
                return (strtolower($fromdb) == md5($extpassword));
            } else if ($this->config->passtype === 'sha1') {
                return (strtolower($fromdb) == sha1($extpassword));
            } else if ($this->config->passtype === 'saltedcrypt') {
                require_once($CFG->libdir.'/password_compat/lib/password.php');
                return password_verify($extpassword, $fromdb);
            } else {
                return false;
            }

        }
    }

    /**
     * Connect to external database.
     *
     * @return ADOConnection
     * @throws moodle_exception
     */
    function db_init() {
        if ($this->is_configured() === false) {
            throw new moodle_exception('auth_dbcantconnect', 'auth_db');
        }

        // Connect to the external database (forcing new connection).
        $authdb = ADONewConnection($this->config->type);
        if (!empty($this->config->debugauthdb)) {
            $authdb->debug = true;
            ob_start(); //Start output buffer to allow later use of the page headers.
        }
        $authdb->Connect($this->config->host, $this->config->user, $this->config->pass, $this->config->name, true);
        $authdb->SetFetchMode(ADODB_FETCH_ASSOC);
        if (!empty($this->config->setupsql)) {
            $authdb->Execute($this->config->setupsql);
        }

        return $authdb;
    }

    /**
     * Returns user attribute mappings between moodle and ldap.
     *
     * @return array
     */
    function db_attributes() {
        $moodleattributes = array();
        // If we have custom fields then merge them with user fields.
        $customfields = $this->get_custom_user_profile_fields();
        if (!empty($customfields) && !empty($this->userfields)) {
            $userfields = array_merge($this->userfields, $customfields);
        } else {
            $userfields = $this->userfields;
        }

        foreach ($userfields as $field) {
            if (!empty($this->config->{"field_map_$field"})) {
                $moodleattributes[$field] = $this->config->{"field_map_$field"};
            }
        }
        $moodleattributes['username'] = $this->config->fielduser;
        return $moodleattributes;
    }

    /**
     * Reads any other information for a user from external database,
     * then returns it in an array.
     *
     * @param string $username
     * @return array
     */
    function get_userinfo($username) {
        global $CFG;

        $extusername = core_text::convert($username, 'utf-8', $this->config->extencoding);

        $authdb = $this->db_init();

        // Array to map local fieldnames we want, to external fieldnames.
        $selectfields = $this->db_attributes();

        $result = array();
        // If at least one field is mapped from external db, get that mapped data.
        if ($selectfields) {
            $select = array();
            foreach ($selectfields as $localname=>$externalname) {
                $select[] = "$externalname";
            }
            $select = implode(', ', $select);
            $sql = "SELECT $select
                      FROM {$this->config->table}
                     WHERE {$this->config->fielduser} = '".$this->ext_addslashes($extusername)."'";

            if ($rs = $authdb->Execute($sql)) {
                if (!$rs->EOF) {
                    $fields = $rs->FetchRow();
                    // Convert the associative array to an array of its values so we don't have to worry about the case of its keys.
                    $fields = array_values($fields);
                    foreach (array_keys($selectfields) as $index => $localname) {
                        $value = $fields[$index];
                        $result[$localname] = core_text::convert($value, $this->config->extencoding, 'utf-8');
                     }
                 }
                 $rs->Close();
            }
        }
        $authdb->Close();
        return $result;
    }

    /**
     * Change a user's password.
     *
     * @param  stdClass  $user      User table object
     * @param  string  $newpassword Plaintext password
     * @return bool                 True on success
     */
    function user_update_password($user, $newpassword) {
        global $DB;

        if ($this->is_internal()) {
            $puser = $DB->get_record('user', array('id'=>$user->id), '*', MUST_EXIST);
            // This will also update the stored hash to the latest algorithm
            // if the existing hash is using an out-of-date algorithm (or the
            // legacy md5 algorithm).
            if (update_internal_user_password($puser, $newpassword)) {
                $user->password = $puser->password;
                return true;
            } else {
                return false;
            }
        } else {
            // We should have never been called!
            return false;
        }
    }

    /**
     * Synchronizes user from external db to moodle user table.
     *
     * Sync should be done by using idnumber attribute, not username.
     * You need to pass firstsync parameter to function to fill in
     * idnumbers if they don't exists in moodle user table.
     *
     * Syncing users removes (disables) users that don't exists anymore in external db.
     * Creates new users and updates coursecreator status of users.
     *
     * This implementation is simpler but less scalable than the one found in the LDAP module.
     *
     * @param progress_trace $trace
     * @param bool $do_updates  Optional: set to true to force an update of existing accounts
     * @return int 0 means success, 1 means failure
     */
    function sync_users(progress_trace $trace, $do_updates=false) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/user/lib.php');

        // List external users.
        $userlist = $this->get_userlist();

        // Delete obsolete internal users.
        if (!empty($this->config->removeuser)) {

            $suspendselect = "";
            if ($this->config->removeuser == AUTH_REMOVEUSER_SUSPEND) {
                $suspendselect = "AND u.suspended = 0";
            }

            // Find obsolete users.
            if (count($userlist)) {
                list($notin_sql, $params) = $DB->get_in_or_equal($userlist, SQL_PARAMS_NAMED, 'u', false);
                $params['authtype'] = $this->authtype;
                $sql = "SELECT u.*
                          FROM {user} u
                         WHERE u.auth=:authtype AND u.deleted=0 AND u.mnethostid=:mnethostid $suspendselect AND u.username $notin_sql";
            } else {
                $sql = "SELECT u.*
                          FROM {user} u
                         WHERE u.auth=:authtype AND u.deleted=0 AND u.mnethostid=:mnethostid $suspendselect";
                $params = array();
                $params['authtype'] = $this->authtype;
            }
            $params['mnethostid'] = $CFG->mnet_localhost_id;
            $remove_users = $DB->get_records_sql($sql, $params);

            if (!empty($remove_users)) {
                $trace->output(get_string('auth_dbuserstoremove','auth_db', count($remove_users)));

                foreach ($remove_users as $user) {
                    if ($this->config->removeuser == AUTH_REMOVEUSER_FULLDELETE) {
                        delete_user($user);
                        $trace->output(get_string('auth_dbdeleteuser', 'auth_db', array('name'=>$user->username, 'id'=>$user->id)), 1);
                    } else if ($this->config->removeuser == AUTH_REMOVEUSER_SUSPEND) {
                        $updateuser = new stdClass();
                        $updateuser->id   = $user->id;
                        $updateuser->suspended = 1;
                        user_update_user($updateuser, false);
                        $trace->output(get_string('auth_dbsuspenduser', 'auth_db', array('name'=>$user->username, 'id'=>$user->id)), 1);
                    }
                }
            }
            unset($remove_users);
        }

        if (!count($userlist)) {
            // Exit right here, nothing else to do.
            $trace->finished();
            return 0;
        }

        // Update existing accounts.
        if ($do_updates) {
            // Narrow down what fields we need to update.
            $all_keys = array_keys(get_object_vars($this->config));
            $updatekeys = array();
            foreach ($all_keys as $key) {
                if (preg_match('/^field_updatelocal_(.+)$/',$key, $match)) {
                    if ($this->config->{$key} === 'onlogin') {
                        array_push($updatekeys, $match[1]); // The actual key name.
                    }
                }
            }
            unset($all_keys); unset($key);

            // Only go ahead if we actually have fields to update locally.
            if (!empty($updatekeys)) {
                list($in_sql, $params) = $DB->get_in_or_equal($userlist, SQL_PARAMS_NAMED, 'u', true);
                $params['authtype'] = $this->authtype;
                $sql = "SELECT u.id, u.username
                          FROM {user} u
                         WHERE u.auth=:authtype AND u.deleted=0 AND u.username {$in_sql}";
                if ($update_users = $DB->get_records_sql($sql, $params)) {
                    $trace->output("User entries to update: ".count($update_users));

                    foreach ($update_users as $user) {
                        if ($this->update_user_record($user->username, $updatekeys)) {
                            $trace->output(get_string('auth_dbupdatinguser', 'auth_db', array('name'=>$user->username, 'id'=>$user->id)), 1);
                        } else {
                            $trace->output(get_string('auth_dbupdatinguser', 'auth_db', array('name'=>$user->username, 'id'=>$user->id))." - ".get_string('skipped'), 1);
                        }
                    }
                    unset($update_users);
                }
            }
        }


        // Create missing accounts.
        // NOTE: this is very memory intensive and generally inefficient.
        $suspendselect = "";
        if ($this->config->removeuser == AUTH_REMOVEUSER_SUSPEND) {
            $suspendselect = "AND u.suspended = 0";
        }
        $sql = "SELECT u.id, u.username
                  FROM {user} u
                 WHERE u.auth=:authtype AND u.deleted='0' AND mnethostid=:mnethostid $suspendselect";

        $users = $DB->get_records_sql($sql, array('authtype'=>$this->authtype, 'mnethostid'=>$CFG->mnet_localhost_id));

        // Simplify down to usernames.
        $usernames = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                array_push($usernames, $user->username);
            }
            unset($users);
        }

        $add_users = array_diff($userlist, $usernames);
        unset($usernames);

        if (!empty($add_users)) {
            $trace->output(get_string('auth_dbuserstoadd','auth_db',count($add_users)));
            // Do not use transactions around this foreach, we want to skip problematic users, not revert everything.
            foreach($add_users as $user) {
                $username = $user;
                if ($this->config->removeuser == AUTH_REMOVEUSER_SUSPEND) {
                    if ($olduser = $DB->get_record('user', array('username' => $username, 'deleted' => 0, 'suspended' => 1,
                            'mnethostid' => $CFG->mnet_localhost_id, 'auth' => $this->authtype))) {
                        $updateuser = new stdClass();
                        $updateuser->id = $olduser->id;
                        $updateuser->suspended = 0;
                        user_update_user($updateuser);
                        $trace->output(get_string('auth_dbreviveduser', 'auth_db', array('name' => $username,
                            'id' => $olduser->id)), 1);
                        continue;
                    }
                }

                // Do not try to undelete users here, instead select suspending if you ever expect users will reappear.

                // Prep a few params.
                $user = $this->get_userinfo_asobj($user);
                $user->username   = $username;
                $user->confirmed  = 1;
                $user->auth       = $this->authtype;
                $user->mnethostid = $CFG->mnet_localhost_id;
                if (empty($user->lang)) {
                    $user->lang = $CFG->lang;
                }
                if ($collision = $DB->get_record_select('user', "username = :username AND mnethostid = :mnethostid AND auth <> :auth", array('username'=>$user->username, 'mnethostid'=>$CFG->mnet_localhost_id, 'auth'=>$this->authtype), 'id,username,auth')) {
                    $trace->output(get_string('auth_dbinsertuserduplicate', 'auth_db', array('username'=>$user->username, 'auth'=>$collision->auth)), 1);
                    continue;
                }
                try {
                    $id = user_create_user($user, false); // It is truly a new user.
                    $trace->output(get_string('auth_dbinsertuser', 'auth_db', array('name'=>$user->username, 'id'=>$id)), 1);
                } catch (moodle_exception $e) {
                    $trace->output(get_string('auth_dbinsertusererror', 'auth_db', $user->username), 1);
                    continue;
                }
                // If relevant, tag for password generation.
                if ($this->is_internal()) {
                    set_user_preference('auth_forcepasswordchange', 1, $id);
                    set_user_preference('create_password',          1, $id);
                }
                // Make sure user context is present.
                context_user::instance($id);
            }
            unset($add_users);
        }
        $trace->finished();
        return 0;
    }

    function user_exists($username) {

        // Init result value.
        $result = false;

        $extusername = core_text::convert($username, 'utf-8', $this->config->extencoding);

        $authdb = $this->db_init();

        $rs = $authdb->Execute("SELECT *
                                  FROM {$this->config->table}
                                 WHERE {$this->config->fielduser} = '".$this->ext_addslashes($extusername)."' ");

        if (!$rs) {
            print_error('auth_dbcantconnect','auth_db');
        } else if (!$rs->EOF) {
            // User exists externally.
            $result = true;
        }

        $authdb->Close();
        return $result;
    }


    function get_userlist() {

        // Init result value.
        $result = array();

        $authdb = $this->db_init();

        // Fetch userlist.
        $rs = $authdb->Execute("SELECT {$this->config->fielduser}
                                  FROM {$this->config->table} ");

        if (!$rs) {
            print_error('auth_dbcantconnect','auth_db');
        } else if (!$rs->EOF) {
            while ($rec = $rs->FetchRow()) {
                $rec = array_change_key_case((array)$rec, CASE_LOWER);
                array_push($result, $rec[strtolower($this->config->fielduser)]);
            }
        }

        $authdb->Close();
        return $result;
    }

    /**
     * Reads user information from DB and return it in an object.
     *
     * @param string $username username
     * @return array
     */
    function get_userinfo_asobj($username) {
        $user_array = truncate_userinfo($this->get_userinfo($username));
        $user = new stdClass();
        foreach($user_array as $key=>$value) {
            $user->{$key} = $value;
        }
        return $user;
    }

    /**
     * will update a local user record from an external source.
     * is a lighter version of the one in moodlelib -- won't do
     * expensive ops such as enrolment.
     *
     * If you don't pass $updatekeys, there is a performance hit and
     * values removed from DB won't be removed from moodle.
     *
     * @param string $username username
     * @param bool $updatekeys
     * @return stdClass
     */
    function update_user_record($username, $updatekeys=false) {
        global $CFG, $DB;

        //just in case check text case
        $username = trim(core_text::strtolower($username));

        // get the current user record
        $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id));
        if (empty($user)) { // trouble
            error_log("Cannot update non-existent user: $username");
            print_error('auth_dbusernotexist','auth_db',$username);
            die;
        }

        // Ensure userid is not overwritten.
        $userid = $user->id;
        $needsupdate = false;

        $updateuser = new stdClass();
        $updateuser->id = $userid;
        if ($newinfo = $this->get_userinfo($username)) {
            $newinfo = truncate_userinfo($newinfo);

            if (empty($updatekeys)) { // All keys? This does not support removing values.
                $updatekeys = array_keys($newinfo);
            }

            foreach ($updatekeys as $key) {
                if (isset($newinfo[$key])) {
                    $value = $newinfo[$key];
                } else {
                    $value = '';
                }

                if (!empty($this->config->{'field_updatelocal_' . $key})) {
                    if (isset($user->{$key}) and $user->{$key} != $value) { // Only update if it's changed.
                        $needsupdate = true;
                        $updateuser->$key = $value;
                    }
                }
            }
        }
        if ($needsupdate) {
            require_once($CFG->dirroot . '/user/lib.php');
            user_update_user($updateuser);
        }
        return $DB->get_record('user', array('id'=>$userid, 'deleted'=>0));
    }

    /**
     * Called when the user record is updated.
     * Modifies user in external database. It takes olduser (before changes) and newuser (after changes)
     * compares information saved modified information to external db.
     *
     * @param stdClass $olduser     Userobject before modifications
     * @param stdClass $newuser     Userobject new modified userobject
     * @return boolean result
     *
     */
    function user_update($olduser, $newuser) {
        if (isset($olduser->username) and isset($newuser->username) and $olduser->username != $newuser->username) {
            error_log("ERROR:User renaming not allowed in ext db");
            return false;
        }

        if (isset($olduser->auth) and $olduser->auth != $this->authtype) {
            return true; // Just change auth and skip update.
        }

        $curruser = $this->get_userinfo($olduser->username);
        if (empty($curruser)) {
            error_log("ERROR:User $olduser->username found in ext db");
            return false;
        }

        $extusername = core_text::convert($olduser->username, 'utf-8', $this->config->extencoding);

        $authdb = $this->db_init();

        $update = array();
        foreach($curruser as $key=>$value) {
            if ($key == 'username') {
                continue; // Skip this.
            }
            if (empty($this->config->{"field_updateremote_$key"})) {
                continue; // Remote update not requested.
            }
            if (!isset($newuser->$key)) {
                continue;
            }
            $nuvalue = $newuser->$key;
            // Support for textarea fields.
            if (isset($nuvalue['text'])) {
                $nuvalue = $nuvalue['text'];
            }
            if ($nuvalue != $value) {
                $update[] = $this->config->{"field_map_$key"}."='".$this->ext_addslashes(core_text::convert($nuvalue, 'utf-8', $this->config->extencoding))."'";
            }
        }
        if (!empty($update)) {
            $authdb->Execute("UPDATE {$this->config->table}
                                 SET ".implode(',', $update)."
                               WHERE {$this->config->fielduser}='".$this->ext_addslashes($extusername)."'");
        }
        $authdb->Close();
        return true;
    }

    /**
     * A chance to validate form data, and last chance to
     * do stuff before it is inserted in config_plugin
     *
     * @param stfdClass $form
     * @param array $err errors
     * @return void
     */
     function validate_form($form, &$err) {
        if ($form->passtype === 'internal') {
            $this->config->changepasswordurl = '';
            set_config('changepasswordurl', '', 'auth/db');
        }
    }

    function prevent_local_passwords() {
        return !$this->is_internal();
    }

    /**
     * Returns true if this authentication plugin is "internal".
     *
     * Internal plugins use password hashes from Moodle user table for authentication.
     *
     * @return bool
     */
    function is_internal() {
        if (!isset($this->config->passtype)) {
            return true;
        }
        return ($this->config->passtype === 'internal');
    }

    /**
     * Returns false if this plugin is enabled but not configured.
     *
     * @return bool
     */
    public function is_configured() {
        if (!empty($this->config->type)) {
            return true;
        }
        return false;
    }

    /**
     * Indicates if moodle should automatically update internal user
     * records with data from external sources using the information
     * from auth_plugin_base::get_userinfo().
     *
     * @return bool true means automatically copy data from ext to user table
     */
    function is_synchronised_with_external() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return ($this->is_internal() or !empty($this->config->changepasswordurl));
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        if ($this->is_internal() || empty($this->config->changepasswordurl)) {
            // Standard form.
            return null;
        } else {
            // Use admin defined custom url.
            return new moodle_url($this->config->changepasswordurl);
        }
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        return $this->is_internal();
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param stdClass $config
     * @param array $err errors
     * @param array $user_fields
     * @return void
     */
    function config_form($config, $err, $user_fields) {
        include 'config.html';
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     *
     * @param srdClass $config
     * @return bool always true or exception
     */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset($config->host)) {
            $config->host = 'localhost';
        }
        if (!isset($config->type)) {
            $config->type = 'mysql';
        }
        if (!isset($config->sybasequoting)) {
            $config->sybasequoting = 0;
        }
        if (!isset($config->name)) {
            $config->name = '';
        }
        if (!isset($config->user)) {
            $config->user = '';
        }
        if (!isset($config->pass)) {
            $config->pass = '';
        }
        if (!isset($config->table)) {
            $config->table = '';
        }
        if (!isset($config->fielduser)) {
            $config->fielduser = '';
        }
        if (!isset($config->fieldpass)) {
            $config->fieldpass = '';
        }
        if (!isset($config->passtype)) {
            $config->passtype = 'plaintext';
        }
        if (!isset($config->extencoding)) {
            $config->extencoding = 'utf-8';
        }
        if (!isset($config->setupsql)) {
            $config->setupsql = '';
        }
        if (!isset($config->debugauthdb)) {
            $config->debugauthdb = 0;
        }
        if (!isset($config->removeuser)) {
            $config->removeuser = AUTH_REMOVEUSER_KEEP;
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // Save settings.
        set_config('host',          $config->host,          'auth/db');
        set_config('type',          $config->type,          'auth/db');
        set_config('sybasequoting', $config->sybasequoting, 'auth/db');
        set_config('name',          $config->name,          'auth/db');
        set_config('user',          $config->user,          'auth/db');
        set_config('pass',          $config->pass,          'auth/db');
        set_config('table',         $config->table,         'auth/db');
        set_config('fielduser',     $config->fielduser,     'auth/db');
        set_config('fieldpass',     $config->fieldpass,     'auth/db');
        set_config('passtype',      $config->passtype,      'auth/db');
        set_config('extencoding',   trim($config->extencoding), 'auth/db');
        set_config('setupsql',      trim($config->setupsql),'auth/db');
        set_config('debugauthdb',   $config->debugauthdb,   'auth/db');
        set_config('removeuser',    $config->removeuser,    'auth/db');
        set_config('changepasswordurl', trim($config->changepasswordurl), 'auth/db');

        return true;
    }

    /**
     * Add slashes, we can not use placeholders or system functions.
     *
     * @param string $text
     * @return string
     */
    function ext_addslashes($text) {
        if (empty($this->config->sybasequoting)) {
            $text = str_replace('\\', '\\\\', $text);
            $text = str_replace(array('\'', '"', "\0"), array('\\\'', '\\"', '\\0'), $text);
        } else {
            $text = str_replace("'", "''", $text);
        }
        return $text;
    }

    /**
     * Test if settings are ok, print info to output.
     * @private
     */
    public function test_settings() {
        global $CFG, $OUTPUT;

        // NOTE: this is not localised intentionally, admins are supposed to understand English at least a bit...

        raise_memory_limit(MEMORY_HUGE);

        if (empty($this->config->table)) {
            echo $OUTPUT->notification('External table not specified.', 'notifyproblem');
            return;
        }

        if (empty($this->config->fielduser)) {
            echo $OUTPUT->notification('External user field not specified.', 'notifyproblem');
            return;
        }

        $olddebug = $CFG->debug;
        $olddisplay = ini_get('display_errors');
        ini_set('display_errors', '1');
        $CFG->debug = DEBUG_DEVELOPER;
        $olddebugauthdb = $this->config->debugauthdb;
        $this->config->debugauthdb = 1;
        error_reporting($CFG->debug);

        $adodb = $this->db_init();

        if (!$adodb or !$adodb->IsConnected()) {
            $this->config->debugauthdb = $olddebugauthdb;
            $CFG->debug = $olddebug;
            ini_set('display_errors', $olddisplay);
            error_reporting($CFG->debug);
            ob_end_flush();

            echo $OUTPUT->notification('Cannot connect the database.', 'notifyproblem');
            return;
        }

        $rs = $adodb->Execute("SELECT *
                                 FROM {$this->config->table}
                                WHERE {$this->config->fielduser} <> 'random_unlikely_username'"); // Any unlikely name is ok here.

        if (!$rs) {
            echo $OUTPUT->notification('Can not read external table.', 'notifyproblem');

        } else if ($rs->EOF) {
            echo $OUTPUT->notification('External table is empty.', 'notifyproblem');
            $rs->close();

        } else {
            $fields_obj = $rs->FetchObj();
            $columns = array_keys((array)$fields_obj);

            echo $OUTPUT->notification('External table contains following columns:<br />'.implode(', ', $columns), 'notifysuccess');
            $rs->close();
        }

        $adodb->Close();

        $this->config->debugauthdb = $olddebugauthdb;
        $CFG->debug = $olddebug;
        ini_set('display_errors', $olddisplay);
        error_reporting($CFG->debug);
        ob_end_flush();
    }
}


