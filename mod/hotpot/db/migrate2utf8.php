<?

function migrate2utf8_hotpot_strings_string($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$hotpotstrings = get_record('hotpot_strings','id',$recordid)) {
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
    $result = $textlib->convert($hotpotstrings->string, $fromenc);

    $newhotpotstrings = new object;
    $newhotpotstrings->id = $recordid;
    $newhotpotstrings->string = $result;
    update_record('hotpot_strings',$newhotpotstrings);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_hotpot_questions_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    $SQL = "SELECT h.course
           FROM {$CFG->prefix}hotpot h,
                {$CFG->prefix}hotpot_questions hq
           WHERE h.id = hq.hotpot
                 AND hq.id = $recordid";

    if (!$hotpot = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }
    
    if (!$hotpotquestion = get_record('hotpot_questions','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($hotpot->course);  //Non existing!
    $userlang   = get_main_teacher_lang($hotpot->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    
/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($hotpotquestion->name, $fromenc);

    $newhotpotquestion = new object;
    $newhotpotquestion->id = $recordid;
    $newhotpotquestion->name = $result;
    update_record('hotpot_questions',$newhotpotquestion);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_hotpot_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$hotpot = get_record('hotpot','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($hotpot->course);  //Non existing!
    $userlang   = get_main_teacher_lang($hotpot->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($hotpot->name, $fromenc);

    $newhotpot = new object;
    $newhotpot->id = $recordid;
    $newhotpot->name = $result;
    update_record('hotpot',$newhotpot);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_hotpot_summary($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$hotpot = get_record('hotpot','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($hotpot->course);  //Non existing!
    $userlang   = get_main_teacher_lang($hotpot->course); //N.E.!!
    
    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($hotpot->summary, $fromenc);

    $newhotpot = new object;
    $newhotpot->id = $recordid;
    $newhotpot->summary = $result;
    update_record('hotpot',$newhotpot);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_hotpot_password($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$hotpot = get_record('hotpot','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($hotpot->course);  //Non existing!
    $userlang   = get_main_teacher_lang($hotpot->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($hotpot->password, $fromenc);

    $newhotpot = new object;
    $newhotpot->id = $recordid;
    $newhotpot->password = $result;
    update_record('hotpot',$newhotpot);
/// And finally, just return the converted field
    return $result;
}
?>
