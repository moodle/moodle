<?
function migrate2utf8_wiki_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$wiki = get_record('wiki','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($wiki->course);  //Non existing!
    $userlang   = get_main_teacher_lang($wiki->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($wiki->name, $fromenc);

    $newwiki = new object;
    $newwiki->id = $recordid;
    $newwiki->name = $result;
    update_record('wiki',$newwiki);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_wiki_summary($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$wiki = get_record('wiki','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($wiki->course);  //Non existing!
    $userlang   = get_main_teacher_lang($wiki->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($wiki->summary, $fromenc);

    $newwiki = new object;
    $newwiki->id = $recordid;
    $newwiki->summary = $result;
    update_record('wiki',$newwiki);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_wiki_pagename($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$wiki = get_record('wiki','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($wiki->course);  //Non existing!
    $userlang   = get_main_teacher_lang($wiki->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($wiki->pagename, $fromenc);

    $newwiki = new object;
    $newwiki->id = $recordid;
    $newwiki->pagename = $result;
    update_record('wiki',$newwiki);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_wiki_initialcontent($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$wiki = get_record('wiki','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($wiki->course);  //Non existing!
    $userlang   = get_main_teacher_lang($wiki->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($wiki->initialcontent, $fromenc);

    $newwiki = new object;
    $newwiki->id = $recordid;
    $newwiki->initialcontent = $result;
    update_record('wiki',$newwiki);
/// And finally, just return the converted field
    return $result;
}
?>
