<?PHP  // $Id$
//CHANGELOG:
//15.08.2004 Added support for user syncronization
//24.02.2003 Added support for coursecreators
//20.02.2003 Added support for user creation
//12.10.2002 Reformatted source for consistency
//03.10.2002 First version to CVS
//29.09.2002 Clean up and splitted code to functions v. 0.02
//29.09.2002 LDAP authentication functions v. 0.01
//Distributed under GPL (c)Petri Asikainen 2002-2004

/* README!
Module is quite complete and  most functinality can be configured from
configinterfave /admin/auth.php. Some of latest additions/features need to
be configured by modifying source code.
 
USER CREATION FEATURE
User-creation makes posible that your current 
users can authenticate with existings usernames/password and new users can 
create own accounts to LDAP-directory. I'm using this feature and new users
are created to LDAP different context, without rights to other system. When
user-creation feature is set like that, there's no known security issues.

If you plan to use user creation feature, look function auth_user_create
and modify it for your needs.
You have to change all hardcoded attribute values to fit your LDAP-server.

I write ldap-module on Novell E-directory / Linux & Solaris , 
so all default values are for it.

LDAP USER SYNCRONIZATION

BACKUP
This is first version of usersync so backup your database, if you like to test this feature!

BINARY FIELDS
I'm testing this against Novell eDirectory where guid field is binary
so I have to use bin2hex() in function auth_get_users (), If your guid field is not binary
comment that line out.

EXISTING USERS
For existing systems there no way to figure out is account from ldap or not.
So sysadmin,  you have to update 'auth' and 'guid' fields for your existing ldap-users by hand (or scripting)
If your users usernamed are stabile, you can use auth_get_users() for this.

AUTOMATING SYNCRONIZATION
Right now moodle does not automaticly run auth_sync_users() so you have to create
your own script like:
auth/ldap/cron.php
<?
    require_once("../../config.php");
    require_once("../../course/lib.php");
    require_once('../../lib/blocklib.php');
    require_once("../../mod/resource/lib.php");
    require_once("lib.php");
    require_once("../../mod/forum/lib.php");
    auth_sync_users();
?>

Usersync is quite heavy process, it could be good idea to place that script outside of webroot and run it  with cron.
                            

Any feedback is wellcome,

Petri Asikainen paca@sci.fi


*/
function auth_user_login ($username, $password) {
/// Returns true if the username and password work
/// and false if they don't

    global $CFG;

    if (!$username or !$password) {    // Don't allow blank usernames or passwords
        return false;
    }
 
    $ldap_connection = auth_ldap_connect();

    if ($ldap_connection) {
        $ldap_user_dn = auth_ldap_find_userdn($ldap_connection, $username);
      
        //if ldap_user_dn is empty, user does not exist
        if(!$ldap_user_dn){
            ldap_close($ldap_connection);
            return false;
        }

        // Try to bind with current username and password
        $ldap_login = @ldap_bind($ldap_connection, $ldap_user_dn, $password);
        ldap_close($ldap_connection);
        if ($ldap_login) {
            return true;
        }
    } else {
        @ldap_close($ldap_connection);
        error("LDAP-module cannot connect to server: $CFG->ldap_host_url");
    }
    return false;
}


 
function auth_get_userinfo($username){
/// reads userinformation from ldap and return it in array()
    global $CFG;

    $config = (array)$CFG;
    $attrmap = auth_ldap_attributes();
   
    $ldap_connection=auth_ldap_connect();

    $result = array();
    $search_attribs = array();
  
    foreach ($attrmap as $key=>$value) {
        if (!in_array($value, $search_attribs)) {
            array_push($search_attribs, $value);
        }    
    }

    $user_dn = auth_ldap_find_userdn($ldap_connection, $username);

    if (empty($CFG->ldap_objectclass)) {        // Can't send empty filter
        $CFG->ldap_objectclass="objectClass=*";
    }
  
    $user_info_result = ldap_read($ldap_connection,$user_dn,$CFG->ldap_objectclass, $search_attribs);

    if ($user_info_result) {
        $user_entry = ldap_get_entries($ldap_connection, $user_info_result);
        foreach ($attrmap as $key=>$value){
            if(isset($user_entry[0][strtolower($value)][0])){
                $result[$key]=$user_entry[0][strtolower($value)][0];
            }
        }
        $result['guid']='ldap';
    }

    @ldap_close($ldap_connection);
    
    return $result;
}

function auth_get_userlist () {
    global $CFG;
    return auth_ldap_get_userlist("($CFG->ldap_user_attribute=*)");
}
function auth_user_exists ($username) {
   global $CFG; 
   //returns true if given usernname exist on ldap
   $users = auth_ldap_get_userlist("($CFG->ldap_user_attribute=$username)");
   return count($users); 
}

