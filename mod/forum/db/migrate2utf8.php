<?
function migrate2utf8_forum_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$forum = get_record('forum','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($forum->course);  //Non existing!
    $userlang   = get_main_teacher_lang($forum->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    
/// Convert the text
    $result = utfconvert($forum->name, $fromenc);

    $newforum = new object;
    $newforum->id = $recordid;
    $newforum->name = $result;
    update_record('forum',$newforum);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_forum_intro($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$forum = get_record('forum','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($forum->course);  //Non existing!
    $userlang   = get_main_teacher_lang($forum->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    
/// Convert the text
    $result = utfconvert($forum->intro, $fromenc);

    $newforum = new object;
    $newforum->id = $recordid;
    $newforum->intro = $result;
    update_record('forum',$newforum);
/// And finally, just return the converted field
    return $result;
}
?>
