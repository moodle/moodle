<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: External Database Authentication
 *
 * Checks against an external database.
 *
 * 2006-08-28  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * External database authentication plugin.
 */
class auth_plugin_db {

    /**
     * The configuration details for the plugin.
     */
    var $config;

    /**
     * Constructor.
     */
    function auth_plugin_db() {
        $this->config = get_config('auth/db');
        if (empty($this->config->extencoding)) {
            $this->config->extencoding = 'utf-8';
        }
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     *
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {

        global $CFG;

        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->extencoding);
        $extpassword = $textlib->convert(stripslashes($password), 'utf-8', $this->config->extencoding);

        // Connect to the external database (forcing new connection)
        $authdb = &ADONewConnection($this->config->type);
        if (!empty($this->config->debugauthdb)) {
            $authdb->debug = true;
            ob_start();//start output buffer to allow later use of the page headers
        }
        $authdb->Connect($this->config->host, $this->config->user, $this->config->pass, $this->config->name, true);
        $authdb->SetFetchMode(ADODB_FETCH_ASSOC);
        if (!empty($this->config->setupsql)) {
            $authdb->Execute($this->config->setupsql);
        }

        if ($this->config->passtype === 'internal') {
            // lookup username externally, but resolve
            // password locally -- to support backend that
            // don't track passwords
            $rs = $authdb->Execute("SELECT * FROM {$this->config->table}
                                     WHERE {$this->config->fielduser} = '".$this->ext_addslashes($extusername)."' ");
            $authdb->Close();

            if (!$rs) {
                print_error('auth_dbcantconnect','auth');
                return false;
            }

            if ( $rs->RecordCount() ) {
                // user exists exterally
                // check username/password internally
                if ($user = get_record('user', 'username', $username, 'mnethostid', $CFG->mnet_localhost_id)) {
                    return validate_internal_user_password($user, $password);
                }
            } else {
                // user does not exist externally
                return false;
            }

        } else {
            // normal case: use external db for passwords

            if ($this->config->passtype === 'md5') {   // Re-format password accordingly
                $extpassword = md5($extpassword);
            } else if ($this->config->passtype === 'sha1') {
                $extpassword = sha1($extpassword);
            }

            $rs = $authdb->Execute("SELECT * FROM {$this->config->table}
                                WHERE {$this->config->fielduser} = '".$this->ext_addslashes($extusername)."'
                                  AND {$this->config->fieldpass} = '".$this->ext_addslashes($extpassword)."' ");
            $authdb->Close();

            if (!$rs) {
                print_error('auth_dbcantconnect','auth');
                return false;
            }

            if ( $rs->RecordCount() ) {
                return true;
            } else {
                return false;
            }

        }
    }


    /**
     * Reads any other information for a user from external database,
     * then returns it in an array
     *
     * @param string $username (with system magic quotes)
     */
    function get_userinfo($username) {

        global $CFG;

        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->extencoding);

        // Connect to the external database (forcing new connection)
        $authdb = &ADONewConnection($this->config->type);
        if (!empty($this->config->debugauthdb)) {
            $authdb->debug = true;
            ob_start();//start output buffer to allow later use of the page headers
        }
        $authdb->Connect($this->config->host, $this->config->user, $this->config->pass, $this->config->name, true);
        $authdb->SetFetchMode(ADODB_FETCH_ASSOC);
        if (!empty($this->config->setupsql)) {
            $authdb->Execute($this->config->setupsql);
        }

        $fields = array("firstname", "lastname", "email", "phone1", "phone2",
                        "department", "address", "city", "country", "description",
                        "idnumber", "lang");


        //Array to map local fieldnames we want, to external fieldnames
        $selectfields = array();
        foreach ($fields as $field) {
            if (!empty($this->config->{'field_map_' . $field})) {
                $selectfields[$field] = $this->config->{'field_map_' . $field};
            }
        }
        $result = array();
        //If at least one field is mapped from external db, get that mapped data:
        if ($selectfields) {
            $select = '';
            foreach ($selectfields as $localname=>$externalname) {
                $select .= ", $externalname AS $localname";
            }
            $select = 'SELECT ' . substr($select,1);
            $sql = $select .
                " FROM {$this->config->table}" .
                " WHERE {$this->config->fielduser} = '".$this->ext_addslashes($extusername)."'";
            if ($rs = $authdb->Execute($sql)) {
                if ( $rs->RecordCount() == 1 ) {
                    $fields_obj = rs_fetch_record($rs);
                    foreach ($selectfields as $localname=>$externalname) {
                        $result[$localname] = $textlib->convert($fields_obj->{$localname}, $this->config->extencoding, 'utf-8');
                     }
                 }
                 rs_close($rs);
            }
        }
        $authdb->Close();
        return $result;
    }


    /**
     * Change a user's password
     *
     * @param  object  $user         The user to update
     * @param  string  $newpassword  The new password
     *
     * @return bool                  True on success
     */
    function user_update_password($user, $newpassword) {

        global $CFG;
        if ($this->config->passtype === 'internal') {
            update_internal_user_password($user, $newpassword, true);
        } else {
            // we should have never been called!
            return false;
        }
    }

    /**
     * syncronizes user fron external db to moodle user table
     *
     * Sync shouid be done by using idnumber attribute, not username.
     * You need to pass firstsync parameter to function to fill in
     * idnumbers if they dont exists in moodle user table.
     *
     * Syncing users removes (disables) users that dont exists anymore in external db.
     * Creates new users and updates coursecreator status of users.
     *
     * @param bool $do_updates  Optional: set to true to force an update of existing accounts
     *
     * This implementation is simpler but less scalable than the one found in the LDAP module.
     *
     */
    function sync_users ($do_updates=false) {

        global $CFG;
        $pcfg = get_config('auth/db');

        ///
        /// list external users
        ///
        $userlist = $this->get_userlist();
        $quoteduserlist = implode("', '", addslashes_recursive($userlist));
        $quoteduserlist = "'$quoteduserlist'";

        ///
        /// delete obsolete internal users
        ///

        // find obsolete users
        if (count($userlist)) {
            $sql = 'SELECT u.id, u.username
                    FROM ' . $CFG->prefix .'user u
                    WHERE u.auth=\'db\' AND u.deleted=\'0\' AND u.username NOT IN (' . $quoteduserlist . ')';
        } else {
            $sql = 'SELECT u.id, u.username
                    FROM ' . $CFG->prefix .'user u
                    WHERE u.auth=\'db\' AND u.deleted=\'0\' ';
        }
        $remove_users = get_records_sql($sql);

        if (!empty($remove_users)) {
            print_string('auth_dbuserstoremove','auth', count($remove_users));
            echo "\n";

            begin_sql();
            foreach ($remove_users as $user) {
                //following is copy pasted from admin/user.php
                //maybe this should moved to function in lib/datalib.php
                $updateuser = new object();
                $updateuser->id = $user->id;
                $updateuser->deleted = 1;
                $updateuser->timemodified = time();
                if (update_record('user', $updateuser)) {
                    echo "\t";
                    print_string('auth_dbdeleteuser', 'auth', array($user->username, $user->id));
                    echo "\n";
                } else {
                    echo "\t";
                    print_string('auth_dbdeleteusererror', 'auth', $user->username);
                    echo "\n";
                }
                //copy pasted part ends
            }
            commit_sql();
        }
        unset($remove_users); // free mem!

        if (!count($userlist)) {
            // exit right here
            // nothing else to do
            return true;
        }

        ///
        /// update existing accounts
        ///
        if ($do_updates) {
            // narrow down what fields we need to update
            $all_keys = array_keys(get_object_vars($this->config));
            $updatekeys = array();
            foreach ($all_keys as $key) {
                if (preg_match('/^field_updatelocal_(.+)$/',$key, $match)) {
                    if ($this->config->{$key} === 'onlogin') {
                        array_push($updatekeys, $match[1]); // the actual key name
                    }
                }
            }
            // print_r($all_keys); print_r($updatekeys);
            unset($all_keys); unset($key);

            // only go ahead if we actually
            // have fields to update locally
            if (!empty($updatekeys)) {
                $sql = 'SELECT u.id, u.username
                        FROM ' . $CFG->prefix .'user u
                        WHERE u.auth=\'db\' AND u.deleted=\'0\' AND u.username IN (' . $quoteduserlist . ')';
                if ($update_users = get_records_sql($sql)) {

                    foreach ($update_users as $user) {
                        $this->db_update_user_record(addslashes($user->username), $updatekeys);
                    }
                    unset($update_users); // free memory
                }
            }
        }


        ///
        /// create missing accounts
        ///
        // NOTE: this is very memory intensive
        // and generally inefficient
        $sql = 'SELECT u.id, u.username
                FROM ' . $CFG->prefix .'user u
                WHERE u.auth=\'db\' AND u.deleted=\'0\'';

        $users = get_records_sql($sql);

        // simplify down to usernames
        $usernames = array();
        foreach ($users as $user) {
            array_push($usernames, $user->username);
        }
        unset($users);

        $add_users = array_diff($userlist, $usernames);
        unset($usernames);

        if (!empty($add_users)) {
            print_string('auth_dbuserstoadd','auth',count($add_users));
            echo "\n";
            begin_sql();
            foreach($add_users as $user) {
                $username = $user;
                $user = $this->get_userinfo_asobj($user);

                // prep a few params
                $user->username   = $username;
                $user->modified   = time();
                $user->confirmed  = 1;
                $user->auth       = 'db';
                $user->mnethostid = $CFG->mnet_localhost_id;
                if (empty($user->lang)) {
                    $user->lang = $CFG->lang;
                }

                $user = addslashes_object($user);
                // maybe the user has been deleted before
                if ($old_user = get_record('user', 'username', $user->username, 'deleted', 1, 'mnethostid', $user->mnethostid)) {
                    $user->id = $old_user->id;
                    set_field('user', 'deleted', 0, 'username', $user->username);
                    echo "\t";
                    print_string('auth_dbrevive','auth',array(stripslashes($user->username), $user->id));
                    echo "\n";
                } elseif ($id = insert_record ('user',$user)) { // it is truly a new user
                    echo "\t";
                    print_string('auth_dbinsertuser','auth',array(stripslashes($user->username), $id));
                    echo "\n";
                    $user->id = $id;
                    // if relevant, tag for password generation
                    if ($this->config->passtype === 'internal') {
                        set_user_preference('auth_forcepasswordchange', 1, $id);
                        set_user_preference('create_password',          1, $id);
                    }
                } else {
                    echo "\t";
                    print_string('auth_dbinsertusererror', 'auth', $user->username);
                    echo "\n";
                }
            }
            commit_sql();
            unset($add_users); // free mem
        }
        return true;
    }

    function user_exists ($username) {

        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->extencoding);

        // Connect to the external database (forcing new connection)
        $authdb = &ADONewConnection($this->config->type);
        if (!empty($this->config->debugauthdb)) {
            $authdb->debug = true;
            ob_start();//start output buffer to allow later use of the page headers
        }
        $authdb->Connect($this->config->host, $this->config->user, $this->config->pass, $this->config->name, true);
        $authdb->SetFetchMode(ADODB_FETCH_ASSOC);
        if (!empty($this->config->setupsql)) {
            $authdb->Execute($this->config->setupsql);
        }

        $rs = $authdb->Execute("SELECT * FROM {$this->config->table}
                                     WHERE {$this->config->fielduser} = '".$this->ext_addslashes($extusername)."' ");
        $authdb->Close();

        if (!$rs) {
            print_error('auth_dbcantconnect','auth');
            return false;
        }

        if ( $rs->RecordCount() ) {
            // user exists exterally
            return $rs->RecordCount();
        } else {
            // user does not exist externally
            return false;
        }
    }


    function get_userlist() {

        // Connect to the external database (forcing new connection)
        $authdb = &ADONewConnection($this->config->type);
        if (!empty($this->config->debugauthdb)) {
            $authdb->debug = true;
            ob_start();//start output buffer to allow later use of the page headers
        }
        $authdb->Connect($this->config->host, $this->config->user, $this->config->pass, $this->config->name, true);
        $authdb->SetFetchMode(ADODB_FETCH_ASSOC);
        if (!empty($this->config->setupsql)) {
            $authdb->Execute($this->config->setupsql);
        }

        // fetch userlist
        $rs = $authdb->Execute("SELECT {$this->config->fielduser} AS username
                                FROM   {$this->config->table} ");
        $authdb->Close();

        if (!$rs) {
            print_error('auth_dbcantconnect','auth');
            return false;
        }

        if ( $rs->RecordCount() ) {
            $userlist = array();
            while ($rec = rs_fetch_next_record($rs)) {
                array_push($userlist, $rec->username);
            }
            return $userlist;
        } else {
            return array();
        }
    }

    /**
     * reads userinformation from DB and return it in an object
     *
     * @param string $username username (with system magic quotes)
     * @return array
     */
    function get_userinfo_asobj($username) {
        $user_array = truncate_userinfo($this->get_userinfo($username));
        $user = new object();
        foreach($user_array as $key=>$value) {
            $user->{$key} = $value;
        }
        return $user;
    }

    /**
     * will update a local user record from an external source.
     * is a lighter version of the one in moodlelib -- won't do
     * expensive ops such as enrolment
     *
     * If you don't pass $updatekeys, there is a performance hit and
     * values removed from DB won't be removed from moodle.
     *
     * @param string $username username (with system magic quotes)
     */
     function db_update_user_record($username, $updatekeys=false) {
        global $CFG;

        $pcfg = get_config('auth/db');

        //just in case check text case
        $username = trim(moodle_strtolower($username));

        // get the current user record
        $user = get_record('user', 'username', $username, 'mnethostid', $CFG->mnet_localhost_id);
        if (empty($user)) { // trouble
            error_log("Cannot update non-existent user: $username");
            print_error('auth_dbusernotexist','auth',$username);
            die;
        }

        // Ensure userid is not overwritten
        $userid = $user->id;

        // TODO: this had a function_exists() - now we have a $this
        if ($newinfo = $this->get_userinfo($username)) {
            $newinfo = truncate_userinfo($newinfo);

            if (empty($updatekeys)) { // all keys? this does not support removing values
                $updatekeys = array_keys($newinfo);
            }

            foreach ($updatekeys as $key) {
                if (isset($newinfo[$key])) {
                    $value = $newinfo[$key];
                } else {
                    $value = '';
                }

                if (!empty($this->config->{'field_updatelocal_' . $key})) {
                        if ($user->{$key} != $value) { // only update if it's changed
                            set_field('user', $key, addslashes($value), 'id', $userid);
                        }
                }
            }
        }
        return get_record_select("user", "id = '$userid' AND deleted <> '1'");
    }

    /**
     * A chance to validate form data, and last chance to
     * do stuff before it is inserted in config_plugin
     */
     function validate_form(&$form, &$err) {
        if ($form->passtype === 'internal') {
            $this->config->changepasswordurl = '';
            set_config('changepasswordurl', '', 'auth/db');
        }
        return true;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @returns bool
     */
    function is_internal() {
        return false;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @returns bool
     */
    function can_change_password() {
        return ($this->config->passtype === 'internal');
    }

    /**
     * Returns the URL for changing the user's pw, or false if the default can
     * be used.
     *
     * @returns bool
     */
    function change_password_url() {
        return $this->config->changepasswordurl;
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err) {
        include 'config.html';
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
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
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        $config = stripslashes_recursive($config);
        // save settings
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
        set_config('setupsql',      trim($config->setupsql), 'auth/db');
        set_config('debugauthdb',   $config->debugauthdb,   'auth/db');
        set_config('changepasswordurl', trim($config->changepasswordurl), 'auth/db');

        return true;
    }

    function ext_addslashes($text) {
        // using custom made function for now
        if (empty($this->config->sybasequoting)) {
            $text = str_replace('\\', '\\\\', $text);
            $text = str_replace(array('\'', '"', "\0"), array('\\\'', '\\"', '\\0'), $text);
        } else {
            $text = str_replace("'", "''", $text);
        }
        return $text;
    }
}

?>