function auth_user_create ($userobject,$plainpass) {
//create new user to ldap
//use auth_user_exists to prevent dublicate usernames
//return true if user is created, false on error
	global $CFG;
    $attrmap = auth_ldap_attributes();
    $ldapconnect = auth_ldap_connect();
    $ldapbind = auth_ldap_bind($ldapconnect);
    
    $newuser = array();
     
    foreach ($attrmap as $key=>$value){
            if(isset($userobject->$key) ){
                $newuser[$value]=utf8_encode($userobject->$key);
            }
    }
    
    //Following sets all mandatory and other forced attribute values
    //this should be moved to config inteface ASAP
    $newuser['objectClass']= array("inetOrgPerson","organizationalPerson","person","top");
    $newuser['uniqueId']= $userobject->username;
    $newuser['logindisabled']="TRUE";
    $newuser['userpassword']=$plainpass;
    unset($newuser[country]);
        
    $uadd = ldap_add($ldapconnect, $CFG->ldap_user_attribute."=$userobject->username,".$CFG->ldap_create_context, $newuser);

    ldap_close($ldapconnect);
    return $uadd;
    
}

function auth_get_users($filter='*') {
//returns all userobjects from external database
    global $CFG;

    $fresult = array();
    $ldap_connection = auth_ldap_connect();

    auth_ldap_bind($ldap_connection);

    if (! isset($CFG->ldap_objectclass)) {
        $CFG->ldap_objectclass="objectClass=*";
    }

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
            $ldap_result = ldap_search($ldap_connection, $context,
                                       $filter,
                                       $search_attribs);
        } else {
            //search only in this context
            $ldap_result = ldap_list($ldap_connection, $context,
                                     $filter,
                                     $search_attribs);
        }

        $users = auth_ldap_get_entries($ldap_connection, $ldap_result);

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

