<?php // $Id$

function choice_upgrade($oldversion) {
    
    global $CFG;

// This function does anything necessary to upgrade
// older versions to match current functionality

    if ($oldversion < 2002090800) {
        execute_sql(" ALTER TABLE `choice` CHANGE `answer1` `answer1` VARCHAR( 255 )");
        execute_sql(" ALTER TABLE `choice` CHANGE `answer2` `answer2` VARCHAR( 255 )");
    }
    if ($oldversion < 2002102400) {
        execute_sql(" ALTER TABLE `choice` ADD `answer3` varchar(255) NOT NULL AFTER `answer2`");
        execute_sql(" ALTER TABLE `choice` ADD `answer4` varchar(255) NOT NULL AFTER `answer3`");
        execute_sql(" ALTER TABLE `choice` ADD `answer5` varchar(255) NOT NULL AFTER `answer4`");
        execute_sql(" ALTER TABLE `choice` ADD `answer6` varchar(255) NOT NULL AFTER `answer5`");
    }
    if ($oldversion < 2002122300) {
        execute_sql("ALTER TABLE `choice_answers` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
    }
    if ($oldversion < 2003010100) {
        execute_sql(" ALTER TABLE `choice` ADD `format` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `text` ");
        execute_sql(" ALTER TABLE `choice` ADD `publish` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `answer6` ");
    }

    if ($oldversion < 2004010100) {
        table_column("choice", "", "showunanswered", "integer", "4", "unsigned", "0", "", "publish");
    }
    if ($oldversion < 2004021700) {
        modify_database("", "INSERT INTO prefix_log_display VALUES ('choice', 'choose', 'choice', 'name');");
        modify_database("", "INSERT INTO prefix_log_display VALUES ('choice', 'choose again', 'choice', 'name');");
    }
    if ($oldversion < 2004070100) {
        table_column("choice", "", "timeclose", "integer", "10", "unsigned", "0", "", "showunanswered");
        table_column("choice", "", "timeopen", "integer", "10", "unsigned", "0", "", "showunanswered");
    }
    if ($oldversion < 2004070101) {
        table_column("choice", "", "release", "integer", "2", "unsigned", "0", "", "publish");
        table_column("choice", "", "allowupdate", "integer", "2", "unsigned", "0", "", "release");
    }
    if ($oldversion < 2004070102) {
        modify_database("", "UPDATE prefix_choice SET allowupdate = '1' WHERE publish = 0;");
        modify_database("", "UPDATE prefix_choice SET release = '1' WHERE publish > 0;");
        modify_database("", "UPDATE prefix_choice SET publish = publish - 1 WHERE publish > 0;");
    }

    if ($oldversion < 2004111200){  // drop first to avoid conflicts when upgrading from 1.4+
        execute_sql("ALTER TABLE {$CFG->prefix}choice DROP INDEX course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}choice_answers DROP INDEX choice;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}choice_answers DROP INDEX userid;",false);       
        
        modify_database('','ALTER TABLE prefix_choice ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_choice_answers ADD INDEX choice (choice);');
        modify_database('','ALTER TABLE prefix_choice_answers ADD INDEX userid (userid);');
    }
    
    if ($oldversion < 2005033000){  
        execute_sql("RENAME TABLE {$CFG->prefix}choice_answers TO {$CFG->prefix}choice_responses;");
        execute_sql("CREATE TABLE {$CFG->prefix}choice_answers (id int(10) unsigned NOT NULL auto_increment, choice int(10) unsigned NOT NULL default '0', answer TEXT, timemodified int(10) NOT NULL default '0', PRIMARY KEY  (id), UNIQUE KEY id (id), KEY choice (choice)) TYPE=MyISAM;",false);      
        execute_sql("ALTER TABLE {$CFG->prefix}choice ADD `display` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `release`;");
        execute_sql("ALTER TABLE {$CFG->prefix}choice_responses CHANGE answer answerid integer(10) NOT NULL default '0';");        
        
        //move old answers into new answers table.
        $records = get_records('choice');
        if (!empty($records)) {
            foreach ($records as $thischoice) {
                for ($i = 1; $i < 7; $i++) {                
                    $instance = new stdClass;
                    $instance->choice = $thischoice->id;
                    $instance->answerid = $thischoice->{'answer'.$i};
                    $instance->timemodified = $thischoice->timemodified;
                    $result = insert_record('choice_answers', $instance);
                    //now fix all responses to the answers.
                    execute_sql("UPDATE {$CFG->prefix}choice_responses SET answerid={$result} WHERE choice={$thischoice->id} AND answerid={$i}");                                                            
                }
            }
        }
        
        //drop old fields
        modify_database('','ALTER TABLE prefix_choice DROP `answer1`;');
        modify_database('','ALTER TABLE prefix_choice DROP `answer2`;');
        modify_database('','ALTER TABLE prefix_choice DROP `answer3`;');
        modify_database('','ALTER TABLE prefix_choice DROP `answer4`;');
        modify_database('','ALTER TABLE prefix_choice DROP `answer5`;');
        modify_database('','ALTER TABLE prefix_choice DROP `answer6`;');
        
    }
    

    return true;
}


?>
