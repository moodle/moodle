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

    if ($oldversion < 2004052100) {
        include_once("$CFG->dirroot/mod/workshop/lib.php");
        workshop_refresh_events();
    }
    if ($oldversion < 2004060401) {
        modify_database('','CREATE INDEX prefix_workshop_course_idx ON prefix_workshop (course);');
        modify_database('','CREATE INDEX prefix_workshop_assessments_workshopid_idx ON prefix_workshop_assessments (workshopid);');
        modify_database('','CREATE INDEX prefix_workshop_assessments_submissionid_idx ON prefix_workshop_assessments (submissionid);');
        modify_database('','CREATE INDEX prefix_workshop_assessments_userid_idx ON prefix_workshop_assessments (userid);');
        modify_database('','CREATE INDEX prefix_workshop_assessments_mailed_idx ON prefix_workshop_assessments (mailed);');
        modify_database('','CREATE INDEX prefix_workshop_comments_workshopid_idx ON prefix_workshop_comments (workshopid);');
        modify_database('','CREATE INDEX prefix_workshop_comments_assessmentid_idx ON prefix_workshop_comments (assessmentid);');
        modify_database('','CREATE INDEX prefix_workshop_comments_userid_idx ON prefix_workshop_comments (userid);');
        modify_database('','CREATE INDEX prefix_workshop_comments_mailed_idx ON prefix_workshop_comments (mailed);');
        modify_database('','CREATE INDEX prefix_workshop_elements_workshopid_idx ON prefix_workshop_elements (workshopid);');
        modify_database('','CREATE INDEX prefix_workshop_grades_workshopid_idx ON prefix_workshop_grades (workshopid);');
        modify_database('','CREATE INDEX prefix_workshop_grades_assessmentid_idx ON prefix_workshop_grades (assessmentid);');
        modify_database('','CREATE INDEX prefix_workshop_submissions_workshopid_idx ON prefix_workshop_submissions (workshopid);');
        modify_database('','CREATE INDEX prefix_workshop_submissions_userid_idx ON prefix_workshop_submissions (userid);');
        modify_database('','CREATE INDEX prefix_workshop_submissions_mailed_idx ON prefix_workshop_submissions (mailed);');
    }

    return true;

}


?>

