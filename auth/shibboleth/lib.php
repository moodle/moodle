<?PHP  // $Id$
//CHANGELOG:
//28.10.2004 SHIBBOLETH Authentication functions v.0.1
//Distributed under GPL (c)Markus Hagman 2004-

function auth_user_login($username, $password) {
    global $CFG;

/// If we are in the shibboleth directory then we trust the server var
    if (!empty($_SERVER[$CFG->shib_user_attribute])) {
        return ($_SERVER[$CFG->shib_user_attribute] == $username);
    }    

/// If we are not, then the server is probably set to not be Shibboleth-only
/// and the user has used the normal login screen, so we redirect to the shibboleth
/// directory for a proper check
    redirect($CFG->wwwroot.'/auth/shibboleth/index.php');

/// There's no point doing anything further here
    exit;
}

function auth_get_userinfo($username) {
// reads user information from shibboleth attributes and return it in array()
    global $CFG;

    // Check whether we have got all the essential attributes
    if (
           empty($_SERVER[$CFG->shib_user_attribute])
        || empty($_SERVER[$CFG->auth_shib_user_firstname])
        || empty($_SERVER[$CFG->auth_shib_user_lastname])
        || empty($_SERVER[$CFG->auth_shib_user_email])
        ) {
        error("Moodle needs certain Shibboleth attributes which are not present in your case. The attributes are: '".$CFG->shib_user_attribute."' ('".$_SERVER[$CFG->shib_user_attribute]."'), '".$CFG->auth_shib_user_firstname."' ('".$_SERVER[$CFG->auth_shib_user_firstname]."'), '".$CFG->auth_shib_user_lastname."' ('".$_SERVER[$CFG->auth_shib_user_lastname]."') and '".$CFG->auth_shib_user_email."' ('".$_SERVER[$CFG->auth_shib_user_email]."')<br>Please contact your Identity Service Provider.");
    }

    $config = (array)$CFG;
    $attrmap = auth_shib_attributes();
   
    $result = array();
    $search_attribs = array();
  
    foreach ($attrmap as $key=>$value) {
        $result[$key]=utf8_decode($_SERVER[$value]);
    }
    
     // Provide an API to modify the information to fit the Moodle internal
    // data representation
    if (   
          $config["shib_convert_data"] 
          && $config["shib_convert_data"] != ''
          && is_readable($config["shib_convert_data"])
        ){
        
        // Include a custom file outside the Moodle dir to
        // modify the variable $moodleattributes
        include($config["shib_convert_data"]);
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
