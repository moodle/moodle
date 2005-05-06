<?php // $Id$

function quiz_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG, $QUIZ_QTYPES;
    require_once("$CFG->dirroot/mod/quiz/locallib.php");

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
                    echo "Changing question id $question->id stamp to ".$duplicate->id.$add."<br />";
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

    if ($oldversion < 2004060200) {
        execute_sql(" ALTER TABLE {$CFG->prefix}quiz ADD timelimit INT(2) UNSIGNED DEFAULT '0' NOT NULL ");
    }

    if ($oldversion < 2004070700) {
        table_column("quiz", "", "password", "varchar", "255", "", "", "not null", "");
        table_column("quiz", "", "subnet", "varchar", "255", "", "", "not null", "");
    }

    if ($oldversion < 2004073001) {
        // Six new tables:

        // One table for handling units for numerical questions
        modify_database ("", " CREATE TABLE `prefix_quiz_numerical_units` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `question` int(10) unsigned NOT NULL default '0',
                               `multiplier` decimal(40,20) NOT NULL default '1.00000000000000000000',
                               `unit` varchar(50) NOT NULL default '',
                               PRIMARY KEY  (`id`)
                ) TYPE=MyISAM COMMENT='Optional unit options for numerical questions'; ");

        // Four tables for handling distribution and storage of
        // individual data for dataset dependent question types
        modify_database ("", " CREATE TABLE `prefix_quiz_attemptonlast_datasets` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `category` int(10) unsigned NOT NULL default '0',
                               `userid` int(10) unsigned NOT NULL default '0',
                               `datasetnumber` int(10) unsigned NOT NULL default '0',
                               PRIMARY KEY  (`id`),
                               UNIQUE KEY `category` (`category`,`userid`)
            ) TYPE=MyISAM COMMENT='Dataset number for attemptonlast attempts per user'; ");
        modify_database ("", " CREATE TABLE `prefix_quiz_dataset_definitions` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `category` int(10) unsigned NOT NULL default '0',
                               `name` varchar(255) NOT NULL default '',
                               `type` int(10) NOT NULL default '0',
                               `options` varchar(255) NOT NULL default '',
                               `itemcount` int(10) unsigned NOT NULL default '0',
                               PRIMARY KEY  (`id`)
            ) TYPE=MyISAM COMMENT='Organises and stores properties for dataset items'; ");
        modify_database ("", " CREATE TABLE `prefix_quiz_dataset_items` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `definition` int(10) unsigned NOT NULL default '0',
                               `number` int(10) unsigned NOT NULL default '0',
                               `value` varchar(255) NOT NULL default '',
                               PRIMARY KEY  (`id`),
                               KEY `definition` (`definition`)
                             ) TYPE=MyISAM COMMENT='Individual dataset items'; ");
        modify_database ("", " CREATE TABLE `prefix_quiz_question_datasets` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `question` int(10) unsigned NOT NULL default '0',
                               `datasetdefinition` int(10) unsigned NOT NULL default '0',
                               PRIMARY KEY  (`id`),
                               KEY `question` (`question`,`datasetdefinition`)
            ) TYPE=MyISAM COMMENT='Many-many relation between questions and dataset definitions'; ");

        // One table for new question type calculated
        //  - the first dataset dependent question type
        modify_database ("", " CREATE TABLE `prefix_quiz_calculated` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `question` int(10) unsigned NOT NULL default '0',
                               `answer` int(10) unsigned NOT NULL default '0',
                               `tolerance` varchar(20) NOT NULL default '0.0',
                               `tolerancetype` int(10) NOT NULL default '1',
                               `correctanswerlength` int(10) NOT NULL default '2',
                               PRIMARY KEY  (`id`),
                               KEY `question` (`question`)
                ) TYPE=MyISAM COMMENT='Options for questions of type calculated'; ");
    }

    if ($oldversion < 2004111400) {
        table_column("quiz_responses", "answer", "answer", "text", "", "", "", "not null");
    }

    if ($oldversion < 2004111700) {
        execute_sql("ALTER TABLE {$CFG->prefix}quiz DROP INDEX course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_calculated DROP INDEX answer;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_categories DROP INDEX course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_dataset_definitions DROP INDEX category;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_numerical DROP INDEX question;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_numerical_units DROP INDEX question;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_questions DROP INDEX category;",false);

        modify_database('','ALTER TABLE prefix_quiz ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_quiz_calculated ADD INDEX answer (answer);');
        modify_database('','ALTER TABLE prefix_quiz_categories ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_quiz_dataset_definitions ADD INDEX category (category);');
        modify_database('','ALTER TABLE prefix_quiz_numerical ADD INDEX question (question);');
        modify_database('','ALTER TABLE prefix_quiz_numerical_units ADD INDEX question (question);');
        modify_database('','ALTER TABLE prefix_quiz_questions ADD INDEX category (category);');
    }

    if ($oldversion < 2004120501) {
        table_column("quiz_calculated", "", "correctanswerformat", "integer", "10", "", "2", "not null", "correctanswerlength");
    }

    if ($oldversion < 2004121400) {  // New field to determine popup window behaviour
        table_column("quiz", "", "popup", "integer", "4", "", "0", "not null", "subnet");
    }

    if ($oldversion < 2005010201) {
        table_column('quiz_categories', '', 'parent');
        table_column('quiz_categories', '', 'sortorder', 'integer', '10', '', '999');
    }

    if ($oldversion < 2005010300) {
        table_column("quiz", "", "questionsperpage", "integer", "10", "", "0", "not null", "review");
    }

    if ($oldversion < 2005012700) {
        table_column('quiz_grades', 'grade', 'grade', 'real', 2, '');
    }

    if ($oldversion < 2005021400) {
        table_column("quiz", "", "decimalpoints", "integer", "4", "", "2", "not null", "grademethod");
    }

    if($oldversion < 2005022800) {
        table_column('quiz_questions', '', 'hidden', 'integer', '1', 'unsigned', '0', 'not null', 'version');
        table_column('quiz_responses', '', 'originalquestion', 'integer', '10', 'unsigned', '0', 'not null', 'question');
        modify_database ('', "CREATE TABLE `prefix_quiz_question_version` (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `quiz` int(10) unsigned NOT NULL default '0',
                              `oldquestion` int(10) unsigned NOT NULL default '0',
                              `newquestion` int(10) unsigned NOT NULL default '0',
                              `userid` int(10) unsigned NOT NULL default '0',
                              `timestamp` int(10) unsigned NOT NULL default '0',
                              PRIMARY KEY  (`id`)
                            ) TYPE=MyISAM COMMENT='The mapping between old and new versions of a question';");
    }

    if ($oldversion < 2005032000) {
        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('quiz', 'editquestions', 'quiz', 'name') ");
    }

    if ($oldversion < 2005032300) {
        modify_database ('', 'ALTER TABLE prefix_quiz_question_version RENAME prefix_quiz_question_versions;');
    }

    if ($oldversion < 2005041200) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        $sql = "select course from {$CFG->prefix}quiz_categories, {$CFG->prefix}quiz_questions ";
        $sql .= "where {$CFG->prefix}quiz_category.id = {$CFG->prefix}quiz_questions.category ";
        $sql .= "and {$CFG->prefix}quiz_questions.id = ";
        $wtm->update( 'quiz_questions', 'questiontext', 'questiontextformat', $sql );
    }

    if ($oldversion < 2005041304) {
        // make random questions hidden
        modify_database('', "UPDATE prefix_quiz_questions SET hidden = '1' WHERE qtype ='".RANDOM."';");
    }

    if ($oldversion < 2005042002) {
        table_column('quiz_answers', 'answer', 'answer', 'text', '', '', '', 'not null', '');
    }

    if ($oldversion < 2005042400) {

    // Changes to quiz table

        // The bits of the optionflags field will hold various option flags
        table_column('quiz', '', 'optionflags', 'integer', '10', 'unsigned', '0', 'not null', 'timeclose');

        // The penalty scheme
        table_column('quiz', '', 'penaltyscheme', 'integer', '4', 'unsigned', '0', 'not null', 'optionflags');

        // The review options are now all stored in the bits of the review field
        table_column('quiz', 'review', 'review', 'integer', 10, 'unsigned', 0, 'not null', '');
        // We need to set the bits using the data in the existing fields
        if ($quizzes = get_records('quiz')) {
            foreach ($quizzes as $quiz) {
                $review = (QUIZ_REVIEW_IMMEDIATELY & (QUIZ_REVIEW_RESPONSES + QUIZ_REVIEW_SCORES));
                if ($quiz->feedback) {
                    $review += (QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_FEEDBACK);
                }
                if ($quiz->correctanswers) {
                    $review += (QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_ANSWERS);
                }
                if ($quiz->review & 1) {
                    $review += QUIZ_REVIEW_CLOSED;
                }
                if ($quiz->review & 2) {
                    $review += QUIZ_REVIEW_OPEN;
                }
                set_field('quiz', 'review', $review, 'id', $quiz->id);
            }
        }
        // update the default settings in $CFG
        $review = (QUIZ_REVIEW_IMMEDIATELY & (QUIZ_REVIEW_RESPONSES + QUIZ_REVIEW_SCORES));
        if (!empty($CFG->quiz_feedback)) {
            $review += (QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_FEEDBACK);
        }
        if (!empty($CFG->quiz_correctanswers)) {
            $review += (QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_ANSWERS);
        }
        if (isset($CFG->quiz_review) and ($CFG->quiz_review & 1)) {
            $review += QUIZ_REVIEW_CLOSED;
        }
        if (isset($CFG->quiz_review) and ($CFG->quiz_review & 2)) {
            $review += QUIZ_REVIEW_OPEN;
        }
        set_config('quiz_review', $review);

        // We can now drop the fields whose data has been moved to the review field
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz` DROP feedback");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz` DROP correctanswers");

    /// Changes to quiz_attempts table

        // The preview flag marks teacher previews
        table_column('quiz_attempts', '', 'preview', 'tinyint', '2', 'unsigned', '0', 'not null', 'timemodified');

        // The layout is the list of questions with inserted page breaks.
        table_column('quiz_attempts', '', 'layout', 'text', '', '', '', 'not null', 'timemodified');
        // For old quiz attempts we will set this to the repaginated question list from $quiz->questions

        if ($quizzes) {
            $quiz->questions = ($quiz->questionsperpage) ? quiz_repaginate($quiz->questions, $quiz->questionsperpage) : $quiz->questions;
            foreach ($quizzes as $quiz) {

                // repaginate
                if ($quiz->questionsperpage) {
                    $quiz->questions = quiz_repaginate($quiz->questions, $quiz->questionsperpage);
                    set_field('quiz', 'questions', $quiz->questions, 'id', $quiz->id);
                }
                set_field('quiz_attempts', 'layout', $quiz->questions, 'quiz', $quiz->id);

                // set preview flag
                if ($teachers = get_course_teachers($quiz->course)) {
                    $teacherids = implode(',', array_keys($teachers));
                    execute_sql("UPDATE {$CFG->prefix}quiz_attempts SET preview = 1 WHERE userid IN ($teacherids)");
                }
            }
        }

    /// Renaming tables

        // rename the quiz_question_grades table to quiz_question_instances
        modify_database ('', 'ALTER TABLE prefix_quiz_question_grades RENAME prefix_quiz_question_instances;');

        // rename the quiz_responses table quiz_states
        modify_database ('', 'ALTER TABLE prefix_quiz_responses RENAME prefix_quiz_states;');

    /// add columns to quiz_states table

        // The sequence number of the state.
        table_column('quiz_states', '', 'seq_number', 'integer', '6', 'unsigned', '0', 'not null', 'originalquestion');
        // For existing states we leave this at 0 because in the old quiz code there was only one response allowed

        // The time the state was created.
        table_column('quiz_states', '', 'timestamp', 'integer', '10', 'unsigned', '0', 'not null', 'answer');
        // For existing states we will below set this to the timemodified field of the attempt

        // The type of event that led to the creation of the state
        table_column('quiz_states', '', 'event', 'integer', '4', 'unsigned', '0', 'not null', 'timestamp');

        // The raw grade
        table_column('quiz_states', '', 'raw_grade', 'varchar', '10', '', '', 'not null', 'grade');
        // For existing states (no penalties) this is equal to the grade
        execute_sql("UPDATE {$CFG->prefix}quiz_states SET raw_grade = grade");

        // The penalty that the response attracted
        table_column('quiz_states', '', 'penalty', 'varchar', '10', '', '0.0', 'not null', 'raw_grade');
        // For existing states this can stay at 0 because penalties did not exist previously.

    /// New table for pointers to newest and newest graded states

        modify_database('', "CREATE TABLE `prefix_quiz_newest_states` (
                             `id` int(10) unsigned NOT NULL auto_increment,
                             `attemptid` int(10) unsigned NOT NULL default '0',
                             `questionid` int(10) unsigned NOT NULL default '0',
                             `new` int(10) unsigned NOT NULL default '0',
                             `newgraded` int(10) unsigned NOT NULL default '0',
                             `sumpenalty` varchar(10) NOT NULL default '0.0',
                             PRIMARY KEY  (`id`),
                             UNIQUE KEY `attemptid` (`attemptid`,`questionid`)
                           ) TYPE=MyISAM COMMENT='Gives ids of the newest open and newest graded states';");

    /// Now upgrade states and newest_states where necessary
        // to save time on large sites only do this for attempts that have not yet been finished.
        if ($attempts = get_records_select('quiz_attempts', 'timefinish = 0')) {
            foreach ($attempts as $attempt) {
                quiz_upgrade_states($attempt);
            }
        }

    /// Entries for the log_display table

        modify_database('', " INSERT INTO prefix_log_display VALUES ('quiz', 'preview', 'quiz', 'name');");
        modify_database('', " INSERT INTO prefix_log_display VALUES ('quiz', 'start attempt', 'quiz', 'name');");
        modify_database('', " INSERT INTO prefix_log_display VALUES ('quiz', 'close attempt', 'quiz', 'name');");

    /// Fix numerical question type
        table_column('quiz_numerical', '', 'tolerance', 'varchar', '255', '', '0.0', 'not null', 'question');
        if($rows = get_records('quiz_numerical')) {
            foreach ($rows as $row) {
                $row->tolerance = ((float)$row->min + (float)$row->max)/2;
                set_field('quiz_numerical', 'tolerance', $row->tolerance, 'id', $row->id);
            }
        }
        // modify_database('', 'ALTER TABLE `prefix_quiz_numerical` DROP `answer`'); // There is no need for it at all
        modify_database('', 'ALTER TABLE `prefix_quiz_numerical` DROP `min`'); // Replaced by tolerance
        modify_database('', 'ALTER TABLE `prefix_quiz_numerical` DROP `max`'); // Replaced by tolerance

    /// Tables for Remote Questions
        modify_database ('', "CREATE TABLE `prefix_quiz_rqp` (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `question` int(10) unsigned NOT NULL default '0',
                              `type` int(10) unsigned NOT NULL default '0',
                              `source` longblob NOT NULL default '',
                              `format` varchar(255) NOT NULL default '',
                              `flags` tinyint(3) unsigned NOT NULL default '0',
                              `maxscore` int(10) unsigned NOT NULL default '1',
                              PRIMARY KEY  (`id`),
                              KEY `question` (`question`)
                              ) TYPE=MyISAM COMMENT='Options for RQP questions';");

        modify_database ('', "CREATE TABLE `prefix_quiz_rqp_type` (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `name` varchar(255) NOT NULL default '',
                              `rendering_server` varchar(255) NOT NULL default '',
                              `cloning_server` varchar(255) NOT NULL default '',
                              `flags` tinyint(3) NOT NULL default '0',
                              PRIMARY KEY  (`id`),
                              UNIQUE KEY `name` (`name`)
                              ) TYPE=MyISAM COMMENT='RQP question types and the servers to be used to process them';");

        modify_database ('', "CREATE TABLE `prefix_quiz_rqp_states` (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `stateid` int(10) unsigned NOT NULL default '0',
                              `responses` text NOT NULL default '',
                              `persistent_data` text NOT NULL default '',
                              `template_vars` text NOT NULL default '',
                              PRIMARY KEY  (`id`)
                              ) TYPE=MyISAM COMMENT='RQP question type specific state information';");
    }

    if ($oldversion < 2005042900) {

        table_column('quiz_multianswers', '', 'sequence',  'varchar', '255', '', '', 'not null', 'question');
        table_column('quiz_numerical', '', 'answers', 'varchar', '255', '', '', 'not null', 'answer');
        modify_database('', 'UPDATE prefix_quiz_numerical SET answers = answer');
        table_column('quiz_questions', '', 'parent', 'integer', '10', 'unsigned', '0', 'not null', 'category');
        modify_database('', "UPDATE prefix_quiz_questions SET parent = id WHERE qtype ='".RANDOM."';");

        // convert multianswer questions to the new model
        if ($multianswers = get_records_sql("SELECT m.id, q.category, q.id AS parent,
                                        q.name, q.questiontextformat, m.norm AS
                                        defaultgrade, m.answertype AS qtype,
                                        q.version, q.hidden, m.answers,
                                        m.positionkey
                                        FROM {$CFG->prefix}quiz_questions q,
                                             {$CFG->prefix}quiz_multianswers m
                                        WHERE q.qtype = '".MULTIANSWER."'
                                        AND   q.id = m.question
                                        ORDER BY q.id ASC, m.positionkey ASC")) {
            $multianswers = array_values($multianswers);
            $n        = count($multianswers);
            $parent   = $multianswers[0]->parent;
            $sequence = array();

            // turn reporting off temporarily to avoid one line output per set_field
            global $db;
            $olddebug = $db->debug;
            // $db->debug = false;
            for ($i = 0; $i < $n; $i++) {
                $answers = $multianswers[$i]->answers; unset($multianswers[$i]->answers);
                $pos = $multianswers[$i]->positionkey; unset($multianswers[$i]->positionkey);

            // create questions for all the multianswer victims
                unset($multianswers[$i]->id);
                $multianswers[$i]->length = 0;
                $multianswers[$i]->questiontext = '';
                $multianswers[$i]->stamp = make_unique_id_code();
                $id = insert_record('quiz_questions', $multianswers[$i]);
                $sequence[$pos] = $id;

            // update the answers table to point to these new questions
                modify_database('', "UPDATE prefix_quiz_answers SET question = '$id' WHERE id IN ($answers);");
            // update the questiontype tables to point to these new questions
                if (SHORTANSWER == $multianswers[$i]->qtype) {
                    modify_database('', "UPDATE prefix_quiz_shortanswer SET question = '$id' WHERE answers = '$answers';");
                } else if (NUMERICAL == $multianswers[$i]->qtype) {
                    if (strpos($answers, ',')) {
                        $numerical = get_records_list('quiz_numerical', 'answer', $answers);
                        // Get the biggest tolerance value
                        $tolerance = 0;
                        foreach ($numerical as $num) {
                            $tolerance = ($tolerance < $num->tolerance ? $num->tolerance : $tolerance);
                        }
                        delete_records_select('quiz_numerical', "answer IN ($answers)");
                        $new = new stdClass;
                        $new->question  = $id;
                        $new->tolerance = $tolerance;
                        $new->answers   = $answers;
                        insert_record('quiz_numerical', $new);
                        unset($numerical, $new, $tolerance);
                    } else {
                        modify_database('', "UPDATE prefix_quiz_numerical SET question = '$id', answers = '$answers' WHERE answer IN ($answers);");
                    }
                } else if (MULTICHOICE == $multianswers[$i]->qtype) {
                    modify_database('', "UPDATE prefix_quiz_multichoice SET question = '$id' WHERE answers = '$answers';");
                }

                if (!isset($multianswers[$i+1]) || $parent != $multianswers[$i+1]->parent) {
                    delete_records('quiz_multianswers', 'question', $parent);
                    $multi = new stdClass;
                    $multi->question = $parent;
                    $multi->sequence = implode(',', $sequence);
                    insert_record('quiz_multianswers', $multi);
                    if (isset($multianswers[$i+1])) {
                        $parent   = $multianswers[$i+1]->parent;
                        $sequence = array();
                    }
                }
            }
            $db->debug = $olddebug;
        }

        // Remove redundant fields from quiz_multianswers
        modify_database('', 'ALTER TABLE `prefix_quiz_multianswers` DROP `answers`');
        modify_database('', 'ALTER TABLE `prefix_quiz_multianswers` DROP `positionkey`');
        modify_database('', 'ALTER TABLE `prefix_quiz_multianswers` DROP `answertype`');
        modify_database('', 'ALTER TABLE `prefix_quiz_multianswers` DROP `norm`');

        // Change numerical from answer to answers
        modify_database('', 'ALTER TABLE `prefix_quiz_numerical` DROP `answer`');
    }

    if ($oldversion < 2005050300) {
        // length of question determines question numbering. Currently all questions require one
        // question number except for DESCRIPTION questions.
        table_column('quiz_questions', '', 'length', 'integer', '10', 'unsigned', '1', 'not null', 'qtype');
        execute_sql("UPDATE {$CFG->prefix}quiz_questions SET length = 0 WHERE qtype = ".DESCRIPTION);
    }

    if ($oldversion < 2005050500) {
        table_column('quiz_questions', '', 'penalty', 'float', '', '', '0.1', 'not null', 'defaultgrade');
    }
    return true;
}

?>
