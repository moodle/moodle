<?php // $Id$

function choice_upgrade($oldversion) {
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

    return true;
}


?>

