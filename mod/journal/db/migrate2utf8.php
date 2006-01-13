<?
function migrate2utf8_journal_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$journal = get_record('journal','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($journal->course);  //Non existing!
    $userlang   = get_main_teacher_lang($journal->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($journal->name, $fromenc);

    $newjournal = new object;
    $newjournal->id = $recordid;
    $newjournal->name = $result;
    update_record('journal',$newjournal);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_journal_intro($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$journal = get_record('journal','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($journal->course);  //Non existing!
    $userlang   = get_main_teacher_lang($journal->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($journal->intro, $fromenc);

    $newjournal = new object;
    $newjournal->id = $recordid;
    $newjournal->intro = $result;
    update_record('journal',$newjournal);
/// And finally, just return the converted field
    return $result;
}
?>
