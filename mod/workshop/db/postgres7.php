<?php // $Id$

function workshop_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2003050400) {
	table_column("workshop","graded", "agreeassessments", "INT","2", "", "0" ,"NOT NULL");
	table_column("workshop", "showgrades","hidegrades", "INT","2", "","0", "NOT NULL");
	table_column("workshop_assessments","","timeagreed", "INT","8", "UNSIGNED", "0", "NOT NULL" );
	
	execute_sql("
            CREATE TABLE {$CFG->prefix}workshop_comments (
            id SERIAL8 PRIMARY KEY  ,
            workshopid int8 NOT NULL default '0', 
            assessmentid int8  NOT NULL default '0',
            userid int8 NOT NULL default '0',
            timecreated int8  NOT NULL default '0',
	        mailed int2  NOT NULL default '0',
            comments text NOT NULL
        )
        ");
    }

    if ($oldversion < 2003051400) {
        table_column("workshop","","showleaguetable", "INTEGER", "4", "unsigned", "0", "not null", "gradingweight");
		execute_sql("
		CREATE TABLE {$CFG->prefix}workshop_rubrics (
		  id SERIAL8 PRIMARY KEY,
		  workshopid int8 NOT NULL default '0',
		  elementid int8 NOT NULL default '0',
		  rubricno int4  NOT NULL default '0',
		  description text NOT NULL,
		) 
        ");
	}
		
	if ($oldversion < 2003082200) {
        table_column("workshop_rubrics", "elementid", "elementno", "INTEGER", "10", "unsigned", "0", "not null", "id"); 	
	}

	if ($oldversion < 2003092500) {
        table_column("workshop", "", "overallocation", "INTEGER", "4", "unsigned", "0", "not null", "nsassesments");
	}

    if ($oldversion < 2003100200) {
	
        table_column("workshop_assesments", "", "resubmission", "INTEGER", "4", "unsigned", "0", "not null", "mailed");
	}
		
    if ($oldversion < 2003100800) {
        // tidy up log_display entries
        execute_sql("DELETE FROM {$CFG->prefix}log_display WHERE module = 'workshop'");
        execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES('workshop', 'assessments', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('workshop', 'close', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('workshop', 'display', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('workshop', 'resubmit', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('workshop', 'set up', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('workshop', 'submissions', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('workshop', 'view', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('workshop', 'update', 'workshop', 'name')");
    }
    
    if ($oldversion < 2003113000) {
        table_column("workshop", "", "teacherloading", "INTEGER", "4", "unsigned", "5", "NOT NULL", "mailed");
        table_column("workshop", "", "assessmentstodrop", "INTEGER", "4", "unsigned", "0", "NOT NULL", "");
        table_column("workshop_assessments", "", "donotuse", "INTEGER", "4", "unsigned", "0", "NOT NULL", "resubmission");
        execute_sql("CREATE INDEX {$CFG->prefix}workshop_grades_assesmentid_idx (assessmentid)");
    }

    if ($oldversion < 2004052100) {
        include_once("$CFG->dirroot/mod/workshop/lib.php");
        workshop_refresh_events();
    }

    if ($oldversion < 2004081100) {
		table_column("workshop", "", "gradinggrade", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
		table_column("workshop", "", "assessmentcomps", "INTEGER", "4", "UNSIGNED", "2", "NOT NULL", "ntassessments");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN gradingweight");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN mergegrades");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN peerweight");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN includeteachersgrade");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN biasweight");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN reliabilityweight");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN teacherloading");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN assessmentstodrop");
    }

    
    return true;

}    


?>

