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
    	modify_database("", "ALTER TABLE {$CFG->prefix}scorm_sco_users DROP cmi_launch_data;");
    }
    if ($oldversion < 2004071700) {
    	table_column("scorm_scoes", "", "manifest", "VARCHAR", "255", "", "", "NOT NULL", "scorm");
    	table_column("scorm_scoes", "", "organization", "VARCHAR", "255", "", "", "NOT NULL", "manifest");
    }
    if ($oldversion < 2004071900) {
        table_column("scorm", "", "maxgrade", "real", "3", "", "0", "NOT NULL", "reference");
        table_column("scorm", "", "grademethod", "integer", "", "", "0", "NOT NULL", "maxgrade");
    }
    
    if ($oldversion < 2004083124) {
        modify_database('','CREATE INDEX prefix_scorm_course_idx ON prefix_scorm (course);');
        modify_database('','CREATE INDEX prefix_scorm_scoes_scorm_idx ON prefix_scorm_scoes (scorm);');
        modify_database('','CREATE INDEX prefix_scorm_sco_users_userid_idx ON  prefix_scorm_sco_users (userid);');
        modify_database('','CREATE INDEX prefix_scorm_sco_users_scormid_idx ON  prefix_scorm_sco_users (scormid);');
        modify_database('','CREATE INDEX prefix_scorm_sco_users_scoid_idx ON  prefix_scorm_sco_users (scoid);');
    }

    return true;
}


?>

