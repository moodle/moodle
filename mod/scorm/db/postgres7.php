<?php // $Id$

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
    
    if ($oldversion < 2004111200) {
        execute_sql("DROP INDEX {$CFG->prefix}scorm_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}scorm_scoes_scorm_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}scorm_sco_users_userid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}scorm_sco_users_scormid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}scorm_sco_users_scoid_idx;",false);

        modify_database('','CREATE INDEX prefix_scorm_course_idx ON prefix_scorm (course);');
        modify_database('','CREATE INDEX prefix_scorm_scoes_scorm_idx ON prefix_scorm_scoes (scorm);');
        modify_database('','CREATE INDEX prefix_scorm_sco_users_userid_idx ON  prefix_scorm_sco_users (userid);');
        modify_database('','CREATE INDEX prefix_scorm_sco_users_scormid_idx ON  prefix_scorm_sco_users (scormid);');
        modify_database('','CREATE INDEX prefix_scorm_sco_users_scoid_idx ON  prefix_scorm_sco_users (scoid);');
    }
    
    if ($oldversion < 2005031300) {
	table_column("scorm_scoes", "", "prerequisites", "VARCHAR", "200", "", "", "NOT NULL", "title");
	table_column("scorm_scoes", "", "maxtimeallowed", "VARCHAR", "13", "", "", "NOT NULL", "prerequisites");
	table_column("scorm_scoes", "", "timelimitaction", "VARCHAR", "19", "", "", "NOT NULL", "maxtimeallowed");
	table_column("scorm_scoes", "", "masteryscore", "VARCHAR", "200", "", "", "NOT NULL", "datafromlms");
	
	$oldScoesData = get_records_select("scorm_scoes",null,"id ASC");
	table_column("scorm_scoes", "type", "scormtype", "VARCHAR", "5", "", "", "NOT NULL");
	if(!empty($oldScoesData)) {
    	foreach ($oldScoesData as $sco) {
    	    $sco->scormtype = $sco->type;
    	    unset($sco->type);
    	    update_record("scorm_scoes",$sco);
    	}
    }
	
	execute_sql("CREATE TABLE prefix_scorm_scoes_track (
			id SERIAL,
			userid integer NOT NULL default '0',
			scormid integer NOT NULL default '0',
			scoid integer NOT NULL default '0',
			element varchar(255) NOT NULL default '',
			value text NOT NULL default '',
			PRIMARY KEY (userid, scormid, scoid, element),
			UNIQUE (userid, scormid, scoid, element)
		   );",false); 
		   
	modify_database('','CREATE INDEX prefix_scorm_scoes_track_userdata_idx ON  prefix_scorm_scoes_track (userid, scormid, scoid);');
		     
	$oldTrackingData = get_records_select("scorm_sco_users",null,"id ASC");
	$oldElementArray = array ('cmi_core_lesson_location','cmi_core_lesson_status','cmi_core_exit','cmi_core_total_time','cmi_core_score_raw','cmi_suspend_data');

    if(!empty($oldTrackingData)) {
    	foreach ($oldTrackingData as $oldTrack) {
    	    $newTrack = '';
       	    $newTrack->userid = $oldTrack->userid;
       	    $newTrack->scormid = $oldTrack->scormid;
       	    $newTrack->scoid = $oldTrack->scoid;
       	    
       	    foreach ( $oldElementArray as $element) {
       	    	$newTrack->element = $element;
       	    	$newTrack->value = $oldTrack->$element;
       	    	if ($newTrack->value == NULL) {
       	    	    $newTrack->value = '';
       	    	}
       	    	insert_record("scorm_scoes_track",$newTrack,false);
       	    }
    	}
    }

	modify_database('',"DROP TABLE prefix_scorm_sco_users");
	modify_database('',"INSERT INTO prefix_log_display VALUES ('resource', 'review', 'resource', 'name')");
    }

    if ($oldversion < 2005040200) {
        execute_sql('ALTER TABLE '.$CFG->prefix.'scorm DROP popup');    // Old field
    }
    
    if ($oldversion < 2005040400) {
        table_column("scorm_scoes", "", "parameters", "VARCHAR", "255", "", "", "NOT NULL", "launch");
    }
    
    if ($oldversion < 2005040700) {
    	execute_sql("DROP INDEX {$CFG->prefix}scorm_scoes_track_userid_idx;",false); 
    	modify_database('','CREATE INDEX prefix_scorm_scoes_track_userdata_idx ON  prefix_scorm_scoes_track (userid, scormid, scoid);');
    }

    return true;
}


?>

