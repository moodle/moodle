<?
function migrate2utf_scorm_scoes_manifest($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }
    
    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }
 
    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->manifest, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->manifest = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_organization($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->organization, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->organization = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_parent($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->parent, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->parent = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}


function migrate2utf_scorm_scoes_identifier($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->identifier, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->identifier = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_launch($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->launch, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->launch = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_parameters($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->parameters, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->parameters = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_scormtype($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->scormtype, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->scormtype = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_title($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->title, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->title = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_prerequisites($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->prerequisites, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->prerequisites = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_maxtimeallowed($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->maxtimeallowed, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->maxtimeallowed = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_timelimitaction($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->timelimitaction, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->timelimitaction = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_datafromlms($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->datafromlms, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->datafromlms = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_scoes_masteryscore($recordid){
    global $CFG;

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

    if (!$scorm = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scormscoes = get_record('scorm_scoes','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scormscoes->masteryscore, $fromenc);

    $newscormscoes = new object;
    $newscormscoes->id = $recordid;
    $newscormscoes->masteryscore = $result;
    update_record('scorm_scoes',$newscormscoes);
/// And finally, just return the converted field
    return $result;
}


function migrate2utf_scorm_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scorm = get_record('scorm','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scorm->name, $fromenc);

    $newscorm = new object;
    $newscorm->id = $recordid;
    $newscorm->name = $result;
    update_record('scorm',$newscorm);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_reference($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scorm = get_record('scorm','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scorm->reference, $fromenc);

    $newscorm = new object;
    $newscorm->id = $recordid;
    $newscorm->reference = $result;
    update_record('scorm',$newscorm);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_summary($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scorm = get_record('scorm','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scorm->summary, $fromenc);

    $newscorm = new object;
    $newscorm->id = $recordid;
    $newscorm->summary = $result;
    update_record('scorm',$newscorm);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_scorm_options($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$scorm = get_record('scorm','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($scorm->course);  //Non existing!
    $userlang   = get_main_teacher_lang($scorm->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($scorm->options, $fromenc);

    $newscorm = new object;
    $newscorm->id = $recordid;
    $newscorm->options = $result;
    update_record('scorm',$newscorm);
/// And finally, just return the converted field
    return $result;
}
?>
