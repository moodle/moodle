<?
function migrate2utf8_chat_name($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if (!$chat = get_record('chat', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($chat->course);  //Non existing!
    $userlang   = get_main_teacher_lang($chat->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($assignment->name, $fromenc);

    $newchat = new object;
    $newchat->id = $recordid;
    $newchat->name = $result;
    update_record('chat',$newchat);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf8_chat_intro($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }
    if (!$chat = get_record('chat', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($chat->course);  //Non existing!
    $userlang   = get_main_teacher_lang($chat->course); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($assignment->intro, $fromenc);

    $newchat = new object;
    $newchat->id = $recordid;
    $newchat->intro = $result;
    update_record('chat',$newchat);
/// And finally, just return the converted field
    return $result;
}

?>
