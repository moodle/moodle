<?PHP // $Id$

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
        modify_database("", "UPDATE prefix_choice SET release = '1' WHERE publish > 0;");
        modify_database("", "UPDATE prefix_choice SET publish = publish - 1 WHERE publish > 0;");
    }

    return true;
}


?>

