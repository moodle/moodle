<?
function migrate2utf8_lams_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$lams = get_record('lams','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($lams->course);  //Non existing!
        $userlang   = get_main_teacher_lang($lams->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($lams->name, $fromenc);

        $newlams = new object;
        $newlams->id = $recordid;
        $newlams->name = $result;
        update_record('lams',$newlams);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_lams_introduction($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$lams = get_record('lams','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($lams->course);  //Non existing!
        $userlang   = get_main_teacher_lang($lams->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($lams->introduction, $fromenc);

        $newlams = new object;
        $newlams->id = $recordid;
        $newlams->introduction = $result;
        update_record('lams',$newlams);
    }
/// And finally, just return the converted field
    return $result;
}
?>
