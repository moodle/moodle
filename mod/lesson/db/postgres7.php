<?PHP

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function lesson_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2004021600) {

       delete_records("log_display", "module", "lesson");

       modify_database ("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('lesson', 'start', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('lesson', 'end', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('lesson', 'view', 'lesson_pages', 'title');");

    }

    if ($oldversion < 2004022200) {

        table_column("lesson", "", "maxattempts", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "maxanswers");
        table_column("lesson", "", "nextpagedefault", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "maxattempts");
        table_column("lesson", "", "maxpages", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "nextpagedefault");
        table_column("lesson_pages", "", "qtype", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "lessonid");
        table_column("lesson_pages", "", "qoption", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "qtype");
        table_column("lesson_answers", "", "grade", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "jumpto");

    }

    if ($oldversion < 2004032000) {           // Upgrade some old beta lessons
        execute_sql(" UPDATE \"{$CFG->prefix}lesson_pages\" SET qtype = 3 WHERE qtype = 0");
    }
    
    if ($oldversion < 2004032400) {
        table_column("lesson", "", "usemaxgrade", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
        table_column("lesson", "", "minquestions", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "nextpagedefault");
    }
 
    if ($oldversion < 2004032700) {
        table_column("lesson_answers", "", "flags", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
    }
    
    if ($oldversion < 2004060501) {
        // matching questions need 2 records for responses and the
        // 2 records must appear before the old ones.  So, delete the old ones,
        // create the 2 needed, then re-insert the old ones for each matching question.
        if ($matchingquestions = get_records('lesson_pages', 'qtype', 5)) {  // get our matching questions
            foreach ($matchingquestions as $matchingquestion) {
                if ($answers = get_records('lesson_answers', 'pageid', $matchingquestion->id)) { // get answers
                    if (delete_records('lesson_answers',  'pageid', $matchingquestion->id)) {  // delete them
                        $time = time();
                        // make our 2 response answers
                        $newanswer->lessonid = $matchingquestion->lessonid;
                        $newanswer->pageid = $matchingquestion->id;
                        $newanswer->timecreated = $time;
                        $newanswer->timemodified = 0;
                        insert_record('lesson_answers', $newanswer);
                        insert_record('lesson_answers', $newanswer);
                        // insert our old answers
                        foreach ($answers as $answer) {
                            $answer->timecreated = $time;
                            $answer->timemodified = 0;
                            insert_record('lesson_answers', (object) array_map('addslashes', (array)$answer));
                        }
                    }
                }
            }
        }
    }
    
    if ($oldversion < 2004072100) {
        execute_sql(" create table ".$CFG->prefix."lesson_high_scores
                    ( id serial8 primary key,
                      lessonid int8 not null default '0',
                      userid int8 not null default '0',
                      gradeid int8 not null default '0',
                      nickname varchar(5) not null default ''
                    )");

        execute_sql(" create table ".$CFG->prefix."lesson_essay
                    ( id serial8 primary key,
                      lessonid int8 not null default '0',
                      userid int8 not null default '0',
                      pageid int8 not null default '0',
                      answerid int8 not null default '0',
                      try int8 not null default '0',
                      answer text not null default '',
                      graded int4 not null default 0,
                      score int8 not null default 0,
                      response text not null default '',
                      sent int4 not null default 0,
                      timesubmitted int8 not null default '0'
                    )");

        execute_sql(" create table ".$CFG->prefix."lesson_branch
                    ( id serial8 primary key,
                      lessonid int8 not null default '0',
                      userid int8 not null default '0',
                      pageid int8 not null default '0',
                      retry int8 not null default '0',
                      flag  int4 not null default '0',
                      timeseen int8 not null default '0'
                    )");

        
        execute_sql(" create table ".$CFG->prefix."lesson_timer
                    ( id serial8 primary key,
                      lessonid int8 not null default '0',
                    userid int8 not null default '0',
                    starttime int8 not null default '0',
                      lessontime int8 not nul default '0'l
                    )");

    

        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson_pages ADD layout TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER qoption");
        table_column('lesson_pages','','layout','int','3','unsigned', '1', 'not null', 'qoption');
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson_pages ADD display TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER layout");
        table_column('lesson_pages','','display','int','3','unsigned',  '1',  'not null', 'layout');

        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson_answers ADD score INT(10) NOT NULL DEFAULT '0' AFTER grade");
        table_column('lesson_answers','','score','int','10','unsigned',  '1',  'not null', 'grade');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD usepassword TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER name");
        table_column('lesson','','usepassword','int','3','unsigned',  '0',  'not null', 'name');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD password VARCHAR(32) NOT NULL DEFAULT '' AFTER usepassword");
        table_column('lesson','','password','varchar','32','',  '',  'not null', 'usepassword');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD custom TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER grade");
        table_column('lesson','','custom','int','3','unsigned',  '0',  'not null', 'grade');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD ongoing TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER custom");
        table_column('lesson','','ongoing','int','3','unsigned',  '0',  'not null', 'custom');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD timed TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxpages");
        table_column('lesson','','timed','int','3','unsigned',  '0',  'not null', 'maxpages');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD maxtime INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER timed");
        table_column('lesson','','maxtime','int','10','unsigned',  '0',  'not null', 'timed');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD tree TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER retake");
        table_column('lesson','','tree','int','3','unsigned',  '0',  'not null', 'retake');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD slideshow TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER tree");
        table_column('lesson','','slideshow','int','3','unsigned',  '0',  'not null', 'tree');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD width INT(10) UNSIGNED NOT NULL DEFAULT '640' AFTER slideshow");
        table_column('lesson','','width','int','10','unsigned',  '640',  'not null', 'slideshow');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD height INT(10) UNSIGNED NOT NULL DEFAULT '480' AFTER width");
        table_column('lesson','','height','int','10','unsigned',  '480',  'not null', 'width');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD bgcolor CHAR(7) NOT NULL DEFAULT '#FFFFFF' AFTER height");
        table_column('lesson','','bgcolor','varchar','7','unsigned',  '#FFFFFF',  'not null', 'height');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD displayleft TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER bgcolor");
        table_column('lesson','','displayleft','int','3','unsigned',  '0',  'not null', 'bgcolor');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD highscores TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER displayleft");
        table_column('lesson','','highscores','int','3','unsigned',  '0',  'not null', 'displayleft');
        
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD maxhighscores INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER highscores");
        table_column('lesson','','maxhighscores','int','10','unsigned',  '0',  'not null', 'highscores');

    }

    if ($oldversion < 2004081100) {
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD practice TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER name");
        table_column('lesson','','practice','int','3','unsigned',  '0',  'not null', 'name');
        //execute_sql(" ALTER TABLE {$CFG->prefix}lesson ADD review TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxattempts");
        table_column('lesson','','review','int','3','unsigned',  '0',  'not null', 'maxattempts');
    }    
    
    if ($oldversion < 2004081700) {
        execute_sql("CREATE TABLE {$CFG->prefix}lesson_default 
        ( id serial8 primary key,
          course int8 NOT NULL default '0',
          practice int4 NOT NULL default '0',
          password varchar(32) NOT NULL default '',
          usepassword int4 NOT NULL default '0',
          grade int4 NOT NULL default '0',
          custom int4 NOT NULL default '0',
          ongoing int4 NOT NULL default '0',
          usemaxgrade int4 NOT NULL default '0',
          maxanswers int4 NOT NULL default '4',
          maxattempts int4 NOT NULL default '5',
          review int4 NOT NULL default '0',
          nextpagedefault int4 NOT NULL default '0',
          minquestions int4 NOT NULL default '0',
          maxpages int4 NOT NULL default '0',
          timed int4 NOT NULL default '0',
          maxtime int8 NOT NULL default '0',
          retake int4 NOT NULL default '1',
          tree int4 NOT NULL default '0',
          slideshow int4 NOT NULL default '0',
          width int8 NOT NULL default '640',
          height int8 NOT NULL default '480',
          bgcolor varchar(7) default '#FFFFFF',
          displayleft int4 NOT NULL default '0',
          highscores int4 NOT NULL default '0',
          maxhighscores int8 NOT NULL default '0'
        )");
    }
    
    if ($oldversion < 2004100400) {
        //execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_attempts` ADD `useranswer` text NOT NULL AFTER correct");
        table_column('lesson_attempts', '', 'useranswer', 'text', '', '', '', 'NOT NULL', 'correct');
    }
    
    if ($oldversion < 2004100700) {
        //execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `modattempts` tinyint(3) unsigned NOT NULL default '0' AFTER practice");
        table_column('lesson', '', 'modattempts', 'INT', '4', 'unsigned', '0', 'NOT NULL', 'practice');
    }

    if ($oldversion < 2004102600) {
        //execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_default` ADD `modattempts` tinyint(3) unsigned NOT NULL default '0' AFTER practice");
        table_column('lesson_default', '', 'modattempts', 'INT', '4', 'unsigned', '0', 'NOT NULL', 'practice');
    }

    if ($oldversion < 2004111200) {
        execute_sql("DROP INDEX {$CFG->prefix}lesson_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}lesson_answers_lessonid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}lesson_answers_pageid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}lesson_attempts_lessonid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}lesson_attempts_pageid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}lesson_attempts_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}lesson_grades_lessonid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}lesson_grades_userid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}lesson_pages_lessonid_idx;",false);

        modify_database('','CREATE INDEX prefix_lesson_course_idx ON prefix_lesson (course);');
        modify_database('','CREATE INDEX prefix_lesson_answers_lessonid_idx ON prefix_lesson_answers (lessonid);');
        modify_database('','CREATE INDEX prefix_lesson_answers_pageid_idx ON prefix_lesson_answers (pageid);');
        modify_database('','CREATE INDEX prefix_lesson_attempts_lessonid_idx ON prefix_lesson_attempts (lessonid);');
        modify_database('','CREATE INDEX prefix_lesson_attempts_pageid_idx ON prefix_lesson_attempts (pageid);');
        modify_database('','CREATE INDEX prefix_lesson_attempts_userid_idx ON prefix_lesson_attempts (userid);');
        modify_database('','CREATE INDEX prefix_lesson_grades_lessonid_idx ON prefix_lesson_grades (lessonid);');
        modify_database('','CREATE INDEX prefix_lesson_grades_userid_idx ON prefix_lesson_grades (userid);');
        modify_database('','CREATE INDEX prefix_lesson_pages_lessonid_idx ON prefix_lesson_pages (lessonid);');
   }
   
    if ($oldversion < 2005060900) {
        table_column('lesson_grades', 'grade', 'grade', 'real', '', 'unsigned', '0', 'not null');
    }
    
    if ($oldversion < 2005060901) { // Mass cleanup of bad postgres upgrade scripts
        modify_database('','ALTER TABLE prefix_lesson ALTER bgcolor SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER custom SET NOT NULL');
        table_column('lesson','height','height','integer','16','unsigned','480');
        modify_database('','ALTER TABLE prefix_lesson ALTER highscores SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER maxattempts SET DEFAULT 5');
        table_column('lesson','maxhighscores','maxhighscores','integer','16');
        modify_database('','ALTER TABLE prefix_lesson ALTER displayleft SET NOT NULL');
        table_column('lesson','','minquestions','integer','8');
        notify('The above error can be ignored if the column already exists, its possible that it was cleaned up already before running this upgrade');
        table_column('lesson','maxtime','maxtime','integer','16');
        modify_database('','ALTER TABLE prefix_lesson ALTER ongoing SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER password SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER practice SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER review SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER slideshow SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER timed SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER tree SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER usepassword SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson ALTER width SET NOT NULL');
        table_column('lesson','width','width','integer','16','unsigned','640');
        table_column('lesson_answers','flags','flags','integer','8');
        table_column('lesson_answers','grade','grade','integer','8');
        table_column('lesson_answers','score','score','integer','16');
        modify_database('','ALTER TABLE prefix_lesson_grades ALTER grade SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson_pages ALTER display SET NOT NULL');
        modify_database('','ALTER TABLE prefix_lesson_pages ALTER layout SET NOT NULL');
        table_column('lesson_pages','qoption','qoption','integer','8');
        table_column('lesson_pages','qtype','qtype','integer','8');
    }

    if ($oldversion < 2005061500) {
        table_column('lesson', '', 'mediafile', 'varchar', '255', '', '', 'not null', 'tree');
    }
    
    if ($oldversion < 2005063000) {
        table_column('lesson', '', 'dependency', 'INT', '8', 'unsigned', '0', 'not null', 'usepassword');
        table_column('lesson', '', 'conditions', 'text', '', '', '', 'not null', 'dependency');
    }
    
    if ($oldversion < 2005101900) {
        table_column('lesson', '', 'progressbar', 'INT', '3', 'unsigned', '0', 'not null', 'displayleft');
        table_column('lesson', '', 'displayleftif', 'INT', '3', 'unsigned', '0', 'not null', 'displayleft');
    }
    
    if ($oldversion < 2005102800) {
        table_column('lesson', '', 'mediaclose', 'INT', '3', 'unsigned', '0', 'not null', 'mediafile');
        table_column('lesson', '', 'mediaheight', 'INT', '10', 'unsigned', '100', 'not null', 'mediafile');
        table_column('lesson', '', 'mediawidth', 'INT', '10', 'unsigned', '650', 'not null', 'mediafile');
    }

    if ($oldversion < 2005110200) {
        table_column('lesson', '', 'activitylink', 'INT', '10', 'unsigned', '0', 'not null', 'tree');
    }
    
    if ($oldversion < 2006031900) {
        execute_sql('ALTER TABLE  '. $CFG->prefix . 'lesson DROP COLUMN tree');
        execute_sql('ALTER TABLE  '. $CFG->prefix . 'lesson_default DROP COLUMN tree');
    }
    
    if ($oldversion < 2006050100) {   
        table_column('lesson_default', '', 'conditions', 'text', '', '', '', 'not null', 'password');
        table_column('lesson_default', '', 'progressbar', 'tinyint', '3', 'unsigned', '0', 'not null', 'displayleft');
        table_column('lesson_default', '', 'displayleftif', 'int', '3', 'unsigned', '0', 'not null', 'displayleft'); 
        table_column('lesson_default', '', 'mediaclose', 'tinyint', '3', 'unsigned', '0', 'not null', 'retake');
        table_column('lesson_default', '', 'mediaheight', 'int', '10', 'unsigned', '100', 'not null', 'retake');
        table_column('lesson_default', '', 'mediawidth', 'int', '10', 'unsigned', '650', 'not null', 'retake');
    }

    if ($oldversion < 2006050101) {
        // drop the unused table
        execute_sql('DROP TABLE '.$CFG->prefix.'lesson_essay', false);

        // properly set the correct default values
        table_column('lesson', 'activitylink', 'activitylink', 'integer', '8', '', '0');
        table_column('lesson', 'dependency', 'dependency', 'integer', '8', '', '0');

        modify_database('', 'ALTER TABLE prefix_lesson_timer
            ALTER COLUMN lessontime SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_timer
            ALTER COLUMN lessonid SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_timer
            ALTER COLUMN userid SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_timer
            ALTER COLUMN starttime SET DEFAULT 0');

        modify_database('', 'ALTER TABLE prefix_lesson_branch
            ALTER COLUMN lessonid SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_branch
            ALTER COLUMN timeseen SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_branch
            ALTER COLUMN userid SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_branch
            ALTER COLUMN retry SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_branch
            ALTER COLUMN pageid SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_branch
            ALTER COLUMN flag SET DEFAULT 0');

        modify_database('', 'ALTER TABLE prefix_lesson_high_scores
            ALTER COLUMN nickname SET DEFAULT \'\'');
        modify_database('', 'ALTER TABLE prefix_lesson_high_scores
            ALTER COLUMN lessonid SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_high_scores
            ALTER COLUMN gradeid SET DEFAULT 0');
        modify_database('', 'ALTER TABLE prefix_lesson_high_scores
            ALTER COLUMN userid SET DEFAULT 0');
    }
    
    if ($oldversion < 2006091202) {
        table_column('lesson', '', 'feedback', 'int', '3', 'unsigned', '1', 'not null', 'nextpagedefault'); 
        table_column('lesson_default', '', 'feedback', 'int', '3', 'unsigned', '1', 'not null', 'nextpagedefault'); 
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

   return true;
}

?>
