<?PHP // $Id$

function scorm_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality
    global $CFG;
    if ($oldversion < 2004033000) {
    	table_column("scorm", "", "auto", "integer", "1", "", "0", "NOT NULL", "summary"); 
    }
    if ($oldversion < 2004040900) {
        table_column("scorm_sco_users", "", "cmi_core_score_raw", "real", "3", "", "0", "NOT NULL", "cmi_core_session_time");
    }
    if ($oldversion < 2004061800) {
    	table_column("scorm", "", "popup", "varchar", "255", "", "", "NOT NULL", "auto");
    	table_column("scorm", "reference", "reference", "varchar", "255", "", "", "NOT NULL");
    }
    if ($oldversion < 2004070800) {
    	table_column("scorm_scoes", "", "datafromlms", "TEXT", "", "", "", "NOT NULL", "title");
    	modify_database("", "ALTER TABLE `{$CFG->prefix}scorm_sco_users` DROP `cmi_launch_data`;");
    }
    return true;
}


?>

