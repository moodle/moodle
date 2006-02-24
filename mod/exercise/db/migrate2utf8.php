<?
function migrate2utf8_exercise_elements_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT ex.course
           FROM {$CFG->prefix}exercise ex,
                {$CFG->prefix}exercise_elements exe
           WHERE ex.id = exe.exerciseid
                 AND exe.id = $recordid";

    if (!$exercise = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }
    
    if (!$exerciseelement = get_record('exercise_elements','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($exercise->course);  //Non existing!
        $userlang   = get_main_teacher_lang($exercise->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($exerciseelement->description, $fromenc);

        $newexerciseelement = new object;
        $newexerciseelement->id = $recordid;
        $newexerciseelement->description = $result;
        update_record('exercise_elements',$newexerciseelement);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_exercise_grades_feedback($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT ex.course
           FROM {$CFG->prefix}exercise ex,
                {$CFG->prefix}exercise_grades exg
           WHERE ex.id = exg.exerciseid
                 AND exg.id = $recordid";

    if (!$exercise = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$exercisegrade = get_record('exercise_grades','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($exercise->course);  //Non existing!
        $userlang   = get_main_teacher_lang($exercise->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($exercisegrade->feedback, $fromenc);

        $newexercisegrade = new object;
        $newexercisegrade->id = $recordid;
        $newexercisegrade->feedback = $result;
        update_record('exercise_grades',$newexercisegrade);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_exercise_rubrics_description($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT ex.course
           FROM {$CFG->prefix}exercise ex,
                {$CFG->prefix}exercise_rubrics exr
           WHERE ex.id = exr.exerciseid
                 AND exr.id = $recordid";

    if (!$exercise = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$exerciserubric = get_record('exercise_rubrics','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($exercise->course);  //Non existing!
        $userlang   = get_main_teacher_lang($exercise->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($exerciserubric->description, $fromenc);

        $newexerciserubric = new object;
        $newexerciserubric->id = $recordid;
        $newexerciserubric->description = $result;
        update_record('exercise_rubrics',$newexerciserubric);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_exercise_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$exercise = get_record('exercise','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($exercise->course);  //Non existing!
        $userlang   = get_main_teacher_lang($exercise->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($exercise->name, $fromenc);

        $newexercise = new object;
        $newexercise->id = $recordid;
        $newexercise->name = $result;
        update_record('exercise',$newexercise);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_exercise_password($recordid){
    global $CFG, $globallang;

    ///um
}
?>
