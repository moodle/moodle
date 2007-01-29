<?php  // $Id$
       // Authentication by looking up an external database table


function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

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
    $authdb = &ADONewConnection($CFG->auth_dbtype); 
    $authdb->PConnect($CFG->auth_dbhost,$CFG->auth_dbuser,$CFG->auth_dbpass,$CFG->auth_dbname);
    $authdb->SetFetchMode(ADODB_FETCH_ASSOC); ///Set Assoc mode always after DB connection

    if ($CFG->auth_dbpasstype === 'internal') { 
        // lookup username externally, but resolve
        // password locally -- to support backend that
        // don't track passwords
        $rs = $authdb->Execute("SELECT * FROM $CFG->auth_dbtable 
                                 WHERE $CFG->auth_dbfielduser = '$username' ");
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

        if ($CFG->auth_dbpasstype === 'md5') {   // Re-format password accordingly
            $password = md5($password);
        }

        $rs = $authdb->Execute("SELECT * FROM $CFG->auth_dbtable 
                            WHERE $CFG->auth_dbfielduser = '$username' 
                              AND $CFG->auth_dbfieldpass = '$password' ");
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


function auth_get_userinfo($username){
// Reads any other information for a user from external database,
// then returns it in an array

    global $CFG;

    $config = (array) $CFG;

    $pcfg = get_config('auth/db');
    $pcfg = (array) $pcfg;

    ADOLoadCode($CFG->auth_dbtype);          
    $authdb = &ADONewConnection();         
    $authdb->PConnect($CFG->auth_dbhost,$CFG->auth_dbuser,$CFG->auth_dbpass,$CFG->auth_dbname); 
    $authdb->SetFetchMode(ADODB_FETCH_ASSOC); ///Set Assoc mode always after DB connection

    $fields = array("firstname", "lastname", "email", "phone1", "phone2", 
                    "department", "address", "city", "country", "description", 
                    "idnumber", "lang");

    $result = array();

    foreach ($fields as $field) {
        if ($pcfg["field_map_$field"]) {
            if ($rs = $authdb->Execute("SELECT ".$pcfg["field_map_$field"]." as myfield FROM $CFG->auth_dbtable
                                        WHERE $CFG->auth_dbfielduser = '$username'")) {
                if ( $rs->RecordCount() == 1 ) {
                    $fields_obj = rs_fetch_record($rs);
                    if (!empty($CFG->unicodedb)) {
                        $result["$field"] = addslashes(stripslashes($fields_obj->myfield));
                    } else {
                        $result["$field"] = addslashes(stripslashes(utf8_decode($fields_obj->myfield)));
                    }
                }
                rs_close($rs);
            }
        }
    }

    return $result;
}


function auth_user_update_password($username, $newpassword) {
    global $CFG;
    if ($CFG->auth_dbpasstype === 'internal') {
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
function auth_sync_users ($do_updates=0) {
    
    global $CFG;
    $pcfg = get_config('auth/db');

    ///
    /// list external users
    ///
    $userlist = auth_get_userlist();
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

    if (!empty($remove_users)){
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
        $all_keys = array_keys(get_object_vars($pcfg));
        $updatekeys = array();
        foreach ($all_keys as $key) {
            if (preg_match('/^field_updatelocal_(.+)$/',$key, $match)) {
                if ($pcfg->{$key} === 'onlogin') {
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
                auth_db_update_user_record($user->username, $updatekeys);
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

    if(!empty($add_users)){
        print "User entries to add: ". count($add_users). "\n";
        begin_sql();
        foreach($add_users as $user){
            $username = $user;
            $user = auth_get_userinfo_asobj($user);
            
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
                if ($CFG->auth_dbpasstype === 'internal') {
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

function auth_user_exists ($username) {
    global $CFG;
    $authdb = &ADONewConnection($CFG->auth_dbtype); 
    $authdb->PConnect($CFG->auth_dbhost,$CFG->auth_dbuser,$CFG->auth_dbpass,$CFG->auth_dbname); 
    $authdb->SetFetchMode(ADODB_FETCH_ASSOC); ///Set Assoc mode always after DB connection

    $rs = $authdb->Execute("SELECT * FROM $CFG->auth_dbtable 
                                 WHERE $CFG->auth_dbfielduser = '$username' ");
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


function auth_get_userlist() {

    global $CFG;

    // Connect to the external database
    $authdb = &ADONewConnection($CFG->auth_dbtype); 
    $authdb->PConnect($CFG->auth_dbhost,$CFG->auth_dbuser,$CFG->auth_dbpass,$CFG->auth_dbname); 
    $authdb->SetFetchMode(ADODB_FETCH_ASSOC); ///Set Assoc mode always after DB connection

    // fetch userlist
    $rs = $authdb->Execute("SELECT $CFG->auth_dbfielduser AS username
                            FROM   $CFG->auth_dbtable ");
    $authdb->Close();

    if (!$rs) {
        notify("Could not connect to the specified authentication database...");
        return false;
    }
    
    if ( $rs->RecordCount() ) {
        $userlist = array();
        while ($rec = rs_fetch_next_record($rs)) {
            array_push($userlist, $rec->username);
        }
        rs_close($rs);
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
function auth_get_userinfo_asobj($username){
    $user_array = truncate_userinfo(auth_get_userinfo($username));
    $user = new object;
    foreach($user_array as $key=>$value){
        $user->{$key} = $value;
    }
    return $user;
}

function auth_db_update_user_record($username, $updatekeys=false) {
/// will update a local user record from an external source. 
/// is a lighter version of the one in moodlelib -- won't do 
/// expensive ops such as enrolment
///
/// If you don't pass $updatekeys, there is a performance hit and 
/// values removed from DB won't be removed from moodle. 

    global $CFG;

    $pcfg = get_config('auth/db');

    //just in case check text case
    $username = trim(moodle_strtolower($username));
    
    // get the current user record
    $user = get_record('user', 'username', $username);
    if (empty($user)) { // trouble
        error_log("Cannot update non-existent user: $username");
        die;
    }

    if (function_exists('auth_get_userinfo')) {
        if ($newinfo = auth_get_userinfo($username)) {
            $newinfo = truncate_userinfo($newinfo);
            
            if (empty($updatekeys)) { // all keys? this does not support removing values
                $updatekeys = array_keys($newinfo);
            }
            
            foreach ($updatekeys as $key){
                unset($value);
                if (isset($newinfo[$key])) {
                    $value = $newinfo[$key];
                    $value = addslashes(stripslashes($value)); // Just in case
                } else {
                    $value = '';
                }
                if (!empty($pcfg->{'field_updatelocal_' . $key})) { 
                       if ($user->{$key} != $value) { // only update if it's changed
                           set_field('user', $key, $value, 'username', $username);
                       }
                }
            }
        }
    }
    return get_record_select("user", "username = '$username' AND deleted <> '1'");
}

// A chance to validate form data, and last chance to 
// do stuff before it is inserted in config_plugin
function auth_validate_form(&$form, &$err) {
    
    // compat until we rework auth a bit
    if ($form['auth_dbpasstype'] === 'internal') {
        $CFG->auth_db_stdchangepassword = true;
        if ($conf = get_record('config', 'name', 'auth_db_stdchangepassword')) {
            $conf->value = 1;
            if (! update_record('config', $conf)) {
                notify("Could not update $name to $value");
            }
        } else {
            $conf = new StdClass;
            $conf->name = 'auth_db_stdchangepassword';
            $conf->value = 1;
            if (! insert_record('config', $conf)) {
                notify("Error: could not add new variable $name !");
            }
        }
    } else {
        $CFG->auth_db_stdchangepassword = false;
        if ($conf = get_record('config', 'name', 'auth_db_stdchangepassword')) {
            $conf->value = 0;
            if (! update_record('config', $conf)) {
                notify("Could not update $name to $value");
            }
        } else {
            $conf = new StdClass;
            $conf->name = 'auth_db_stdchangepassword';
            $conf->value = 0;
            if (! insert_record('config', $conf)) {
                notify("Error: could not add new variable $name !");
            }
        }
    }
    return true;
}

?>
