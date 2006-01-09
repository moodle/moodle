<?

function migrate2utf8_survey_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$survey = get_record('survey','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($survey->course);  //Non existing!
    $userlang   = get_main_teacher_lang($survey->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($survey->name, $fromenc);

    $newsurvey = new object;
    $newsurvey->id = $recordid;
    $newsurvey->name = $result;
    update_record('survey',$newsurvey);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_survey_intro($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$survey = get_record('survey','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($survey->course);  //Non existing!
    $userlang   = get_main_teacher_lang($survey->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($survey->intro, $fromenc);

    $newsurvey = new object;
    $newsurvey->id = $recordid;
    $newsurvey->intro = $result;
    update_record('survey',$newsurvey);
/// And finally, just return the converted field
    return $result;
}
?>
