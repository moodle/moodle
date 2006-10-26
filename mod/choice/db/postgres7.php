<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

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
        modify_database("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('choice', 'choose', 'choice', 'name');");
        modify_database("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('choice', 'choose again', 'choice', 'name');");
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
    if ($oldversion < 2005033000){
        if (execute_sql("CREATE TABLE {$CFG->prefix}choice_options (id SERIAL PRIMARY KEY, choiceid integer NOT NULL default '0', text TEXT, timemodified integer NOT NULL default '0');") ) {
            execute_sql("CREATE INDEX {$CFG->prefix}choice_options_choice_idx ON {$CFG->prefix}choice_options (choiceid);");

            table_column('choice_answers', 'choice', 'choiceid', 'integer', '10', 'unsigned', 0, 'not null');
            table_column('choice_answers', 'answer', 'optionid', 'integer', '10', 'unsigned', 0, 'not null');
            table_column('choice', '', 'display', 'integer', '4', 'unsigned', 0, 'not null', 'release');

            // move old answers from choice to choice_options
            if ($choices = get_records('choice')) {
                foreach ($choices as $choice) {
                    for ($i=1; $i<=6; $i++) {      // We used to have six columns
                        $option = new stdClass;
                        $option->text         = addslashes($choice->{'answer'.$i});
                        if ($option->text) {   /// Don't bother with blank options
                            $option->choiceid     = $choice->id;
                            $option->timemodified = $choice->timemodified;
                            if ($option->id = insert_record('choice_options', $option)) {
                                /// Update all the user answers to fit the new value 
                                execute_sql("UPDATE {$CFG->prefix}choice_answers
                                                SET optionid={$option->id}
                                              WHERE choiceid={$choice->id}
                                                AND optionid={$i}");
                            }
                        }
                    }
                }
            }

      // drop old fields
         modify_database('','ALTER TABLE prefix_choice DROP answer1;');
         modify_database('','ALTER TABLE prefix_choice DROP answer2;');
         modify_database('','ALTER TABLE prefix_choice DROP answer3;');
         modify_database('','ALTER TABLE prefix_choice DROP answer4;');
         modify_database('','ALTER TABLE prefix_choice DROP answer5;');
         modify_database('','ALTER TABLE prefix_choice DROP answer6;');

       }
    }

    if ($oldversion < 2005041100) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        $wtm->update( 'choice','text','format' );
    }
    if ($oldversion < 2005041500) { //new limit feature
        table_column('choice', '', 'limitanswers', 'INTEGER', '2', 'unsigned', 0, 'not null', 'showunanswered');
        table_column('choice_options', '', 'maxanswers', 'INTEGER', '10', 'unsigned', 0, 'null', 'text');
    }

    if ($oldversion < 2005041501) { // Mass cleanup of bad upgrade scripts
        modify_database('','CREATE INDEX prefix_choice_answers_choice_idx ON prefix_choice_answers (choiceid)');
        notify('The above error can be ignored if the index already exists, its possible that it was cleaned up already before running this upgrade');
        modify_database('','ALTER TABLE prefix_choice ALTER display SET NOT NULL');
        modify_database('','ALTER TABLE prefix_choice ALTER limitanswers SET NOT NULL');
        modify_database('','ALTER TABLE prefix_choice_answers ALTER choiceid SET NOT NULL');
        modify_database('','ALTER TABLE prefix_choice_answers ALTER optionid SET NOT NULL');
    }
    if ($oldversion < 2006020900) { //rename release column to showanswers - Release is now reserved word in mySql
        table_column('choice', 'release', 'showresults', 'TINYINT', '2', 'unsigned', 0, 'not null');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}

?>
