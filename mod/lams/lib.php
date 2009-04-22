<?PHP  // $Id$

/// Library of functions and constants for module lams

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/soaplib.php');


function lams_add_instance($lams) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.
    global $USER;
    $lams->timemodified = time();
    $lams->learning_session_id = lams_get_lesson($USER->username,$lams->sequence,$lams->course,$lams->name,$lams->introduction,"normal");
  return insert_record("lams", $lams);
}


function lams_update_instance($lams) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.
    //echo "enter lams_update_instance<br/>";
  $lams->timemodified = time();
  $lams->id = $lams->instance;
  lams_delete_lesson($USER->username,$lams->learning_session_id);
    $lams->learning_session_id = lams_get_lesson($USER->username,$lams->sequence,$lams->course,$lams->name,$lams->introduction,"normal");
    if(!$lams->learning_session_id){
        return false;
    }
    # May have to add extra stuff in here #
  //echo $lams->id."<br/>";
    //echo $lams->sequence."<br/>";
    //echo $lams->course."<br/>";
    //echo $lams->name."<br/>";
    //echo $lams->introduction."<br/>";
    //echo $lams->learning_session_id."<br/>";
    //echo "exit lams_update_instance<br/>";
  return update_record("lams", $lams);
}


function lams_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

  if (! $lams = get_record("lams", "id", "$id")) {
      return false;
  }

  $result = true;

  # Delete any dependent records here #
    lams_delete_lesson($USER->username,$lams->learning_session_id);
  if (! delete_records("lams", "id", "$lams->id")) {
      $result = false;
  }

  return $result;
}

function lams_user_outline($course, $user, $mod, $lams) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

  return $return;
}

function lams_user_complete($course, $user, $mod, $lams) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

  return true;
}

function lams_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

  global $CFG;

  return true;
}

function lams_get_participants($lamsid) {
//Must return an array of user records (all data) who are participants
//for a given instance of lams. Must include every user involved
//in the instance, independient of his role (student, teacher, admin...)
//See other modules as example.

  return false;
}

function lams_scale_used ($lamsid,$scaleid) {
//This function returns if a scale is being used by one lams
//it it has support for grading and scales. Commented code should be
//modified if necessary. See forum, glossary or journal modules
//as reference.

  $return = false;

  //$rec = get_record("lams","id","$lamsid","scale","-$scaleid");
  //
  //if (!empty($rec)  && !empty($scaleid)) {
  //    $return = true;
  //}

  return $return;
}

/**
 * Checks if scale is being used by any instance of lams
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any lams
 */