function auth_sync_users () {
//Syncronizes userdb with ldap
//This will add, rename 
    global $CFG ;
    $users = auth_get_users();
    $usedguids = Array();
    
    foreach ($users as $user) {
        $usedguids[] = $user->guid; //we will need all used guids later
        //update modified time
        $user->modified = time();
        //All users are confirmed
        $user->confirmed = 1;
        // if user does not exist create it
        if (!record_exists('user','auth', 'ldap', 'guid', $user->guid)) {
            if (insert_record ('user',$user)) {
                echo "inserted user $user->username with guid $user->guid \n";
            } else {
                echo "error inserting user $user->username with guid $user->guid \n";
            }
            continue ;
        } else {
           //update username
           set_field('user', 'username', $user->username , 'auth', 'ldap', 'guid', $user->guid);
           //no id-information in ldap so get now
           $userid = get_field('user', 'id', 'auth', 'ldap', 'guid', $user->guid);

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
    
    //find nonexisting users from moodles userdb
    $sql = "SELECT * FROM ".$CFG->prefix."user WHERE deleted = '0' AND auth = 'ldap' AND guid  NOT IN ('".implode('\' , \'',$usedguids)."');" ;
    $result = get_records_sql($sql);

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

function auth_user_activate ($username) {
//activate new ldap-user after email-address is confirmed
	global $CFG;

    $ldapconnect = auth_ldap_connect();
    $ldapbind = auth_ldap_bind($ldapconnect);

    $userdn = auth_ldap_find_userdn($ldapconnect, $username);
    
    $newinfo['loginDisabled']="FALSE";

    $result = ldap_modify($ldapconnect, $userdn, $newinfo);
    ldap_close($ldapconnect);
    return $result;
}

function auth_user_disable ($username) {
//activate new ldap-user after email-address is confirmed
	global $CFG;

    $ldapconnect = auth_ldap_connect();
    $ldapbind = auth_ldap_bind($ldapconnect);

    $userdn = auth_ldap_find_userdn($ldapconnect, $username);
    $newinfo['loginDisabled']="TRUE";

    $result = ldap_modify($ldapconnect, $userdn, $newinfo);
    ldap_close($ldapconnect);
    return $result;
}

function auth_iscreator($username=0) {
///if user is member of creator group return true
    global $USER , $CFG; 
    if (! $username) {
        $username=$USER->username;
    }
   
    if ((! $CFG->ldap_creators) OR (! $CFG->ldap_memberattribute)) {
        return false;
    } 

    return auth_ldap_isgroupmember($username, $CFG->ldap_creators);
 
}

//PRIVATE FUNCTIONS starts
//private functions are named as auth_ldap*

function auth_ldap_isgroupmember ($username='', $groupdns='') {
// Takes username and groupdn(s) , separated by ;
// Returns true if user is member of any given groups

    global $CFG, $USER;

    $ldapconnect = auth_ldap_connect();
    $ldapbind = auth_ldap_bind($ldapconnect);
   
    if (empty($username) OR empty($groupdns)) {
        return false;
    }
    
    $groups = explode(";",$groupdns);

    //build filter
    $filter = "(& ($CFG->ldap_user_attribute=$username)(|";
    foreach ($groups as $group){
        $filter .= "($CFG->ldap_memberattribute=$group)";
    }
    $filter .= "))";
    //search
    $result = auth_ldap_get_userlist($filter);
   
    return count($result);

}
function auth_ldap_connect(){
/// connects to ldap-server
    global $CFG;

    $result = ldap_connect($CFG->ldap_host_url);

    if ($result) {
        if (!empty($CFG->ldap_version)) {
            ldap_set_option($result, LDAP_OPT_PROTOCOL_VERSION, $CFG->ldap_version);
        }

        return $result;

    } else {
        error("LDAP-module cannot connect to server: $CFG->ldap_host_url");
        return false;
    }
}



function auth_ldap_bind($ldap_connection){
/// makes bind to ldap for searching users
/// uses ldap_bind_dn or anonymous bind

    global $CFG;

    if ($CFG->ldap_bind_dn){
        //bind with search-user
        if (!ldap_bind($ldap_connection, $CFG->ldap_bind_dn,$CFG->ldap_bind_pw)){
            error("Error: could not bind ldap with ldap_bind_dn/pw");
            return false;
        }

    } else {
        //bind anonymously 
        if ( !ldap_bind($ldap_connection)){
            error("Error: could not bind ldap anonymously");
            return false;
        }  
    }

    return true;
}



function auth_ldap_find_userdn ($ldap_connection, $username){
/// return dn of username
/// like: cn=username,ou=suborg,o=org
/// or false if username not found

    global $CFG;

    //default return value
    $ldap_user_dn = FALSE;

    auth_ldap_bind($ldap_connection);

    //get all contexts and look for first matching user
    $ldap_contexts = explode(";",$CFG->ldap_contexts);
    
    if (!empty($CFG->ldap_create_context)){
	  array_push($ldap_contexts, $CFG->ldap_create_context);
    }
  
    foreach ($ldap_contexts as $context) {

        $context == trim($context);

        if ($CFG->ldap_search_sub){
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldap_connection, $context, "(".$CFG->ldap_user_attribute."=".$username.")",array($CFG->ldap_user_attribute));

        } else {
            //search only in this context
            $ldap_result = ldap_list($ldap_connection, $context, "(".$CFG->ldap_user_attribute."=".$username.")",array($CFG->ldap_user_attribute));
        }
 
        $entry = ldap_first_entry($ldap_connection,$ldap_result);

        if ($entry){
            $ldap_user_dn = ldap_get_dn($ldap_connection, $entry);
            break ;
        }
    }

    return $ldap_user_dn;
}

function auth_ldap_attributes (){
//returns array containg attribute mappings between Moodle and ldap
	global $CFG;

    $config = (array)$CFG;
    $fields = array("firstname", "lastname", "email", "phone1", "phone2", 
                    "department", "address", "city", "country", "description", 
                    "idnumber", "lang", "guid");

    $moodleattributes = array();
    foreach ($fields as $field) {
        if ($config["auth_user_$field"]) {
            $moodleattributes[$field] = $config["auth_user_$field"];
        }
    }
    $moodleattributes['username']=$config["ldap_user_attribute"];
	return $moodleattributes;
}

function auth_ldap_get_userlist($filter="*") {
/// returns all users from ldap servers
    global $CFG;

    $fresult = array();
    $ldap_connection = auth_ldap_connect();

    auth_ldap_bind($ldap_connection);

    if (! isset($CFG->ldap_objectclass)) {
        $CFG->ldap_objectclass="objectClass=*";
    }

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
            $ldap_result = ldap_search($ldap_connection, $context,
                                       $filter,
                                       array($CFG->ldap_user_attribute));
        } else {
            //search only in this context
            $ldap_result = ldap_list($ldap_connection, $context,
                                     $filter,
                                     array($CFG->ldap_user_attribute));
        }

        $users = ldap_get_entries($ldap_connection, $ldap_result);

        //add found users to list
        for ($i=0;$i<$users['count'];$i++) {
            array_push($fresult, ($users[$i][$CFG->ldap_user_attribute][0]) );
        }
    }
   
    return $fresult;
}

function auth_ldap_get_entries($conn, $searchresult){
//Returns values like ldap_get_entries but is
//binary compatible
    $i=0;
    $fresult=array();
    $entry = ldap_first_entry($conn, $searchresult);
    do {
        $attributes = ldap_get_attributes($conn, $entry);
        for($j=0; $j<$attributes['count']; $j++) {
            $values = ldap_get_values_len($conn, $entry,$attributes[$j]);
            $fresult[$i][$attributes[$j]] = $values;
        }         
        $i++;               
    }
    while ($entry = ldap_next_entry($conn, $entry));
    //we're done
    return ($fresult);
}




?>
