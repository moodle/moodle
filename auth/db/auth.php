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

// This page cannot be called directly
if (!isset($CFG)) exit;

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
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @returns bool Authentication success or failure.
     */
    function user_login ($username, $password) {

        global $CFG;

        // This is a hack to workaround what seems to be a bug in ADOdb with accessing 
        // two databases of the same kind ... it seems to get confused when trying to access
        // the first database again, after having accessed the second.
        // The following hack will make the database explicit which keeps it happy
        // This seems to broke postgesql so ..

        $prefix = $CFG->prefix.'';    // Remember it.  The '' is to prevent PHP5 reference.. see bug 3223

        if ($CFG->dbtype != 'postgres7') {
            $CFG->prefix = $CFG->dbname.$CFG->prefix;
        }

        // Connect to the external database
        $authdb = &ADONewConnection($this->config->type); 
        $authdb->PConnect($this->config->host, $this->config->user, $this->config->pass, $this->config->name); 
        $authdb->SetFetchMode(ADODB_FETCH_ASSOC);

        if ($this->config->passtype === 'internal') { 
            // lookup username externally, but resolve
            // password locally -- to support backend that
            // don't track passwords
            $rs = $authdb->Execute("SELECT * FROM {$this->config->table} 
                                     WHERE {$this->config->fielduser} = '$username' ");
            $authdb->Close();

            if (!$rs) {
                notify("Could not connect to the specified authentication database...");

                return false;
            }
        
            if ( $rs->RecordCount() ) {
                // user exists exterally
                // check username/password internally
                if ($user = get_record('user', 'username', $username)) {
                    return validate_internal_user_password($user, $password);
                }
            } else {
                // user does not exist externally
                return false;
            }  

        } else { 
            // normal case: use external db for passwords

            if ($this->config->passtype === 'md5') {   // Re-format password accordingly
                $password = md5($password);
            }

            $rs = $authdb->Execute("SELECT * FROM {$this->config->table} 
                                WHERE {$this->config->fielduser} = '$username' 
                                  AND {$this->config->fieldpass} = '$password' ");
            $authdb->Close();
            
            $CFG->prefix = $prefix;
            
            if (!$rs) {
                notify("Could not connect to the specified authentication database...");
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
     */
    function get_userinfo($username) {

        global $CFG;

        ADOLoadCode($this->config->type);          
        $authdb = &ADONewConnection();         
        $authdb->PConnect($this->config->host, $this->config->user, $this->config->pass, $this->config->name); 
        $authdb->SetFetchMode(ADODB_FETCH_ASSOC);

        $fields = array("firstname", "lastname", "email", "phone1", "phone2", 
                        "department", "address", "city", "country", "description", 
                        "idnumber", "lang");

        $result = array();

        foreach ($fields as $field) {
            if ($this->config->{'field_map_' . $field}) {
                if ($rs = $authdb->Execute("SELECT " . $this->config->{'field_map_' . $field} . " FROM {$this->config->table}
                                            WHERE {$this->config->fielduser} = '$username'")) {
                    if ( $rs->RecordCount() == 1 ) {
                        if (!empty($CFG->unicodedb)) {
                            $result["$field"] = addslashes(stripslashes($rs->fields[0]));
                        } else {
                            $result["$field"] = addslashes(stripslashes(utf8_decode($rs->fields[0])));
                        }
                    }
                }
            }
        }
        $authdb->Close();

        return $result;
    }


    function user_update_password($username, $newpassword) {

        if ($this->config->passtype === 'internal') {
            return set_field('user', 'password', md5($newpassword), 'username', $username);
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
    function sync_users ($do_updates=0) {
        
        global $CFG;
        $pcfg = get_config('auth/db');

        ///
        /// list external users
        ///
        $userlist = $this->get_userlist();
        $quoteduserlist = implode("', '", $userlist);
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
            print "User entries to remove: ". count($remove_users) . "\n";

            begin_sql();
            foreach ($remove_users as $user) {
                //following is copy pasted from admin/user.php
                //maybe this should moved to function in lib/datalib.php
                unset($updateuser);
                $updateuser->id = $user->id;
                $updateuser->deleted = "1";
                $updateuser->timemodified = time();
                if (update_record("user", $updateuser)) {
                    // unenrol_student($user->id);  // From all courses
                    // remove_teacher($user->id);   // From all courses
                    // remove_admin($user->id);
                    delete_records('role_assignments', 'userid', $user->id); // unassign all roles
                    notify(get_string("deletedactivity", "", fullname($user, true)) );
                } else {
                    notify(get_string("deletednot", "", fullname($user, true)));
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
                $update_users = get_records_sql($sql);
            
                foreach ($update_users as $user) {
                    $this->db_update_user_record($user->username, $updatekeys);
                }
                unset($update_users); // free memory
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
            print "User entries to add: ". count($add_users). "\n";
            begin_sql();
            foreach($add_users as $user) {
                $username = $user;
                $user = $this->get_userinfo_asobj($user);
                
                // prep a few params
                $user->username  = $username;
                $user->modified  = time();
                $user->confirmed = 1;
                $user->auth      = 'db';
                
                // insert it
                $old_debug=$CFG->debug; 
                $CFG->debug=10;
                
                // maybe the user has been deleted before
                if ($old_user = get_record('user', 'username', $user->username, 'deleted', 1)) {
                    $user->id = $old_user->id;
                    set_field('user', 'deleted', 0, 'username', $user->username);
                    echo "Revived user $user->username id $user->id\n";
                } elseif ($id=insert_record ('user',$user)) { // it is truly a new user
                    echo "inserted user $user->username id $id\n";
                    $user->id = $id;
                    // if relevant, tag for password generation
                    if ($this->config->passtype === 'internal') {
                        set_user_preference('auth_forcepasswordchange', 1, $id);
                        set_user_preference('create_password',          1, $id);
                    }
                } else {
                    echo "error inserting user $user->username \n";
                }
                $CFG->debug=$old_debug;                        
            }
            commit_sql();
            unset($add_users); // free mem
        }
        return true;
    }

    function user_exists ($username) {
        $authdb = &ADONewConnection($this->config->type); 
        $authdb->PConnect($this->config->host, $this->config->user, $this->config->pass, $this->config->name); 
        $authdb->SetFetchMode(ADODB_FETCH_ASSOC);

        $rs = $authdb->Execute("SELECT * FROM {$this->config->table} 
                                     WHERE {$this->config->fielduser} = '$username' ");
        $authdb->Close();

        if (!$rs) {
            notify("Could not connect to the specified authentication database...");
            return false;
        }
        
        if ( $rs->RecordCount() ) {
            // user exists exterally
            // check username/password internally
            // ?? there is no $password variable, so why??
            /*if ($user = get_record('user', 'username', $username)) {
                return ($user->password == md5($password));
            }*/
            return $rs->RecordCount();
        } else {
            // user does not exist externally
            return false;
        }  
    }


    function get_userlist() {
        // Connect to the external database
        $authdb = &ADONewConnection($this->config->type); 
        $authdb->PConnect($this->config->host,$this->config->user,$this->config->pass,$this->config->name); 
        $authdb->SetFetchMode(ADODB_FETCH_ASSOC);

        // fetch userlist
        $rs = $authdb->Execute("SELECT {$this->config->fielduser} AS username
                                FROM   {$this->config->table} ");
        $authdb->Close();

        if (!$rs) {
            notify("Could not connect to the specified authentication database...");
            return false;
        }
        
        if ( $rs->RecordCount() ) {
            $userlist = array();
            while ($rec = $rs->FetchRow()) {
                array_push($userlist, $rec['username']);
            }
            return $userlist;
        } else {
            return array();
        }        
    }

    /**
     * reads userinformation from DB and return it in an object
     *
     * @param string $username username
     * @return array
     */
    function get_userinfo_asobj($username) {
        $user_array = truncate_userinfo($this->get_userinfo($username));
        $user = new object;
        foreach($user_array as $key=>$value) {
            $user->{$key} = $value;
        }
        return $user;
    }

    /*
     * will update a local user record from an external source. 
     * is a lighter version of the one in moodlelib -- won't do 
     * expensive ops such as enrolment
     *
     * If you don't pass $updatekeys, there is a performance hit and 
     * values removed from DB won't be removed from moodle.
     */
     function db_update_user_record($username, $updatekeys=false) {

        $pcfg = get_config('auth/db');

        //just in case check text case
        $username = trim(moodle_strtolower($username));
        
        // get the current user record
        $user = get_record('user', 'username', $username);
        if (empty($user)) { // trouble
            error_log("Cannot update non-existent user: $username");
            die;
        }

        // TODO: this had a function_exists() - now we have a $this 
        if ($newinfo = $this->get_userinfo($username)) {
            $newinfo = truncate_userinfo($newinfo);
            
            if (empty($updatekeys)) { // all keys? this does not support removing values
                $updatekeys = array_keys($newinfo);
            }
            
            foreach ($updatekeys as $key) {
                unset($value);
                if (isset($newinfo[$key])) {
                    $value = $newinfo[$key];
                    $value = addslashes(stripslashes($value)); // Just in case
                } else {
                    $value = '';
                }
                if (!empty($this->config->{'field_updatelocal_' . $key})) { 
                        if ($user->{$key} != $value) { // only update if it's changed
                            set_field('user', $key, $value, 'username', $username);
                        }
                }
            }
        }
        return get_record_select("user", "username = '$username' AND deleted <> '1'");
    }

    // A chance to validate form data, and last chance to 
    // do stuff before it is inserted in config_plugin
    function validate_form(&$form, &$err) {
        if ($form['passtype'] === 'internal') {
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
        include "config.html";
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset($config->host)) {
            $config->host = "localhost";
        }
        if (!isset($config->type)) {
            $config->type = "mysql";
        }
        if (!isset($config->name)) {
            $config->name = "";
        }
        if (!isset($config->user)) {
            $config->user = "";
        }
        if (!isset($config->pass)) {
            $config->pass = "";
        }
        if (!isset($config->table)) {
            $config->table = "";
        }
        if (!isset($config->fielduser)) {
            $config->fielduser = "";
        }
        if (!isset($config->fieldpass)) {
            $config->fieldpass = "";
        }
        if (!isset($config->passtype)) {
            $config->passtype = "plaintext";
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // save settings
        set_config('host',      $config->host,      'auth/db');
        set_config('type',      $config->type,      'auth/db');
        set_config('name',      $config->name,      'auth/db');
        set_config('user',      $config->user,      'auth/db');
        set_config('pass',      $config->pass,      'auth/db');
        set_config('table',     $config->table,     'auth/db');
        set_config('fielduser', $config->fielduser, 'auth/db');
        set_config('fieldpass', $config->fieldpass, 'auth/db');
        set_config('passtype',  $config->passtype,  'auth/db');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/db');
        
        return true;
    }

}

?>
