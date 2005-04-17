<?PHP  // $Id$
//CHANGELOG:
//28.10.2004 SHIBBOLETH Authentication functions v.0.1
//Distributed under GPL (c)Markus Hagman 2004-

function auth_user_login ($username, $password) {
    global $CFG;

/// If we are in the shibboleth directory then we trust the server var
    if (!empty($_SERVER[$CFG->shib_user_attribute])) {
        return ($_SERVER[$CFG->shib_user_attribute] == $username);
    }

/// If we are not, then the server is probably set to not be Shibboleth-only
/// and the user has used the normal login screen, so we redirect to the shibboleth
/// directory for a proper check
    redirect($CFG->wwwroot.'/auth/shibboleth/index.php';

/// There's no point doing anything further here
    exit;
}

function auth_get_userinfo($username) {
// reads user information from shibboleth attributes and return it in array()
    global $CFG;

    $config = (array)$CFG;
    $attrmap = auth_shib_attributes();
   
    $result = array();
    $search_attribs = array();
  
    foreach ($attrmap as $key=>$value) {
        $result[$key]=utf8_decode($_SERVER[$value]);
    }
    return $result;
}

function auth_shib_attributes (){
//returns array containg attribute mappings between Moodle and shibboleth
	global $CFG;

    $config = (array)$CFG;
    $fields = array("firstname", "lastname", "email", "phone1", "phone2", 
                    "department", "address", "city", "country", "description", 
                    "idnumber", "lang", "guid");

    $moodleattributes = array();
    foreach ($fields as $field) {
        if ($config["auth_shib_user_$field"]) {
            $moodleattributes[$field] = $config["auth_shib_user_$field"];
        }
    }
    $moodleattributes['username']=$config["shib_user_attribute"];
	return $moodleattributes;
}
?>
