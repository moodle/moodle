<?PHP // $Id$

function quiz_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2002101800) {
        execute_sql(" ALTER TABLE `quiz_attempts` ".
                    " ADD `timestart` INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER `sumgrades` , ".
                    " ADD `timefinish` INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER `timestart` ");
        execute_sql(" UPDATE `quiz_attempts` SET timestart = timemodified ");
        execute_sql(" UPDATE `quiz_attempts` SET timefinish = timemodified ");
    }
    if ($oldversion < 2002102101) {
        execute_sql(" DELETE FROM log_display WHERE module = 'quiz' ");
        execute_sql(" INSERT INTO log_display VALUES ('quiz', 'view', 'quiz', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('quiz', 'report', 'quiz', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('quiz', 'attempt', 'quiz', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('quiz', 'submit', 'quiz', 'name') ");
    }

    return true;
}

?>
