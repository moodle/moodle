<?PHP  // $Id$
//CHANGELOG:
//28.10.2004 SHIBBOLETH Authentication functions v.0.1
//Distributed under GPL (c)Markus Hagman 2004-

function auth_user_login ($username, $password) {
    global $CFG;
	if($_SERVER[$CFG->shib_user_attribute] == $username) {
        return true;
    }
    // Returns false if the username doesn't exist yet
    // Returns true if the username and password work
    if ($user = get_user_info_from_db("username", $username)) {
		if($user->auth == "shibboleth") {
            return false;
            exit;
        } else {
            return ($user->password == md5($password));
            exit;
        }
    }   

    return false;
}

function auth_get_userinfo($username) {
// reads user information from shibboleth attributes and return it in array()
    global $CFG;

    $config = (array)$CFG;
    $attrmap = auth_shib_attributes();
   
    $result = array();
    $search_attribs = array();
  
    foreach ($attrmap as $key=>$value) {
        $result[$key]=$_SERVER[$value];
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
        if ($config["auth_user_$field"]) {
            $moodleattributes[$field] = $config["auth_user_$field"];
        }
    }
    $moodleattributes['username']=$config["shib_user_attribute"];
	return $moodleattributes;
}
?>
