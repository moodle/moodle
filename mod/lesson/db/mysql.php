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

        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxattempts` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxanswers");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `nextpagedefault` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxattempts");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxpages` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER nextpagedefault");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `qtype` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER lessonid");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `qoption` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER qtype");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_answers` ADD `grade` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER jumpto");

    }

    if ($oldversion < 2004032000) {           // Upgrade some old beta lessons
        execute_sql(" UPDATE `{$CFG->prefix}lesson_pages` SET qtype = 3 WHERE qtype = 0");
    }
    
    if ($oldversion < 2004032400) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `usemaxgrade` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER grade");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `minquestions` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER nextpagedefault");
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
                    ( id int(10) unsigned not null auto_increment,
                      lessonid int(10) unsigned not null default '0',
                      userid int(10) unsigned not null default '0',
                      gradeid int(10) unsigned not null default '0',
                      nickname varchar(5) not null default '',
                      PRIMARY KEY  (`id`)
                    )");

        execute_sql(" create table ".$CFG->prefix."lesson_branch
                    ( id int(10) unsigned not null auto_increment,
                      lessonid int(10) unsigned not null default '0',
                      userid int(10) unsigned not null default '0',
                      pageid int(10) unsigned not null default '0',
                      retry int(10) unsigned not null default '0',
                      flag  tinyint(3) unsigned not null default '0',
                      timeseen int(10) unsigned not null default '0',
                      PRIMARY KEY  (`id`)
                    )");

        
        execute_sql(" create table ".$CFG->prefix."lesson_timer
                    ( id int(10) unsigned not null auto_increment,
                      lessonid int(10) unsigned not null default '0',
                    userid int(10) unsigned not null default '0',
                    starttime int(10) unsigned not null default '0',
                      lessontime int(10) unsigned not null default '0',
                      PRIMARY KEY  (`id`)
                    )");

    
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `layout` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER qoption");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `display` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER layout");

        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_answers` ADD `score` INT(10) NOT NULL DEFAULT '0' AFTER grade");
    
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `usepassword` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER name");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `password` VARCHAR(32) NOT NULL DEFAULT '' AFTER usepassword");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `custom` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER grade");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `ongoing` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER custom");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `timed` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxpages");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxtime` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER timed");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `tree` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER retake");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `slideshow` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER tree");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `width` INT(10) UNSIGNED NOT NULL DEFAULT '640' AFTER slideshow");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `height` INT(10) UNSIGNED NOT NULL DEFAULT '480' AFTER width");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `bgcolor` CHAR(7) NOT NULL DEFAULT '#FFFFFF' AFTER height");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `displayleft` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER bgcolor");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `highscores` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER displayleft");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxhighscores` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER highscores");

    }

    if ($oldversion < 2004081100) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `practice` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER name");
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `review` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxattempts");
    }    
    
    if ($oldversion < 2004081700) {
        execute_sql("CREATE TABLE `{$CFG->prefix}lesson_default` 
        ( `id` int(10) unsigned NOT NULL auto_increment,
          `course` int(10) unsigned NOT NULL default '0',
          `practice` tinyint(3) unsigned NOT NULL default '0',
          `password` varchar(32) NOT NULL default '',
          `usepassword` int(3) unsigned NOT NULL default '0',
          `grade` tinyint(3) NOT NULL default '0',
          `custom` int(3) unsigned NOT NULL default '0',
          `ongoing` int(3) unsigned NOT NULL default '0',
          `usemaxgrade` tinyint(3) unsigned NOT NULL default '0',
          `maxanswers` int(3) unsigned NOT NULL default '4',
          `maxattempts` int(3) unsigned NOT NULL default '5',
          `review` tinyint(3) unsigned NOT NULL default '0',
          `nextpagedefault` int(3) unsigned NOT NULL default '0',
          `minquestions` tinyint(3) unsigned NOT NULL default '0',
          `maxpages` int(3) unsigned NOT NULL default '0',
          `timed` int(3) unsigned NOT NULL default '0',
          `maxtime` int(10) unsigned NOT NULL default '0',
          `retake` int(3) unsigned NOT NULL default '1',
          `tree` int(3) unsigned NOT NULL default '0',
          `slideshow` int(3) unsigned NOT NULL default '0',
          `width` int(10) unsigned NOT NULL default '640',
          `height` int(10) unsigned NOT NULL default '480',
          `bgcolor` varchar(7) default '#FFFFFF',
          `displayleft` int(3) unsigned NOT NULL default '0',
          `highscores` int(3) unsigned NOT NULL default '0',
          `maxhighscores` int(10) NOT NULL default '0',
          PRIMARY KEY  (`id`)
        ) COMMENT = 'Defines lesson_default'");
    }

    if ($oldversion < 2004100400) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_attempts` ADD `useranswer` text NOT NULL AFTER correct");
    }
    
    if ($oldversion < 2004100700) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `modattempts` tinyint(3) unsigned NOT NULL default '0' AFTER practice");
    }

    if ($oldversion < 2004102600) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_default` ADD `modattempts` tinyint(3) unsigned NOT NULL default '0' AFTER practice");
    }

    if ($oldversion < 2004111200) {
        execute_sql("ALTER TABLE {$CFG->prefix}lesson DROP INDEX course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}lesson_answers DROP INDEX lessonid;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}lesson_attempts DROP INDEX lessonid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}lesson_attempts DROP INDEX pageid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}lesson_grades DROP INDEX lessonid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}lesson_grades DROP INDEX userid;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}lesson_pages DROP INDEX lessonid;",false);

        modify_database('','ALTER TABLE prefix_lesson ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_lesson_answers ADD INDEX lessonid (lessonid);');
        modify_database('','ALTER TABLE prefix_lesson_attempts ADD INDEX lessonid (lessonid);');
        modify_database('','ALTER TABLE prefix_lesson_attempts ADD INDEX pageid (pageid);');
        modify_database('','ALTER TABLE prefix_lesson_grades ADD INDEX lessonid (lessonid);');
        modify_database('','ALTER TABLE prefix_lesson_grades ADD INDEX userid (userid);');
        modify_database('','ALTER TABLE prefix_lesson_pages ADD INDEX lessonid (lessonid);');
    }
   
    if ($oldversion < 2005060900) {
        table_column('lesson_grades', 'grade', 'grade', 'float', '', 'unsigned', '0', 'not null');
    }
    
    if ($oldversion < 2005061500) {
        table_column('lesson', '', 'mediafile', 'varchar', '255', '', '', 'not null', 'tree');
    }
    
    if ($oldversion < 2005063000) {
        table_column('lesson', '', 'dependency', 'int', '10', 'unsigned', '0', 'not null', 'usepassword');
        table_column('lesson', '', 'conditions', 'text', '', '', '', 'not null', 'dependency');
    }
    
    if ($oldversion < 2005101900) {
        table_column('lesson', '', 'progressbar', 'tinyint', '3', 'unsigned', '0', 'not null', 'displayleft');
        table_column('lesson', '', 'displayleftif', 'int', '3', 'unsigned', '0', 'not null', 'displayleft');
    }
    
    if ($oldversion < 2005102800) {
        table_column('lesson', '', 'mediaclose', 'tinyint', '3', 'unsigned', '0', 'not null', 'mediafile');
        table_column('lesson', '', 'mediaheight', 'int', '10', 'unsigned', '100', 'not null', 'mediafile');
        table_column('lesson', '', 'mediawidth', 'int', '10', 'unsigned', '650', 'not null', 'mediafile');
    }

    if ($oldversion < 2005110200) {
        table_column('lesson', '', 'activitylink', 'int', '10', 'unsigned', '0', 'not null', 'tree');
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
    
    if ($oldversion < 2006091202) {
        table_column('lesson', '', 'feedback', 'int', '3', 'unsigned', '1', 'not null', 'nextpagedefault'); 
        table_column('lesson_default', '', 'feedback', 'int', '3', 'unsigned', '1', 'not null', 'nextpagedefault'); 
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}

?>
