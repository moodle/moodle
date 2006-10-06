<?php // $Id$

// fix for MDL-6336
// handling serialized object
function migrate2utf8_lesson_attempts_useranswer($recordid) {
    global $CFG, $globallang;
  
    /// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record_sql("SELECT la.userid
           FROM {$CFG->prefix}lesson_attempts la
           WHERE la.id=$recordid");

    $course = get_record_sql("SELECT l.course
           FROM {$CFG->prefix}lesson l,
                {$CFG->prefix}lesson_attempts la
           WHERE l.id = la.lessonid
                 AND la.id = $recordid");
  
    if (!$lessonattempts = get_record('lesson_attempts','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
  
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($course->course);
        $userlang   = get_user_lang($user->userid);        
        $fromencstudent = get_original_encoding($sitelang, $courselang, $userlang); // this is used for answer field
        $userlang = get_main_teacher_lang($course->course);       
        $fromencteacher = get_original_encoding($sitelang, $courselang, $userlang); // this is used for response field       
    }
    
    $result = $lessonattempts->useranswer; // init to avoid warnings
    // if unserialize success, meaning it is an object
    if ($attempt = unserialize($lessonattempts->useranswer)) {
        $attempt->answer = utfconvert($attempt->answer, $fromencstudent);
        $attempt->response = utfconvert($attempt->response, $fromencteacher);
        $newla = new object;
        $newla->id = $recordid;
        $newla->useranswer = serialize($attempt); // serialize it back    
        migrate2utf8_update_record('lesson_attempts', $newla);
      
    } else { // just a string
        $result = utfconvert($lessonattempts->useranswer, $fromencstudent);
        $newla = new object;
        $newla->id = $recordid;
        $newla->useranswer =  $result;// serialize it back
        migrate2utf8_update_record('lesson_attempts', $newla);
    }
    
    return $result;
}

function migrate2utf8_lesson_answers_answer($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT l.course
           FROM {$CFG->prefix}lesson l,
                {$CFG->prefix}lesson_answers la
           WHERE l.id = la.lessonid
                 AND la.id = $recordid";

    if (!$lesson = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$lessonanswers = get_record('lesson_answers','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($lesson->course);  //Non existing!
        $userlang   = get_main_teacher_lang($lesson->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($lessonanswers->answer, $fromenc);

        $newlessonanswers = new object;
        $newlessonanswers->id = $recordid;
        $newlessonanswers->answer = $result;
        migrate2utf8_update_record('lesson_answers',$newlessonanswers);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_lesson_answers_response($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT l.course
           FROM {$CFG->prefix}lesson l,
                {$CFG->prefix}lesson_answers la
           WHERE l.id = la.lessonid
                 AND la.id = $recordid";

    if (!$lesson = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$lessonanswers = get_record('lesson_answers','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($lesson->course);  //Non existing!
        $userlang   = get_main_teacher_lang($lesson->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($lessonanswers->response, $fromenc);

        $newlessonanswers = new object;
        $newlessonanswers->id = $recordid;
        $newlessonanswers->response = $result;
        migrate2utf8_update_record('lesson_answers',$newlessonanswers);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_lesson_default_password($recordid){

    ///um

}


function migrate2utf8_lesson_pages_contents($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT l.course
           FROM {$CFG->prefix}lesson l,
                {$CFG->prefix}lesson_pages lp
           WHERE l.id = lp.lessonid
                 AND lp.id = $recordid";

    if (!$lesson = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$lessonpages = get_record('lesson_pages','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($lesson->course);  //Non existing!
        $userlang   = get_main_teacher_lang($lesson->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($lessonpages->contents, $fromenc);

        $newlessonpages = new object;
        $newlessonpages->id = $recordid;
        $newlessonpages->contents = $result;
        migrate2utf8_update_record('lesson_pages',$newlessonpages);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_lesson_pages_title($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT l.course
           FROM {$CFG->prefix}lesson l,
                {$CFG->prefix}lesson_pages lp
           WHERE l.id = lp.lessonid
                 AND lp.id = $recordid";

    if (!$lesson = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }
    if (!$lessonpages = get_record('lesson_pages','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($lesson->course);  //Non existing!
        $userlang   = get_main_teacher_lang($lesson->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($lessonpages->title, $fromenc);

        $newlessonpages = new object;
        $newlessonpages->id = $recordid;
        $newlessonpages->title = $result;
        migrate2utf8_update_record('lesson_pages',$newlessonpages);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_lesson_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$lesson = get_record('lesson','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($lesson->course);  //Non existing!
        $userlang   = get_main_teacher_lang($lesson->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($lesson->name, $fromenc);

        $newlesson = new object;
        $newlesson->id = $recordid;
        $newlesson->name = $result;
        migrate2utf8_update_record('lesson',$newlesson);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_lesson_password($recordid){
///um
}
?>
