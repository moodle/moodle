<?

function migrate2utf8_config_value($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$config = get_record('config', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = null; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($config->value, $fromenc);

    $newconfig = new object;
    $newconfig->id = $recordid;
    $newconfig->value = $result;
    update_record('config',$newconfig);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_config_plugins_value($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$configplugins = get_record('config_plugins', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = null; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($configplugins->value, $fromenc);

    $newconfigplugins = new object;
    $newconfigplugins->id = $recordid;
    $newconfigplugins->value = $result;
    update_record('config_plugins',$newconfigplugins);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_categories_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$coursecategories = get_record('course_categories', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = null; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($coursecategories->name, $fromenc);

    $newcoursecategories = new object;
    $newcoursecategories->id = $recordid;
    $newcoursecategories->name = $result;
    update_record('course_categories',$newcoursecategories);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_categories_description($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$coursecategories = get_record('course_categories', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = null; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($coursecategories->description, $fromenc);

    $newcoursecategories = new object;
    $newcoursecategories->id = $recordid;
    $newcoursecategories->description = $result;
    update_record('course_categories',$newcoursecategories);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_sections_summary($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    
    if (!$coursesections = get_record('course_sections', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($coursesections->course);  //Non existing!
    $userlang   = get_main_teacher_lang($coursesections->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($coursesections->summary, $fromenc);

    $newcoursesections = new object;
    $newcoursesections->id = $recordid;
    $newcoursesections->summary = $result;
    update_record('course_sections',$newcoursesections);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_fullname($recordid){
    global $CFG;

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

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($courserequest->fullname, $fromenc);

    $newcourserequest = new object;
    $newcourserequest->id = $recordid;
    $newcourserequest->fullname = $result;
    update_record('course_request',$newcourserequest);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_shortname($recordid){
    global $CFG;

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

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($courserequest->shortname, $fromenc);

    $newcourserequest = new object;
    $newcourserequest->id = $recordid;
    $newcourserequest->shortname = $result;
    update_record('course_request',$newcourserequest);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_summary($recordid){
    global $CFG;

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

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($courserequest->summary, $fromenc);

    $newcourserequest = new object;
    $newcourserequest->id = $recordid;
    $newcourserequest->summary = $result;
    update_record('course_request',$newcourserequest);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_reason($recordid){
    global $CFG;

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

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($courserequest->reason, $fromenc);

    $newcourserequest = new object;
    $newcourserequest->id = $recordid;
    $newcourserequest->reason = $result;
    update_record('course_request',$newcourserequest);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_request_password($recordid){
    global $CFG;

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

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($courserequest->password, $fromenc);

    $newcourserequest = new object;
    $newcourserequest->id = $recordid;
    $newcourserequest->password = $result;
    update_record('course_request',$newcourserequest);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_cache_filters_rawtext($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$cachefilters = get_record('cache_filters', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = null; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($cachefilters->rawtext, $fromenc);

    $newcachefilters = new object;
    $newcachefilters->id = $recordid;
    $newcachefilters->rawtext = $result;
    update_record('cache_filters',$newcachefilters);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_cache_text_formattedtext($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$cachetext = get_record('cache_text', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = null; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($cachetext->formattedtext, $fromenc);

    $newcachetext = new object;
    $newcachetext->id = $recordid;
    $newcachetext->formattedtext = $result;
    update_record('cache_text',$newcachetext);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_grade_category_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$gradecategory = get_record('grade_category', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($gradecategory->courseid);  //Non existing!
    $userlang   = get_main_teacher_lang($gradecategory->courseid); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($gradecategory->name, $fromenc);

    $newgradecategory = new object;
    $newgradecategory->id = $recordid;
    $newgradecategory->name = $result;
    update_record('grade_category',$newgradecategory);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_grade_letter_letter($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$gradeletter = get_record('grade_letter', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($gradeletter->courseid);  //Non existing!
    $userlang   = get_main_teacher_lang($gradeletter->courseid); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($gradeletter->letter, $fromenc);

    $newgradeletter = new object;
    $newgradeletter->id = $recordid;
    $newgradeletter->letter = $result;
    update_record('grade_letter',$newgradeletter);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($group->courseid);  //Non existing!
    $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($group->name, $fromenc);

    $newgroup = new object;
    $newgroup->id = $recordid;
    $newgroup->name = $result;
    update_record('groups',$newgroup);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_description($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($group->courseid);  //Non existing!
    $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($group->description, $fromenc);

    $newgroup = new object;
    $newgroup->id = $recordid;
    $newgroup->description = $result;
    update_record('groups',$newgroup);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_lang($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($group->courseid);  //Non existing!
    $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($group->lang, $fromenc);

    $newgroup = new object;
    $newgroup->id = $recordid;
    $newgroup->lang = $result;
    update_record('groups',$newgroup);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_groups_theme($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$group = get_record('groups', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($group->courseid);  //Non existing!
    $userlang   = get_main_teacher_lang($group->courseid); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($group->theme, $fromenc);

    $newgroup = new object;
    $newgroup->id = $recordid;
    $newgroup->theme = $result;
    update_record('groups',$newgroup);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_message_message($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$message = get_record('message_read', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$message->useridfrom);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($message->message, $fromenc);

    $newmessage = new object;
    $newmessage->id = $recordid;
    $newmessage->message = $result;
    update_record('message_read',$newmessage);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_message_read_message($recordid){
    global $CFG;

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

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($messageread->message, $fromenc);

    $newmessageread = new object;
    $newmessageread->id = $recordid;
    $newmessageread->message = $result;
    update_record('message_read',$newmessage);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_modules_search($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$modules = get_record('modules', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    
    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = null; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($modules->search, $fromenc);

    $newmodules = new object;
    $newmodules->id = $recordid;
    $newmodules->search = $result;
    update_record('modules',$newmodules);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_password($recordid){



}

function migrate2utf8_user_idnumber($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($user->idnumber, $fromenc);

    $newuser = new object;
    $newuser->id = $recordid;
    $newuser->idnumber = $result;
    update_record('user',$newuser);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_firstname($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($user->firstname, $fromenc);

    $newuser = new object;
    $newuser->id = $recordid;
    $newuser->firstname = $result;
    update_record('user',$newuser);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_lastname($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($user->lastname, $fromenc);

    $newuser = new object;
    $newuser->id = $recordid;
    $newuser->lastname = $result;
    update_record('user',$newuser);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_institution($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($user->institution , $fromenc);

    $newuser = new object;
    $newuser->id = $recordid;
    $newuser->institution = $result;
    update_record('user',$newuser);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_department($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($user->department, $fromenc);

    $newuser = new object;
    $newuser->id = $recordid;
    $newuser->department = $result;
    update_record('user',$newuser);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_address($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($user->address, $fromenc);

    $newuser = new object;
    $newuser->id = $recordid;
    $newuser->address = $result;
    update_record('user',$newuser);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_city($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($user->city, $fromenc);

    $newuser = new object;
    $newuser->id = $recordid;
    $newuser->city = $result;
    update_record('user',$newuser);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_description($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($user->description, $fromenc);
echo "useruseruser ".($result);
    $newuser = new object;
    $newuser->id = $recordid;
    $newuser->description = $result;
    update_record('user',$newuser);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_user_secret($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$recordid);

    $sitelang   = $CFG->lang;
    $courselang = null;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($user->secret, $fromenc);

    $newuser = new object;
    $newuser->id = $recordid;
    $newuser->secret = $result;
    update_record('user',$newuser);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_password($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->password, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->password = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_fullname($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->fullname, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->fullname = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_shortname($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->shortname, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->shortname = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_idnumber($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->idnumber, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->idnumber = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_summary($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->summary, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->summary = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_modinfo($recordid){
    global $CFG;

}

function migrate2utf8_course_teacher($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->teacher, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->teacher = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_teachers($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->teachers, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->teachers = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_student($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->student, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->student = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_students($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->students, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->students = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_course_cost($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$course = get_record('course', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($course->id);  //Non existing!
    $userlang   = get_main_teacher_lang($course->id); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($course->cost, $fromenc);

    $newcourse = new object;
    $newcourse->id = $recordid;
    $newcourse->cost = $result;
    update_record('course',$newcourse);
/// And finally, just return the converted field
    return $result;
}
?>
