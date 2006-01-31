<?
function migrate2utf8_label_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$label = get_record('label','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($label->course);  //Non existing!
    $userlang   = get_main_teacher_lang($label->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    
/// Convert the text
    $result = utfconvert($label->name, $fromenc);

    $newlabel = new object;
    $newlabel->id = $recordid;
    $newlabel->name = $result;
    update_record('label',$newlabel);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_label_content($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$label = get_record('label','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($label->course);  //Non existing!
    $userlang   = get_main_teacher_lang($label->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    
/// Convert the text
    $result = utfconvert($label->content, $fromenc);

    $newlabel = new object;
    $newlabel->id = $recordid;
    $newlabel->content = $result;
    update_record('label',$newlabel);
/// And finally, just return the converted field
    return $result;
}
?>
