<?
function migrate2utf_workshop_stockcomments_comments($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT w.course
           FROM {$CFG->prefix}workshop w,
                {$CFG->prefix}workshop_stockcomments ws
           WHERE w.id = ws.workshopid
                 AND ws.id = $recordid";
                 
    if (!$workshop = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$workshopstockcomments = get_record('workshop_stockcomments','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($workshop->course);  //Non existing!
    $userlang   = get_main_teacher_lang($workshop->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($workshopstockcomments->comment, $fromenc);

    $newworkshopstockcomments = new object;
    $newworkshopstockcomments->id = $recordid;
    $newworkshopstockcomments->comment = $result;
    update_record('workshop_stockcomments',$newworkshopstockcomments);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_workshop_rubrics_description($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT w.course
           FROM {$CFG->prefix}workshop w,
                {$CFG->prefix}workshop_stockcomments ws
           WHERE w.id = ws.workshopid
                 AND ws.id = $recordid";

    if (!$workshop = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$workshoprubrics = get_record('workshop_stockcomments','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($workshop->course);  //Non existing!
    $userlang   = get_main_teacher_lang($workshop->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($workshoprubrics->description, $fromenc);

    $newworkshoprubrics = new object;
    $newworkshoprubrics->id = $recordid;
    $newworkshoprubrics->description = $result;
    update_record('workshop_rubricss',$newworkshoprubrics);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_workshop_grades_feedback($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT w.course
           FROM {$CFG->prefix}workshop w,
                {$CFG->prefix}workshop_grades wg
           WHERE w.id = wg.workshopid
                 AND wg.id = $recordid";

    if (!$workshop = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$workshopgrades = get_record('workshop_grades','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($workshop->course);  //Non existing!
    $userlang   = get_main_teacher_lang($workshop->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($workshopgrades->feedback, $fromenc);

    $newworkshopgrades = new object;
    $newworkshopgrades->id = $recordid;
    $newworkshopgrades->feedback = $result;
    update_record('workshop_grades',$newworkshopgrades);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_workshop_elements_description($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT w.course
           FROM {$CFG->prefix}workshop w,
                {$CFG->prefix}workshop_elements we
           WHERE w.id = we.workshopid
                 AND we.id = $recordid";

    if (!$workshop = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$workshopelements = get_record('workshop_elements','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($workshop->course);  //Non existing!
    $userlang   = get_main_teacher_lang($workshop->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($workshopelements->description, $fromenc);

    $newworkshopelements = new object;
    $newworkshopelements->id = $recordid;
    $newworkshopelements->description = $result;
    update_record('workshop_elements',$newworkshopelements);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_workshop_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$workshop = get_record('workshop','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($workshop->course);  //Non existing!
    $userlang   = get_main_teacher_lang($workshop->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($workshop->name, $fromenc);

    $newworkshop = new object;
    $newworkshop->id = $recordid;
    $newworkshop->name = $result;
    update_record('workshop',$newworkshop);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_workshop_description($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$workshop = get_record('workshop','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($workshop->course);  //Non existing!
    $userlang   = get_main_teacher_lang($workshop->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($workshop->description, $fromenc);

    $newworkshop = new object;
    $newworkshop->id = $recordid;
    $newworkshop->description = $result;
    update_record('workshop',$newworkshop);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_workshop_password($recordid){
    global $CFG;
}
?>
