<?php // $Id: migrate2utf8.php,v 1.1 2006/03/12 18:40:01 skodak Exp $
function migrate2utf8_book_name($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$book = get_record('book','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($book->course);  //Non existing!
        $userlang   = get_main_teacher_lang($book->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// We are going to use textlib facilities
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
/// Convert the text
        $result = utfconvert($book->name, $fromenc);

        $newbook = new object;
        $newbook->id = $recordid;
        $newbook->name = $result;
        migrate2utf8_update_record('book',$newbook);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_book_summary($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$book = get_record('book','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($book->course);  //Non existing!
        $userlang   = get_main_teacher_lang($book->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($book->summary, $fromenc);
        $newbook = new object;
        $newbook->id = $recordid;
        $newbook->summary = $result;
        migrate2utf8_update_record('book',$newbook);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_book_chapters_title($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$chapter = get_record('book_chapters','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$book = get_record('book','id',$chapter->bookid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($book->course);  //Non existing!
        $userlang   = get_main_teacher_lang($book->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($chapter->title, $fromenc);
        $newchapter = new object;
        $newchapter->id = $recordid;
        $newchapter->title = $result;
        migrate2utf8_update_record('book_chapters',$newchapter);
    }
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_book_chapters_content($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$chapter = get_record('book_chapters','id',$recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$book = get_record('book','id',$chapter->bookid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = get_course_lang($book->course);  //Non existing!
        $userlang   = get_main_teacher_lang($book->course); //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }
/// We are going to use textlib facilities
    
/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($chapter->content, $fromenc);
        $newchapter = new object;
        $newchapter->id = $recordid;
        $newchapter->content = $result;
        migrate2utf8_update_record('book_chapters',$newchapter);
    }
/// And finally, just return the converted field
    return $result;
}

?>
