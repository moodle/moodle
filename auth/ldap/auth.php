<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: LDAP Authentication
 *
 * Authentication using LDAP (Lightweight Directory Access Protocol).
 *
 * 2006-08-28  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * LDAP authentication plugin.
 */
class auth_plugin_ldap {

    /**
     * The configuration details for the plugin.
     */
    var $config;

    /**
     * Constructor with initialisation.
     */
    function auth_plugin_ldap() {
        $this->config = get_config('auth/ldap');
        if (empty($this->config->ldapencoding)) {
            $this->config->ldapencoding = 'utf-8';
        }
        if (empty($this->config->user_type)) {
            $this->config->user_type = 'default';
        }

        $default = $this->ldap_getdefaults();

        //use defaults if values not given
        foreach ($default as $key => $value) {
            // watch out - 0, false are correct values too
            if (!isset($this->config->{$key}) or $this->config->{$key} == '') {
                $this->config->{$key} = $value[$this->config->user_type];
            }
        }
        //hack prefix to objectclass
        if (empty($this->config->objectclass)) {        // Can't send empty filter
            $this->config->objectclass='objectClass=*';
        } else if (strpos($this->config->objectclass, 'objectClass=') !== 0) {
            $this->config->objectclass = 'objectClass='.$this->config->objectclass;
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
    function user_login($username, $password) {
        if (! function_exists('ldap_bind')) {
            print_error('auth_ldapnotinstalled','auth');
            return false;
        }

        if (!$username or !$password) {    // Don't allow blank usernames or passwords
            return false;
        }

        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->ldapencoding);
        $extpassword = $textlib->convert(stripslashes($password), 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();

        if ($ldapconnection) {
            $ldap_user_dn = $this->ldap_find_userdn($ldapconnection, $extusername);

            //if ldap_user_dn is empty, user does not exist
            if (!$ldap_user_dn) {
                ldap_close($ldapconnection);
                return false;
            }

            // Try to bind with current username and password
            $ldap_login = @ldap_bind($ldapconnection, $ldap_user_dn, $extpassword);
            ldap_close($ldapconnection);
            if ($ldap_login) {
                return true;
            }
        }
        else {
            @ldap_close($ldapconnection);
            print_error('auth_ldap_noconnect','auth',$this->config->host_url);
        }
        return false;
    }

    /**
     * reads userinformation from ldap and return it in array()
     *
     * Read user information from external database and returns it as array().
     * Function should return all information available. If you are saving
     * this information to moodle user-table you should honor syncronization flags
     *
     * @param string $username username (with system magic quotes)
     *
     * @return mixed array with no magic quotes or false on error
     */
    function get_userinfo($username) {
        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();
        $attrmap = $this->ldap_attributes();

        $result = array();
        $search_attribs = array();

        foreach ($attrmap as $key=>$values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            foreach ($values as $value) {
                if (!in_array($value, $search_attribs)) {
                    array_push($search_attribs, $value);
                }
            }
        }

        $user_dn = $this->ldap_find_userdn($ldapconnection, $extusername);

        if (!$user_info_result = ldap_read($ldapconnection, $user_dn, $this->config->objectclass, $search_attribs)) {
            return false; // error!
        }
        $user_entry = $this->ldap_get_entries($ldapconnection, $user_info_result);
        if (empty($user_entry)) {
            return false; // entry not found
        }

        foreach ($attrmap as $key=>$values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            $ldapval = NULL;
            foreach ($values as $value) {
                if (!array_key_exists($value, $user_entry[0])) {
                    continue; // wrong data mapping!
                }
                if (is_array($user_entry[0][$value])) {
                    $newval = $textlib->convert($user_entry[0][$value][0], $this->config->ldapencoding, 'utf-8');
                } else {
                    $newval = $textlib->convert($user_entry[0][$value], $this->config->ldapencoding, 'utf-8');
                }
                if (!empty($newval)) { // favour ldap entries that are set
                    $ldapval = $newval;
                }
            }
            if (!is_null($ldapval)) {
                $result[$key] = $ldapval;
            }
        }

        @ldap_close($ldapconnection);
        return $result;
    }

    /**
     * reads userinformation from ldap and return it in an object
     *
     * @param string $username username (with system magic quotes)
     * @return mixed object or false on error
     */
    function get_userinfo_asobj($username) {
        $user_array = $this->get_userinfo($username);
        if ($user_array == false) {
            return false; //error or not found
        }
        $user_array = truncate_userinfo($user_array);
        $user = new object();
        foreach ($user_array as $key=>$value) {
            $user->{$key} = $value;
        }
        return $user;
    }

    /**
     * returns all usernames from external database
     *
     * get_userlist returns all usernames from external database
     *
     * @return array
     */
    function get_userlist() {
        return $this->ldap_get_userlist("({$this->config->user_attribute}=*)");
    }

    /**
     * checks if user exists on external db
     *
     * @param string $username (with system magic quotes)
     */
    function user_exists($username) {

        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->ldapencoding);

        //returns true if given username exist on ldap
        $users = $this->ldap_get_userlist("({$this->config->user_attribute}=".$this->filter_addslashes($extusername).")");
        return count($users);
    }

    /**
     * Creates a new user on external database.
     * By using information in userobject
     * Use user_exists to prevent dublicate usernames
     *
     * @param mixed $userobject  Moodle userobject  (with system magic quotes)
     * @param mixed $plainpass   Plaintext password (with system magic quotes)
     */
    function user_create($userobject, $plainpass) {
        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($userobject->username), 'utf-8', $this->config->ldapencoding);
        $extpassword = $textlib->convert(stripslashes($plainpass), 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();
        $attrmap = $this->ldap_attributes();

        $newuser = array();

        foreach ($attrmap as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            foreach ($values as $value) {
                if (!empty($userobject->$key) ) {
                    $newuser[$value] = $textlib->convert(stripslashes($userobject->$key), 'utf-8', $this->config->ldapencoding);
                }
            }
        }

        //Following sets all mandatory and other forced attribute values
        //User should be creted as login disabled untill email confirmation is processed
        //Feel free to add your user type and send patches to paca@sci.fi to add them
        //Moodle distribution

        switch ($this->config->user_type)  {
            case 'edir':
                $newuser['objectClass']   = array("inetOrgPerson","organizationalPerson","person","top");
                $newuser['uniqueId']      = $extusername;
                $newuser['logindisabled'] = "TRUE";
                $newuser['userpassword']  = $extpassword;
                break;
            default:
               print_error('auth_ldap_unsupportedusertype','auth',$this->config->user_type);
        }
        $uadd = $this->ldap_add($ldapconnection, $this->config->user_attribute.'="'.$this->ldap_addslashes($userobject->username).','.$this->config->create_context.'"', $newuser);
        ldap_close($ldapconnection);
        return $uadd;

    }

    /**
     * return number of days to user password expires
     *
     * If userpassword does not expire it should return 0. If password is already expired
     * it should return negative value.
     *
     * @param mixed $username username
     * @return integer
     */
    function password_expire($username) {
        $result = false;

        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();
        $user_dn = $this->ldap_find_userdn($ldapconnection, $extusername);
        $search_attribs = array($this->config->expireattr);
        $sr = ldap_read($ldapconnection, $user_dn, 'objectclass=*', $search_attribs);
        if ($sr)  {
            $info = $this->ldap_get_entries($ldapconnection, $sr);
            if (empty ($info) or empty($info[0][$this->config->expireattr][0])) {
                //error_log("ldap: no expiration value".$info[0][$this->config->expireattr]);
                // no expiration attribute, password does not expire
                $result = 0;
            }
            else {
                $now = time();
                $expiretime = $this->ldap_expirationtime2unix($info[0][$this->config->expireattr][0]);
                if ($expiretime > $now) {
                    $result = ceil(($expiretime - $now) / DAYSECS);
                }
                else {
                    $result = floor(($expiretime - $now) / DAYSECS);
                }
            }
        } else {
            error_log("ldap: password_expire did't find expiration time.");
        }

        //error_log("ldap: password_expire user $user_dn expires in $result days!");
        return $result;
    }

    /**
     * syncronizes user fron external db to moodle user table
     *
     * Sync is now using username attribute.
     *
     * Syncing users removes or suspends users that dont exists anymore in external db.
     * Creates new users and updates coursecreator status of users.
     *
     * @param int $bulk_insert_records will insert $bulkinsert_records per insert statement
     *                         valid only with $unsafe. increase to a couple thousand for
     *                         blinding fast inserts -- but test it: you may hit mysqld's
     *                         max_allowed_packet limit.
     * @param bool $do_updates will do pull in data updates from ldap if relevant
     */
    function sync_users ($bulk_insert_records = 1000, $do_updates = true) {

        global $CFG;

        $textlib = textlib_get_instance();

        $droptablesql = array(); /// sql commands to drop the table (because session scope could be a problem for
                                 /// some persistent drivers like ODBTP (mssql) or if this function is invoked
                                 /// from within a PHP application using persistent connections

        // configure a temp table
        print "Configuring temp table\n";
        switch (strtolower($CFG->dbfamily)) {
            case 'mysql':
                $temptable = $CFG->prefix . 'extuser';
                $droptablesql[] = 'DROP TEMPORARY TABLE ' . $temptable; // sql command to drop the table (because session scope could be a problem)
                execute_sql_arr($droptablesql, true, false); /// Drop temp table to avoid persistence problems later
                echo "Creating temp table $temptable\n";
                execute_sql('CREATE TEMPORARY TABLE ' . $temptable . ' (username VARCHAR(64), PRIMARY KEY (username)) TYPE=MyISAM', false);
                break;
            case 'postgres':
                $temptable = $CFG->prefix . 'extuser';
                $droptablesql[] = 'DROP TABLE ' . $temptable; // sql command to drop the table (because session scope could be a problem)
                execute_sql_arr($droptablesql, true, false); /// Drop temp table to avoid persistence problems later
                echo "Creating temp table $temptable\n";
                $bulk_insert_records = 1; // no support for multiple sets of values
                execute_sql('CREATE TEMPORARY TABLE '. $temptable . ' (username VARCHAR(64), PRIMARY KEY (username))', false);
                break;
            case 'mssql':
                $temptable = '#'.$CFG->prefix . 'extuser'; /// MSSQL temp tables begin with #
                $droptablesql[] = 'DROP TABLE ' . $temptable; // sql command to drop the table (because session scope could be a problem)
                execute_sql_arr($droptablesql, true, false); /// Drop temp table to avoid persistence problems later
                echo "Creating temp table $temptable\n";
                $bulk_insert_records = 1; // no support for multiple sets of values
                execute_sql('CREATE TABLE ' . $temptable . ' (username VARCHAR(64), PRIMARY KEY (username))', false);
                break;
            case 'oracle':
                $temptable = $CFG->prefix . 'extuser';
                $droptablesql[] = 'TRUNCATE TABLE ' . $temptable; // oracle requires truncate before being able to drop a temp table
                $droptablesql[] = 'DROP TABLE ' . $temptable; // sql command to drop the table (because session scope could be a problem)
                execute_sql_arr($droptablesql, true, false); /// Drop temp table to avoid persistence problems later
                echo "Creating temp table $temptable\n";
                $bulk_insert_records = 1; // no support for multiple sets of values
                execute_sql('CREATE GLOBAL TEMPORARY TABLE '.$temptable.' (username VARCHAR(64), PRIMARY KEY (username)) ON COMMIT PRESERVE ROWS', false);
                break;
        }

        print "Connecting to ldap...\n";
        $ldapconnection = $this->ldap_connect();

        if (!$ldapconnection) {
            @ldap_close($ldapconnection);
            print get_string('auth_ldap_noconnect','auth',$this->config->host_url);
            exit;
        }

        ////
        //// get user's list from ldap to sql in a scalable fashion
        ////
        // prepare some data we'll need
        $filter = "(&(".$this->config->user_attribute."=*)(".$this->config->objectclass."))";

        $contexts = explode(";",$this->config->contexts);

        if (!empty($this->config->create_context)) {
              array_push($contexts, $this->config->create_context);
        }

        $fresult = array();
        foreach ($contexts as $context) {
            $context = trim($context);
            if (empty($context)) {
                continue;
            }
            begin_sql();
            if ($this->config->search_sub) {
                //use ldap_search to find first user from subtree
                $ldap_result = ldap_search($ldapconnection, $context,
                                           $filter,
                                           array($this->config->user_attribute));
            } else {
                //search only in this context
                $ldap_result = ldap_list($ldapconnection, $context,
                                         $filter,
                                         array($this->config->user_attribute));
            }

            if ($entry = ldap_first_entry($ldapconnection, $ldap_result)) {
                do {
                    $value = ldap_get_values_len($ldapconnection, $entry, $this->config->user_attribute);
                    $value = $textlib->convert($value[0], $this->config->ldapencoding, 'utf-8');
                    array_push($fresult, $value);
                    if (count($fresult) >= $bulk_insert_records) {
                        $this->ldap_bulk_insert($fresult, $temptable);
                        $fresult = array();
                    }
                } while ($entry = ldap_next_entry($ldapconnection, $entry));
            }
            unset($ldap_result); // free mem

            // insert any remaining users and release mem
            if (count($fresult)) {
                $this->ldap_bulk_insert($fresult, $temptable);
                $fresult = array();
            }
            commit_sql();
        }

        /// preserve our user database
        /// if the temp table is empty, it probably means that something went wrong, exit
        /// so as to avoid mass deletion of users; which is hard to undo
        $count = get_record_sql('SELECT COUNT(username) AS count, 1 FROM ' . $temptable);
        $count = $count->{'count'};
        if ($count < 1) {
            print "Did not get any users from LDAP -- error? -- exiting\n";
            exit;
        } else {
            print "Got $count records from LDAP\n\n";
        }


/// User removal
        // find users in DB that aren't in ldap -- to be removed!
        // this is still not as scalable (but how often do we mass delete?)
        if (!empty($this->config->removeuser)) {
            $sql = "SELECT u.id, u.username, u.email
                    FROM {$CFG->prefix}user u
                        LEFT JOIN $temptable e ON u.username = e.username
                    WHERE u.auth='ldap'
                        AND u.deleted=0
                        AND e.username IS NULL";
            $remove_users = get_records_sql($sql);

            if (!empty($remove_users)) {
                print "User entries to remove: ". count($remove_users) . "\n";

                begin_sql();
                foreach ($remove_users as $user) {
                    if ($this->config->removeuser == 2) {
                        //following is copy pasted from admin/user.php
                        //maybe this should moved to function in lib/datalib.php
                        $updateuser = new object();
                        $updateuser->id           = $user->id;
                        $updateuser->deleted      = 1;
                        $updateuser->username     = addslashes("$user->email.".time());  // Remember it just in case
                        $updateuser->email        = '';               // Clear this field to free it up
                        $updateuser->idnumber     = '';               // Clear this field to free it up
                        $updateuser->timemodified = time();
                        if (update_record('user', $updateuser)) {
                            delete_records('role_assignments', 'userid', $user->id); // unassign all roles
                        //copy pasted part ends
                            echo "\t"; print_string('auth_dbdeleteuser', 'auth', array($user->username, $user->id)); echo "\n";
                        } else {
                            echo "\t"; print_string('auth_dbdeleteusererror', 'auth', $user->username); echo "\n";
                        }
                    } else if ($this->config->removeuser == 1) {
                        $updateuser = new object();
                        $updateuser->id = $user->id;
                        $updateuser->auth = 'nologin';
                        if (update_record('user', $updateuser)) {
                            echo "\t"; print_string('auth_dbsuspenduser', 'auth', array($user->username, $user->id)); echo "\n";
                        } else {
                            echo "\t"; print_string('auth_dbsuspendusererror', 'auth', $user->username); echo "\n";
                        }
                    }
                }
                commit_sql();
            } else {
                print "No user entries to be removed\n";
            }
            unset($remove_users); // free mem!
        }

/// Revive suspended users
        if (!empty($this->config->removeuser) and $this->config->removeuser == 1) {
            $sql = "SELECT u.id, u.username
                    FROM $temptable e, {$CFG->prefix}user u
                    WHERE e.username=u.username
                        AND u.auth='nologin'";
            $revive_users = get_records_sql($sql);

            if (!empty($revive_users)) {
                print "User entries to be revived: ". count($revive_users) . "\n";

                begin_sql();
                foreach ($revive_users as $user) {
                    $updateuser = new object();
                    $updateuser->id = $user->id;
                    $updateuser->auth = 'ldap';
                    if (update_record('user', $updateuser)) {
                        echo "\t"; print_string('auth_dbreviveser', 'auth', array($user->username, $user->id)); echo "\n";
                    } else {
                        echo "\t"; print_string('auth_dbreviveusererror', 'auth', $user->username); echo "\n";
                    }
                }
                commit_sql();
            } else {
                print "No user entries to be revived\n";
            }

            unset($revive_users);
        }


/// User Updates - time-consuming (optional)
        if ($do_updates) {
            // narrow down what fields we need to update
            $all_keys = array_keys(get_object_vars($this->config));
            $updatekeys = array();
            foreach ($all_keys as $key) {
                if (preg_match('/^field_updatelocal_(.+)$/',$key, $match)) {
                    // if we have a field to update it from
                    // and it must be updated 'onlogin' we
                    // update it on cron
                    if ( !empty($this->config->{'field_map_'.$match[1]})
                         and $this->config->{$match[0]} === 'onlogin') {
                        array_push($updatekeys, $match[1]); // the actual key name
                    }
                }
            }
            // print_r($all_keys); print_r($updatekeys);
            unset($all_keys); unset($key);

        } else {
            print "No updates to be done\n";
        }
        if ( $do_updates and !empty($updatekeys) ) { // run updates only if relevant
            $users = get_records_sql("SELECT u.username, u.id
                                      FROM {$CFG->prefix}user u
                                      WHERE u.deleted=0 AND u.auth='ldap'");
            if (!empty($users)) {
                print "User entries to update: ". count($users). "\n";

                $sitecontext = get_context_instance(CONTEXT_SYSTEM);
                if (!empty($this->config->creators) and !empty($this->config->memberattribute)
                  and $roles = get_roles_with_capability('moodle/legacy:coursecreator', CAP_ALLOW)) {
                    $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
                } else {
                    $creatorrole = false;
                }

                begin_sql();
                $xcount = 0;
                $maxxcount = 100;

                foreach ($users as $user) {
                    echo "\t"; print_string('auth_dbupdatinguser', 'auth', array($user->username, $user->id));
                    if (!$this->update_user_record(addslashes($user->username), $updatekeys)) {
                        echo " - ".get_string('skipped');
                    }
                    echo "\n";
                    $xcount++;

                    // update course creators if needed
                    if ($creatorrole !== false) {
                        if ($this->iscreator($user->username)) {
                            role_assign($creatorrole->id, $user->id, 0, $sitecontext->id, 0, 0, 0, 'ldap');
                        } else {
                            role_unassign($creatorrole->id, $user->id, 0, $sitecontext->id);
                        }
                    }

                    if ($xcount++ > $maxxcount) {
                        commit_sql();
                        begin_sql();
                        $xcount = 0;
                    }
                }
                commit_sql();
                unset($users); // free mem
            }
        } else { // end do updates
            print "No updates to be done\n";
        }

/// User Additions
        // find users missing in DB that are in LDAP
        // note that get_records_sql wants at least 2 fields returned,
        // and gives me a nifty object I don't want.
        // note: we do not care about deleted accounts anymore, this feature was replaced by suspending to nologin auth plugin
        $sql = "SELECT e.username, e.username
                FROM $temptable e LEFT JOIN {$CFG->prefix}user u ON e.username = u.username
                WHERE u.id IS NULL";
        $add_users = get_records_sql($sql); // get rid of the fat

        if (!empty($add_users)) {
            print "User entries to add: ". count($add_users). "\n";

            $sitecontext = get_context_instance(CONTEXT_SYSTEM);
            if (!empty($this->config->creators) and !empty($this->config->memberattribute)
              and $roles = get_roles_with_capability('moodle/legacy:coursecreator', CAP_ALLOW)) {
                $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
            } else {
                $creatorrole = false;
            }

            begin_sql();
            foreach ($add_users as $user) {
                $user = $this->get_userinfo_asobj(addslashes($user->username));

                // prep a few params
                $user->modified   = time();
                $user->confirmed  = 1;
                $user->auth       = 'ldap';
                $user->mnethostid = $CFG->mnet_localhost_id;
                if (empty($user->lang)) {
                    $user->lang = $CFG->lang;
                }

                $user = addslashes_recursive($user);

                if ($id = insert_record('user',$user)) {
                    echo "\t"; print_string('auth_dbinsertuser', 'auth', array(stripslashes($user->username), $id)); echo "\n";
                    $userobj = $this->update_user_record($user->username);
                    if (!empty($this->config->forcechangepassword)) {
                        set_user_preference('auth_forcepasswordchange', 1, $userobj->id);
                    }
                } else {
                    echo "\t"; print_string('auth_dbinsertusererror', 'auth', $user->username); echo "\n";
                }

                // add course creators if needed
                if ($creatorrole !== false and $this->iscreator(stripslashes($user->username))) {
                    role_assign($creatorrole->id, $user->id, 0, $sitecontext->id, 0, 0, 0, 'ldap');
                }
            }
            commit_sql();
            unset($add_users); // free mem
        } else {
            print "No users to be added\n";
        }
        return true;
    }

    /**
     * Update a local user record from an external source.
     * This is a lighter version of the one in moodlelib -- won't do
     * expensive ops such as enrolment.
     *
     * If you don't pass $updatekeys, there is a performance hit and
     * values removed from LDAP won't be removed from moodle.
     *
     * @param string $username username (with system magic quotes)
     */
    function update_user_record($username, $updatekeys = false) {
        global $CFG;

        //just in case check text case
        $username = trim(moodle_strtolower($username));

        // get the current user record
        $user = get_record('user', 'username', $username, 'mnethostid', $CFG->mnet_localhost_id);
        if (empty($user)) { // trouble
            error_log("Cannot update non-existent user: ".stripslashes($username));
            print_error('auth_dbusernotexist','auth',$username);
            die;
        }

        // Protect the userid from being overwritten
        $userid = $user->id;

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
        } else {
            return false;
        }
        return get_record_select('user', "id = $userid AND deleted = 0");
    }

    /**
     * Bulk insert in SQL's temp table
     * @param array $users is an array of usernames
     */
    function ldap_bulk_insert($users, $temptable) {

        // bulk insert -- superfast with $bulk_insert_records
        $sql = 'INSERT INTO ' . $temptable . ' (username) VALUES ';
        // make those values safe
        $users = addslashes_recursive($users);
        // join and quote the whole lot
        $sql = $sql . "('" . implode("'),('", $users) . "')";
        print "\t+ " . count($users) . " users\n";
        execute_sql($sql, false);
    }


    /**
     * Activates (enables) user in external db so user can login to external db
     *
     * @param mixed $username    username (with system magic quotes)
     * @return boolen result
     */
    function user_activate($username) {
        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();

        $userdn = $this->ldap_find_userdn($ldapconnection, $extusername);
        switch ($this->config->user_type)  {
            case 'edir':
                $newinfo['loginDisabled']="FALSE";
                break;
            default:
                error ('auth: ldap user_activate() does not support selected usertype:"'.$this->config->user_type.'" (..yet)');
        }
        $result = ldap_modify($ldapconnection, $userdn, $newinfo);
        ldap_close($ldapconnection);
        return $result;
    }

    /**
     * Disables user in external db so user can't login to external db
     *
     * @param mixed $username    username
     * @return boolean result
     */
/*    function user_disable($username) {
        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();

        $userdn = $this->ldap_find_userdn($ldapconnection, $extusername);
        switch ($this->config->user_type)  {
            case 'edir':
                $newinfo['loginDisabled']="TRUE";
                break;
            default:
                error ('auth: ldap user_disable() does not support selected usertype (..yet)');
        }
        $result = ldap_modify($ldapconnection, $userdn, $newinfo);
        ldap_close($ldapconnection);
        return $result;
    }*/

    /**
     * Returns true if user should be coursecreator.
     *
     * @param mixed $username    username (with system magic quotes)
     * @return boolean result
     */
    function iscreator($username = false) {
        global $USER;

        if (empty($this->config->creators) or empty($this->config->memberattribute)) {
            return false;
        }

        if ($username === false) {
            $username = $USER->username;
        } else {
            $username = stripslashes($username);
        }

        $textlib = textlib_get_instance();
        $extusername = $textlib->convert($username, 'utf-8', $this->config->ldapencoding);

        return $this->ldap_isgroupmember($extusername, $this->config->creators);
    }

    /**
     * Called when the user record is updated.
     * Modifies user in external database. It takes olduser (before changes) and newuser (after changes)
     * conpares information saved modified information to external db.
     *
     * @param mixed $olduser     Userobject before modifications    (without system magic quotes)
     * @param mixed $newuser     Userobject new modified userobject (without system magic quotes)
     * @return boolean result
     *
     */
    function user_update($olduser, $newuser) {

        global $USER;

        if (isset($olduser->username) and isset($newuser->username) and $olduser->username != $newuser->username) {
            error_log("ERROR:User renaming not allowed in LDAP");
            return false;
        }

        if (isset($olduser->auth) and $olduser->auth == 'ldap') {
            return true; // just change auth and skip update
        }

        $textlib = textlib_get_instance();
        $extoldusername = $textlib->convert($olduser->username, 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();

        $search_attribs = array();

        $attrmap = $this->ldap_attributes();
        foreach ($attrmap as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            foreach ($values as $value) {
                if (!in_array($value, $search_attribs)) {
                    array_push($search_attribs, $value);
                }
            }
        }

        $user_dn = $this->ldap_find_userdn($ldapconnection, $extoldusername);

        $user_info_result = ldap_read($ldapconnection, $user_dn,
                                $this->config->objectclass, $search_attribs);

        if ($user_info_result) {

            $user_entry = $this->ldap_get_entries($ldapconnection, $user_info_result);
            if (empty($user_entry)) {
                return false; // old user not found!
            } else if (count($user_entry) > 1) {
                trigger_error("ldap: Strange! More than one user record found in ldap. Only using the first one.");
                return false;
            }
            $user_entry = $user_entry[0];

            //error_log(var_export($user_entry) . 'fpp' );

            foreach ($attrmap as $key => $ldapkeys) {
                // only process if the moodle field ($key) has changed and we
                // are set to update LDAP with it
                if (isset($olduser->$key) and isset($newuser->$key)
                  and $olduser->$key !== $newuser->$key
                  and !empty($this->config->{'field_updateremote_'. $key})) {
                    // for ldap values that could be in more than one
                    // ldap key, we will do our best to match
                    // where they came from
                    $ambiguous = true;
                    $changed   = false;
                    if (!is_array($ldapkeys)) {
                        $ldapkeys = array($ldapkeys);
                    }
                    if (count($ldapkeys) < 2) {
                        $ambiguous = false;
                    }

                    $nuvalue = $textlib->convert($newuser->$key, 'utf-8', $this->config->ldapencoding);
                    $ouvalue = $textlib->convert($olduser->$key, 'utf-8', $this->config->ldapencoding);

                    foreach ($ldapkeys as $ldapkey) {
                        $ldapkey   = $ldapkey;
                        $ldapvalue = $user_entry[$ldapkey][0];
                        if (!$ambiguous) {
                            // skip update if the values already match
                            if ($nuvalue !== $ldapvalue) {
                                //this might fail due to schema validation
                                if (@ldap_modify($ldapconnection, $user_dn, array($ldapkey => $nuvalue))) {
                                    continue;
                                } else {
                                    error_log('Error updating LDAP record. Error code: '
                                      . ldap_errno($ldapconnection) . '; Error string : '
                                      . ldap_err2str(ldap_errno($ldapconnection))
                                      . "\nKey ($key) - old moodle value: '$ouvalue' new value: '$nuvalue'");
                                    continue;
                                }
                            }
                        } else {
                            // ambiguous
                            // value empty before in Moodle (and LDAP) - use 1st ldap candidate field
                            // no need to guess
                            if ($ouvalue === '') { // value empty before - use 1st ldap candidate
                                //this might fail due to schema validation
                                if (@ldap_modify($ldapconnection, $user_dn, array($ldapkey => $nuvalue))) {
                                    $changed = true;
                                    continue;
                                } else {
                                    error_log('Error updating LDAP record. Error code: '
                                      . ldap_errno($ldapconnection) . '; Error string : '
                                      . ldap_err2str(ldap_errno($ldapconnection))
                                      . "\nKey ($key) - old moodle value: '$ouvalue' new value: '$nuvalue'");
                                    continue;
                                }
                            }

                            // we found which ldap key to update!
                            if ($ouvalue !== '' and $ouvalue === $ldapvalue ) {
                                //this might fail due to schema validation
                                if (@ldap_modify($ldapconnection, $user_dn, array($ldapkey => $nuvalue))) {
                                    $changed = true;
                                    continue;
                                } else {
                                    error_log('Error updating LDAP record. Error code: '
                                      . ldap_errno($ldapconnection) . '; Error string : '
                                      . ldap_err2str(ldap_errno($ldapconnection))
                                      . "\nKey ($key) - old moodle value: '$ouvalue' new value: '$nuvalue'");
                                    continue;
                                }
                            }
                        }
                    }

                    if ($ambiguous and !$changed) {
                        error_log("Failed to update LDAP with ambiguous field $key".
                                  "  old moodle value: '" . $ouvalue .
                                  "' new value '" . $nuvalue );
                    }
                }
            }
        } else {
            error_log("ERROR:No user found in LDAP");
            @ldap_close($ldapconnection);
            return false;
        }

        @ldap_close($ldapconnection);

        return true;

    }

    /**
     * changes userpassword in external db
     *
     * called when the user password is updated.
     * changes userpassword in external db
     *
     * @param  object  $user        User table object  (with system magic quotes)
     * @param  string  $newpassword Plaintext password (with system magic quotes)
     * @return boolean result
     *
     */
    function user_update_password($user, $newpassword) {
    /// called when the user password is updated -- it assumes it is called by an admin
    /// or that you've otherwise checked the user's credentials
    /// IMPORTANT: $newpassword must be cleartext, not crypted/md5'ed

        global $USER;
        $result = false;
        $username = $user->username;

        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->ldapencoding);
        $extpassword = $textlib->convert(stripslashes($newpassword), 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();

        $user_dn = $this->ldap_find_userdn($ldapconnection, $extusername);

        if (!$user_dn) {
            error_log('LDAP Error in user_update_password(). No DN for: ' . stripslashes($user->username));
            return false;
        }

        switch ($this->config->user_type) {
            case 'edir':
                //Change password
                $result = ldap_modify($ldapconnection, $user_dn, array('userPassword' => $extpassword));
                if (!$result) {
                    error_log('LDAP Error in user_update_password(). Error code: '
                              . ldap_errno($ldapconnection) . '; Error string : '
                              . ldap_err2str(ldap_errno($ldapconnection)));
                }
                //Update password expiration time, grace logins count
                $search_attribs = array($this->config->expireattr, 'passwordExpirationInterval','loginGraceLimit' );
                $sr = ldap_read($ldapconnection, $user_dn, 'objectclass=*', $search_attribs);
                if ($sr)  {
                    $info=$this->ldap_get_entries($ldapconnection, $sr);
                    $newattrs = array();
                    if (!empty($info[0][$this->config->expireattr][0])) {
                        //Set expiration time only if passwordExpirationInterval is defined
                        if (!empty($info[0]['passwordExpirationInterval'][0])) {
                           $expirationtime = time() + $info[0]['passwordExpirationInterval'][0];
                           $ldapexpirationtime = $this->ldap_unix2expirationtime($expirationtime);
                           $newattrs['passwordExpirationTime'] = $ldapexpirationtime;
                        }

                        //set gracelogin count
                        if (!empty($info[0]['loginGraceLimit'][0])) {
                           $newattrs['loginGraceRemaining']= $info[0]['loginGraceLimit'][0];
                        }

                        //Store attribute changes to ldap
                        $result = ldap_modify($ldapconnection, $user_dn, $newattrs);
                        if (!$result) {
                           error_log('LDAP Error in user_update_password() when modifying expirationtime and/or gracelogins. Error code: '
                                     . ldap_errno($ldapconnection) . '; Error string : '
                                     . ldap_err2str(ldap_errno($ldapconnection)));
                        }
                    }
                }
                else {
                    error_log('LDAP Error in user_update_password() when reading password expiration time. Error code: '
                              . ldap_errno($ldapconnection) . '; Error string : '
                              . ldap_err2str(ldap_errno($ldapconnection)));
                }
                break;

            default:
                $usedconnection = &$ldapconnection;
                // send ldap the password in cleartext, it will md5 it itself
                $result = ldap_modify($ldapconnection, $user_dn, array('userPassword' => $extpassword));
                if (!$result) {
                    error_log('LDAP Error in user_update_password(). Error code: '
                        . ldap_errno($ldapconnection) . '; Error string : '
                        . ldap_err2str(ldap_errno($ldapconnection)));
                }

        }

        @ldap_close($ldapconnection);
        return $result;
    }

    //PRIVATE FUNCTIONS starts
    //private functions are named as ldap_*

    /**
     * returns predefined usertypes
     *
     * @return array of predefined usertypes
     */
    function ldap_suppported_usertypes() {
        $types = array();
        $types['edir']='Novell Edirectory';
        $types['rfc2307']='posixAccount (rfc2307)';
        $types['rfc2307bis']='posixAccount (rfc2307bis)';
        $types['samba']='sambaSamAccount (v.3.0.7)';
        $types['ad']='MS ActiveDirectory';
        $types['default']=get_string('default');
        return $types;
    }


    /**
     * Initializes needed variables for ldap-module
     *
     * Uses names defined in ldap_supported_usertypes.
     * $default is first defined as:
     * $default['pseudoname'] = array(
     *                      'typename1' => 'value',
     *                      'typename2' => 'value'
     *                      ....
     *                      );
     *
     * @return array of default values
     */
    function ldap_getdefaults() {
        $default['objectclass'] = array(
                            'edir' => 'User',
                            'rfc2307' => 'posixAccount',
                            'rfc2307bis' => 'posixAccount',
                            'samba' => 'sambaSamAccount',
                            'ad' => 'user',
                            'default' => '*'
                            );
        $default['user_attribute'] = array(
                            'edir' => 'cn',
                            'rfc2307' => 'uid',
                            'rfc2307bis' => 'uid',
                            'samba' => 'uid',
                            'ad' => 'cn',
                            'default' => 'cn'
                            );
        $default['memberattribute'] = array(
                            'edir' => 'member',
                            'rfc2307' => 'member',
                            'rfc2307bis' => 'member',
                            'samba' => 'member',
                            'ad' => 'member',
                            'default' => 'member'
                            );
        $default['memberattribute_isdn'] = array(
                            'edir' => '1',
                            'rfc2307' => '0',
                            'rfc2307bis' => '1',
                            'samba' => '0', //is this right?
                            'ad' => '1',
                            'default' => '0'
                            );
        $default['expireattr'] = array (
                            'edir' => 'passwordExpirationTime',
                            'rfc2307' => 'shadowExpire',
                            'rfc2307bis' => 'shadowExpire',
                            'samba' => '', //No support yet
                            'ad' => '', //No support yet
                            'default' => ''
                            );
        return $default;
    }

    /**
     * return binaryfields of selected usertype
     *
     *
     * @return array
     */
    function ldap_getbinaryfields () {
        $binaryfields = array (
                            'edir' => array('guid'),
                            'rfc2307' => array(),
                            'rfc2307bis' => array(),
                            'samba' => array(),
                            'ad' => array(),
                            'default' => array()
                            );
        if (!empty($this->config->user_type)) {
            return $binaryfields[$this->config->user_type];
        }
        else {
            return $binaryfields['default'];
        }
    }

    function ldap_isbinary ($field) {
        if (empty($field)) {
            return false;
        }
        return array_search($field, $this->ldap_getbinaryfields());
    }

    /**
     * take expirationtime and return it as unixseconds
     *
     * takes expriration timestamp as readed from ldap
     * returns it as unix seconds
     * depends on $this->config->user_type variable
     *
     * @param mixed time   Time stamp readed from ldap as it is.
     * @return timestamp
     */
    function ldap_expirationtime2unix ($time) {
        $result = false;
        switch ($this->config->user_type) {
            case 'edir':
                $yr=substr($time,0,4);
                $mo=substr($time,4,2);
                $dt=substr($time,6,2);
                $hr=substr($time,8,2);
                $min=substr($time,10,2);
                $sec=substr($time,12,2);
                $result = mktime($hr,$min,$sec,$mo,$dt,$yr);
                break;
            case 'posix':
                $result = $time * DAYSECS; //The shadowExpire contains the number of DAYS between 01/01/1970 and the actual expiration date
                break;
            default:
                print_error('auth_ldap_usertypeundefined', 'auth');
        }
        return $result;
    }

    /**
     * takes unixtime and return it formated for storing in ldap
     *
     * @param integer unix time stamp
     */
    function ldap_unix2expirationtime($time) {
        $result = false;
        switch ($this->config->user_type) {
            case 'edir':
                $result=date('YmdHis', $time).'Z';
                break;
            case 'posix':
                $result = $time ; //Already in correct format
                break;
            default:
                print_error('auth_ldap_usertypeundefined2', 'auth');
        }
        return $result;

    }

    /**
     * checks if user belong to specific group(s)
     *
     * Returns true if user belongs group in grupdns string.
     *
     * @param mixed $username    username
     * @param mixed $groupdns    string of group dn separated by ;
     *
     */
    function ldap_isgroupmember($extusername='', $groupdns='') {
    // Takes username and groupdn(s) , separated by ;
    // Returns true if user is member of any given groups

        $result = false;
        $ldapconnection = $this->ldap_connect();

        if (empty($username) or empty($groupdns)) {
            return $result;
            }

        if ($this->config->memberattribute_isdn) {
            $username=$this->ldap_find_userdn($ldapconnection, $username);
        }
        if (! $username ) {
            return $result;
        }

        $groups = explode(";",$groupdns);

        foreach ($groups as $group) {
            $group = trim($group);
            if (empty($group)) {
                continue;
            }
            //echo "Checking group $group for member $username\n";
            $search = @ldap_read($ldapconnection, $group,  '('.$this->config->memberattribute.'='.$this->filter_addslashes($username).')', array($this->config->memberattribute));

            if (!empty($search) and ldap_count_entries($ldapconnection, $search)) {$info = $this->ldap_get_entries($ldapconnection, $search);

                if (count($info) > 0 ) {
                    // user is member of group
                    $result = true;
                    break;
                }
        }
    }

        return $result;

    }

    /**
     * connects to ldap server
     *
     * Tries connect to specified ldap servers.
     * Returns connection result or error.
     *
     * @return connection result
     */
    function ldap_connect($binddn='',$bindpwd='') {
        //Select bind password, With empty values use
        //ldap_bind_* variables or anonymous bind if ldap_bind_* are empty
        if ($binddn == '' and $bindpwd == '') {
            if (!empty($this->config->bind_dn)) {
               $binddn = $this->config->bind_dn;
            }
            if (!empty($this->config->bind_pw)) {
               $bindpwd = $this->config->bind_pw;
            }
        }

        $urls = explode(";",$this->config->host_url);

        foreach ($urls as $server) {
            $server = trim($server);
            if (empty($server)) {
                continue;
            }

            $connresult = ldap_connect($server);
            //ldap_connect returns ALWAYS true

            if (!empty($this->config->version)) {
                ldap_set_option($connresult, LDAP_OPT_PROTOCOL_VERSION, $this->config->version);
            }

            if (!empty($binddn)) {
                //bind with search-user
                //$debuginfo .= 'Using bind user'.$binddn.'and password:'.$bindpwd;
                $bindresult=ldap_bind($connresult, $binddn,$bindpwd);
            }
            else {
                //bind anonymously
                $bindresult=@ldap_bind($connresult);
            }

            if (!empty($this->config->opt_deref)) {
                ldap_set_option($connresult, LDAP_OPT_DEREF, $this->config->opt_deref);
            }

            if ($bindresult) {
                return $connresult;
            }

            $debuginfo .= "<br/>Server: '$server' <br/> Connection: '$connresult'<br/> Bind result: '$bindresult'</br>";
        }

        //If any of servers are alive we have already returned connection
        print_error('auth_ldap_noconnect_all','auth',$this->config->user_type);
        return false;
    }

    /**
     * retuns dn of username
     *
     * Search specified contexts for username and return user dn
     * like: cn=username,ou=suborg,o=org
     *
     * @param mixed $ldapconnection  $ldapconnection result
     * @param mixed $username username (external encoding no slashes)
     *
     */

    function ldap_find_userdn ($ldapconnection, $extusername) {

        //default return value
        $ldap_user_dn = FALSE;

        //get all contexts and look for first matching user
        $ldap_contexts = explode(";",$this->config->contexts);

        if (!empty($this->config->create_context)) {
          array_push($ldap_contexts, $this->config->create_context);
        }

        foreach ($ldap_contexts as $context) {

            $context = trim($context);
            if (empty($context)) {
                continue;
            }

            if ($this->config->search_sub) {
                //use ldap_search to find first user from subtree
                $ldap_result = ldap_search($ldapconnection, $context, "(".$this->config->user_attribute."=".$this->filter_addslashes($extusername).")",array($this->config->user_attribute));

            }
            else {
                //search only in this context
                $ldap_result = ldap_list($ldapconnection, $context, "(".$this->config->user_attribute."=".$this->filter_addslashes($extusername).")",array($this->config->user_attribute));
            }

            $entry = ldap_first_entry($ldapconnection,$ldap_result);

            if ($entry) {
                $ldap_user_dn = ldap_get_dn($ldapconnection, $entry);
                break ;
            }
        }

        return $ldap_user_dn;
    }

    /**
     * retuns user attribute mappings between moodle and ldap
     *
     * @return array
     */

    function ldap_attributes () {
        $fields = array("firstname", "lastname", "email", "phone1", "phone2",
                        "department", "address", "city", "country", "description",
                        "idnumber", "lang" );
        $moodleattributes = array();
        foreach ($fields as $field) {
            if (!empty($this->config->{"field_map_$field"})) {
                $moodleattributes[$field] = $this->config->{"field_map_$field"};
                if (preg_match('/,/',$moodleattributes[$field])) {
                    $moodleattributes[$field] = explode(',', $moodleattributes[$field]); // split ?
                }
            }
        }
        $moodleattributes['username'] = $this->config->user_attribute;
        return $moodleattributes;
    }

    /**
     * return all usernames from ldap
     *
     * @return array
     */

    function ldap_get_userlist($filter="*") {
    /// returns all users from ldap servers
        $fresult = array();

        $ldapconnection = $this->ldap_connect();

        if ($filter=="*") {
           $filter = "(&(".$this->config->user_attribute."=*)(".$this->config->objectclass."))";
        }

        $contexts = explode(";",$this->config->contexts);

        if (!empty($this->config->create_context)) {
              array_push($contexts, $this->config->create_context);
        }

        foreach ($contexts as $context) {

            $context = trim($context);
            if (empty($context)) {
                continue;
            }

            if ($this->config->search_sub) {
                //use ldap_search to find first user from subtree
                $ldap_result = ldap_search($ldapconnection, $context,$filter,array($this->config->user_attribute));
            }
            else {
                //search only in this context
                $ldap_result = ldap_list($ldapconnection, $context,
                                         $filter,
                                         array($this->config->user_attribute));
            }

            $users = $this->ldap_get_entries($ldapconnection, $ldap_result);

            //add found users to list
            for ($i=0;$i<count($users);$i++) {
                array_push($fresult, ($users[$i][$this->config->user_attribute][0]) );
            }
        }

        return $fresult;
    }

    /**
     * return entries from ldap
     *
     * Returns values like ldap_get_entries but is
     * binary compatible and return all attributes as array
     *
     * @return array ldap-entries
     */

    function ldap_get_entries($conn, $searchresult) {
    //Returns values like ldap_get_entries but is
    //binary compatible
        $i=0;
        $fresult=array();
        $entry = ldap_first_entry($conn, $searchresult);
        do {
            $attributes = @ldap_get_attributes($conn, $entry);
            for ($j=0; $j<$attributes['count']; $j++) {
                $values = ldap_get_values_len($conn, $entry,$attributes[$j]);
                if (is_array($values)) {
                $fresult[$i][$attributes[$j]] = $values;
                }
                else {
                    $fresult[$i][$attributes[$j]] = array($values);
                }
            }
            $i++;
        }
        while ($entry = @ldap_next_entry($conn, $entry));
        //were done
        return ($fresult);
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return false;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return !empty($this->config->stdchangepassword) or !empty($this->config->changepasswordurl);
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return string url
     */
    function change_password_url() {
        if (empty($this->config->stdchangepassword)) {
            return $this->config->changepasswordurl;
        } else {
            return '';
        }
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include 'config.html';
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset($config->host_url))
            { $config->host_url = ''; }
        if (empty($config->ldapencoding))
            { $config->ldapencoding = 'utf-8'; }
        if (!isset($config->contexts))
            { $config->contexts = ''; }
        if (!isset($config->user_type))
            { $config->user_type = 'default'; }
        if (!isset($config->user_attribute))
            { $config->user_attribute = ''; }
        if (!isset($config->search_sub))
            { $config->search_sub = ''; }
        if (!isset($config->opt_deref))
            { $config->opt_deref = ''; }
        if (!isset($config->preventpassindb))
            { $config->preventpassindb = 0; }
        if (!isset($config->bind_dn))
            {$config->bind_dn = ''; }
        if (!isset($config->bind_pw))
            {$config->bind_pw = ''; }
        if (!isset($config->version))
            {$config->version = '2'; }
        if (!isset($config->objectclass))
            {$config->objectclass = ''; }
        if (!isset($config->memberattribute))
            {$config->memberattribute = ''; }
        if (!isset($config->creators))
            {$config->creators = ''; }
        if (!isset($config->create_context))
            {$config->create_context = ''; }
        if (!isset($config->expiration))
            {$config->expiration = ''; }
        if (!isset($config->expiration_warning))
            {$config->expiration_warning = '10'; }
        if (!isset($config->expireattr))
            {$config->expireattr = ''; }
        if (!isset($config->gracelogins))
            {$config->gracelogins = ''; }
        if (!isset($config->graceattr))
            {$config->graceattr = ''; }
        if (!isset($config->auth_user_create))
            {$config->auth_user_create = ''; }
        if (!isset($config->forcechangepassword))
            {$config->forcechangepassword = 0; }
        if (!isset($config->stdchangepassword))
            {$config->stdchangepassword = 0; }
        if (!isset($config->changepasswordurl))
            {$config->changepasswordurl = ''; }
        if (!isset($config->removeuser))
            {$config->removeuser = 0; }

        // save settings
        set_config('host_url', $config->host_url, 'auth/ldap');
        set_config('ldapencoding', $config->ldapencoding, 'auth/ldap');
        set_config('host_url', $config->host_url, 'auth/ldap');
        set_config('contexts', $config->contexts, 'auth/ldap');
        set_config('user_type', $config->user_type, 'auth/ldap');
        set_config('user_attribute', $config->user_attribute, 'auth/ldap');
        set_config('search_sub', $config->search_sub, 'auth/ldap');
        set_config('opt_deref', $config->opt_deref, 'auth/ldap');
        set_config('preventpassindb', $config->preventpassindb, 'auth/ldap');
        set_config('bind_dn', $config->bind_dn, 'auth/ldap');
        set_config('bind_pw', $config->bind_pw, 'auth/ldap');
        set_config('version', $config->version, 'auth/ldap');
        set_config('objectclass', $config->objectclass, 'auth/ldap');
        set_config('memberattribute', $config->memberattribute, 'auth/ldap');
        set_config('creators', $config->creators, 'auth/ldap');
        set_config('create_context', $config->create_context, 'auth/ldap');
        set_config('expiration', $config->expiration, 'auth/ldap');
        set_config('expiration_warning', $config->expiration_warning, 'auth/ldap');
        set_config('expireattr', $config->expireattr, 'auth/ldap');
        set_config('gracelogins', $config->gracelogins, 'auth/ldap');
        set_config('graceattr', $config->graceattr, 'auth/ldap');
        set_config('auth_user_create', $config->auth_user_create, 'auth/ldap');
        set_config('forcechangepassword', $config->forcechangepassword, 'auth/ldap');
        set_config('stdchangepassword', $config->stdchangepassword, 'auth/ldap');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/ldap');
        set_config('removeuser', $config->removeuser, 'auth/ldap');

        return true;
    }

    /**
     * Quote control characters in texts used in ldap filters - see rfc2254.txt
     *
     * @param string
     */
    function filter_addslashes($text) {
        $text = str_replace('\\', '\\5c', $text);
        $text = str_replace(array('*',    '(',    ')',    "\0"),
                            array('\\2a', '\\28', '\\29', '\\00'), $text);
        return $text;
    }

    /**
     * Quote control characters in quoted "texts" used in ldap
     *
     * @param string
     */
    function ldap_addslashes($text) {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(array('"',   "\0"),
                            array('\\"', '\\00'), $text);
        return $text;
    }
}

?>
