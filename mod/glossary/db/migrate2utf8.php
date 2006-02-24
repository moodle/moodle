<?
function migrate2utf8_glossary_categories_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    $SQL = "SELECT g.course
           FROM {$CFG->prefix}glossary g,
                {$CFG->prefix}glossary_categories gc
           WHERE g.id = gc.glossaryid
                 AND gc.id = $recordid";

    if (!$glossary = get_record_sql($SQL)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$glossarycategory = get_record('glossary_categories','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($glossary->course);  //Non existing!
        $userlang   = get_main_teacher_lang($glossary->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($glossarycategory->name, $fromenc);

        $newglossarycategory = new object;
        $newglossarycategory->id = $recordid;
        $newglossarycategory->name = $result;
        update_record('glossary_categories',$newglossarycategory);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_glossary_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$glossary = get_record('glossary','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($glossary->course);  //Non existing!
        $userlang   = get_main_teacher_lang($glossary->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($glossary->name, $fromenc);

        $newglossary = new object;
        $newglossary->id = $recordid;
        $newglossary->name = $result;
        update_record('glossary',$newglossary);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_glossary_intro($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$glossary = get_record('glossary','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($glossary->course);  //Non existing!
        $userlang   = get_main_teacher_lang($glossary->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($glossary->intro, $fromenc);

        $newglossary = new object;
        $newglossary->id = $recordid;
        $newglossary->intro = $result;
        update_record('glossary',$newglossary);
    }
/// And finally, just return the converted field
    return $result;
}
?>
