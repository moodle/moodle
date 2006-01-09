<?
function migrate2utf_block_rss_client_title($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$rssclient = get_record('block_rss_client','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$rssclient->userid);

    $sitelang   = $CFG->lang;
    $courselang = NULL;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($rssclient->title, $fromenc);

    $newrssclient = new object;
    $newrssclient->id = $recordid;
    $newrssclient->title = $result;
    update_record('block_rss_client',$newrssclient);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_block_rss_client_preferredtitle($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT brc.userid
           FROM {$CFG->prefix}block_rss_client brc
           WHERE brc.id = $recordid";

    if (!$rssuserid = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$rssclient = get_record('block_rss_client','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$rssuserid->userid);

    $sitelang   = $CFG->lang;
    $courselang = NULL;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($rssclient->preferredtitle, $fromenc);

    $newrssclient = new object;
    $newrssclient->id = $recordid;
    $newrssclient->preferredtitle = $result;
    update_record('block_rss_client',$newrssclient);
/// And finally, just return the converted field
    return $result;
}

function migrate2utf_block_rss_client_description($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $SQL = "SELECT brc.userid
           FROM {$CFG->prefix}block_rss_client brc
           WHERE brc.id = $recordid";

    if (!$rssuserid = get_record_sql($SQL) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$rssclient = get_record('block_rss_client','id',$recordid) {
        log_the_problem_somewhere();
        return false;
    }

    $user = get_record('user','id',$rssuserid->userid);

    $sitelang   = $CFG->lang;
    $courselang = NULL;  //Non existing!
    $userlang   = $user->lang; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($rssclient->description, $fromenc);

    $newrssclient = new object;
    $newrssclient->id = $recordid;
    $newrssclient->description = $result;
    update_record('block_rss_client',$newrssclient);
/// And finally, just return the converted field
    return $result;
}
?>
