<?PHP // $Id$

function choice_upgrade($oldversion) {
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

    return true;
}


?>
