<?PHP  
/**
 *
 * @author Petri Asikainen
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodleauth
 
 * LDAPA-authentication functions
 *
 * 30.09.2004 Removed outdated documentation
 * 24.09.2004 Lot of changes:
 *           -Added usertype configuration, this removes need for separate obejcclass and attributename configuration
 *            Overriding values is still supported
 *          
 * 21.09.2004 Added support for multiple ldap-servers.
 *          Theres no nedd to use auth_ldap_bind,
 *          Anymore auth_ldap_connect does this for you
 * 19.09.2004 Lot of changes are coming from Martin Langhoff
 *          Current code is working but can change a lot. Be warned...
 * 15.08.2004 Added support for user syncronization
 * 24.02.2003 Added support for coursecreators
 * 20.02.2003 Added support for user creation
 * 12.10.2002 Reformatted source for consistency
 * 03.10.2002 First version to CVS
 * 29.09.2002 Clean up and splitted code to functions v. 0.02
 * 29.09.2002 LDAP authentication functions v. 0.01
 */

/**
 * authenticates user againt external userdatabase
 *
 * Returns true if the username and password work
 * and false if they don't
 *
 * @param string  $username
 * @param string  $password
 *
*/

// LDAP functions are reused by other auth libs
if (!defined('AUTH_LDAP_NAME')) {
    define('AUTH_LDAP_NAME', 'ldap');
}

