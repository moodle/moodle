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
function auth_user_login ($username, $password) {

    global $CFG;

    if (!$username or !$password) {    // Don't allow blank usernames or passwords
        return false;
    }
 
    $ldapconnection  = auth_ldap_connect();

    if ($ldapconnection) {
        $ldap_user_dn = auth_ldap_find_userdn($ldapconnection, $username);
      
        //if ldap_user_dn is empty, user does not exist
        if(!$ldap_user_dn){
            ldap_close($ldapconnection);
            return false;
        }

        // Try to bind with current username and password
        $ldap_login = @ldap_bind($ldapconnection, $ldap_user_dn, $password);
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
  
    foreach ($attrmap as $key=>$value) {
        if (!in_array($value, $search_attribs)) {
            array_push($search_attribs, $value);
        }    
    }

    $user_dn = auth_ldap_find_userdn($ldapconnection, $username);

    $user_info_result = ldap_read($ldapconnection,$user_dn,$CFG->ldap_objectclass, $search_attribs);

    if ($user_info_result) {
        $user_entry = auth_ldap_get_entries($ldapconnection, $user_info_result);
        foreach ($attrmap as $key=>$value){
            if(isset($user_entry[0][strtolower($value)][0])){
                $result[$key]=utf8_decode($user_entry[0][strtolower($value)][0]);
            }
        }
    }

    @ldap_close($ldapconnection);
    
    return $result;
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
     
    foreach ($attrmap as $key=>$value){
            if(!empty($userobject->$key) ){
                if (isset($CFG->{auth_user_.$key._updateremote}) && $CFG->{auth_user_.$key._updateremote} == "1" ) { 
                    $newuser[$value]=utf8_encode($userobject->$key);
                }     
            }
    }
    
    //Following sets all mandatory and other forced attribute values
    //MODIFY following to suite your enviroment
    $newuser['objectClass']= array("inetOrgPerson","organizationalPerson","person","top");
    $newuser['uniqueId']= $userobject->username;
    $newuser['logindisabled']="TRUE";
    $newuser['userpassword']=$plainpass;
        
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
function auth_get_users($filter='*') {
    global $CFG;

    $ldapconnection = auth_ldap_connect();
    $fresult = array();

    if ($filter=="*") {
       $filter = "(&(".$CFG->ldap_user_attribute."=*)(".$CFG->ldap_objectclass."))";
    }

    $contexts = explode(";",$CFG->ldap_contexts);
 
    if (!empty($CFG->ldap_create_context)){
          array_push($contexts, $CFG->ldap_create_context);
    }

    $attrmap = auth_ldap_attributes();
   
    $search_attribs = array();
  
    foreach ($attrmap as $key=>$value) {
        if (!in_array($value, $search_attribs)) {
            array_push($search_attribs, $value);
        }    
    }


    foreach ($contexts as $context) {

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
            $user->auth='ldap';
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
function auth_sync_users ($firstsync=0, $unsafe_optimizations = false, $bulk_insert_records = 1) {
//Syncronizes userdb with ldap
//This will add, rename 
/// OPTIONAL PARAMETERS
/// $unsafe_optimizations = true  // will skip over moodle standard DB interfaces and use very optimized
////             and non-portable SQL -- useful only for mysql or postgres7
/// $bulk_insert_records = 1 // will insert $bulkinsert_records per insert statement
///                         valid only with $unsafe. increase to a couple thousand for
///                         blinding fast inserts -- but test it: you may hit mysqld's 
///                         max_allowed_packet limit.

    global $CFG ;
    auth_ldap_init();
    $ldapusers     = auth_get_users();
    $usedidnumbers = Array();

    // these are only populated if we managed to find added/removed users
    $add_users    = false;
    $remove_users = false;
    
    if($unsafe_optimizations){
        // create a temp table
        if(strtolower($CFG->dbtype) === 'mysql'){
            // help old mysql versions cope with large temp tables
            execute_sql('SET SQL_BIG_TABLES=1'); 
            execute_sql('CREATE TEMPORARY TABLE ' . $CFG->prefix .'extuser (idnumber VARCHAR(12), PRIMARY KEY (idnumber)) TYPE=MyISAM'); 
        } elseif (strtolower($CFG->dbtype) === 'postgres7'){
            execute_sql('CREATE TEMPORARY TABLE '.$CFG->prefix.'extuser (idnumber VARCHAR(12), PRIMARY KEY (idnumber))'); 
        }
        
        $userids = array_keys($ldapusers);
        // bulk insert -- superfast with $bulk_insert_records
        while(count($userids)){
            $sql = 'INSERT INTO '.$CFG->prefix.'extuser (idnumber) VALUES ';
            $values = array_splice($userids, -($bulk_insert_records) ); 
            // make those values safe
            array_map('addslashes', $values);
            // join and quote the whole lot
            $sql = $sql . '(\'' . join('\'),(\'', $values) . '\')';
            execute_sql($sql); 
        }
        
        /// REMOVE execute_sql('delete from mdl_user where idnumber like \'%s\'');
        
        // find users in DB that aren't in ldap -- to be removed!
        $sql = 'SELECT u.* 
                FROM ' . $CFG->prefix .'user u LEFT JOIN ' . $CFG->prefix .'extuser e 
                        ON u.idnumber = e.idnumber 
                WHERE u.auth=\'ldap\' AND u.deleted=\'0\' AND e.idnumber IS NULL';
        $remove_users = get_records_sql($sql); 
        print "User entries to remove: ". count($remove_users) . "\n";
        
        // find users missing in DB that are in LDAP
        // note that get_records_sql wants at least 2 fields returned,
        // and gives me a nifty object I don't want.
        $sql = 'SELECT e.idnumber,1 
                FROM ' . $CFG->prefix .'extuser e  LEFT JOIN ' . $CFG->prefix .'user u
                        ON e.idnumber = u.idnumber 
                WHERE  u.id IS NULL';
        $add_users = array_keys(get_records_sql($sql)) || array(); // get rid of the fat        
        print "User entries to add: ". count($add_users). "\n";
    }
    
    foreach ($ldapusers as $user) {
    
        $usedidnumbers[] = $user->idnumber; //we will need all used idnumbers later
        //update modified time
        $user->modified = time();
        //All users are confirmed
        $user->confirmed = 1;
        // if user does not exist create it
        if ( ($unsafe_optimizations && is_array($add_users) && in_array($user->idnumber, $add_users) )
              || (!$unsafe_optimizations  &&!record_exists('user','auth', 'ldap', 'idnumber', $user->idnumber)) ) {
            if (insert_record ('user',$user)) {
                echo "inserted user $user->username with idnumber $user->idnumber \n";
            } else {
                echo "error inserting user $user->username with idnumber $user->idnumber \n";
            }
            update_user_record($user->username);
            continue ;
        } else {
           //update username
           set_field('user', 'username', $user->username , 'auth', 'ldap', 'idnumber', $user->idnumber);
           //no id-information in ldap so get now
           update_user_record($user->username);
           $userid = get_field('user', 'id', 'auth', 'ldap', 'idnumber', $user->idnumber);
           
           if (auth_iscreator($user->username)) {
                 if (! record_exists("user_coursecreators", "userid", $userid)) {
                      $cdata['userid']=$userid;
                      $creator = insert_record("user_coursecreators",$cdata);
                      if (! $creator) {
                          error("Cannot add user to course creators.");
                      }
                  }
            } else {
                 if ( record_exists("user_coursecreators", "userid", $userid)) {
                      $creator = delete_records("user_coursecreators", "userid", $userid);
                      if (! $creator) {
                          error("Cannot remove user from course creators.");
                      }
                 }
            }
        }
    }    
    
    if($unsafe_optimizations){
        $result=(is_array($remove_users) ? $remove_users : array());
    } else{
        //find nonexisting users from moodles userdb
        $sql = "SELECT * FROM ".$CFG->prefix."user WHERE deleted = '0' AND auth = 'ldap' AND idnumber  NOT IN ('".implode('\' , \'',$usedidnumbers)."');" ;
        $result = get_records_sql($sql);
    }

    if (!empty($result)){
        foreach ($result as $user) {
            //following is copy pasted from admin/user.php
            //maybe this should moved to function in lib/datalib.php
            unset($updateuser);
            $updateuser->id = $user->id;
            $updateuser->deleted = "1";
            $updateuser->username = "$user->email.".time();  // Remember it just in case
            $updateuser->email = "";               // Clear this field to free it up
            $updateuser->timemodified = time();
            if (update_record("user", $updateuser)) {
                unenrol_student($user->id);  // From all courses
                remove_teacher($user->id);   // From all courses
                remove_admin($user->id);
                notify(get_string("deletedactivity", "", fullname($user, true)) );
            } else {
                notify(get_string("deletednot", "", fullname($user, true)));
            }
            //copy pasted part ends
        }     
    }    
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
    
    $newinfo['loginDisabled']="FALSE";

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
    $newinfo['loginDisabled']="TRUE";

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
        return false;
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
    
    $ldapconnection = auth_ldap_connect();
    
    $result = array();
    $search_attribs = array();

    $attrmap = auth_ldap_attributes();  
    foreach ($attrmap as $key=>$value) {
        if (!in_array($value, $search_attribs)) {
            array_push($search_attribs, $value);
        }    
    }

    $user_dn = auth_ldap_find_userdn($ldapconnection, $olduser->username);

    $user_info_result = ldap_read($ldapconnection,$user_dn,$CFG->ldap_objectclass, $search_attribs);

    if ($user_info_result){

        $user_entry = auth_ldap_get_entries($ldapconnection, $user_info_result);
        //error_log(var_export($user_entry) . 'fpp' );

        foreach ($attrmap as $key=>$ldapkey){
            if (isset($CFG->{'auth_user_'. $key.'_updateremote'}) && $CFG->{'auth_user_'. $key.'_updateremote'}){
                // skip update if the values already match
                if( !($newuser->$key === $user_entry[0][strtolower($ldapkey)][0]) ){
                    ldap_modify($ldapconnection, $user_dn, array($ldapkey => utf8_encode($newuser->$key)));
                } else { 
                    error_log("Skip updating field $key for entry $user_dn: it seems to be already same on LDAP. " . 
                              "  old moodle value: '" . $olduser->$key . 
                              "' new value '" . $newuser->$key . 
                              "' current value in ldap entry " . $user_entry[0][strtolower($ldapkey)][0]);
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
 * @return boolean result
 *
 */
function auth_user_update_password($username, $newpassword) {
/// called when the user password is updated -- it assumes it is called by an admin
/// or that you've otherwise checked the user's credentials
/// IMPORTANT: $newpassword must be cleartext, not crypted/md5'ed

    global $CFG;
    $result = false;
     
    $ldapconnection = auth_ldap_connect();

    $user_dn = auth_ldap_find_userdn($ldapconnection, $username);
    
    if(!$user_dn){
        error_log('LDAP Error in auth_user_update_password(). No DN for: ' . $username); 
        return false;
    }
    // send ldap the password in cleartext, it will md5 it itself
    $result = ldap_modify($ldapconnection, $user_dn, array('userPassword' => $newpassword));
    
    if(!$result){
        error_log('LDAP Error in auth_user_update_password(). Error code: ' 
                  . ldap_errno($ldapconnection) . '; Error string : '
                  . ldap_err2str(ldap_errno($ldapconnection)));
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
                        'ad' => 'member', //is this right?
                        'default' => 'member'
                        );
    $default['ldap_memberattribute_isdn'] = array(
                        'edir' => '1',
                        'rfs2307' => '0',
                        'rfs2307bis' => '1',
                        'samba' => '0', //is this right?
                        'ad' => '0', //is this right?
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
 * takes expriration timestamp readed from ldap
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
            $result = mktime($hr,$min,$sec,$mo,dt,$yr); 
            break;
        case 'posix':
            $result = $time * DAYSECS ; //The shadowExpire contains the number of DAYS between 01/01/1970 and the actual expiration date
            break;
        default:  
            error('CFG->ldap_user_type not defined or function auth_ldap_expirationtime2unix does not support selected type!');
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

    $groups = explode(";",$groupdns);

    foreach ($groups as $group){
        $search = @ldap_read($ldapconnection, $group,  '('.$CFG->ldap_memberattribute.'='.$username.')', array($CFG->ldap_memberattribute));
        if ($search) {$info = auth_ldap_get_entries($ldapconnection, $search);
        
            if ($info['count'] > 0 ) {
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
function auth_ldap_connect(){
/// connects  and binds to ldap-server
/// Returns connection result

    global $CFG;
    auth_ldap_init();
    $urls = explode(";",$CFG->ldap_host_url);

    foreach ($urls as $server){
        $connresult = ldap_connect($server);
        //ldap_connect returns ALWAYS true

        if (!empty($CFG->ldap_version)) {
            ldap_set_option($connresult, LDAP_OPT_PROTOCOL_VERSION, $CFG->ldap_version);
        }

        if ($CFG->ldap_bind_dn){
            //bind with search-user
            $bindresult=@ldap_bind($connresult, $CFG->ldap_bind_dn,$CFG->ldap_bind_pw);
        } else {
            //bind anonymously
            $bindresult=@ldap_bind($connresult);
        }    

        if ($bindresult) {
            return $connresult;
        }
    }    
    
    //If any of servers are alive we have already returned connection
    error("LDAP-module cannot connect any LDAP servers : $CFG->ldap_host_url");
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

        $context == trim($context);

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

    $moodleattributes = array();
    foreach ($fields as $field) {
        if (!empty($config["auth_user_$field"])) {
            $moodleattributes[$field] = $config["auth_user_$field"];
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

        $users = auth_ldap_get_entries($ldapconnection, $ldap_result);

        //add found users to list
        for ($i=0;$i<$users['count'];$i++) {
            array_push($fresult, ($users[$i][$CFG->ldap_user_attribute][0]) );
        }
    }
   
    return $fresult;
}

/**
 * return entries from ldap
 *
 * Returns values like ldap_get_entries but is
 * binary compatible
 *
 * @return array ldap-entries
 */
   
function auth_ldap_get_entries($conn, $searchresult){
//Returns values like ldap_get_entries but is
//binary compatible
    $i=0;
    $fresult=array();
    $entry = ldap_first_entry($conn, $searchresult);
    $count=0;
    do {
        $attributes = ldap_get_attributes($conn, $entry);
        for($j=0; $j<$attributes['count']; $j++) {
            $values = ldap_get_values_len($conn, $entry,$attributes[$j]);
            $fresult[$i][$attributes[$j]] = $values;
        }         
        $i++;               
        $count++;
    }
    while ($entry = ldap_next_entry($conn, $entry));
    //were done
    $fresult['count']=$count;
    return ($fresult);
}




?>
