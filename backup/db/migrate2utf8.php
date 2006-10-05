<?php // $Id$

function migrate2utf8_backup_ids_info($recordid){
    global $CFG, $globallang;

/// Some trivial checks
    if (empty($recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if (!$backupids= get_record('backup_ids', 'id', $recordid)) {
        log_the_problem_somewhere();
        return false;
    }

    if ($globallang) {
        $fromenc = $globallang;
    } else {
        $sitelang   = $CFG->lang;
        $courselang = null;
        $userlang   = null; //N.E.!!

        $fromenc = get_original_encoding($sitelang, $courselang, $userlang);
    }

/// Initialise $result
    $result = $backupids->info;

/// Convert the text
    if (($fromenc != 'utf-8') && ($fromenc != 'UTF-8')) {
        $result = utfconvert($backupids->info, $fromenc);

        $newbackupids = new object;
        $newbackupids->id = $recordid;
        $newbackupids->info = $result;
        migrate2utf8_update_record('backup_ids',$newbackupids);
    }
/// And finally, just return the converted field
    return $result;
}
?>
