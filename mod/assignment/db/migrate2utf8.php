<?
function migrate2utf8_assignment_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if (!$assignment = get_record('assignment', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($assignment->course);  //Non existing!
        $userlang   = get_main_teacher_lang($assignment->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($assignment->name, $fromenc);

        $newassignment = new object;
        $newassignment->id = $recordid;
        $newassignment->name = $result;
        update_record('assignment',$newassignment);
    }
/// And finally, just return the converted field

    return $result;
}

function migrate2utf8_assignment_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if (!$assignment = get_record('assignment', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($assignment->course);  //Non existing!
        $userlang   = get_main_teacher_lang($assignment->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($assignment->description, $fromenc);

        $newassignment = new object;
        $newassignment->id = $recordid;
        $newassignment->description = $result;
        update_record('assignment',$newassignment);
    }
/// And finally, just return the converted field
    return $result;
}

?>
