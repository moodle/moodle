<?
function migrate2utf8_assignment_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if (!$assignment = get_record('assignment', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($assignment->course);  //Non existing!
    $userlang   = get_main_teacher_lang($assignment->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($assignment->name, $fromenc);
    
    
    $newassignment = new object;
    $newassignment->id = $recordid;
    $newassignment->name = $result;
    update_record('assignment',$newassignment);
/// And finally, just return the converted field

    return $result;
}

function migrate2utf8_assignment_description($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if (!$assignment = get_record('assignment', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($assignment->course);  //Non existing!
    $userlang   = get_main_teacher_lang($assignment->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($assignment->description, $fromenc);

    $newassignment = new object;
    $newassignment->id = $recordid;
    $newassignment->description = $result;
    update_record('assignment',$newassignment);
/// And finally, just return the converted field
    return $result;
}

?>
