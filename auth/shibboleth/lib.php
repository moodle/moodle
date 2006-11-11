<?PHP  // $Id$
//CHANGELOG:
//10.2004 SHIBBOLETH Authentication functions v.0.1
//05.2005 Various extensions and fixes by Lukas Haemmerle
//10.2005 Added better error messags
//05.2006 Added better handling of mutli-valued attributes
//
//Distributed under GPL (c)Markus Hagman 2004-2006

function auth_user_login($username, $password) {
    global $CFG;

    $pluginconfig   = get_config('auth/shibboleth');
    
    // If we are in the shibboleth directory then we trust the server var
    if (!empty($_SERVER[$pluginconfig->shib_user_attribute])) {
        return ($_SERVER[$pluginconfig->shib_user_attribute] == $username);
    } else {
    // If we are not, the user has used the manual login and the login name is
    // unknown, so we return false.
        return false;
    }
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
        $result[$key]= get_first_string($_SERVER[$value]);
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

function get_first_string($string){
// Cleans and returns first of potential many values (multi-valued attributes)

    $list = split( ';', $string);
    $clean_string = rtrim($list[0]);

    return $clean_string;

}
?>
