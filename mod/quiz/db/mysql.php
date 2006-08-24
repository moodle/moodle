<?php // $Id$

function quiz_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG, $QTYPES, $db;
    $success = true;
    
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
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('quiz', 'view', 'quiz', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('quiz', 'report', 'quiz', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('quiz', 'attempt', 'quiz', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('quiz', 'submit', 'quiz', 'name') ");
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
        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('quiz', 'review', 'quiz', 'name') ");
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
        modify_database("","INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'add', 'quiz', 'name');");
        modify_database("","INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'update', 'quiz', 'name');");
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
        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('quiz', 'editquestions', 'quiz', 'name') ");
    }

    if ($oldversion < 2005032300) {
        modify_database ('', 'ALTER TABLE prefix_quiz_question_version RENAME prefix_quiz_question_versions;');
    }

    if ($oldversion < 2005041200) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        $sql = "select course from {$CFG->prefix}quiz_categories c, {$CFG->prefix}quiz_questions q ";
        $sql .= "where c.id = q.category ";
        $sql .= "and q.id = ";
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

    /// Changes to quiz_attempts table

        // The preview flag marks teacher previews
        table_column('quiz_attempts', '', 'preview', 'tinyint', '2', 'unsigned', '0', 'not null', 'timemodified');

        // The layout is the list of questions with inserted page breaks.
        table_column('quiz_attempts', '', 'layout', 'text', '', '', '', 'not null', 'timemodified');
        // For old quiz attempts we will set this to the repaginated question list from $quiz->questions

    /// The following updates of field values require a loop through all quizzes
        // This is because earlier versions of mysql don't allow joins in UPDATE
        if ($quizzes = get_records('quiz')) {

            // turn reporting off temporarily to avoid one line output per set_field
            $olddebug = $db->debug;
            $db->debug = false;
            echo 'Now updating '.count($quizzes).' quizzes';
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

                // set review flags in quiz table
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
            $db->debug = $olddebug;
        }

        // We can now drop the fields whose data has been moved to the review field
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz` DROP feedback");
        execute_sql(" ALTER TABLE `{$CFG->prefix}quiz` DROP correctanswers");

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

    /// Now upgrade some fields in states and newest_states tables where necessary
        // to save time on large sites only do this for attempts that have not yet been finished.
        if ($attempts = get_records_select('quiz_attempts', 'timefinish = 0')) {
            echo 'Update the states for the '.count($attempts).' open attempts';
            // turn reporting off temporarily to avoid one line output per set_field
            $olddebug = $db->debug;
            $db->debug = false;
            foreach ($attempts as $attempt) {
                quiz_upgrade_states($attempt);
            }
            $db->debug = $olddebug;
        }

    /// Entries for the log_display table

        modify_database('', " INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'preview', 'quiz', 'name');");
        modify_database('', " INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'start attempt', 'quiz', 'name');");
        modify_database('', " INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'close attempt', 'quiz', 'name');");

    /// update the default settings in $CFG
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

    /// Use tolerance instead of min and max in numerical question type
        table_column('quiz_numerical', '', 'tolerance', 'varchar', '255', '', '0.0', 'not null', 'answer');
        execute_sql("UPDATE {$CFG->prefix}quiz_numerical SET tolerance = (max-min)/2");
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

    if ($oldversion < 2005050300) {
        // length of question determines question numbering. Currently all questions require one
        // question number except for DESCRIPTION questions.
        table_column('quiz_questions', '', 'length', 'integer', '10', 'unsigned', '1', 'not null', 'qtype');
        execute_sql("UPDATE {$CFG->prefix}quiz_questions SET length = 0 WHERE qtype = ".DESCRIPTION);
    }

    if ($oldversion < 2005050408) {
        table_column('quiz_questions', '', 'penalty', 'float', '', '', '0.1', 'not null', 'defaultgrade');
        table_column('quiz_newest_states', 'new', 'newest', 'integer', '10', 'unsigned', '0', 'not null');
    }

    if ($oldversion < 2005051400) {
        modify_database('', 'ALTER TABLE prefix_quiz_rqp_type RENAME prefix_quiz_rqp_types;');
        modify_database('', "CREATE TABLE `prefix_quiz_rqp_servers` (
                      id int(10) unsigned NOT NULL auto_increment,
                      typeid int(10) unsigned NOT NULL default '0',
                      url varchar(255) NOT NULL default '',
                      can_render tinyint(2) unsigned NOT NULL default '0',
                      can_author tinyint(2) unsigned NOT NULL default '0',
                      PRIMARY KEY  (id)
                    ) TYPE=MyISAM COMMENT='Information about RQP servers';");
        if ($types = get_records('quiz_rqp_types')) {
            foreach($types as $type) {
                $server->typeid = $type->id;
                $server->url = $type->rendering_server;
                $server->can_render = 1;
                insert_record('quiz_rqp_servers', $server);
            }
        }
        modify_database('', 'ALTER TABLE prefix_quiz_rqp_types DROP rendering_server');
        modify_database('', 'ALTER TABLE prefix_quiz_rqp_types DROP cloning_server');
        modify_database('', 'ALTER TABLE prefix_quiz_rqp_types DROP flags');
    }

    if ($oldversion < 2005051401) {
        // Some earlier changes are undone here, so we need another condition
        if ($oldversion >= 2005042900) {
            // Restore the answer field
            table_column('quiz_numerical', '', 'answer', 'integer', '10', 'unsigned', '0', 'not null', 'answers');
            $singleanswer = array();
            if ($numericals = get_records('quiz_numerical')) {
                $numericals = array_values($numericals);
                $n = count($numericals);
                for ($i = 0; $i < $n; $i++) {
                    $numerical =& $numericals[$i];
                    if (strpos($numerical->answers, ',')) { // comma separated list?
                        // Back this up to delete the record after the new ones are created
                        $id = $numerical->id;
                        unset($numerical->id);
                        // We need to create a record for each answer id
                        $answers = explode(',', $numerical->answers);
                        foreach ($answers as $answer) {
                            $numerical->answer = $answer;
                            insert_record('quiz_numerical', $numerical);
                        }
                        // ... and get rid of the old record
                        delete_records('quiz_numerical', 'id', $id);
                    } else {
                        $singleanswer[] = $numerical->id;
                    }
                }
            }

            // Do all of these at once
            if (!empty($singleanswer)) {
                $singleanswer = implode(',', $singleanswer);
                modify_database('', "UPDATE prefix_quiz_numerical SET answer = answers WHERE id IN ($singleanswer);");
            }

            // All answer fields are set, so we can delete the answers field
            modify_database('', 'ALTER TABLE `prefix_quiz_numerical` DROP `answers`');

        // If the earlier changes weren't made we can safely do only the
        // bits here.
        } else {
            // Comma separated questionids will be stored as sequence
            table_column('quiz_multianswers', '', 'sequence',  'varchar', '255', '', '', 'not null', 'question');
            // Change the type of positionkey to int, so that the sorting works!
            table_column('quiz_multianswers', 'positionkey', 'positionkey',  'integer', '10', 'unsigned', '0', 'not null', '');
            table_column('quiz_questions', '', 'parent', 'integer', '10', 'unsigned', '0', 'not null', 'category');
            modify_database('', "UPDATE prefix_quiz_questions SET parent = id WHERE qtype ='".RANDOM."';");

            // Each multianswer record is converted to a question object and then
            // inserted as a new question into the quiz_questions table.
            // After that the question fields in the quiz_answers table and the
            // qtype specific tables are updated to point to the new question id.
            // Note: The quiz_numerical table is different as it stores one record
            //       per defined answer (to allow different tolerance values for
            //       different possible answers. (Currently multiple answers are
            //       not supported by the numerical editing interface, but
            //       all processing code does support that possibility.
            if ($multianswers = get_records_sql("SELECT m.id, q.category, " .
                                            "q.id AS parent, " . // question id (of multianswer question) as parent
                                            "q.name, q.questiontextformat, " .
                                            "m.norm AS defaultgrade, " . // norm is snow stored as defaultgrade
                                            "m.answertype AS qtype, " .  // just rename this
                                            "q.version, q.hidden, m.answers, " .
                                            "m.positionkey " .
                                            "FROM {$CFG->prefix}quiz_questions q, " .
                                            "     {$CFG->prefix}quiz_multianswers m " .
                                            "WHERE q.qtype = '".MULTIANSWER."' " .
                                            "AND   q.id = m.question " .
                                            "ORDER BY q.id ASC, m.positionkey ASC")) { // ordered by positionkey
                $multianswers = array_values($multianswers);
                $n        = count($multianswers);
                $parent   = $multianswers[0]->parent;
                $sequence = array();
                $positions = array();

                // Turn reporting off temporarily to avoid one line output per set_field
                global $db;
                $olddebug = $db->debug;
                $db->debug = false;
                echo 'Now updating '.$n.' cloze questions.';
                for ($i = 0; $i < $n; $i++) {
                    // Backup these two values before unsetting the object fields
                    $answers = $multianswers[$i]->answers; unset($multianswers[$i]->answers);
                    $pos = $multianswers[$i]->positionkey; unset($multianswers[$i]->positionkey);

                    // Needed for substituting multianswer ids with position keys in the $state->answer field
                    $positions[$multianswers[$i]->id] = $pos;

                // Create questions for all the multianswer victims
                    unset($multianswers[$i]->id);
                    $multianswers[$i]->length = 0;
                    $multianswers[$i]->questiontext = '';
                    $multianswers[$i]->stamp = make_unique_id_code();
                    $multianswers[$i]->name = addslashes($multianswers[$i]->name);
                    // $multianswers[$i]->parent is set in the query
                    // $multianswers[$i]->defaultgrade is set in the query
                    // $multianswers[$i]->qtype is set in the query
                    $id = insert_record('quiz_questions', $multianswers[$i]);
                    $sequence[$pos] = $id;

                // Update the quiz_answers table to point to these new questions
                execute_sql("UPDATE {$CFG->prefix}quiz_answers SET question = '$id' WHERE id IN ($answers)", false);
                // Update the questiontype tables to point to these new questions

                    if (SHORTANSWER == $multianswers[$i]->qtype) {
                        execute_sql("UPDATE {$CFG->prefix}quiz_shortanswer SET question = '$id' WHERE answers = '$answers'", false);
                    } else if (MULTICHOICE == $multianswers[$i]->qtype) {
                        execute_sql("UPDATE {$CFG->prefix}quiz_multichoice SET question = '$id' WHERE answers = '$answers'", false);
                    } else if (NUMERICAL == $multianswers[$i]->qtype) {
                        execute_sql("UPDATE {$CFG->prefix}quiz_numerical SET question = '$id' WHERE answer IN ($answers)", false);
                    }

                    // Whenever we're through with the subquestions of one multianswer
                    // question we delete the old records in the multianswers table,
                    // store a new record with the sequence in the multianswers table
                    // and point $parent to the next multianswer question.
                    if (!isset($multianswers[$i+1]) || $parent != $multianswers[$i+1]->parent) {

                        // Substituting multianswer ids with position keys in the $state->answer field
                        if ($states = get_records('quiz_states', 'question', $parent)) {
                            foreach ($states as $state) {
                                $reg = array();
                                preg_match_all('/(?:^|,)([0-9]+)-([^,]*)/', $state->answer, $reg);
                                $state->answer = '';
                                $m = count($reg[1]);
                                for ($j = 0; $j < $m; $j++) {
                                    if (isset($positions[$reg[1][$j]])) {
                                        $state->answer .= $positions[$reg[1][$j]] . '-' . $reg[2][$j] . ',';
                                    } else {
                                        notify("Undefined multianswer id ({$reg[1][$j]}) used in state #{$state->id}!");
                                        $state->answer .= $j+1 . '-' . $reg[2][$j] . ',';
                                    }
                                }
                                $state->answer = rtrim($state->answer, ','); // strip trailing comma
                                update_record('quiz_states', $state);
                            }
                        }

                        delete_records('quiz_multianswers', 'question', $parent);
                        $multi = new stdClass;
                        $multi->question = $parent;
                        $multi->sequence = implode(',', $sequence);
                        insert_record('quiz_multianswers', $multi);

                        if (isset($multianswers[$i+1])) {
                            $parent    = $multianswers[$i+1]->parent;
                            $sequence  = array();
                            $positions = array();
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
        }
    }

    if ($oldversion < 2005052004) {
        // We need to remove some duplicate entries that may be present in some databases
        // due to a faulty restore script

        // Remove duplicate entries from quiz_numerical
        if ($dups = get_records_sql("
                SELECT question, answer, count(*) as num
                FROM {$CFG->prefix}quiz_numerical
                GROUP BY question, answer
                HAVING count(*) > 1"
            )) {
            foreach ($dups as $dup) {
                $ids = get_records_sql("
                    SELECT id, id
                    FROM {$CFG->prefix}quiz_numerical
                    WHERE question = '$dup->question'
                    AND answer = '$dup->answer'"
                );
                $skip = true;
                foreach ($ids as $id) {
                    if ($skip) {
                        $skip = false;
                    } else {
                        delete_records('quiz_numerical','id', $id->id);
                    }
                }
            }
        }

        // Remove duplicate entries from quiz_shortanswer
        if ($dups = get_records_sql("
                SELECT question, answers, count(*) as num
                FROM {$CFG->prefix}quiz_shortanswer
                GROUP BY question, answers
                HAVING count(*) > 1"
            )) {
            foreach ($dups as $dup) {
                $ids = get_records_sql("
                    SELECT id, id
                    FROM {$CFG->prefix}quiz_shortanswer
                    WHERE question = '$dup->question'
                    AND answers = '$dup->answers'"
                );
                $skip = true;
                foreach ($ids as $id) {
                    if ($skip) {
                        $skip = false;
                    } else {
                        delete_records('quiz_shortanswer','id', $id->id);
                    }
                }
            }
        }

        // Remove duplicate entries from quiz_multichoice
        if ($dups = get_records_sql("
                SELECT question, answers, count(*) as num
                FROM {$CFG->prefix}quiz_multichoice
                GROUP BY question, answers
                HAVING count(*) > 1"
            )) {
            foreach ($dups as $dup) {
                $ids = get_records_sql("
                    SELECT id, id
                    FROM {$CFG->prefix}quiz_multichoice
                    WHERE question = '$dup->question'
                    AND answers = '$dup->answers'"
                );
                $skip = true;
                foreach ($ids as $id) {
                    if ($skip) {
                        $skip = false;
                    } else {
                        delete_records('quiz_multichoice','id', $id->id);
                    }
                }
            }
        }
    }

    if ($oldversion < 2005060300) {
        //Search all the orphan categories (those whose course doesn't exist)
        //and process them, deleting or moving them to site course - Bug 2459

        //Set debug to false
        $olddebug = $db->debug;
        $db->debug = false;

        //Iterate over all the quiz_categories records to get their course id
        if ($courses = get_records_sql ("SELECT DISTINCT course as id, course
                                         FROM {$CFG->prefix}quiz_categories")) {
            //Iterate over courses
            foreach ($courses as $course) {
                //If the course doesn't exist, orphan category found!
                //Process it with question_delete_course(). It will do all the hard work.
                if (!record_exists('course', 'id', $course->id)) {
                    require_once("$CFG->libdir/questionlib.php ");
                    question_delete_course($course);
                }
            }
        }
        //Reset rebug to its original state
        $db->debug = $olddebug;
    }

    if ($oldversion < 2005062600) {
        modify_database ('', "
            CREATE TABLE `prefix_quiz_essay` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `question` int(10) unsigned NOT NULL default '0',
                `answer` varchar(255) NOT NULL default '',
                PRIMARY KEY  (`id`),
                KEY `question` (`question`)
           ) TYPE=MyISAM COMMENT='Options for essay questions'");
    
        modify_database ('', "
            CREATE TABLE `prefix_quiz_essay_states` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `stateid` int(10) unsigned NOT NULL default '0',
              `graded` tinyint(4) unsigned NOT NULL default '0',
              `fraction` varchar(10) NOT NULL default '0.0',
              `response` text NOT NULL,
              PRIMARY KEY  (`id`)
            ) TYPE=MyISAM COMMENT='essay question type specific state information'");
    }

    if ($oldversion < 2005070202) {
        // add new unique id to prepare the way for lesson module to have its own attempts table
        table_column('quiz_attempts', '', 'uniqueid', 'integer', '10', 'unsigned', '0', 'not null', 'id');
        // initially we can use the id as the unique id because no other modules use attempts yet.
        execute_sql("UPDATE {$CFG->prefix}quiz_attempts SET uniqueid = id", false);
        // we set $CFG->attemptuniqueid to the next available id
        $record = get_record_sql("SELECT max(id)+1 AS nextid FROM {$CFG->prefix}quiz_attempts");
        set_config('attemptuniqueid', empty($record->nextid) ? 1 : $record->nextid);
    }
    
    if ($oldversion < 2006020801) {
        // add new field to store time delay between the first and second quiz attempt
        table_column('quiz', '', 'delay1', 'integer', '10', 'unsigned', '0', 'not null', 'popup');
        // add new field to store time delay between the second and any additional quizes
        table_column('quiz', '', 'delay2', 'integer', '10', 'unsigned', '0', 'not null', 'delay1');
    }

    if ($oldversion < 2006021101) {
        // set defaultgrade field properly (probably not necessary, but better make sure)
        execute_sql("UPDATE {$CFG->prefix}quiz_questions SET defaultgrade = '1' WHERE defaultgrade = '0'", false);
        execute_sql("UPDATE {$CFG->prefix}quiz_questions SET defaultgrade = '0' WHERE qtype = '".DESCRIPTION."'", false);
    }

    if ($oldversion < 2006021103) {
        // add new field to store the question-level shuffleanswers option
        table_column('quiz_match', '', 'shuffleanswers', 'tinyint', '4', 'unsigned', '1', 'not null', 'subquestions');
        table_column('quiz_multichoice', '', 'shuffleanswers', 'tinyint', '4', 'unsigned', '1', 'not null', 'single');
        table_column('quiz_randomsamatch', '', 'shuffleanswers', 'tinyint', '4', 'unsigned', '1', 'not null', 'choose');
    }

    if ($oldversion < 2006021104) {
        // add originalversion field for the new versioning mechanism
        table_column('quiz_question_versions', '', 'originalquestion', 'int', '10', 'unsigned', '0', 'not null', 'newquestion');
    }

    if ($oldversion < 2006021301) {
        modify_database('','ALTER TABLE prefix_quiz_attempts ADD UNIQUE INDEX uniqueid (uniqueid);');
    }

    if ($oldversion < 2006021302) {
        table_column('quiz_match_sub', '', 'code', 'int', '10', 'unsigned', '0', 'not null', 'id');
        execute_sql("UPDATE {$CFG->prefix}quiz_match_sub SET code = id", false);
    }
    if ($oldversion < 2006021304) {
        // convert sequence field to text to accomodate very long sequences, see bug 4257
        table_column('quiz_multianswers', 'sequence', 'sequence',  'text', '', '', '', 'not null', 'question');
    }

    if ($oldversion < 2006021501) {
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_newest_states RENAME {$CFG->prefix}question_sessions", false);
    }

    if ($oldversion < 2006022200) {
        // convert grade fields to float
        set_field('quiz_attempts', 'sumgrades', 0, 'sumgrades', '');
        table_column('quiz_attempts', 'sumgrades', 'sumgrades',  'float', '', '', '0', 'not null');

        set_field('quiz_answers', 'fraction', 0, 'fraction', '');
        table_column('quiz_answers', 'fraction', 'fraction',  'float', '', '', '0', 'not null');

        set_field('quiz_essay_states', 'fraction', 0, 'fraction', '');
        table_column('quiz_essay_states', 'fraction', 'fraction',  'float', '', '', '0', 'not null');

        set_field('quiz_states', 'grade', 0, 'grade', '');
        table_column('quiz_states', 'grade', 'grade',  'float', '', '', '0', 'not null');

        set_field('quiz_states', 'raw_grade', 0, 'raw_grade', '');
        table_column('quiz_states', 'raw_grade', 'raw_grade',  'float', '', '', '0', 'not null');

        set_field('quiz_states', 'penalty', 0, 'penalty', '');
        table_column('quiz_states', 'penalty', 'penalty',  'float', '', '', '0', 'not null');

        set_field('question_sessions', 'sumpenalty', 0, 'sumpenalty', '');
        table_column('question_sessions', 'sumpenalty', 'sumpenalty',  'float', '', '', '0', 'not null');
    }

    if ($oldversion < 2006022400) {
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_questions RENAME {$CFG->prefix}question", false);
    }

    if ($oldversion < 2006022402) {
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_states RENAME {$CFG->prefix}question_states", false);
    }

    if ($oldversion < 2006022800) {
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_answers RENAME {$CFG->prefix}question_answers", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_categories RENAME {$CFG->prefix}question_categories", false);
    }

    if ($oldversion < 2006031202) {
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_truefalse RENAME {$CFG->prefix}question_truefalse", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_shortanswer RENAME {$CFG->prefix}question_shortanswer", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_multianswers RENAME {$CFG->prefix}question_multianswer", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_multichoice RENAME {$CFG->prefix}question_multichoice", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_numerical RENAME {$CFG->prefix}question_numerical", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_numerical_units RENAME {$CFG->prefix}question_numerical_units", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_randomsamatch RENAME {$CFG->prefix}question_randomsamatch", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_match RENAME {$CFG->prefix}question_match", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_match_sub RENAME {$CFG->prefix}question_match_sub", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_calculated RENAME {$CFG->prefix}question_calculated", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_dataset_definitions RENAME {$CFG->prefix}question_dataset_definitions", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_dataset_items RENAME {$CFG->prefix}question_dataset_items", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_question_datasets RENAME {$CFG->prefix}question_datasets", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_rqp RENAME {$CFG->prefix}question_rqp", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_rqp_servers RENAME {$CFG->prefix}question_rqp_servers", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_rqp_states RENAME {$CFG->prefix}question_rqp_states", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_rqp_types RENAME {$CFG->prefix}question_rqp_types", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_essay RENAME {$CFG->prefix}question_essay", false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_essay_states RENAME {$CFG->prefix}question_essay_states", false);
    }

    if ($oldversion < 2006032100) {
        // change from the old questiontype numbers to using the questiontype names
        table_column('question', 'qtype', 'qtype',  'varchar', 20, '', '', 'not null');
        set_field('question', 'qtype', 'shortanswer', 'qtype', 1);
        set_field('question', 'qtype', 'truefalse', 'qtype', 2);
        set_field('question', 'qtype', 'multichoice', 'qtype', 3);
        set_field('question', 'qtype', 'random', 'qtype', 4);
        set_field('question', 'qtype', 'match', 'qtype', 5);
        set_field('question', 'qtype', 'randomsamatch', 'qtype', 6);
        set_field('question', 'qtype', 'description', 'qtype', 7);
        set_field('question', 'qtype', 'numerical', 'qtype', 8);
        set_field('question', 'qtype', 'multianswer', 'qtype', 9);
        set_field('question', 'qtype', 'calculated', 'qtype', 10);
        set_field('question', 'qtype', 'rqp', 'qtype', 11);
        set_field('question', 'qtype', 'essay', 'qtype', 12);
    }

    if ($oldversion < 2006032200) {
        // set version for all questiontypes that already have their tables installed
        set_config('qtype_calculated_version', 2006032100);
        set_config('qtype_essay_version', 2006032100);
        set_config('qtype_match_version', 2006032100);
        set_config('qtype_multianswer_version', 2006032100);
        set_config('qtype_multichoice_version', 2006032100);
        set_config('qtype_numerical_version', 2006032100);
        set_config('qtype_randomsamatch_version', 2006032100);
        set_config('qtype_rqp_version', 2006032100);
        set_config('qtype_shortanswer_version', 2006032100);
        set_config('qtype_truefalse_version', 2006032100);
    }

    if ($oldversion < 2006040600) {
        table_column('question_sessions', '', 'comment', 'text', '', '', '', 'not null', 'sumpenalty');
    }

    if ($oldversion < 2006040900) {
        modify_database('', "UPDATE prefix_question SET parent = id WHERE qtype ='random';");
    }

    if ($oldversion < 2006041000) {
        modify_database('', " INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'continue attempt', 'quiz', 'name');");
    }

    if ($oldversion < 2006041001) {
        table_column('question', 'version', 'version', 'varchar', 255);
    }

    if ($oldversion < 2006042800) {
        // Check we have some un-renamed tables (verified in some servers)
        if ($tables = $db->MetaTables('TABLES')) {
            if (in_array($CFG->prefix.'quiz_randommatch', $tables) &&
                !in_array($CFG->prefix.'question_randomsamatch', $tables)) {
                modify_database ("", "ALTER TABLE prefix_quiz_randommatch RENAME prefix_question_randomsamatch ");
            }
            // Check for one possible missing field in one table
            if ($columns = $db->MetaColumnNames($CFG->prefix.'question_randomsamatch')) {
                if (!in_array('shuffleanswers', $columns)) {
                    table_column('question_randomsamatch', '', 'shuffleanswers', 'tinyint', '4', 'unsigned', '1', 'not null', 'choose');
                }
            }
        }
    }

    if ($oldversion < 2006051300) {
        // The newgraded field must always point to a valid state
        modify_database("","UPDATE prefix_question_sessions SET newgraded = newest where newgraded = '0'");

        // Only perform this if hasn't been performed before (in MOODLE_16_STABLE branch - bug 5717)
        $tables = $db->MetaTables('TABLES');
        if (!in_array($CFG->prefix . 'question_attempts', $tables)) {
            // The following table is discussed in bug 5468
            modify_database ("", "CREATE TABLE prefix_question_attempts (
                                      id int(10) unsigned NOT NULL auto_increment,
                                      modulename varchar(20) NOT NULL default 'quiz',
                                      PRIMARY KEY  (id)
                                    ) TYPE=MyISAM COMMENT='Student attempts. This table gets extended by the modules';");
            // create one entry for all the existing quiz attempts
            modify_database ("", "INSERT INTO prefix_question_attempts (id)
                                       SELECT uniqueid
                                       FROM prefix_quiz_attempts;");
        }
    }

    if ($oldversion < 2006060700) { // fix for 5720

        // Copy the teacher comments from the question_essay_states table to the new
        // question_sessions table.

        // Get the attempt unique ID, teacher comment, graded flag, state ID, and question ID
        // based on the quesiont_essay_states
        if ($results = get_records_sql("SELECT a.uniqueid, es.response AS essaycomment, es.graded AS isgraded, 
                                               qs.id AS stateid, qs.question AS questionid 
                                        FROM {$CFG->prefix}question_states as qs,
                                             {$CFG->prefix}question_essay_states es, 
                                             {$CFG->prefix}quiz_attempts a 
                                        WHERE es.stateid = qs.id AND a.uniqueid = qs.attempt")) {
            foreach ($results as $result) {
                // Create a state object to be used for updating
                $state = new stdClass;
                $state->id = $result->stateid;

                if ($result->isgraded) {
                    // Graded - save comment to the sessions and change state event to QUESTION_EVENTMANUALGRADE
                    if (!set_field('question_sessions', 'comment', $result->essaycomment, 'attemptid', $result->uniqueid, 'questionid', $result->questionid)) {
                        notify("Essay Table Migration: Cannot save comment");
                    }
                    $state->event = 9; //QUESTION_EVENTMANUALGRADE;
                } else {
                    // Not graded
                    $state->event = 7; //QUESTION_EVENTSUBMIT;
                }

                // Save the event
                if (!update_record('question_states', $state)) {
                    notify("Essay Table Migration: Cannot update state");
                }
            }
        }
        
        // dropping unused tables
        execute_sql('DROP TABLE '.$CFG->prefix.'question_essay_states');
        execute_sql('DROP TABLE '.$CFG->prefix.'question_essay');
        execute_sql('DROP TABLE '.$CFG->prefix.'quiz_attemptonlast_datasets', false);
    }

    if ($oldversion < 2006081000) {
        // Add a column to the the question table to store the question commentary.
        $success = $success && table_column('question', '', 'commentarytext', 'text', '', '', '', 'not null', 'image');

        // Adjust the quiz review options so that commentary is displayed whenever feedback is.
        $success = $success && execute_sql('UPDATE ' . $CFG->prefix . 'quiz SET review = ' .
                '(review & ~' . QUIZ_REVIEW_COMMENTARY . ') | ' . // Clear any existing junk from the commenary bits.
                '((review & ' . QUIZ_REVIEW_FEEDBACK . ') * 8)'); // Set the commentary bits to be the same as the feedback ones.

        // Same adjustment to the defaults for new quizzes.
        $success = $success && set_config('quiz_review', ($CFG->quiz_review & ~QUIZ_REVIEW_COMMENTARY) |
                (($CFG->quiz_review & QUIZ_REVIEW_FEEDBACK) << 3));
    }
    
    if ($success && $oldversion < 2006081400) {
        $success = $success && modify_database('', "
            CREATE TABLE prefix_quiz_feedback (
                id int(10) unsigned NOT NULL auto_increment,
                quizid int(10) unsigned NOT NULL default '0',
                feedbacktext text NOT NULL default '',
                mingrade double NOT NULL default '0',
                maxgrade double NOT NULL default '0',
                PRIMARY KEY (id),
                KEY quizid (quizid)
            ) TYPE=MyISAM COMMENT='Feedback given to students based on their overall score on the test';
        ");
    
        $success = $success && execute_sql("
            INSERT INTO {$CFG->prefix}quiz_feedback (quizid, feedbacktext, maxgrade, mingrade)
            SELECT id, '', grade + 1, 0 FROM {$CFG->prefix}quiz;
        ");
    }

    if ($success && $oldversion < 2006082400) {
        $success = $success && table_column('question_sessions', 'comment', 'manualcomment', 'text', '', '', '');
    }

    return $success;
}

?>