function lams_scale_used_anywhere($scaleid) {
 return false;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other lams functions go here.  Each of them must have a name that
/// starts with lams_

function lams_get_soap_client($relativeurl) {
    global $CFG;
    if(!isset($CFG->lams_serverurl))
    {
        return NULL;
    }
    $wsdl = $CFG->lams_serverurl.$relativeurl;
    $s = new soap_client($wsdl,true,false,false,false,false,2,3);
    return $s;
}

/**
 * Get sequences(learning designs) for the user in LAMS
 *
 * @param string $username The username of the user. Set this to "" if you would just like to get sequences for the currently logged in user.
 * @return Array sequence array
 * @TODO complete the documentation of this function
 */
function lams_get_sequences($username,$courseid) {
    global $CFG,$USER;
    if(!isset($CFG->lams_serverid)||!isset($CFG->lams_serverkey)||!isset($CFG->lams_serverurl))
    {
        return get_string("notsetup", "lams");
    }
    $relativeurl="/services/LearningDesignService?wsdl";
    $s = lams_get_soap_client($relativeurl);
    if(is_null($s)){
        return NULL;
    }
    $datetime =    date("F d,Y g:i a");
    /*
    $login = lams_get_user($username,$courseid);
    if(empty($login)){
        return NULL;
    }
    */
    if(!isset($username)){
        $username = $USER->username;
    }
    $rawstring = trim($datetime).trim($username).trim($CFG->lams_serverid).trim($CFG->lams_serverkey);
    $hashvalue = sha1(strtolower($rawstring));
    $parameters = array($CFG->lams_serverid,$datetime,$hashvalue,$username);
    $result = $s->call('getAllLearningDesigns',$parameters);//Array of simpleLearningDesign objects
    if($s->getError()){//if some exception happened
        $result = $s->getError();//return the string describing the error
    }
    unset($s);
    return $result;
}

/**
 * Get learning session(lesson) id from LAMS
 *
 * @param string $username The username of the user. Set this to "" if you would just like the currently logged in user to create the lesson
 * @param int $ldid The id of the learning design that the lesson is based on
 * @param int $courseid The id of the course that the lesson is associated with.
 * @param string $title The title of the lesson
 * @param string $desc The description of the lesson
 * @param string $type The type of the lesson. Two types: normal, preview
 * @return int lesson id
 */
function lams_get_lesson($username,$ldid,$courseid,$title,$desc,$type) {
    //echo "enter lams_get_lesson<br/>";
    global $CFG,$USER;
    if(!isset($CFG->lams_serverid)||!isset($CFG->lams_serverkey))
    {
        //echo "serverid or serverkey is not set<br/>";
        return NULL;
    }
    $relativeurl="/services/LearningSessionService?wsdl";
    $s = lams_get_soap_client($relativeurl);
    if(is_null($s)){
        //echo "soap client is null<br/>";
        return NULL;
    }
    $datetime =    date("F d,Y g:i a");
    if(!isset($username)){
        $username = $USER->username;
    }
    $plaintext = $datetime.$username.$CFG->lams_serverid.$CFG->lams_serverkey;
    //echo $plaintext;
    $hashvalue = sha1(strtolower($plaintext));
    //echo $hashvalue;
    $parameters = array($CFG->lams_serverid,$datetime,$hashvalue,$username,$ldid,$courseid,$title,$desc,$type);
    $result = $s->call('createLearningSession',$parameters);
    //echo "result:".$result."<br/>";
    //echo "exit lams_get_lesson<br/>";
    if($s->getError()){
        $result = $s->getError();
    }
    unset($s);
    return $result;
}

/**
 * Delete learning session(lesson) from LAMS
 *
 * @param string $username The username of the user. Set this to "" if you would just like the currently logged in user to create the lesson
 * @param int $lsid The id of the learning session(lesson)
 * @return true or false
 */
function lams_delete_lesson($username,$lsid) {
    //echo "enter lams_get_lesson<br/>";
    global $CFG,$USER;
    if(!isset($CFG->lams_serverid)||!isset($CFG->lams_serverkey))
    {
        return "The LAMS serverId and serverKey have not been set up";
    }
    $relativeurl="/services/LearningSessionService?wsdl";
    $s = lams_get_soap_client($relativeurl);
    if(is_null($s)){
        return "Failed to get soap client based on:".$relativeurl;
    }
    $datetime =    date("F d,Y g:i a");
    if(!isset($username)){
        $username = $USER->username;
    }
    $plaintext = $datetime.$username.$CFG->lams_serverid.$CFG->lams_serverkey;
    //echo $plaintext;
    $hashvalue = sha1(strtolower($plaintext));
    //echo $hashvalue;
    $parameters = array($CFG->lams_serverid,$datetime,$hashvalue,$username,$lsid);
    $result = $s->call('deleteLearningSession',$parameters);
    if($s->getError()){
        $result = $s->getError();
    }
    unset($s);
    return $result;
}


/**
 * Get class in LAMS
 * @param int courseid
 * @return int class id
 * @TODO complete the documentation of this function
 */
 /*
function lams_get_class($courseid) {
    global $CFG,$USER;
    //echo "enter lams_get_class"."<br/>";
    $orgId = lams_get_organisation();
    if(empty($orgId)){
        return NULL;
    }
  $lams_course = get_record("lams_course","course", $courseid);
  if(empty($lams_course)){//LAMS class hasn't been created
       //create LAMS class
      $relativeurl="/services/UserManagementService?wsdl";
      $s = lams_get_soap_client($relativeurl);
      if(is_null($s)){
          return NULL;
      }
      $datetime =    date("F d,Y g:i a");
      $rawstring = $datetime.$CFG->lams_serverid.$CFG->lams_serverkey;
      $hashvalue = sha1(strtolower($rawstring));
      $parameters = array($CFG->lams_serverid,$datetime,$hashvalue);
      $result = $s->call('createClass',$parameters);
      //echo "<xmp/>".$s->request."</xmp>";
      //echo "<xmp/>".$s->response."</xmp>";
      //echo "result:".$result."<br/>";
       $lams_course->course = $courseid;
       $lams_course->classid = $result;
        insert_record("lams_course",$lams_course);
        //echo "exit lams_get_class"."<br/>";
         return $result;
  }else{
      //echo "exit lams_get_class"."<br/>";
      return $lams_course->classid;
  }
}
*/
/**
 * Get organisation in LAMS
 *
 * @return int organisation id
 * @TODO complete the documentation of this function
 */
 /*
function lams_get_organisation() {
    global $CFG,$USER;
    //echo "enter lams_get_organisaiton"."<br/>";
    if(!isset($CFG->lams_serverid)||!isset($CFG->lams_serverkey))
    {
        return NULL;
    }
    if(!isset($CFG->lams_orgid)){
      $relativeurl="/services/UserManagementService?wsdl";
      $s = lams_get_soap_client($relativeurl);
      if(empty($s)){
          return NULL;
      }
      $datetime =    date("F d,Y g:i a");
      $rawstring = $datetime.$CFG->lams_serverid.$CFG->lams_serverkey;
      $hashvalue = sha1(strtolower($rawstring));
      $parameters = array($CFG->lams_serverid,$datetime,$hashvalue);
      $result = $s->call('createOrganisation',$parameters);
      //echo "<xmp/>".$s->request."</xmp>";
      //echo "<xmp/>".$s->response."</xmp>";
      set_config("lams_orgid",$result);
      //echo "result:".$result."<br/>";
      //echo "exit lams_get_organisaiton"."<br/>";
      return $result;
    }else{
        //echo "exit lams_get_organisaiton"."<br/>";
        return $CFG->lams_orgid;
    }
}
*/

/**
 * Get user in LAMS
 *
 * @param string $username The username of the user. Set this to "" if you would just like to create LAMS user for the currently logged in user
 * @param string $roles The user's roles in LAMS
 * @param int $classid The id of the class that the user belongs to. The class should be already created in LAMS by calling lams_create_class()
 * @param int $orgid The id of the organisation that  the user belongs to. The organisation should be already created in LAMS by calling lams_create_organisation()
 * @return user login in LAMS if the user is successfully created
 * @TODO complete the documentation of this function
 */
 /*
function lams_get_user($username,$courseid) {
    global $CFG,$USER;
    //echo "enter lams_get_user"."<br/>";
    if(!isset($CFG->lams_serverid)||!isset($CFG->lams_serverkey))
    {
        return NULL;
    }
    $lams_user = get_record("lams_user","username",$username);
    if(empty($lams_user)){//LAMS user hasn't been created
        $classid = lams_get_class($courseid);
        if(empty($classid)){//Can't get class id from lams_course table. Something wrong!
            return NULL;
        }
        $orgid = lams_get_organisation();//It won't be NULL. See lams_get_class function
        $user = get_record("user","username",$username);
        if(empty($user)){//Something wrong
            return NULL;
        }
        $roles = lams_get_user_roles($user->id,$courseid);
      $relativeurl="/services/UserManagementService?wsdl";
      $s = lams_get_soap_client($relativeurl);
      if(empty($s)){
          return NULL;
      }
      $datetime =    date("F d,Y g:i a");
      $login = $username;
      $rawstring = $datetime.$login.$CFG->lams_serverid.$CFG->lams_serverkey;
      $hashvalue = sha1(strtolower($rawstring));
      $parameters = array($CFG->lams_serverid,$datetime,$hashvalue,$login,"password",$roles,$classid,$orgid);
      $result = $s->call('createUser',$parameters);
      //echo "<xmp/>".$s->request."</xmp>";
      //echo "<xmp/>".$s->response."</xmp>";
      $lams_user->username = $username;
      $lams_user->login = $result;
      insert_record("lams_user",$lams_user);
      //echo "result:".$result."<br/>";
      //echo "exit lams_get_user"."<br/>";
      return $result;
    }else{
        //echo "exit lams_get_user"."<br/>";
        return $lams_user->login;
    }
}
*/

/**
 * Mapping moodle roles to LAMS roles
 *
 * @param int $courseid The id of the course that is being viewed
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @return formatted string describing LAMS roles
 * @TODO fill the gap of roles mapping between moodle and LAMS
 */
 /*
function lams_get_user_roles($userid=0, $courseid){
    $roles = "";
    if(isadmin($userid)){
        $roles = "administrator"."|"."auhtor"."|"."staff";
    }else    if(isteacheredit($courseid,$userid)){
        $roles = "auhtor"."|"."staff";
    }else if(isteacher($courseid,$userid)){
        $roles = "staff";
    }
    if(isstudent($courseid,$userid)){
        if(empty($roles)){
            $roles = "learner";
        }else{
            $roles .= "|"."learner";
        }
    }
    //echo $roles."<br/>";
    return $roles;
}
*/

?>