function auth_user_login ($username, $password) {

    global $CFG;

    if (!$username or !$password) {    // Don't allow blank usernames or passwords
        return false;
    }
 
    // CAS-supplied auth tokens override LDAP auth
    if ($CFG->auth == "cas" && !empty($CFG->cas_enabled)) {
        return cas_ldap_auth_user_login($username, $password);
    }

    $ldapconnection = auth_ldap_connect();

    if ($ldapconnection) {
        $ldap_user_dn = auth_ldap_find_userdn($ldapconnection, $username);
      
        //if ldap_user_dn is empty, user does not exist
        if(!$ldap_user_dn){
            ldap_close($ldapconnection);
            return false;
        }

        // Try to bind with current username and password
        $ldap_login = @ldap_bind($ldapconnection, $ldap_user_dn, stripslashes($password));
        ldap_close($ldapconnection);
        if ($ldap_login) {
            return true;
        }
    } else {
        @ldap_close($ldapconnection);
        error("LDAP-module cannot connect to server: $CFG->ldap_host_url");
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
 * @param string $username username
 * @return array
 */
function auth_get_userinfo($username){
    global $CFG;
    $ldapconnection=auth_ldap_connect();
    $config = (array)$CFG;
    $attrmap = auth_ldap_attributes();
   
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

    $user_dn = auth_ldap_find_userdn($ldapconnection, $username);

    if (empty($CFG->ldap_objectclass)) {        // Can't send empty filter
        $CFG->ldap_objectclass="objectClass=*";
    }
  
    $user_info_result = ldap_read($ldapconnection,$user_dn,$CFG->ldap_objectclass, $search_attribs);

    if ($user_info_result) {
        $user_entry = ldap_get_entries($ldapconnection, $user_info_result);
        foreach ($attrmap as $key=>$values){
            if (!is_array($values)) {
                $values = array($values);
            }
            $ldapval = NULL;
            foreach ($values as $value) {
                if(is_array($user_entry[0][strtolower($value)])) {
                    if (!empty($CFG->unicodedb)) {
                        $newval = addslashes(stripslashes($user_entry[0][strtolower($value)][0]));
                    } else {
                        $newval = addslashes(stripslashes(utf8_decode($user_entry[0][strtolower($value)][0])));
                    }
                }
                else {
                    if (!empty($CFG->unicodedb)) {
                        $newval = addslashes(stripslashes($user_entry[0][strtolower($value)]));
                    } else {
                        $newval = addslashes(stripslashes(utf8_decode($user_entry[0][strtolower($value)])));
                    }
                }
                if (!empty($newval)) { // favour ldap entries that are set
                    $ldapval = $newval;
                } 
            }
            if (!is_null($ldapval)) {
                $result[$key] = $ldapval;
            }
        }
    }

    @ldap_close($ldapconnection);
    
    return $result;
}

/**
 * reads userinformation from ldap and return it in an object
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

/**
 * returns all usernames from external database
 *
 * auth_get_userlist returns all usernames from external database
 *
 * @return array 
 */
function auth_get_userlist () {
    global $CFG;
    auth_ldap_init();
    return auth_ldap_get_userlist("($CFG->ldap_user_attribute=*)");
}
/**
 * checks if user exists on external db
 */
function auth_user_exists ($username) {
   global $CFG; 
   auth_ldap_init();
   //returns true if given usernname exist on ldap
   $users = auth_ldap_get_userlist("($CFG->ldap_user_attribute=$username)");
   return count($users); 
}

/**
 * creates new user on external database
 *
 * auth_user_create() creates new user on external database
 * By using information in userobject
 * Use auth_user_exists to prevent dublicate usernames
 *
 * @param mixed $userobject  Moodle userobject
 * @param mixed $plainpass   Plaintext password
 */
function auth_user_create ($userobject,$plainpass) {
    global $CFG;
    $ldapconnection = auth_ldap_connect();
    $attrmap = auth_ldap_attributes();
    
    $newuser = array();
     
    foreach ($attrmap as $key=>$values){
        if (!is_array($values)) {
            $values = array($values);
        }
        foreach ($values as $value) {
            if(!empty($userobject->$key) ){
                if (!empty($CFG->unicodedb)) {
                    $newuser[$value]= $userobject->$key;
                } else {
                    $newuser[$value]=utf8_encode($userobject->$key);
                }
            }
        }
    }
    
    //Following sets all mandatory and other forced attribute values
    //User should be creted as login disabled untill email confirmation is processed
    //Feel free to add your user type and send patches to paca@sci.fi to add them 
    //Moodle distribution

    switch ($CFG->ldap_user_type)  {
        case 'edir':
            $newuser['objectClass']= array("inetOrgPerson","organizationalPerson","person","top");
            $newuser['uniqueId']= $userobject->username;
            $newuser['logindisabled']="TRUE";
            $newuser['userpassword']=$plainpass;
            break;
        default:
           error('auth: ldap auth_user_create() does not support selected usertype:"'.$CFG->ldap_user_type.'" (..yet)');
    }
    $uadd = ldap_add($ldapconnection, $CFG->ldap_user_attribute."=$userobject->username,".$CFG->ldap_create_context, $newuser);

    ldap_close($ldapconnection);
    return $uadd;
    
}

/*/
 * 
 * auth_get_users() returns userobjects from external database
 *
 * Function returns users from external databe as Moodle userobjects
 * If filter is not present it should return ALL users in external database
 * 
 * @param mixed $filter substring of username
 * @returns array of userobjects 
 */
function auth_get_users($filter='*', $dontlistcreated=false) {
    global $CFG;

    $ldapconnection = auth_ldap_connect();
    $fresult = array();

    if ($filter=="*") {
       $filter = "(&(".$CFG->ldap_user_attribute."=*)(".$CFG->ldap_objectclass."))";
    }

    $contexts = explode(";",$CFG->ldap_contexts);
 
    if (!empty($CFG->ldap_create_context) and empty($dontlistcreated)){
          array_push($contexts, $CFG->ldap_create_context);
    }

    $attrmap = auth_ldap_attributes();
   
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


    foreach ($contexts as $context) {
        
        $context = trim($context);
        if (empty($context)) {
            continue;
        }

        if ($CFG->ldap_search_sub) {
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldapconnection, $context,
                                       $filter,
                                       $search_attribs);
        } else {
            //search only in this context
            $ldap_result = ldap_list($ldapconnection, $context,
                                     $filter,
                                     $search_attribs);
        }

        $users = auth_ldap_get_entries($ldapconnection, $ldap_result);

        //add found users to list
        foreach ($users as $ldapuser=>$attribs) {
            $user = new object();
            foreach ($attrmap as $key=>$value){
                if(isset($users[$ldapuser][$value][0])){
                    $user->$key=$users[$ldapuser][$value][0];
                }
            }    
            //quick way to get around binarystrings
            $user->guid=bin2hex($user->guid);
            //add authentication source stamp 
            $user->auth = AUTH_LDAP_NAME;
            $fresult[$user->username]=$user;

        }
    }
   
    return $fresult;
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
function auth_password_expire($username) {
    global $CFG ;
    $result = false;
    
    $ldapconnection = auth_ldap_connect();
    $user_dn = auth_ldap_find_userdn($ldapconnection, $username);
    $search_attribs = array($CFG->ldap_expireattr);
    $sr = ldap_read($ldapconnection, $user_dn, 'objectclass=*', $search_attribs);
    if ($sr)  {
        $info=auth_ldap_get_entries($ldapconnection, $sr);
        if ( empty($info[0][strtolower($CFG->ldap_expireattr)][0])) {
            //error_log("ldap: no expiration value".$info[0][$CFG->ldap_expireattr]);
            // no expiration attribute, password does not expire
            $result = 0;
        } else {
            $now = time();
            $expiretime = auth_ldap_expirationtime2unix($info[0][strtolower($CFG->ldap_expireattr)][0]);
            if ($expiretime > $now) {
                $result = ceil(($expiretime - $now) / DAYSECS);
            } else {
                $result = floor(($expiretime - $now) / DAYSECS);
            }    
        }
    } else {    
        error_log("ldap: auth_password_expire did't find expiration time!.");
    }    

    //error_log("ldap: auth_password_expire user $user_dn expires in $result days!");
    return $result;
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
 * @param mixed $firstsync  Optional: set to true to fill idnumber fields if not filled yet
 */
function auth_sync_users ($bulk_insert_records = 1000, $do_updates=1) {
//Syncronizes userdb with ldap
//This will add, rename 
/// OPTIONAL PARAMETERS
/// $bulk_insert_records = 1 // will insert $bulkinsert_records per insert statement
///                         valid only with $unsafe. increase to a couple thousand for
///                         blinding fast inserts -- but test it: you may hit mysqld's 
///                         max_allowed_packet limit.
/// $do_updates = 1 // will do pull in data updates from ldap if relevant


    global $CFG ;
    $pcfg = get_config('auth/ldap');

    // configure a temp table 
    print "Configuring temp table\n";    
    if(strtolower($CFG->dbtype) === 'mysql'){
        // help old mysql versions cope with large temp tables
        execute_sql('SET SQL_BIG_TABLES=1', false); 
        execute_sql('CREATE TEMPORARY TABLE ' . $CFG->prefix .'extuser (idnumber VARCHAR(64), PRIMARY KEY (idnumber)) TYPE=MyISAM',false); 
    } elseif (strtolower($CFG->dbtype) === 'postgres7'){
        $bulk_insert_records = 1; // no support for multiple sets of values
        execute_sql('CREATE TEMPORARY TABLE '.$CFG->prefix.'extuser (idnumber VARCHAR(64), PRIMARY KEY (idnumber))',false); 
    }

        
    print "connecting to ldap\n";
    $ldapconnection = auth_ldap_connect();
    if (!$ldapconnection) {
        @ldap_close($ldapconnection);
        notify("LDAP-module cannot connect to server: $CFG->ldap_host_url");
        return false;
    }

    ////
    //// get user's list from ldap to sql in a scalable fashion
    ////
    // prepare some data we'll need
    if (! empty($CFG->ldap_objectclass)) {
        $CFG->ldap_objectclass="objectClass=*";
    }

    $filter = "(&(".$CFG->ldap_user_attribute."=*)(".$CFG->ldap_objectclass."))";

    $contexts = explode(";",$CFG->ldap_contexts);
 
    if (!empty($CFG->ldap_create_context)){
          array_push($contexts, $CFG->ldap_create_context);
    }

    $fresult = array();
    $count = 0;
    foreach ($contexts as $context) {
        $context = trim($context);
        if (empty($context)) {
            continue;
        }
        begin_sql();
        if ($CFG->ldap_search_sub) {
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldapconnection, $context,
                                       $filter,
                                       array($CFG->ldap_user_attribute));
        } else {
            //search only in this context
            $ldap_result = ldap_list($ldapconnection, $context,
                                     $filter,
                                     array($CFG->ldap_user_attribute));
        }

        if ($entry = ldap_first_entry($ldapconnection, $ldap_result)) {
            do {
                $value = ldap_get_values_len($ldapconnection, $entry,$CFG->ldap_user_attribute);
                $value = $value[0];
                $count++;
                array_push($fresult, $value);
                if(count($fresult) >= $bulk_insert_records){
                    auth_ldap_bulk_insert($fresult);
                    //print var_dump($fresult);
                    $fresult=array();
                }         
            }
            while ($entry = ldap_next_entry($ldapconnection, $entry));
        }
        
        // insert any remaining users and release mem
        if(count($fresult)){
            auth_ldap_bulk_insert($fresult);
            $fresult=array();
        }
        commit_sql();
    }
    // free mem
    $ldap_results = 0;

    /// preserve our user database
    /// if the temp table is empty, it probably means that something went wrong, exit
    /// so as to avoid mass deletion of users; which is hard to undo
    $count = get_record_sql('SELECT COUNT(idnumber) AS count, 1 FROM ' . $CFG->prefix .'extuser');
    $count = $count->{'count'};
    if($count < 1){
        print "Did not get any users from LDAP -- error? -- exiting\n";
        exit;
    }

    ////
    //// User removal
    ////
    // find users in DB that aren't in ldap -- to be removed!
    // this is still not as scalable
    $sql = 'SELECT u.id, u.username 
            FROM ' . $CFG->prefix .'user u LEFT JOIN ' . $CFG->prefix .'extuser e 
                    ON u.idnumber = e.idnumber 
            WHERE u.auth=\'' . AUTH_LDAP_NAME . '\' AND u.deleted=\'0\' AND e.idnumber IS NULL';
    //print($sql);            
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
            //$updateuser->username = "$user->username".time();  // Remember it just in case
            //$updateuser->email = "";               // Clear this field to free it up
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
    $remove_users = 0; // free mem!   

    ////
    //// User Updates
    //// (time-consuming, optional)
    ////
    if ($do_updates) {
        // narrow down what fields we need to update
        $all_keys = array_keys(get_object_vars($pcfg));
        $updatekeys = array();
        foreach ($all_keys as $key) {
            if (preg_match('/^field_updatelocal_(.+)$/',$key, $match)) {
                // if we have a field to update it from
                // and it must be updated 'onlogin' we 
                // update it on cron
                if ( !empty($pcfg->{'field_map_'.$match[1]})
                     && $pcfg->{$match[0]} === 'onlogin') { 
                    array_push($updatekeys, $match[1]); // the actual key name
                }
            }
        }
        // print_r($all_keys); print_r($updatekeys);
        unset($all_keys); unset($key);
        
    }
    if ( $do_updates && !(empty($updatekeys)) ) { // run updates only if relevant
        $users = get_records_sql('SELECT u.username, u.id FROM ' . $CFG->prefix . 'user  u WHERE u.deleted=0 and u.auth=\'' . AUTH_LDAP_NAME . '\'' );
        if (!empty($users)) {
            print "User entries to update: ". count($users). "\n";
            
            $sitecontext = get_context_instance(CONTEXT_SYSTEM);

            if ($creatorroles = get_roles_with_capability('moodle/legacy:coursecreator', CAP_ALLOW)) {

                $creatorrole = array_shift($creatorroles);      // We can only use one, let's use the first one

                begin_sql();
                $xcount=0; $maxxcount=100;
                foreach ($users as $user) { 
                    echo "updating user $user->username \n";
                    auth_ldap_update_user_record($user->username, $updatekeys);

                    // update course creators
                    if (!empty($CFG->ldap_creators) && !empty($CFG->ldap_memberattribute) ) {
                        if (auth_iscreator($user->username)) {   // Following calls will not create duplicates
                            role_assign($creatorrole->id, $user->id, 0, $sitecontext->id, 0, 0, 0, 'ldap');
                            $xcount++;
                        } else {
                            role_unassign($creatorrole->id, $user->id, 0, $sitecontext->id);
                            $xcount++;
                        }
                    }

                    if ($xcount++ > $maxxcount) {
                        commit_sql();
                        begin_sql(); 
                        $xcount=0;
                    }
                }
                commit_sql();
                unset($users); // free mem
            }
        }
    } // end do updates
    
    ////
    //// User Additions
    ////
    // find users missing in DB that are in LDAP
    // note that get_records_sql wants at least 2 fields returned,
    // and gives me a nifty object I don't want.
    $sql = 'SELECT e.idnumber,1 
            FROM ' . $CFG->prefix .'extuser e  LEFT JOIN ' . $CFG->prefix .'user u
                    ON e.idnumber = u.idnumber 
            WHERE  u.id IS NULL OR (u.id IS NOT NULL AND u.deleted=1)';
    $add_users = get_records_sql($sql); // get rid of the fat        
    
    if(!empty($add_users)){
        print "User entries to add: ". count($add_users). "\n";

        if ($creatorroles = get_roles_with_capability('moodle/legacy:coursecreator', CAP_ALLOW)) {
            $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
        }

        begin_sql();
        foreach($add_users as $user){
            $user = auth_get_userinfo_asobj($user->idnumber);
            //print $user->username . "\n";
            
            // prep a few params
            $user->modified  = time();
            $user->confirmed = 1;
            $user->auth      = AUTH_LDAP_NAME;
            
            // insert it
            $old_debug=$CFG->debug; 
            $CFG->debug=10;
            
            // maybe the user has been deleted before
            if ($old_user = get_record('user', 'idnumber', $user->idnumber, 'deleted', 1)) {
                $user->id = $old_user->id;
                set_field('user', 'deleted', 0, 'idnumber', $user->idnumber);
                echo "Revived user $user->username with idnumber $user->idnumber id $user->id\n";
            } elseif ($id=insert_record ('user',$user)) { // it is truly a new user
                echo "inserted user $user->username with idnumber $user->idnumber id $id\n";
                $user->id = $id;
            } else {
                echo "error inserting user $user->username with idnumber $user->idnumber \n";
            }
            $CFG->debug=$old_debug;
            $userobj = auth_ldap_update_user_record($user->username);
            if(isset($CFG->{'auth_ldap_forcechangepassword'}) && $CFG->{'auth_ldap_forcechangepassword'}){
                set_user_preference('auth_forcepasswordchange', 1, $userobj->id);
            }
            
            if (isset($creatorrole->id) &&  !empty($CFG->ldap_creators) && !empty($CFG->ldap_memberattribute) ) {
                if (auth_iscreator($user->username)) {
                    if (user_has_role_assignment($user->id, $creatorrole->id, $sitecontext->id)) {
                        role_unassign($creatorrole->id, $user->id, 0, $sitecontext->id);
                    } else {
                        role_assign($creatorrole->id, $user->id, 0, $sitecontext->id, 0, 0, 0, 'ldap');
                    }
                }
            }
        }
        commit_sql();
        unset($add_users); // free mem
    }
    return true;
}

function auth_ldap_update_user_record($username, $updatekeys=false) {
/// will update a local user record from an external source. 
/// is a lighter version of the one in moodlelib -- won't do 
/// expensive ops such as enrolment
///
/// If you don't pass $updatekeys, there is a performance hit and 
/// values removed from LDAP won't be removed from moodle. 

    global $CFG;

    $pcfg = get_config('auth/ldap');

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

function auth_ldap_bulk_insert($users){
// bulk insert in SQL's temp table
// $users is an array of usernames
    global $CFG;
    
    // bulk insert -- superfast with $bulk_insert_records
    $sql = 'INSERT INTO '.$CFG->prefix.'extuser (idnumber) VALUES ';
    // make those values safe
    $users = array_map('addslashes', $users);
    // join and quote the whole lot
    $sql = $sql . '(\'' . join('\'),(\'', $users) . '\')';
    print "+ " . count($users) . " users\n";
    execute_sql($sql, false); 

}


/*
 * auth_user_activate activates user in external db.
 *
 * Activates (enables) user in external db so user can login to external db
 *
 * @param mixed $username    username
 * @return boolen result
 */
function auth_user_activate ($username) {
    
    global $CFG;
    
    $ldapconnection = auth_ldap_connect();

    $userdn = auth_ldap_find_userdn($ldapconnection, $username);
    switch ($CFG->ldap_user_type)  {
        case 'edir':
            $newinfo['loginDisabled']="FALSE";
            break;
        default:
            error ('auth: ldap auth_user_activate() does not support selected usertype:"'.$CFG->ldap_user_type.'" (..yet)');    
    } 
    $result = ldap_modify($ldapconnection, $userdn, $newinfo);
    ldap_close($ldapconnection);
    return $result;
}

/*
 * auth_user_disables disables user in external db.
 *
 * Disables user in external db so user can't login to external db
 *
 * @param mixed $username    username
 * @return boolean result
 */
function auth_user_disable ($username) {
    global $CFG;

    $ldapconnection = auth_ldap_connect();

    $userdn = auth_ldap_find_userdn($ldapconnection, $username);
    switch ($CFG->ldap_user_type)  {
        case 'edir':
            $newinfo['loginDisabled']="TRUE";
            break;
        default:
            error ('auth: ldap auth_user_disable() does not support selected usertype (..yet)');    
    }    
    $result = ldap_modify($ldapconnection, $userdn, $newinfo);
    ldap_close($ldapconnection);
    return $result;
}

/*
 * auth_iscreator returns true if user should be coursecreator
 *
 * auth_iscreator returns true if user should be coursecreator
 *
 * @param mixed $username    username
 * @return boolean result
 */
function auth_iscreator($username=0) {
///if user is member of creator group return true
    global $USER , $CFG; 
    auth_ldap_init();

    if (! $username) {
        $username=$USER->username;
    }
   
    if ((! $CFG->ldap_creators) OR (! $CFG->ldap_memberattribute)) {
        return null;
    } 

    return auth_ldap_isgroupmember($username, $CFG->ldap_creators);
 
}

/* 
 * auth_user_update saves userinformation from moodle to external db
 *
 * Called when the user record is updated.
 * Modifies user in external database. It takes olduser (before changes) and newuser (after changes) 
 * conpares information saved modified information to external db.
 *
 * @param mixed $olduser     Userobject before modifications
 * @param mixed $newuser     Userobject new modified userobject
 * @return boolean result
 *
 */
function auth_user_update($olduser, $newuser) {

    global $USER , $CFG;

    $pcfg = get_config('auth/ldap');
    
    $ldapconnection = auth_ldap_connect();
    
    $result = array();
    $search_attribs = array();

    $attrmap = auth_ldap_attributes();  
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

    $user_dn = auth_ldap_find_userdn($ldapconnection, $olduser->username);

    $user_info_result = ldap_read($ldapconnection,$user_dn,$CFG->ldap_objectclass, $search_attribs);

    if ($user_info_result){

        $user_entry = auth_ldap_get_entries($ldapconnection, $user_info_result);
        if (count($user_entry) > 1) {
            trigger_error("ldap: Strange! More than one user record found in ldap. Only using the first one.");
        }
        $user_entry = $user_entry[0];

        //error_log(var_export($user_entry) . 'fpp' );
        
        foreach ($attrmap as $key=>$ldapkeys){

            // only process if the moodle field ($key) has changed and we
            // are set to update LDAP with it
            if ($olduser->$key !== $newuser->$key &&
                !empty($pcfg->{'field_updateremote_'. $key})) {

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
                 
                foreach ($ldapkeys as $ldapkey) {
                    $ldapkey   = strtolower($ldapkey);
                    $ldapvalue = $user_entry[$ldapkey][0];
                    if (!$ambiguous) {
                        // skip update if the values already match
                        if( !($newuser->$key === $ldapvalue) ){
                            ldap_modify($ldapconnection, $user_dn, array($ldapkey => $newuser->$key));
                        } else { 
                    error_log("Skip updating field $key for entry $user_dn: it seems to be already same on LDAP. " . 
                                      "  old moodle value: '" . $olduser->$key . 
                                      "' new value '" . $newuser->$key . 
                                      "' current value in ldap entry " . $ldapvalue);
                        }
                    } else { // ambiguous
                        // value empty before in Moodle (and LDAP) - use 1st ldap candidate field
                        // no need to guess
                        if (empty($olduser->$key)) { // value empty before - use 1st ldap candidate
                            if(ldap_modify($ldapconnection, $user_dn, array($ldapkey => $newuser->$key))){
                                $changed=true;
                                last;
                            } else {
                                error ('Error updating LDAP record. Error code: ' 
                                  . ldap_errno($ldapconnection) . '; Error string : '
                                  . ldap_err2str(ldap_errno($ldapconnection)));                                
                            }
                        }

                        // we found which ldap key to update!                            
                        if ( !empty($ldapvalue) && $olduser->$key === $ldapvalue ) {
                            // error_log("Matched: ". $olduser->$key . " === " . $ldapvalue);
                            if(ldap_modify($ldapconnection, $user_dn, array($ldapkey => $newuser->$key))){
                                $changed=true;
                                last;
                            } else {
                                error ('Error updating LDAP record. Error code: ' 
                                  . ldap_errno($ldapconnection) . '; Error string : '
                                  . ldap_err2str(ldap_errno($ldapconnection))); 
                            }
                        }
                    }
                }
                
                if ($ambiguous AND !$changed) {
                    error_log("Failed to update LDAP with ambiguous field $key". 
                              "  old moodle value: '" . $olduser->$key . 
                              "' new value '" . $newuser->$key );
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

/*
 * changes userpassword in external db
 *
 * called when the user password is updated.
 * changes userpassword in external db
 *
 * @param mixed $username    Username
 * @param mixed $newpassword Plaintext password
 * @param mixed $oldpassword Plaintext old password to bind ldap with
 * @return boolean result
 *
 */
function auth_user_update_password($username, $newpassword) {
/// called when the user password is updated -- it assumes it is called by an admin
/// or that you've otherwise checked the user's credentials
/// IMPORTANT: $newpassword must be cleartext, not crypted/md5'ed

    global $CFG, $USER;
    $result = false;
     
    $ldapconnection = auth_ldap_connect();

    $user_dn = auth_ldap_find_userdn($ldapconnection, $username);
    
    if(!$user_dn){
        error_log('LDAP Error in auth_user_update_password(). No DN for: ' . $username); 
        return false;
    }

    switch ($CFG->ldap_user_type) {
        case 'edir':
            //Change password
            $result = ldap_modify($ldapconnection, $user_dn, array('userPassword' => $newpassword));
            if(!$result){
                error_log('LDAP Error in auth_user_update_password(). Error code: '
                          . ldap_errno($ldapconnection) . '; Error string : '
                          . ldap_err2str(ldap_errno($ldapconnection)));
            }
            //Update password expiration time, grace logins count
            $search_attribs = array($CFG->ldap_expireattr, 'passwordExpirationInterval','loginGraceLimit' );
            $sr = ldap_read($ldapconnection, $user_dn, 'objectclass=*', $search_attribs);
            if ($sr)  {
                $info=auth_ldap_get_entries($ldapconnection, $sr);
                $newattrs = array();
                if (!empty($info[0][$CFG->ldap_expireattr][0])) {
                    //Set expiration time only if passwordExpirationInterval is defined
                    if (!empty($info[0]['passwordExpirationInterval'][0])) {
                       $expirationtime = time() + $info[0]['passwordExpirationInterval'][0]; 
                       $ldapexpirationtime = auth_ldap_unix2expirationtime($expirationtime);
                       $newattrs['passwordExpirationTime'] = $ldapexpirationtime;
                    }    

                    //set gracelogin count
                    if (!empty($info[0]['loginGraceLimit'][0])) {
                       $newattrs['loginGraceRemaining']= $info[0]['loginGraceLimit'][0]; 
                    }
    
                    //Store attribute changes to ldap
                    $result = ldap_modify($ldapconnection, $user_dn, $newattrs);
                    if(!$result){
                       error_log('LDAP Error in auth_user_update_password() when modifying expirationtime and/or gracelogins. Error code: '
                                 . ldap_errno($ldapconnection) . '; Error string : '
                                 . ldap_err2str(ldap_errno($ldapconnection)));
                    }
                }
            } else {
                error_log('LDAP Error in auth_user_update_password() when reading password expiration time. Error code: '
                          . ldap_errno($ldapconnection) . '; Error string : '
                          . ldap_err2str(ldap_errno($ldapconnection)));
            }    
            break;

        case 'ad':
            // Passwords in Active Directory must be encoded as Unicode
            // strings (UCS-2 Little Endian format) and surrounded with
            // double quotes. See http://support.microsoft.com/?kbid=269190
            if (!function_exists('mb_convert_encoding')) {
                error_log ('You need the mbstring extension to change passwords in Active Directory');
                return false;
            }
            $newpassword = mb_convert_encoding('"'.$newpassword.'"', "UCS-2LE", "auto");
            $result = ldap_modify($ldapconnection, $user_dn, array('unicodePwd' => $newpassword));
            if(!$result){
                error_log('LDAP Error in auth_user_update_password(). Error code: ' 
                          . ldap_errno($ldapconnection) . '; Error string : '
                          . ldap_err2str(ldap_errno($ldapconnection)));
            }
            break;

        default:
            $usedconnection = &$ldapconnection;
            // send ldap the password in cleartext, it will md5 it itself
            $result = ldap_modify($ldapconnection, $user_dn, array('userPassword' => $newpassword));
            if(!$result){
                error_log('LDAP Error in auth_user_update_password(). Error code: ' 
                          . ldap_errno($ldapconnection) . '; Error string : '
                          . ldap_err2str(ldap_errno($ldapconnection)));
            }
    
    }

        
    @ldap_close($ldapconnection);

    return $result;
}

//PRIVATE FUNCTIONS starts
//private functions are named as auth_ldap*

/**
 * returns predefined usertypes
 *
 * @return array of predefined usertypes
 */

function auth_ldap_suppported_usertypes (){
// returns array of supported usertypes (schemas)
// If you like to add our own please name and describe it here
// And then add case clauses in relevant places in functions
// iauth_ldap_init, auth_user_create, auth_check_expire, auth_check_grace
    $types['edir']='Novell Edirectory';
    $types['rfc2307']='posixAccount (rfc2307)';
    $types['rfc2307bis']='posixAccount (rfc2307bis)';
    $types['samba']='sambaSamAccount (v.3.0.7)';
    $types['ad']='MS ActiveDirectory'; 
    return $types;
}    

   
/**
 * initializes needed variables for ldap-module
 *
 * Uses names defined in auth_ldap_supported_usertypes.
 * $default is first defined as:
 * $default['pseudoname'] = array(
 *                      'typename1' => 'value',
 *                      'typename2' => 'value'
 *                      ....
 *                      );
 *
 * @return array of default values
 */

function auth_ldap_getdefaults(){
    $default['ldap_objectclass'] = array(
                        'edir' => 'User',
                        'rfc2703' => 'posixAccount',
                        'rfc2703bis' => 'posixAccount',
                        'samba' => 'sambaSamAccount',
                        'ad' => 'user',
                        'default' => '*'
                        );
    $default['ldap_user_attribute'] = array(
                        'edir' => 'cn',
                        'rfc2307' => 'uid',
                        'rfc2307bis' => 'uid',
                        'samba' => 'uid',
                        'ad' => 'cn',
                        'default' => 'cn'
                        );
    $default['ldap_memberattribute'] = array(
                        'edir' => 'member',
                        'rfc2307' => 'member',
                        'rfc2307bis' => 'member',
                        'samba' => 'member',
                        'ad' => 'member', 
                        'default' => 'member'
                        );
    $default['ldap_memberattribute_isdn'] = array(
                        'edir' => '1',
                        'rfc2307' => '0',
                        'rfc2307bis' => '1',
                        'samba' => '0', //is this right?
                        'ad' => '1',
                        'default' => '0'
                        );
    $default['ldap_expireattr'] = array (
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

function auth_ldap_getbinaryfields () {
    global $CFG;
    $binaryfields = array (
                        'edir' => array('guid'),
                        'rfc2703' => array(),
                        'rfc2703bis' => array(),
                        'samba' => array(),
                        'ad' => array(),
                        'default' => '*'
                        );
    if (!empty($CFG->ldap_user_type)) {
        return $binaryfields[$CFG->ldap_user_type];   
    } else {
        return $binaryfields['default'];
    }    
    }
    
function auth_ldap_isbinary ($field) {
    if (!isset($field)) {
        return null ;
    }    
    return array_search($field, auth_ldap_getbinaryfields());
}    

/**
 * set $CFG-values for ldap_module
 * 
 * Get default configuration values with auth_ldap_getdefaults() 
 * and by using this information $CFG-> values are set
 * If $CFG->value is alredy set current value is honored.
 *
 * 
 */
function auth_ldap_init () {
    global $CFG;
 
    $default = auth_ldap_getdefaults();

    foreach ($default as $key => $value) {
        //set defaults if overriding fields not set
        if(empty($CFG->{$key})) {
            if (!empty($CFG->ldap_user_type) && !empty($default[$key][$CFG->ldap_user_type])) {
                $CFG->{$key} = $default[$key][$CFG->ldap_user_type];
            }else {
                //use default value if user_type not set
                if(!empty($default[$key]['default'])){
                    $CFG->$key = $default[$key]['default'];
                }else {
                    unset($CFG->$key);
                }    
            }
        }
    }   
    //hack prefix to objectclass
    if ('objectClass=' != substr($CFG->ldap_objectclass, 0, 12)) {
       $CFG->ldap_objectclass = 'objectClass='.$CFG->ldap_objectclass;
    }
   
    //all chages go in $CFG , no need to return value
}

/**
 * take expirationtime and return it as unixseconds
 * 
 * takes expriration timestamp as readed from ldap
 * returns it as unix seconds
 * depends on $CFG->usertype variable
 *
 * @param mixed time   Time stamp readed from ldap as it is.
 * @return timestamp
 */

function auth_ldap_expirationtime2unix ($time) {

    global $CFG;
    $result = false;
    switch ($CFG->ldap_user_type) {
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
            $result = $time * DAYSECS ; //The shadowExpire contains the number of DAYS between 01/01/1970 and the actual expiration date
            break;
        default:  
            error('CFG->ldap_user_type not defined or function auth_ldap_expirationtime2unix does not support selected type!');
}
    return $result;
}

/**
 * takes unixtime and return it formated for storing in ldap
 *
 * @param integer unix time stamp
 */
function auth_ldap_unix2expirationtime ($time) {
    global $CFG;
    $result = false;
    switch ($CFG->ldap_user_type) {
        case 'edir':
            $result=date('YmdHis', $time).'Z';  
            break;
        case 'posix':
            $result = $time ; //Already in correct format
            break;
        default:  
            error('CFG->ldap_user_type not defined or function auth_ldap_unixi2expirationtime does not support selected type!');
    }        
    return $result;

}

/*
 * checks if user belong to specific group(s)
 *
 * Returns true if user belongs group in grupdns string.
 *
 * @param mixed $username    username
 * @param mixed $groupdns    string of group dn separated by ;
 *
 */
function auth_ldap_isgroupmember ($username='', $groupdns='') {
// Takes username and groupdn(s) , separated by ;
// Returns true if user is member of any given groups

    global $CFG ;
    $result = false;
    $ldapconnection = auth_ldap_connect();
    
    if (empty($username) OR empty($groupdns)) {
        return $result;
        }

    if ($CFG->ldap_memberattribute_isdn) {
        $username=auth_ldap_find_userdn($ldapconnection, $username);
    }
    if (! $username ) {
        return $result;
    }

    $groups = explode(";",$groupdns);
    
    foreach ($groups as $group){
        $group = trim($group);
        if (empty($group)) {
            continue;
        }
        //echo "Checking group $group for member $username\n";
        $search = @ldap_read($ldapconnection, $group,  '('.$CFG->ldap_memberattribute.'='.$username.')', array($CFG->ldap_memberattribute));

        if (!empty($search) AND ldap_count_entries($ldapconnection, $search)) {$info = auth_ldap_get_entries($ldapconnection, $search);
        
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
function auth_ldap_connect($binddn='',$bindpwd=''){
/// connects  and binds to ldap-server
/// Returns connection result

    global $CFG;
    auth_ldap_init();

    //Select bind password, With empty values use
    //ldap_bind_* variables or anonymous bind if ldap_bind_* are empty
    if ($binddn == '' AND $bindpwd == '') {
        if (!empty($CFG->ldap_bind_dn)){
           $binddn = $CFG->ldap_bind_dn;
        }
        if (!empty($CFG->ldap_bind_pw)){
           $bindpwd = $CFG->ldap_bind_pw;
        }
    }
    
    $urls = explode(";",$CFG->ldap_host_url);
        
    foreach ($urls as $server){
        $server = trim($server);
        if (empty($server)) {
            continue;
        }

        $connresult = ldap_connect($server);
        //ldap_connect returns ALWAYS true
 
        if (!empty($CFG->ldap_version)) {
            ldap_set_option($connresult, LDAP_OPT_PROTOCOL_VERSION, $CFG->ldap_version);
        }

        if (!empty($binddn)){
            //bind with search-user
            //$debuginfo .= 'Using bind user'.$binddn.'and password:'.$bindpwd; 
            $bindresult=ldap_bind($connresult, $binddn,$bindpwd);
        } else {
            //bind anonymously 
            $bindresult=@ldap_bind($connresult);
        }  
       
        if (!empty($CFG->ldap_opt_deref)) {
            ldap_set_option($connresult, LDAP_OPT_DEREF, $CFG->ldap_opt_deref);
        }

        if ($bindresult) {
            return $connresult;
        }
        
        $debuginfo .= "<br/>Server: '$server' <br/> Connection: '$connresult'<br/> Bind result: '$bindresult'</br>";
    }

    //If any of servers are alive we have already returned connection
    error("LDAP-module cannot connect any LDAP servers : $debuginfo");
    return false;
}

/**
 * retuns dn of username
 *
 * Search specified contexts for username and return user dn
 * like: cn=username,ou=suborg,o=org
 *
 * @param mixed $ldapconnection  $ldapconnection result
 * @param mixed $username username
 *
 */

function auth_ldap_find_userdn ($ldapconnection, $username){

    global $CFG;

    //default return value
    $ldap_user_dn = FALSE;

    //get all contexts and look for first matching user
    $ldap_contexts = explode(";",$CFG->ldap_contexts);
    
    if (!empty($CFG->ldap_create_context)){
      array_push($ldap_contexts, $CFG->ldap_create_context);
    }
  
    foreach ($ldap_contexts as $context) {

        $context = trim($context);
        if (empty($context)) {
            continue;
        }

        if ($CFG->ldap_search_sub){
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldapconnection, $context, "(".$CFG->ldap_user_attribute."=".$username.")",array($CFG->ldap_user_attribute));

        } else {
            //search only in this context
            $ldap_result = ldap_list($ldapconnection, $context, "(".$CFG->ldap_user_attribute."=".$username.")",array($CFG->ldap_user_attribute));
        }
 
        $entry = ldap_first_entry($ldapconnection,$ldap_result);

        if ($entry){
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

function auth_ldap_attributes (){

    global $CFG;

    $config = (array)$CFG;
    $fields = array("firstname", "lastname", "email", "phone1", "phone2", 
                    "department", "address", "city", "country", "description", 
                    "idnumber", "lang" );

    $pcfg = get_config('auth/ldap');

    $moodleattributes = array();
    foreach ($fields as $field) {
        if (!empty($pcfg->{"field_map_$field"})) {
            $moodleattributes[$field] = $pcfg->{"field_map_$field"};
            if (preg_match('/,/',$moodleattributes[$field])) {
                $moodleattributes[$field] = explode(',', $moodleattributes[$field]); // split ?
            }
        }
    }
    $moodleattributes['username']=$config["ldap_user_attribute"];
    return $moodleattributes;
}

/**
 * return all usernames from ldap
 *
 * @return array
 */

function auth_ldap_get_userlist($filter="*") {
/// returns all users from ldap servers
    global $CFG;

    $fresult = array();

    $ldapconnection = auth_ldap_connect();

    if ($filter=="*") {
       $filter = "(&(".$CFG->ldap_user_attribute."=*)(".$CFG->ldap_objectclass."))";
    }

    $contexts = explode(";",$CFG->ldap_contexts);
 
    if (!empty($CFG->ldap_create_context)){
          array_push($contexts, $CFG->ldap_create_context);
    }

    foreach ($contexts as $context) {

        $context = trim($context);
        if (empty($context)) {
            continue;
        }

        if ($CFG->ldap_search_sub) {
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldapconnection, $context,$filter,array($CFG->ldap_user_attribute));
        } else {
            //search only in this context
            $ldap_result = ldap_list($ldapconnection, $context,
                                     $filter,
                                     array($CFG->ldap_user_attribute));
        }
        
        $users = auth_ldap_get_entries($ldapconnection, $ldap_result);

        //add found users to list
        for ($i=0;$i<count($users);$i++) {
            array_push($fresult, ($users[$i][$CFG->ldap_user_attribute][0]) );
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
   
function auth_ldap_get_entries($conn, $searchresult){
//Returns values like ldap_get_entries but is
//binary compatible
    $i=0;
    $fresult=array();
    $entry = ldap_first_entry($conn, $searchresult);
    do {
        $attributes = @ldap_get_attributes($conn, $entry);
        for($j=0; $j<$attributes['count']; $j++) {
            $values = ldap_get_values_len($conn, $entry,$attributes[$j]);
            if (is_array($values)) {
            $fresult[$i][$attributes[$j]] = $values;
            } else {
                $fresult[$i][$attributes[$j]] = array($values);
            }
        }         
        $i++;               
    }
    while ($entry = @ldap_next_entry($conn, $entry));
    //were done
    return ($fresult);
}




?>
