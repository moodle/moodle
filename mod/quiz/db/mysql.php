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
    if ($oldversion < 2002102600) {
        execute_sql(" ALTER TABLE `quiz_answers` CHANGE `feedback` `feedback` TEXT NOT NULL ");
    }

    if ($oldversion < 2002122300) {
        execute_sql("ALTER TABLE `quiz_grades` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
        execute_sql("ALTER TABLE `quiz_attempts` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
    }

    // prefixes required from here on, or use table_column()

    if ($oldversion < 2003010100) {
        execute_sql(" ALTER TABLE {$CFG->prefix}quiz ADD review TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL AFTER `grademethod` ");
    }

    if ($oldversion < 2003010301) {
        table_column("quiz_truefalse", "true", "trueanswer", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
        table_column("quiz_truefalse", "false", "falseanswer", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
        table_column("quiz_questions", "type", "qtype", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
    }

    if ($oldversion < 2003022303) {
        modify_database ("", "CREATE TABLE `prefix_quiz_randommatch` (
                             `id` int(10) unsigned NOT NULL auto_increment,
                             `question` int(10) unsigned NOT NULL default '0',
                             `choose` INT UNSIGNED DEFAULT '4' NOT NULL,
                             PRIMARY KEY ( `id` )
                          );");
    }

    if ($oldversion < 2003030303) {
        table_column("quiz_questions", "", "defaultgrade", "INTEGER", "6", "UNSIGNED", "1", "NOT NULL", "image");
    }

	if ($oldversion < 2003032601) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_answers` ADD INDEX(question) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_attempts` ADD INDEX(quiz) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_attempts` ADD INDEX(userid) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_grades` ADD INDEX(quiz) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_grades` ADD INDEX(userid) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_question_grades` ADD INDEX(quiz) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_question_grades` ADD INDEX(question) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_randommatch` ADD INDEX(question) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_responses` ADD INDEX(attempt) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz_responses` ADD INDEX(question) ");
    }

	if ($oldversion < 2003033100) {
        modify_database ("", "ALTER TABLE prefix_quiz_randommatch RENAME prefix_quiz_randomsamatch ");
        modify_database ("", "CREATE TABLE `prefix_quiz_match` (
                             `id` int(10) unsigned NOT NULL auto_increment,
                             `question` int(10) unsigned NOT NULL default '0',
                             `subquestions` varchar(255) NOT NULL default '',
                             PRIMARY KEY  (`id`),
                             KEY `question` (`question`)
                           );");

        modify_database ("", "CREATE TABLE `prefix_quiz_match_sub` (
                             `id` int(10) unsigned NOT NULL auto_increment,
                             `question` int(10) unsigned NOT NULL default '0',
                             `questiontext` text NOT NULL,
                             `answertext` varchar(255) NOT NULL default '',
                             PRIMARY KEY  (`id`),
                             KEY `question` (`question`)
                           );");
    }

    if ($oldversion < 2003040901) {
        table_column("quiz", "", "shufflequestions", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "review");
        table_column("quiz", "", "shuffleanswers", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "shufflequestions");
    }

    if ($oldversion < 2003071001) {

        modify_database ("", " CREATE TABLE `prefix_quiz_numerical` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `question` int(10) unsigned NOT NULL default '0',
                               `answer` int(10) unsigned NOT NULL default '0',
                               `min` varchar(255) NOT NULL default '',
                               `max` varchar(255) NOT NULL default '',
                               PRIMARY KEY  (`id`),
                               KEY `answer` (`answer`)
                             ) TYPE=MyISAM COMMENT='Options for numerical questions'; ");
    }

    if ($oldversion < 2003072400) {
        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('quiz', 'review', 'quiz', 'name') ");
    }

    if ($oldversion < 2003072901) {
        modify_database ("", " CREATE TABLE `prefix_quiz_multianswers` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                                `question` int(10) unsigned NOT NULL default '0',
                                `answers` varchar(255) NOT NULL default '',
                                `positionkey` varchar(255) NOT NULL default '',
                                `answertype` smallint(6) NOT NULL default '0',
                                `norm` int(10) unsigned NOT NULL default '1',
                                PRIMARY KEY  (`id`),
                                KEY `question` (`question`)
                              ) TYPE=MyISAM COMMENT='Options for multianswer questions'; ");
    }

    if ($oldversion < 2003080301) {
        execute_sql(" ALTER TABLE {$CFG->prefix}quiz ADD eachattemptbuildsonthelast TINYINT(4) DEFAULT '0' NOT NULL AFTER `attempts` ");
    }

    if ($oldversion < 2003080400) {
        table_column("quiz", "eachattemptbuildsonthelast", "attemptonlast", "TINYINT", "4", "UNSIGNED", "0", "NOT NULL", "");
    }

    if ($oldversion < 2003082300) {
        table_column("quiz_questions", "", "stamp", "varchar", "255", "", "", "not null", "qtype");
    }

    if ($oldversion < 2003082301) {
        table_column("quiz_questions", "stamp", "stamp", "varchar", "255", "", "", "not null");
        table_column("quiz_questions", "", "version", "integer", "10", "", "1", "not null", "stamp");
        if ($questions = get_records("quiz_questions")) {
            foreach ($questions as $question) {
                $stamp = make_unique_id_code();
                if (!set_field("quiz_questions", "stamp", $stamp, "id", $question->id)) {
                    notify("Error while adding stamp to question id = $question->id");
                }
            }
        }
    }

    if ($oldversion < 2003082700) {
        table_column("quiz_categories", "", "stamp", "varchar", "255", "", "", "not null");
        if ($categories = get_records("quiz_categories")) {
            foreach ($categories as $category) {
                $stamp = make_unique_id_code();
                if (!set_field("quiz_categories", "stamp", $stamp, "id", $category->id)) {
                    notify("Error while adding stamp to category id = $category->id");
                }
            }
        }
    }

    if ($oldversion < 2003111100) {
        $duplicates = get_records_sql("SELECT stamp as id,count(*) as cuenta
                                       FROM {$CFG->prefix}quiz_questions
                                       GROUP BY stamp
                                       HAVING count(*)>1");

        if ($duplicates) {
            notify("You have some quiz questions with duplicate stamps IDs.  Cleaning these up.");
            foreach ($duplicates as $duplicate) {
                $questions = get_records("quiz_questions","stamp",$duplicate->id);
                $add = 1;
                foreach ($questions as $question) {
                    echo "Changing question id $question->id stamp to ".$duplicate->id.$add."<br>";
                    set_field("quiz_questions","stamp",$duplicate->id.$add,"id",$question->id);
                    $add++;
                }
            } 
        } else {
            notify("Checked your quiz questions for stamp duplication errors, but no problems were found.", "green");
        }
    }

    if ($oldversion < 2004021300) {
        table_column("quiz_questions", "", "questiontextformat", "integer", "2", "", "0", "not null", "questiontext");
    }

    if ($oldversion < 2004021900) {
        modify_database("","INSERT INTO prefix_log_display VALUES ('quiz', 'add', 'quiz', 'name');");
        modify_database("","INSERT INTO prefix_log_display VALUES ('quiz', 'update', 'quiz', 'name');");
    }

    if ($oldversion < 2004051700) {
        include_once("$CFG->dirroot/mod/quiz/lib.php");
        quiz_refresh_events();
    }

    return true;
}

?>
