<?php // $Id$

function choice_upgrade($oldversion) {

    global $CFG;

// This function does anything necessary to upgrade
// older versions to match current functionality

    if ($oldversion < 2003010100) {
        execute_sql(" ALTER TABLE `choice` ADD `format` INTEGER DEFAULT '0' NOT NULL AFTER `text` ");
        execute_sql(" ALTER TABLE `choice` ADD `publish` INTEGER DEFAULT '0' NOT NULL AFTER `answer6` ");
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

    if ($oldversion < 2004111200) { // drop first to avoid conflicts when upgrading from 1.4+
        execute_sql("DROP INDEX {$CFG->prefix}choice_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}choice_answers_choice_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}choice_answers_userid_idx;",false);

        modify_database('','CREATE INDEX prefix_choice_course_idx ON prefix_choice (course);');
        modify_database('','CREATE INDEX prefix_choice_answers_choice_idx ON prefix_choice_answers (choice);');
        modify_database('','CREATE INDEX prefix_choice_answers_userid_idx ON prefix_choice_answers (userid);');
    }
   // if ($oldversion < 2005033000){ 
   //     execute_sql("ALTER TABLE {$CFG->prefix}choice_answers RENAME TO {$CFG->prefix}choice_responses;");
   //     execute_sql("CREATE TABLE {$CFG->prefix}choice_answers (id SERIAL PRIMARY KEY, choice integer NOT NULL default '0', answer TEXT, timemodified integer NOT NULL default '0');");
   //     execute_sql("CREATE INDEX {$CFG->prefix}choice_answers_choice_idx ON {$CFG->prefix}choice_answers (choice);");
   //     table_column('choice','','display','integer','10','unsigned','0','not null');
   //     table_column('choice_responses','answer','answerid','integer','10','unsigned','0','not null');
   //     
   //     //move old answers into new answer table.
   //     $records = get_records('choice');
   //     if (!empty($records)) {
   //        foreach ($records as $thischoice) {
   //             for ($i = 1; $i < 5; $i++) {                
   //                 $instance = new stdClass;
   //                $instance->choice = $thischoice->id;
   //                 $instance->answerid = $thischoice->{'answer'.$i};
   //                 $instance->timemodified = $thischoice->timemodified;
   //                 $result = insert_record('choice_answers', $instance);
   //                 //now fix all responses to the answers.
   //                 execute_sql("UPDATE {$CFG->prefix}choice_responses SET answerid={$result} WHERE choice={$thischoice->id} AND answerid={$i}");                                                            
   //             }
   //         }
   //     }
   
      // drop old fields
      //  modify_database('','ALTER TABLE prefix_choice DROP answer1;');
      //  modify_database('','ALTER TABLE prefix_choice DROP answer2;');
      //  modify_database('','ALTER TABLE prefix_choice DROP answer3;');
      //  modify_database('','ALTER TABLE prefix_choice DROP answer4;');
      //  modify_database('','ALTER TABLE prefix_choice DROP answer5;');
      //  modify_database('','ALTER TABLE prefix_choice DROP answer6;');
    
  //  }
    return true;
}

?>

