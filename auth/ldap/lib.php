<?PHP  // $Id$
//CHANGELOG:
//24.02.2003 Added support for coursecreators
//20.02.2003 Added support for user creation
//12.10.2002 Reformatted source for consistency
//03.10.2002 First version to CVS
//29.09.2002 Clean up and splitted code to functions v. 0.02
//29.09.2002 LDAP authentication functions v. 0.01
//Distributed under GPL (c)Petri Asikainen 2002-2003

/* README!
Module is quite complete and  most functinality can be configured from
configinterfave /admin/auth.php. Some of latest additions/features need to
be configured by modifying source code.
 
If you plan to use user creation feature, look function auth_user_create
and modify it for your needs.
You have to change all hardcoded attribute values to fit your LDAP-server.
User-creation makes posible that your current 
users can authenticate with existings usernames/password and new users can 
create own accounts to LDAP-directory. I'm using this feature and new users
are created to LDAP different context, without rights to other system. When
user-creation feature is set like that, there's no known security issues.
I write ldap-module on Novell E-directory / Linux & Solaris , 
so all default values are for it.

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

    if (! isset($CFG->ldap_objectclass)) {
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
   global $CFG, $USER;

   $ldapconnect = auth_ldap_connect();
   $ldapbind = auth_ldap_bind($ldapconnect);

   if (! $username) {
       $username=$USER->username;
   }
   
   if ((! $CFG->ldap_creators) OR (! $CFG->ldap_memberattribute)) {
      return false;
   } else {
      $groups = explode(";",$CFG->ldap_creators);
   }


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

//PRIVATE FUNCTIONS starts
//private functions are named as auth_ldap*

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
                    "idnumber", "lang");

    $moodleattributes = array();
    foreach ($fields as $field) {
        if ($config["auth_user_$field"]) {
            $moodleattributes[$field] = $config["auth_user_$field"];
        }
    }
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

?>
