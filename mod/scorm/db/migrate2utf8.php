<?
function migrate2utf8_scorm_scoes_manifest($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }
    
    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
 
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->manifest, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->manifest = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_organization($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->organization, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->organization = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_parent($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->parent, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->parent = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}


function migrate2utf8_scorm_scoes_identifier($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->identifier, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->identifier = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_launch($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->launch, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->launch = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_parameters($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->parameters, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->parameters = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_scormtype($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->scormtype, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->scormtype = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_title($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->title, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->title = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_prerequisites($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->prerequisites, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->prerequisites = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_maxtimeallowed($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->maxtimeallowed, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->maxtimeallowed = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_timelimitaction($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->timelimitaction, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->timelimitaction = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_datafromlms($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->datafromlms, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->datafromlms = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_scoes_masteryscore($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT s.course
           FROM {$CFG->prefix}scorm s,
                {$CFG->prefix}scorm_scoes ss
           WHERE s.id = ss.scorm
                 AND ss.id = $recordid";

    if (!$scorm = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scormscoes->masteryscore, $fromenc);

        $newscormscoes = new object;
        $newscormscoes->id = $recordid;
        $newscormscoes->masteryscore = $result;
        update_record('scorm_scoes',$newscormscoes);
    }
/// And finally, just return the converted field
    return $result;
}


function migrate2utf8_scorm_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scorm = get_record('scorm','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scorm->name, $fromenc);

        $newscorm = new object;
        $newscorm->id = $recordid;
        $newscorm->name = $result;
        update_record('scorm',$newscorm);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_reference($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scorm = get_record('scorm','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scorm->reference, $fromenc);

        $newscorm = new object;
        $newscorm->id = $recordid;
        $newscorm->reference = $result;
        update_record('scorm',$newscorm);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scorm = get_record('scorm','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scorm->summary, $fromenc);

        $newscorm = new object;
        $newscorm->id = $recordid;
        $newscorm->summary = $result;
        update_record('scorm',$newscorm);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_scorm_options($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scorm = get_record('scorm','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($scorm->course);  //Non existing!
        $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($scorm->options, $fromenc);

        $newscorm = new object;
        $newscorm->id = $recordid;
        $newscorm->options = $result;
        update_record('scorm',$newscorm);
    }
/// And finally, just return the converted field
    return $result;
}
?>
