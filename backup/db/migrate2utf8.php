<?
function migrate2utf8_backup_log_info($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$backuplog = get_record('backup_log', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = get_course_lang($backuplog->courseid);  //Non existing!
    $userlang   = get_main_teacher_lang($backuplog->courseid); //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($backuplog->info, $fromenc);

    $newbackuplog = new object;
    $newbackuplog->id = $recordid;
    $newbackuplog->info = $result;
    update_record('backup_log',$newbackuplog);
/// And finally, just return the converted field
    return $result;
}


function migrate2utf8_backup_ids_info($recordid){
    global $CFG;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$backupids= get_record('backup_ids', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    $sitelang   = $CFG->lang;
    $courselang = null;
    $userlang   = null; //N.E.!!

    $fromenc = get_original_encoding($sitelang, $courselang, $userlang);

/// We are going to use textlib facilities
    $textlib = textlib_get_instance();
/// Convert the text
    $result = $textlib->convert($backupids->info, $fromenc);

    $newbackupids = new object;
    $newbackupids->id = $recordid;
    $newbackupids->info = $result;
    update_record('backup_ids',$newbackupids);
/// And finally, just return the converted field
    return $result;
}
?>
