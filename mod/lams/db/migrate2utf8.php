<?
function migrate2utf_lams_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$lams = get_record('lams','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($lams->course);  //Non existing!
    $userlang   = get_main_teacher_lang($lams->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($lams->name, $fromenc);

    $newlams = new object;
    $newlams->id = $recordid;
    $newlams->name = $result;
    update_record('lams',$newlams);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_lams_introduction($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$lams = get_record('lams','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($lams->course);  //Non existing!
    $userlang   = get_main_teacher_lang($lams->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($lams->introduction, $fromenc);

    $newlams = new object;
    $newlams->id = $recordid;
    $newlams->introduction = $result;
    update_record('lams',$newlams);
/// And finally, just return the converted field
    return $result;
}
?>
