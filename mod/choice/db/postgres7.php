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

    return true;
}


?>

