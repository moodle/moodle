<?PHP  // $Id$
//CHANGELOG:
//12.10.2002 Reformatted source for consistency
//03.10.2002 First version to CVS
//29.09.2002 Clean up and splitted code to functions v. 0.02
//29.09.2002 LDAP authentication functions v. 0.01
//Distributed under GPL (c)Petri Asikainen 2002


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
    $fields = array("firstname", "lastname", "email", "phone1", "phone2", 
                    "department", "address", "city", "country", "description", 
                    "idnumber", "lang");

    $moodleattributes = array();
    foreach ($fields as $field) {
        if ($config["auth_user_$field"]) {
            $moodleattributes[$field] = $config["auth_user_$field"];
        }
    }

    $ldap_connection=auth_ldap_connect();

    $result = array();
    $search_attribs = array();
  
    foreach ($moodleattributes as $key=>$value) {
        array_push($search_attribs, $value);
    }

    $user_dn = auth_ldap_find_userdn($ldap_connection, $username);

    if (! isset($CFG->ldap_objectclass)) {
        $CFG->ldap_objectclass="objectClass=*";
    }
  
    $user_info_result = ldap_read($ldap_connection,$user_dn,$CFG->ldap_objectclass, $search_attribs);

    if ($user_info_result) {
        $user_entry = ldap_get_entries($ldap_connection, $user_info_result);
        foreach ($moodleattributes as $key=>$value){
            if(isset($user_entry[0][$value][0])){
                $result[$key]=$user_entry[0][$value][0];
            }
        }
    }

    @ldap_close($ldap_connection);

    return $result;
}



function auth_get_userlist() {
/// returns all users from ldap servers
    global $CFG;

    $fresult = array();
    $ldap_connection = auth_ldap_connect();

    auth_ldap_bind($ldap_connection);

    if (! isset($CFG->ldap_objectclass)) {
        $CFG->ldap_objectclass="objectClass=*";
    }

    $contexts = explode(";",$CFG->ldap_contexts);

    foreach ($contexts as $context) {

        if ($CFG->ldap_search_sub) {
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldap_connection, $context, 
                                       "(".$CFG->ldap_objectclass.")", 
                                       array($CFG->ldap_user_attribute));
        } else {
            //search only in this context
            $ldap_result = ldap_list($ldap_connection, $context, 
                                     "(".$CFG->ldap_objectclass.")", 
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



function auth_ldap_connect(){
/// connects to ldap-server
    global $CFG;

    $result = ldap_connect($CFG->ldap_host_url);

    if ($result) {
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
  
    foreach ($ldap_contexts as $context) {

        $context == trim($context);

        if ($CFG->ldap_search_sub){
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldap_connection, $context, "(".$CFG->ldap_user_attribute."=".$username.")");

        } else {
            //search only in this context
            $ldap_result = ldap_list($ldap_connection, $context, "(".$CFG->ldap_user_attribute."=".$username.")");
        }
 
        $entry = ldap_first_entry($ldap_connection,$ldap_result);

        if ($entry){
            $ldap_user_dn = ldap_get_dn($ldap_connection, $entry);
            break ;
        }
    }

    return $ldap_user_dn;
}

?>
