<?PHP // $Id$

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
    return true;

    if ($oldversion < 2004052100) {
        include_once("$CFG->dirroot/mod/workshop/lib.php");
        workshop_refresh_events();
    }
}


?>

