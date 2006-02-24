<?
function migrate2utf8_wiki_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$wiki = get_record('wiki','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($wiki->course);  //Non existing!
        $userlang   = get_main_teacher_lang($wiki->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($wiki->name, $fromenc);

        $newwiki = new object;
        $newwiki->id = $recordid;
        $newwiki->name = $result;
        update_record('wiki',$newwiki);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_wiki_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$wiki = get_record('wiki','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($wiki->course);  //Non existing!
        $userlang   = get_main_teacher_lang($wiki->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($wiki->summary, $fromenc);

        $newwiki = new object;
        $newwiki->id = $recordid;
        $newwiki->summary = $result;
        update_record('wiki',$newwiki);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_wiki_pagename($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$wiki = get_record('wiki','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($wiki->course);  //Non existing!
        $userlang   = get_main_teacher_lang($wiki->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($wiki->pagename, $fromenc);

        $newwiki = new object;
        $newwiki->id = $recordid;
        $newwiki->pagename = $result;
        update_record('wiki',$newwiki);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_wiki_initialcontent($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$wiki = get_record('wiki','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($wiki->course);  //Non existing!
        $userlang   = get_main_teacher_lang($wiki->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($wiki->initialcontent, $fromenc);

        $newwiki = new object;
        $newwiki->id = $recordid;
        $newwiki->initialcontent = $result;
        update_record('wiki',$newwiki);
    }
/// And finally, just return the converted field
    return $result;
}

?>
