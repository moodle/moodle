<?


function migrate2utf8_event_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$event = get_record('event', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($event->courseid);  //Non existing!
        if ($event->userid) {
            $userlang = get_user_lang($event->userid);
        } else {
            $userlang = get_main_teacher_lang($event->courseid);
        }

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($event->name, $fromenc);

        $newevent = new object;
        $newevent->id = $recordid;
        $newevent->name = $result;
        update_record('event',$newevent);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_event_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$event = get_record('event', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($event->courseid);  //Non existing!
        if ($event->userid) {
            $userlang = get_user_lang($event->userid);
        } else {
            $userlang = get_main_teacher_lang($event->courseid);
        }

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($event->description, $fromenc);

        $newevent = new object;
        $newevent->id = $recordid;
        $newevent->description = $result;
        update_record('event',$newevent);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_config_value($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$config = get_record('config', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($config->value, $fromenc);

        $newconfig = new object;
        $newconfig->id = $recordid;
        $newconfig->value = $result;
        update_record('config',$newconfig);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_config_plugins_value($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$configplugins = get_record('config_plugins', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($configplugins->value, $fromenc);

        $newconfigplugins = new object;
        $newconfigplugins->id = $recordid;
        $newconfigplugins->value = $result;
        update_record('config_plugins',$newconfigplugins);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_categories_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$coursecategories = get_record('course_categories', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($coursecategories->name, $fromenc);

        $newcoursecategories = new object;
        $newcoursecategories->id = $recordid;
        $newcoursecategories->name = $result;
        update_record('course_categories',$newcoursecategories);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_categories_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$coursecategories = get_record('course_categories', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($coursecategories->description, $fromenc);

        $newcoursecategories = new object;
        $newcoursecategories->id = $recordid;
        $newcoursecategories->description = $result;
        update_record('course_categories',$newcoursecategories);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_sections_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    
    if (!$coursesections = get_record('course_sections', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($coursesections->course);  //Non existing!
        $userlang   = get_main_teacher_lang($coursesections->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($coursesections->summary, $fromenc);

        $newcoursesections = new object;
        $newcoursesections->id = $recordid;
        $newcoursesections->summary = $result;
        update_record('course_sections',$newcoursesections);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_fullname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->fullname, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->fullname = $result;
        update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_shortname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->shortname, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->shortname = $result;
        update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->summary, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->summary = $result;
        update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_reason($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->reason, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->reason = $result;
        update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_password($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$courserequest = get_record('course_request', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$courserequest->requester);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($courserequest->password, $fromenc);

        $newcourserequest = new object;
        $newcourserequest->id = $recordid;
        $newcourserequest->password = $result;
        update_record('course_request',$newcourserequest);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_grade_category_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$gradecategory = get_record('grade_category', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($gradecategory->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($gradecategory->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($gradecategory->name, $fromenc);

        $newgradecategory = new object;
        $newgradecategory->id = $recordid;
        $newgradecategory->name = $result;
        update_record('grade_category',$newgradecategory);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_grade_letter_letter($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$gradeletter = get_record('grade_letter', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($gradeletter->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($gradeletter->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($gradeletter->letter, $fromenc);

        $newgradeletter = new object;
        $newgradeletter->id = $recordid;
        $newgradeletter->letter = $result;
        update_record('grade_letter',$newgradeletter);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($group->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($group->name, $fromenc);

        $newgroup = new object;
        $newgroup->id = $recordid;
        $newgroup->name = $result;
        update_record('groups',$newgroup);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($group->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($group->description, $fromenc);

        $newgroup = new object;
        $newgroup->id = $recordid;
        $newgroup->description = $result;
        update_record('groups',$newgroup);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_lang($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($group->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($group->lang, $fromenc);

        $newgroup = new object;
        $newgroup->id = $recordid;
        $newgroup->lang = $result;
        update_record('groups',$newgroup);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_password($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($group->courseid);  //Non existing!
        $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($group->password, $fromenc);

        $newgroup = new object;
        $newgroup->id = $recordid;
        $newgroup->password = $result;
        update_record('groups',$newgroup);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_message_message($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$message = get_record('message', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$message->useridfrom);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($message->message, $fromenc);

        $newmessage = new object;
        $newmessage->id = $recordid;
        $newmessage->message = $result;
        update_record('message',$newmessage);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_message_read_message($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$messageread = get_record('message_read', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$messageread->useridfrom);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($messageread->message, $fromenc);

        $newmessageread = new object;
        $newmessageread->id = $recordid;
        $newmessageread->message = $result;
        update_record('message_read',$newmessageread);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_modules_search($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$modules = get_record('modules', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($modules->search, $fromenc);

        $newmodules = new object;
        $newmodules->id = $recordid;
        $newmodules->search = $result;
        update_record('modules',$newmodules);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_idnumber($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->idnumber, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->idnumber = $result;
        update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_firstname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->firstname, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->firstname = $result;
        update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_lastname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->lastname, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->lastname = $result;
        update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_institution($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->institution , $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->institution = $result;
        update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_department($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->department, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->department = $result;
        update_record('user',$newuser);
    /// And finally, just return the converted field
    }
    return $result;
}

function migrate2utf8_user_address($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->address, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->address = $result;
        update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_city($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->city, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->city = $result;
        update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->description, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->description = $result;
        update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_secret($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;  //Non existing!
        $userlang   = $user->lang; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($user->secret, $fromenc);

        $newuser = new object;
        $newuser->id = $recordid;
        $newuser->secret = $result;
        update_record('user',$newuser);
    }
/// And finally, just return the converted field
    return $result;
}

//this chnages user->lang from xyz to xyz_utf8, if not already using utf8
function migrate2utf8_user_lang($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    if (strstr($user->lang, 'utf8') === false) {    //user not using utf8 lang
        $user->lang = $user->lang.'_utf8';
    }

    $newuser = new object;
    $newuser->id = $user->id;
    $newuser->lang = $user->lang;
    $result = update_record('user',$newuser);
    
    $langsused = get_record('config','name','langsused');
    $langs = explode(',',$langsused->value);
    if (!in_array($user->lang, $langs)) {
        $langsused->value .= ','.$user->lang;
        update_record('config',$langsused);
    }
    

/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_password($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->password, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->password = $result;
        update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_fullname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->fullname, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->fullname = $result;
        update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_shortname($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->shortname, $fromenc);
        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->shortname = $result;
        update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_idnumber($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->idnumber, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->idnumber = $result;
        update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->summary, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->summary = $result;
        update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_modinfo($recordid){
    global $CFG, $globallang;
    //print_object($mods);
}

function migrate2utf8_course_teacher($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->teacher, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->teacher = $result;
        update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_teachers($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->teachers, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->teachers = $result;
        update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_student($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->student, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->student = $result;
        update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_students($recordid){
    global $CFG, $globallang;
/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->students, $fromenc);

        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->students = $result;
        update_record('course',$newcourse);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_cost($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->id);  //Non existing!
        $userlang   = get_main_teacher_lang($course->id); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($course->cost, $fromenc);
        $newcourse = new object;
        $newcourse->id = $recordid;
        $newcourse->cost = $result;
        update_record('course',$newcourse);
    /// And finally, just return the converted field
    }
    return $result;
}

function migrate2utf8_course_lang($recordid){
    global $CFG, $globallang;

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (strstr($course->lang,'utf8')===false and !empty($course->lang)){
        $course->lang = $course->lang.'_utf8';
    }
    $newcourse = new object;
    $newcourse->id = $course->id;
    $newcourse->lang = $course->lang;
    update_record('course',$newcourse);
    require_once($CFG->dirroot.'/course/lib.php');
    $result = rebuild_course_cache($recordid);    //takes care of serialized modinfo
/// And finally, just return the converted field


    $langsused = get_record('config','name','langsused');
    $langs = explode(',',$langsused->value);
    if (!in_array($course->lang, $langs)) {
        $langsused->value .= ','.$course->lang;
        update_record('config',$langsused);
    }

    return $result;
}
?>
