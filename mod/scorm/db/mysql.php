<?PHP // $Id$

function scorm_upgrade($oldversion) {
/// This function does anything necessary to upgrade
/// older versions to match current functionality
    global $CFG;
    if ($oldversion < 2004033000) {
    	execute_sql(" ALTER TABLE `{$CFG->prefix}scorm` ADD `auto` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `summary`"); 
    }
    if ($oldversion < 2004040900) {
    	execute_sql(" ALTER TABLE `{$CFG->prefix}scorm_sco_users` ADD `cmi_core_score_raw` FLOAT( 3 ) DEFAULT '0' NOT NULL AFTER `cmi_core_session_time`"); 
    }
    return true;
}


?>

