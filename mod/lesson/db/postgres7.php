<?PHP

function lesson_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2004021600) {

       delete_records("log_display", "module", "lesson");

       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'start', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'end', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'view', 'lesson_pages', 'title');");

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
 // CDC-FLAG
    if ($oldversion < 2004072100) {
        execute_sql(" create table ".$CFG->prefix."lesson_high_scores
                    ( id serial8 primary key,
                      lessonid int8 not null,
                      userid int8 not null,
                      gradeid int8 not null,
                      nickname varchar(5) not null
                    )");

        execute_sql(" create table ".$CFG->prefix."lesson_essay
                    ( id serial8 primary key,
                      lessonid int8 not null,
                      userid int8 not null,
                      pageid int8 not null,
                      answerid int8 not null,
                      try int8 not null,
                      answer text not null,
                      graded int4 not null default 0,
                      score int8 not null default 0,
                      response text not null,
                      sent int4 not null default 0,
                      timesubmitted int8 not null
                    )");

        execute_sql(" create table ".$CFG->prefix."lesson_branch
                    ( id serial8 primary key,
                      lessonid int8 not null,
                      userid int8 not null,
                      pageid int8 not null,
                      retry int8 not null,
                      flag  int4 not null,
                      timeseen int8 not null
                    )");

        
        execute_sql(" create table ".$CFG->prefix."lesson_timer
                    ( id serial8 primary key,
                      lessonid int8 not null,
                    userid int8 not null,
                    starttime int8 not null,
                      lessontime int8 not null
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
    // CDC-FLAG end    
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

   return true;
}

?>
