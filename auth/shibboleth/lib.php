<?PHP  // $Id$
//CHANGELOG:
//28.10.2004 SHIBBOLETH Authentication functions v.0.1
//Distributed under GPL (c)Markus Hagman 2004-

function auth_user_login($username, $password) {
    global $CFG;

    $pluginconfig   = get_config('auth/shibboleth');
    
/// If we are in the shibboleth directory then we trust the server var
    if (!empty($_SERVER[$pluginconfig->shib_user_attribute])) {
        return ($_SERVER[$pluginconfig->shib_user_attribute] == $username);
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

    $pluginconfig   = get_config('auth/shibboleth');

    // Check whether we have got all the essential attributes
    if (
           empty($_SERVER[$pluginconfig->shib_user_attribute])
        || empty($_SERVER[$pluginconfig->field_map_firstname])
        || empty($_SERVER[$pluginconfig->field_map_lastname])
        || empty($_SERVER[$pluginconfig->field_map_email])
        ) {
        error(get_string( 'shib_not_all_attributes_error', 'auth' , "'".$pluginconfig->shib_user_attribute."' ('".$_SERVER[$pluginconfig->shib_user_attribute]."'), '".$pluginconfig->field_map_firstname."' ('".$_SERVER[$pluginconfig->field_map_firstname]."'), '".$pluginconfig->field_map_lastname."' ('".$_SERVER[$pluginconfig->field_map_lastname]."') and '".$pluginconfig->field_map_email."' ('".$_SERVER[$pluginconfig->field_map_email]."')"));
    }

    $attrmap = auth_shib_attributes();

    $result = array();
    $search_attribs = array();
  
    foreach ($attrmap as $key=>$value) {
        $result[$key]=utf8_decode($_SERVER[$value]);
    }

     // Provide an API to modify the information to fit the Moodle internal
    // data representation
    if (   
          $pluginconfig->convert_data 
          && $pluginconfig->convert_data != ''
          && is_readable($pluginconfig->convert_data)
        ){
        
        // Include a custom file outside the Moodle dir to
        // modify the variable $moodleattributes
        include($pluginconfig->convert_data);
    }
    
    return $result;
}

function auth_shib_attributes(){
//returns array containg attribute mappings between Moodle and shibboleth
	global $CFG;

    $pluginconfig   = get_config('auth/shibboleth');
    $pluginconfig   = (array) $pluginconfig;

    $fields = array("firstname", "lastname", "email", "phone1", "phone2", 
                    "department", "address", "city", "country", "description", 
                    "idnumber", "lang", "guid");

    $moodleattributes = array();
    foreach ($fields as $field) {
        if ($pluginconfig["field_map_$field"]) {
            $moodleattributes[$field] = $pluginconfig["field_map_$field"];
        }
    }
    $moodleattributes['username']=$pluginconfig["shib_user_attribute"];

	return $moodleattributes;
}
?>
