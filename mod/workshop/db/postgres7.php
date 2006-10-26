<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function workshop_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG, $db;

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
        execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES('workshop', 'assessments', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('workshop', 'close', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('workshop', 'display', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('workshop', 'resubmit', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('workshop', 'set up', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('workshop', 'submissions', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('workshop', 'view', 'workshop', 'name')");
        execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('workshop', 'update', 'workshop', 'name')");
    }

    if ($oldversion < 2003113000) {
        table_column("workshop", "", "teacherloading", "INTEGER", "4", "unsigned", "5", "NOT NULL", "mailed");
        table_column("workshop", "", "assessmentstodrop", "INTEGER", "4", "unsigned", "0", "NOT NULL", "");
        table_column("workshop_assessments", "", "donotuse", "INTEGER", "4", "unsigned", "0", "NOT NULL", "resubmission");
        execute_sql("CREATE INDEX {$CFG->prefix}workshop_grades_assesmentid_idx ON {$CFG->prefix}workshop_grades (assessmentid)");
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
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN teacherloading",false); //silent
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP COLUMN assessmentstodrop",false); //silent
    }

    if ($oldversion < 2004092400) {
        table_column("workshop", "", "nattachments", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "nelements");
        table_column("workshop_submissions", "", "description", "TEXT", "", "", "", "", "mailed");
        // these need to be dropped first in case we're upgrading from 1.4.3 and they already exist
        execute_sql("DROP INDEX {$CFG->prefix}workshop_submissions_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_assessments_submissionid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_assessments_userid_idx;",false);

        execute_sql("CREATE INDEX {$CFG->prefix}workshop_submissions_userid_idx ON {$CFG->prefix}workshop_submissions (userid)");
        execute_sql("CREATE INDEX {$CFG->prefix}workshop_assessments_submissionid_idx ON {$CFG->prefix}workshop_assessments (submissionid)");
        execute_sql("CREATE INDEX {$CFG->prefix}workshop_assessments_userid_idx ON {$CFG->prefix}workshop_assessments (userid)");
    }

    if ($oldversion < 2004092700) {
        table_column("workshop", "", "wtype", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "description");
        table_column("workshop", "", "usepassword", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL");
        table_column("workshop", "", "password", "VARCHAR", "32", "", "", "NOT NULL");
        table_column("workshop_submissions", "", "late", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL");

        // update wkey value
        if ($workshops = get_records("workshop")) {
            foreach ($workshops as $workshop) {
                $wtype = 0; // 3 phases, no grading grades
                if ($workshop->includeself or $workshop->ntassessments) $wtype = 1; // 3 phases with grading grades
                if ($workshop->nsassessments) $wtype = 2; // 5 phases with grading grades
                set_field("workshop", "wtype", $wtype, "id", $workshop->id);
            }
        }
    }

    if ($oldversion < 2004102800) {
        table_column("workshop", "", "releasegrades", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "deadline");
        execute_sql("
        CREATE TABLE {$CFG->prefix}workshop_stockcomments (
          id SERIAL PRIMARY KEY,
          workshopid INT8 NOT NULL default '0',
          elementno INT8 NOT NULL default '0',
          comments text NOT NULL
        )
        ");
    }

    if ($oldversion < 2004111000) {
        table_column("workshop_elements", "", "stddev", "FLOAT", "", "", "0", "NOT NULL");
        table_column("workshop_elements", "", "totalassessments", "INTEGER", "10", "", "0", "NOT NULL");
        table_column("workshop_elements", "weight", "weight", "INTEGER", "4", "UNSIGNED", "11",  "NOT NULL");
        table_column("workshop_submissions", "", "nassessments", "INTEGER", "10", "", "0", "NOT NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_submissions DROP COLUMN teachergrade");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_submissions DROP COLUMN peergrade");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_submissions DROP COLUMN biasgrade");
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_submissions DROP COLUMN reliabilitygrade");
    }

    if ($oldversion < 2004111200) {
        execute_sql("DROP INDEX {$CFG->prefix}workshop_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_assessments_workshopid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_assessments_submissionid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_assessments_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_assessments_mailed_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_comments_workshopid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_comments_assessmentid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_comments_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_comments_mailed_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_elements_workshopid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_grades_workshopid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_grades_assessmentid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_submissions_workshopid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_submissions_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}workshop_submissions_mailed_idx;",false);

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

    if ($oldversion < 2004120402) {
        table_column('workshop', '', 'submissionstart', 'INTEGER', '10', 'UNSIGNED', '0', 'NOT NULL', 'maxbytes');
        table_column('workshop', '', 'assessmentstart', 'INTEGER', '10', 'UNSIGNED', '0', 'NOT NULL', 'submissionstart');
        table_column('workshop', 'deadline', 'submissionend', 'INTEGER', '10', 'UNSIGNED', '0', 'NOT NULL');
        table_column('workshop', '', 'assessmentend', 'INTEGER', '10', 'UNSIGNED', '0', 'NOT NULL', 'submissionend');

        $workshops = get_records('workshop');
        if(!empty($workshops)) {
            foreach ($workshops as $workshop) {
                $early = (time() < $workshop->submissionend) ? 0 : $workshop->submissionend;
                $late = (time() > $workshop->submissionend) ? 0 : $workshop->submissionend;
                set_field('workshop', 'submissionstart', ($workshop->phase > 1) ? $early : $late, 'id', $workshop->id);
                set_field('workshop', 'assessmentstart', ($workshop->phase > 2) ? $early : $late, 'id', $workshop->id);
                set_field('workshop', 'submissionend', ($workshop->phase > 3) ? $early : $late, 'id', $workshop->id);
                set_field('workshop', 'assessmentend', ($workshop->phase > 4) ? $early : $late, 'id', $workshop->id);
            }
        }
        execute_sql('ALTER TABLE  '. $CFG->prefix .'workshop DROP COLUMN phase');

        execute_sql("UPDATE {$CFG->prefix}event SET eventtype = 'submissionend' WHERE eventtype = 'deadline' AND modulename = 'workshop'", false);
    }

    if ($oldversion < 2004120900) {
        table_column('workshop_assessments', '', 'teachergraded', 'INTEGER', '4', 'UNSIGNED', '0', 'NOT NULL', 'gradinggrade');
    }

    if ($oldversion < 2005041200) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        $wtm->update( 'workshop','description','format' );
    }

    if ($oldversion < 2005041201) { // Mass cleanup of bad upgrade scripts
        // Some of those steps might fail, it is normal.
        table_column('workshop','assessmentend','assessmentend','integer','16');
        table_column('workshop','assessmentstart','assessmentstart','integer','16');
        table_column('workshop','','phase','integer','4');
        table_column('workshop','','showleaguetable','integer','8');
        table_column('workshop','releasegrades','releasegrades','integer','16');
        table_column('workshop','submissionend','submissionend','integer','16');
        table_column('workshop','submissionstart','submissionstart','integer','16');
        modify_database('','ALTER TABLE prefix_workshop ALTER teacherweight SET DEFAULT 1');
        modify_database('','ALTER TABLE prefix_workshop DROP timeagreed');
        modify_database('','ALTER TABLE prefix_workshop RENAME inalgrade TO finalgrade');
        table_column('workshop_assessments','','donotuse','integer','8');
        table_column('workshop_assessments','','timeagreed','integer','16');
        modify_database('','ALTER TABLE prefix_workshop_assessments DROP teachergraded');
        modify_database('','ALTER TABLE prefix_workshop_elements RENAME totalrassesments TO totalassessments');
        modify_database('','ALTER TABLE prefix_workshop_submissions ALTER description DROP DEFAULT');
        table_column('workshop_submissions','nassessments','nassessments','integer','16');
        table_column('workshop_elements','totalassessments','totalassessments','integer','16');
        execute_sql("
        CREATE TABLE {$CFG->prefix}workshop_rubrics (
          id SERIAL PRIMARY KEY,
          workshopid int8 NOT NULL default '0',
          elementno int8  NOT NULL default '0',
          rubricno int4  NOT NULL default '0',
          description text NOT NULL
        )
        ");
    }

    if ($oldversion < 2006090500) {
        $columns = $db->MetaColumns($CFG->prefix.'workshop_assessments');
        $columns = array_change_key_case($columns, CASE_LOWER);
        if (!isset($columns['teachergraded'])) {
            table_column('workshop_assessments', '', 'teachergraded', 'INTEGER', '4', 'UNSIGNED', '0', 'NOT NULL', 'gradinggrade');
        }
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;

}


?>
