<?
function migrate2utf8_resource_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$resource = get_record('resource','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($resource->course);  //Non existing!
        $userlang   = get_main_teacher_lang($resource->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
/// Convert the text
        $result = utfconvert($resource->name, $fromenc);

        $newresource = new object;
        $newresource->id = $recordid;
        $newresource->name = $result;
        update_record('resource',$newresource);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_resource_reference($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$resource = get_record('resource','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($resource->course);  //Non existing!
        $userlang   = get_main_teacher_lang($resource->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($resource->reference, $fromenc);

        $newresource = new object;
        $newresource->id = $recordid;
        $newresource->reference = $result;
        update_record('resource',$newresource);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_resource_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$resource = get_record('resource','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($resource->course);  //Non existing!
        $userlang   = get_main_teacher_lang($resource->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($resource->summary, $fromenc);
        $newresource = new object;
        $newresource->id = $recordid;
        $newresource->summary = $result;
        update_record('resource',$newresource);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_resource_alltext($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$resource = get_record('resource','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($resource->course);  //Non existing!
        $userlang   = get_main_teacher_lang($resource->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($resource->alltext, $fromenc);

        $newresource = new object;
        $newresource->id = $recordid;
        $newresource->alltext = $result;
        update_record('resource',$newresource);
    }
/// And finally, just return the converted field
    return $result;
}
?>
