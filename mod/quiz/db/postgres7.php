<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function quiz_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG, $db;
    $success = true;

    include_once("$CFG->dirroot/mod/quiz/locallib.php");

    if ($success && $oldversion < 2003010100) {
        $success = $success && execute_sql(" ALTER TABLE {$CFG->prefix}quiz ADD review integer DEFAULT '0' NOT NULL AFTER `grademethod` ");
    }

    if ($success && $oldversion < 2003010301) {
        $success = $success && table_column("quiz_truefalse", "true", "trueanswer", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
        $success = $success && table_column("quiz_truefalse", "false", "falseanswer", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
        $success = $success && table_column("quiz_questions", "type", "qtype", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
    }

    if ($success && $oldversion < 2003022303) {
        $success = $success && modify_database ("", "CREATE TABLE prefix_quiz_randommatch (
                                  id SERIAL PRIMARY KEY,
                                  question integer NOT NULL default '0',
                                  choose integer NOT NULL default '4'
                              );");
    }
    if ($success && $oldversion < 2003030303) {
        $success = $success && table_column("quiz_questions", "", "defaultgrade", "INTEGER", "6", "UNSIGNED", "1", "NOT NULL", "image");
    }

    if ($success && $oldversion < 2003033100) {
        $success = $success && modify_database ("", "ALTER TABLE prefix_quiz_randommatch RENAME prefix_quiz_randomsamatch ");
        $success = $success && modify_database ("", "CREATE TABLE prefix_quiz_match_sub (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 questiontext text NOT NULL default '',
                                 answertext varchar(255) NOT NULL default ''
                              );");
        $success = $success && modify_database ("", "CREATE INDEX prefix_quiz_match_sub_question_idx ON prefix_quiz_match_sub (question);");

        $success = $success && modify_database ("", "CREATE TABLE prefix_quiz_multichoice (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 layout integer NOT NULL default '0',
                                 answers varchar(255) NOT NULL default '',
                                 single integer NOT NULL default '0'
                               );");
        $success = $success && modify_database ("", "CREATE INDEX prefix_quiz_multichoice_question_idx ON prefix_quiz_multichoice (question);");
    }

    if ($success && $oldversion < 2003040901) {
        $success = $success && table_column("quiz", "", "shufflequestions", "INTEGER", "5", "UNSIGNED", "0", "NOT NULL", "review");
        $success = $success && table_column("quiz", "", "shuffleanswers", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "shufflequestions");
    }
    if ($success && $oldversion < 2003042702) {
        $success = $success && modify_database ("", "CREATE TABLE prefix_quiz_match (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 subquestions varchar(255) NOT NULL default ''
                               );");
        $success = $success && modify_database ("", "CREATE INDEX prefix_quiz_match_question_idx ON prefix_quiz_match (question);");
    }
    if ($success && $oldversion < 2003071001) {
        $success = $success && modify_database ("", " CREATE TABLE prefix_quiz_numerical (
                               id SERIAL PRIMARY KEY,
                               question integer NOT NULL default '0',
                               answer integer NOT NULL default '0',
                               min varchar(255) NOT NULL default '',
                               max varchar(255) NOT NULL default ''
                               ); ");
        $success = $success && modify_database ("", "CREATE INDEX prefix_quiz_numerical_answer_idx ON prefix_quiz_numerical (answer);");
    }

    if ($success && $oldversion < 2003072400) {
        $success = $success && execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('quiz', 'review', 'quiz', 'name') ");
    }

    if ($success && $oldversion < 2003082300) {
        $success = $success && modify_database ("", " CREATE TABLE prefix_quiz_multianswers (
                               id SERIAL PRIMARY KEY,
                               question integer NOT NULL default '0',
                               answers varchar(255) NOT NULL default '',
                               positionkey varchar(255) NOT NULL default '',
                               answertype integer NOT NULL default '0',
                               norm integer NOT NULL default '1'
                              ); ");
        $success = $success && modify_database ("", "CREATE INDEX prefix_quiz_multianswers_question_idx ON prefix_quiz_multianswers (question);");

        $success = $success && table_column("quiz", "", "attemptonlast", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "attempts");

        $success = $success && table_column("quiz_questions", "", "stamp", "varchar", "255", "", "qtype");
    }

    if ($success && $oldversion < 2003082301) {
        $success = $success && table_column("quiz_questions", "", "version", "integer", "10", "", "1", "not null", "stamp");
        if ($questions = get_records("quiz_questions")) {
            foreach ($questions as $question) {
                $stamp = make_unique_id_code();
                if (!($success = $success && set_field("quiz_questions", "stamp", $stamp, "id", $question->id))) {
                    notify("Error while adding stamp to question id = $question->id");
                    break;
                }
            }
        }
    }

    if ($success && $oldversion < 2003082700) {
        table_column("quiz_categories", "", "stamp", "varchar", "255", "", "", "not null");
        if ($categories = get_records("quiz_categories")) {
            foreach ($categories as $category) {
                $stamp = make_unique_id_code();
                if (!($success = $success && set_field("quiz_categories", "stamp", $stamp, "id", $category->id))) {
                    notify("Error while adding stamp to category id = $category->id");
                    break;
                }
            }
        }
    }

    if ($success && $oldversion < 2003111100) {
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
                    $success = $success && set_field("quiz_questions","stamp",$duplicate->id.$add,"id",$question->id);
                    $add++;
                }
            }
        } else {
            notify("Checked your quiz questions for stamp duplication errors, but no problems were found.", "green");
        }
    }

    if ($success && $oldversion < 2004021300) {
        $success = $success && table_column("quiz_questions", "", "questiontextformat", "integer", "2", "", "0", "not null", "questiontext");
    }

    if ($success && $oldversion < 2004021900) {
        $success = $success && modify_database("","INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'add', 'quiz', 'name');");
        $success = $success && modify_database("","INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'update', 'quiz', 'name');");
    }

    if ($success && $oldversion < 2004051700) {
        include_once("$CFG->dirroot/mod/quiz/lib.php");
        $success = $success && quiz_refresh_events();
    }
    if ($success && $oldversion < 2004060200) {
        $success = $success && table_column("quiz", "", "timelimit", "integer", "", "", "0", "NOT NULL", "");
    }

    if ($success && $oldversion < 2004070700) {
        $success = $success && table_column("quiz", "", "password", "varchar", "255", "", "", "not null", "");
        $success = $success && table_column("quiz", "", "subnet", "varchar", "255", "", "", "not null", "");
    }

    if ($success && $oldversion < 2004073001) {
        // Six new tables:


        $success = $success && modify_database ( "", "BEGIN;");

        // One table for handling units for numerical questions
        $success = $success && modify_database ("", " CREATE TABLE prefix_quiz_numerical_units (
                               id SERIAL8 PRIMARY KEY,
                               question INT8  NOT NULL default '0',
                               multiplier decimal(40,20) NOT NULL default '1.00000000000000000000',
                               unit varchar(50) NOT NULL default ''
                );" );


        // Four tables for handling distribution and storage of
        // individual data for dataset dependent question types
        $success = $success && modify_database ("", " CREATE TABLE prefix_quiz_attemptonlast_datasets (
                               id SERIAL8 PRIMARY KEY,
                               category INT8  NOT NULL default '0',
                               userid INT8  NOT NULL default '0',
                               datasetnumber INT8  NOT NULL default '0',
                               CONSTRAINT prefix_quiz_attemptonlast_datasets_category_userid UNIQUE (category,userid)
            ) ;");

        $success = $success && modify_database ("", " CREATE TABLE prefix_quiz_dataset_definitions (
                               id SERIAL8 PRIMARY KEY,
                               category INT8  NOT NULL default '0',
                               name varchar(255) NOT NULL default '',
                               type INT8 NOT NULL default '0',
                               options varchar(255) NOT NULL default '',
                               itemcount INT8  NOT NULL default '0'
            ) ; ");

        $success = $success && modify_database ("", " CREATE TABLE prefix_quiz_dataset_items (
                               id SERIAL8 PRIMARY KEY,
                               definition INT8  NOT NULL default '0',
                               number INT8  NOT NULL default '0',
                               value varchar(255) NOT NULL default ''
                             ) ; ");

        $success = $success && modify_database ("", "CREATE INDEX prefix_quiz_dataset_items_definition_idx ON prefix_quiz_dataset_items (definition);");

        $success = $success && modify_database ("", " CREATE TABLE prefix_quiz_question_datasets (
                               id SERIAL8 PRIMARY KEY,
                               question INT8  NOT NULL default '0',
                               datasetdefinition INT8  NOT NULL default '0'
            ) ; ");

        $success = $success && modify_database ("", "CREATE INDEX prefix_quiz_question_datasets_question_datasetdefinition_idx ON prefix_quiz_question_datasets (question,datasetdefinition);");

        // One table for new question type calculated
        //  - the first dataset dependent question type
        $success = $success && modify_database ("", " CREATE TABLE prefix_quiz_calculated (
                               id SERIAL8 PRIMARY KEY,
                               question INT8  NOT NULL default '0',
                               answer INT8  NOT NULL default '0',
                               tolerance varchar(20) NOT NULL default '0.0',
                               tolerancetype INT8 NOT NULL default '1',
                               correctanswerlength INT8 NOT NULL default '2'
                ) ; ");

        $success = $success && modify_database ("", "CREATE INDEX prefix_quiz_calculated_question_idx ON  prefix_quiz_calculated (question);");

        $success = $success && modify_database ( "", "COMMIT;");
    }

    if ($success && $oldversion < 2004111400) {
        $success = $success && table_column("quiz_responses", "answer", "answer", "text", "", "", "", "not null");
    }

    if ($success && $oldversion < 2004111700) {
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_course_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_answers_question_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_attempts_quiz_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_attempts_userid_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_calculated_answer_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_categories_course_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_dataset_definitions_category_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_grades_quiz_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_grades_userid_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_numerical_question_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_numerical_units_question_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_question_grades_quiz_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_question_grades_question_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_questions_category_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_randomsamatch_question_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_responses_attempt_idx;",false);
        $success = $success && execute_sql("DROP INDEX {$CFG->prefix}quiz_responses_question_idx;",false);

        $success = $success && modify_database('','CREATE INDEX prefix_quiz_course_idx ON prefix_quiz (course);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_answers_question_idx ON prefix_quiz_answers (question);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_attempts_quiz_idx ON prefix_quiz_attempts (quiz);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_attempts_userid_idx ON prefix_quiz_attempts (userid);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_calculated_answer_idx ON prefix_quiz_calculated (answer);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_categories_course_idx ON prefix_quiz_categories (course);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_dataset_definitions_category_idx ON prefix_quiz_dataset_definitions (category);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_grades_quiz_idx ON prefix_quiz_grades (quiz);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_grades_userid_idx ON prefix_quiz_grades (userid);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_numerical_question_idx ON prefix_quiz_numerical (question);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_numerical_units_question_idx ON prefix_quiz_numerical_units (question);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_question_grades_quiz_idx ON prefix_quiz_question_grades (quiz);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_question_grades_question_idx ON prefix_quiz_question_grades (question);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_questions_category_idx ON prefix_quiz_questions (category);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_randomsamatch_question_idx ON prefix_quiz_randomsamatch (question);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_responses_attempt_idx ON prefix_quiz_responses (attempt);');
        $success = $success && modify_database('','CREATE INDEX prefix_quiz_responses_question_idx ON prefix_quiz_responses (question);');
    }

    if ($success && $oldversion < 2004112300) { //try and clean up an old mistake - try and bring us up to what is in postgres7.sql today.
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT category;",false);
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT {$CFG->prefix}quiz_attemptonlast_datasets_category_userid;",false);
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT {$CFG->prefix}quiz_category_userid_unique;",false);
        $success = $success && modify_database('','ALTER TABLE prefix_quiz_attemptonlast_datasets ADD CONSTRAINT prefix_quiz_category_userid_unique UNIQUE (category,userid);');
    }

    if ($success && $oldversion < 2004120501) {
        $success = $success && table_column("quiz_calculated", "", "correctanswerformat", "integer", "10", "", "0", "not null", "correctanswerlength");
    }

    if ($success && $oldversion < 2004121400) {  // New field to determine popup window behaviour
        $success = $success && table_column("quiz", "", "popup", "integer", "4", "", "0", "not null", "subnet");
    }

    if ($success && $oldversion < 2005010201) {
        $success = $success && table_column('quiz_categories', '', 'parent');
        $success = $success && table_column('quiz_categories', '', 'sortorder', 'integer', '10', '', '999');
    }

    if ($success && $oldversion < 2005010300) {
        $success = $success && table_column("quiz", "", "questionsperpage", "integer", "10", "", "0", "not null", "review");
    }

    if ($success && $oldversion < 2005012700) {
        $success = $success && table_column('quiz_grades', 'grade', 'grade', 'real', 2, '');
    }

    if ($success && $oldversion < 2005021400) {
        $success = $success && table_column("quiz", "", "decimalpoints", "integer", "4", "", "2", "not null", "grademethod");
    }

    if($success && $oldversion < 2005022800) {
        $success = $success && table_column('quiz_questions', '', 'hidden', 'integer', '1', 'unsigned', '0', 'not null', 'version');
        $success = $success && table_column('quiz_responses', '', 'originalquestion', 'integer', '10', 'unsigned', '0', 'not null', 'question');
        $success = $success && modify_database ('', "CREATE TABLE prefix_quiz_question_version (
                              id SERIAL PRIMARY KEY,
                              quiz integer NOT NULL default '0',
                              oldquestion integer NOT NULL default '0',
                              newquestion integer NOT NULL default '0',
                              userid integer NOT NULL default '0',
                              timestamp integer NOT NULL default '0');");
    }

    if ($success && $oldversion < 2005032000) {
        $success = $success && execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('quiz', 'editquestions', 'quiz', 'name') ");
    }

    if ($success && $oldversion < 2005032300) {
        $success = $success && modify_database ('', 'ALTER TABLE prefix_quiz_question_version RENAME TO prefix_quiz_question_versions;');
    }

    if ($success && $oldversion < 2005041200) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        $sql = "select course from {$CFG->prefix}quiz_categories, {$CFG->prefix}quiz_questions ";
        $sql .= "where {$CFG->prefix}quiz_category.id = {$CFG->prefix}quiz_questions.category ";
        $sql .= "and {$CFG->prefix}quiz_questions.id = ";
        $wtm->update( 'quiz_questions', 'questiontext', 'questiontextformat', $sql );
    }

    if ($success && $oldversion < 2005041300) {
        $success = $success && modify_database('', "UPDATE prefix_quiz_questions SET hidden = '1' WHERE qtype ='".RANDOM."';");
    }

    if ($success && $oldversion < 2005042002) {
        $success = $success && table_column('quiz_answers', 'answer', 'answer', 'text', '', '', '', 'not null', '');
    }


    if ($success && $oldversion < 2005042400) {
        begin_sql();

        // Changes to quiz table

        // The bits of the optionflags field will hold various option flags
        $success = $success && table_column('quiz', '', 'optionflags', 'integer', '10', 'unsigned', '0', 'not null', 'timeclose');

        // The penalty scheme
        $success = $success && table_column('quiz', '', 'penaltyscheme', 'integer', '4', 'unsigned', '0', 'not null', 'optionflags');

        // The review options are now all stored in the bits of the review field
        $success = $success && table_column('quiz', 'review', 'review', 'integer', 10, 'unsigned', 0, 'not null', '');

        /// Changes to quiz_attempts table

        // The preview flag marks teacher previews
        $success = $success && table_column('quiz_attempts', '', 'preview', 'tinyint', '2', 'unsigned', '0', 'not null', 'timemodified');

        // The layout is the list of questions with inserted page breaks.
        $success = $success && table_column('quiz_attempts', '', 'layout', 'text', '', '', '', 'not null', 'timemodified');
        // For old quiz attempts we will set this to the repaginated question list from $quiz->questions

        /// The following updates of field values require a loop through all quizzes
        // This is because earlier versions of mysql don't allow joins in UPDATE
        if ($quizzes = get_records('quiz')) {

            // turn reporting off temporarily to avoid one line output per set_field
            $olddebug = $db->debug;
            $db->debug = false;
            foreach ($quizzes as $quiz) {

                // repaginate
                $quiz->questions = ($quiz->questionsperpage) ? quiz_repaginate($quiz->questions, $quiz->questionsperpage) : $quiz->questions;
                if ($quiz->questionsperpage) {
                    $quiz->questions = quiz_repaginate($quiz->questions, $quiz->questionsperpage);
                    $success = $success && set_field('quiz', 'questions', $quiz->questions, 'id', $quiz->id);
                }
                set_field('quiz_attempts', 'layout', $quiz->questions, 'quiz', $quiz->id);

                // set preview flag
                if ($teachers = get_course_teachers($quiz->course)) {
                    $teacherids = implode(',', array_keys($teachers));
                    $success = $success && execute_sql("UPDATE {$CFG->prefix}quiz_attempts SET preview = 1 WHERE userid IN ($teacherids)");
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
                $success = $success && set_field('quiz', 'review', $review, 'id', $quiz->id);
            }
            $db->debug = $olddebug;
        }

        // We can now drop the fields whose data has been moved to the review field
        $success = $success && execute_sql(" ALTER TABLE {$CFG->prefix}quiz DROP COLUMN feedback");
        $success = $success && execute_sql(" ALTER TABLE {$CFG->prefix}quiz DROP COLUMN correctanswers");

        /// Renaming tables

        // rename the quiz_question_grades table to quiz_question_instances
        $success = $success && modify_database ('', 'ALTER TABLE prefix_quiz_question_grades RENAME TO prefix_quiz_question_instances;');
        $success = $success && modify_database ('', 'ALTER TABLE prefix_quiz_question_grades_id_seq RENAME TO prefix_quiz_question_instances_id_seq;');
        $success = $success && modify_database ('', 'ALTER TABLE prefix_quiz_question_instances ALTER COLUMN id SET DEFAULT nextval(\'prefix_quiz_question_instances_id_seq\');');
        $success = $success && modify_database ('', 'DROP INDEX prefix_quiz_question_grades_quiz_idx');
        $success = $success && modify_database ('', 'DROP INDEX prefix_quiz_question_grades_question_idx;');
        $success = $success && modify_database ('', 'CREATE INDEX prefix_quiz_question_instances_quiz_idx ON prefix_quiz_question_instances (quiz);');
        $success = $success && modify_database ('', 'CREATE INDEX prefix_quiz_question_instances_question_idx ON prefix_quiz_question_instances (question);');

        // rename the quiz_responses table quiz_states
        $success = $success && modify_database ('', 'ALTER TABLE prefix_quiz_responses RENAME TO prefix_quiz_states;');
        $success = $success && modify_database ('', 'ALTER TABLE prefix_quiz_responses_id_seq RENAME TO prefix_quiz_states_id_seq;');
        $success = $success && modify_database ('', 'ALTER TABLE prefix_quiz_states ALTER COLUMN id SET DEFAULT nextval(\'prefix_quiz_states_id_seq\');');
        $success = $success && modify_database ('', 'DROP INDEX prefix_quiz_responses_attempt_idx;');
        $success = $success && modify_database ('', 'DROP INDEX prefix_quiz_responses_question_idx;');
        $success = $success && modify_database ('', 'CREATE INDEX prefix_quiz_states_attempt_idx ON prefix_quiz_states (attempt);');
        $success = $success && modify_database ('', 'CREATE INDEX prefix_quiz_states_question_idx ON prefix_quiz_states (question);');


        /// add columns to quiz_states table

        // The sequence number of the state.
        $success = $success && table_column('quiz_states', '', 'seq_number', 'integer', '6', 'unsigned', '0', 'not null', 'originalquestion');
        // For existing states we leave this at 0 because in the old quiz code there was only one response allowed

        // The time the state was created.
        $success = $success && table_column('quiz_states', '', 'timestamp', 'integer', '10', 'unsigned', '0', 'not null', 'answer');
        // For existing states we will below set this to the timemodified field of the attempt

        // The type of event that led to the creation of the state
        $success = $success && table_column('quiz_states', '', 'event', 'integer', '4', 'unsigned', '0', 'not null', 'timestamp');

        // The raw grade
        $success = $success && table_column('quiz_states', '', 'raw_grade', 'varchar', '10', '', '', 'not null', 'grade');
        // For existing states (no penalties) this is equal to the grade
        $success = $success && execute_sql("UPDATE {$CFG->prefix}quiz_states SET raw_grade = grade");

        // The penalty that the response attracted
        $success = $success && table_column('quiz_states', '', 'penalty', 'varchar', '10', '', '0.0', 'not null', 'raw_grade');
        // For existing states this can stay at 0 because penalties did not exist previously.

        /// New table for pointers to newest and newest graded states

        $success = $success && modify_database('', "CREATE TABLE prefix_quiz_newest_states (
                               id SERIAL PRIMARY KEY,
                               attemptid integer NOT NULL default '0',
                               questionid integer NOT NULL default '0',
                               newest integer NOT NULL default '0',
                               newgraded integer NOT NULL default '0',
                               sumpenalty varchar(10) NOT NULL default '0.0'
                             );");
        $success = $success && modify_database('CREATE UNIQUE INDEX prefix_quiz_newest_states_attempt_idx ON prefix_quiz_newest_states (attemptid,questionid);');

        /// Now upgrade some fields in states and newest_states tables where necessary
        // to save time on large sites only do this for attempts that have not yet been finished.
        if ($attempts = get_records_select('quiz_attempts', 'timefinish = 0')) {
            // turn reporting off temporarily to avoid one line output per set_field
            $olddebug = $db->debug;
            $db->debug = false;
            foreach ($attempts as $attempt) {
                quiz_upgrade_states($attempt);
            }
            $db->debug = $olddebug;
        }

        /// Entries for the log_display table

        $success = $success && modify_database('', " INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'preview', 'quiz', 'name');");
        $success = $success && modify_database('', " INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'start attempt', 'quiz', 'name');");
        $success = $success && modify_database('', " INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'close attempt', 'quiz', 'name');");

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
        $success = $success && set_config('quiz_review', $review);

        /// Use tolerance instead of min and max in numerical question type
        $success = $success && table_column('quiz_numerical', '', 'tolerance', 'varchar', '255', '', '0.0', 'not null', 'question');
        $success = $success && execute_sql("UPDATE {$CFG->prefix}quiz_numerical SET tolerance = (max::text::real-min::text::real)/2");
        $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_numerical DROP COLUMN min'); // Replaced by tolerance
        $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_numerical DROP COLUMN max'); // Replaced by tolerance

        /// Tables for Remote Questions
        $success = $success && modify_database ('', "CREATE TABLE prefix_quiz_rqp (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 type integer NOT NULL default '0',
                                 source text NOT NULL,
                                 format varchar(255) NOT NULL default '',
                                 flags integer NOT NULL default '0',
                                 maxscore integer NOT NULL default '1'
                               );");

        $success = $success && modify_database ('', "CREATE INDEX prefix_quiz_rqp_question_idx ON prefix_quiz_rqp (question);");

        $success = $success && modify_database ('', "CREATE TABLE prefix_quiz_rqp_states (
                                 id SERIAL PRIMARY KEY,
                                 stateid integer NOT NULL default '0',
                                 responses text NOT NULL,
                                 persistent_data text NOT NULL,
                                 template_vars text NOT NULL
                               );");

        $success = $success && modify_database ('', "CREATE TABLE prefix_quiz_rqp_types (
                                id SERIAL PRIMARY KEY,
                                name varchar(255) NOT NULL default '',
                                rendering_server varchar(255) NOT NULL default '',
                                cloning_server varchar(255) NOT NULL default '',
                                flags integer NOT NULL default '0'
                              );");

        $success = $success && modify_database ('', "CREATE UNIQUE INDEX prefix_quiz_rqp_types_name_uk ON prefix_quiz_rqp_types (name);");

        if ($success) {
            $success = $success && commit_sql();
        } else {
            rollback_sql();
        }
    }

    if ($success && $oldversion < 2005042900 && false) { // We don't want this to be executed any more!!!

        begin_sql();

        $success = $success && table_column('quiz_multianswers', '', 'sequence',  'varchar', '255', '', '', 'not null', 'question');
        $success = $success && table_column('quiz_numerical', '', 'answers', 'varchar', '255', '', '', 'not null', 'answer');
        $success = $success && modify_database('', 'UPDATE prefix_quiz_numerical SET answers = answer');
        $success = $success && table_column('quiz_questions', '', 'parent', 'integer', '10', 'unsigned', '0', 'not null', 'category');
        $success = $success && modify_database('', "UPDATE prefix_quiz_questions SET parent = id WHERE qtype ='".RANDOM."';");

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
            $olddebug = $db->debug;
            $db->debug = false;
            for ($i = 0; $i < $n; $i++) {
                $answers = $multianswers[$i]->answers; unset($multianswers[$i]->answers);
                $pos = $multianswers[$i]->positionkey; unset($multianswers[$i]->positionkey);

                // create questions for all the multianswer victims
                unset($multianswers[$i]->id);
                $multianswers[$i]->length = 0;
                $multianswers[$i]->questiontext = '';
                $multianswers[$i]->stamp = make_unique_id_code();
                $id = insert_record('quiz_questions', $multianswers[$i]);
                $success = $success && $id;
                $sequence[$pos] = $id;

                // update the answers table to point to these new questions
                $success = $success && modify_database('', "UPDATE prefix_quiz_answers SET question = '$id' WHERE id IN ($answers);");
                // update the questiontype tables to point to these new questions
                if (SHORTANSWER == $multianswers[$i]->qtype) {
                    $success = $success && modify_database('', "UPDATE prefix_quiz_shortanswer SET question = '$id' WHERE answers = '$answers';");
                } else if (NUMERICAL == $multianswers[$i]->qtype) {
                    if (strpos($answers, ',')) {
                        $numerical = get_records_list('quiz_numerical', 'answer', $answers);
                        // Get the biggest tolerance value
                        $tolerance = 0;
                        foreach ($numerical as $num) {
                            $tolerance = ($tolerance < $num->tolerance ? $num->tolerance : $tolerance);
                        }
                        $success = $success && delete_records_select('quiz_numerical', "answer IN ($answers)");
                        $new = new stdClass;
                        $new->question  = $id;
                        $new->tolerance = $tolerance;
                        $new->answers   = $answers;
                        $success = $success && insert_record('quiz_numerical', $new);
                        unset($numerical, $new, $tolerance);
                    } else {
                        $success = $success && modify_database('', "UPDATE prefix_quiz_numerical SET question = '$id', answers = '$answers' WHERE answer IN ($answers);");
                    }
                } else if (MULTICHOICE == $multianswers[$i]->qtype) {
                    $success = $success && modify_database('', "UPDATE prefix_quiz_multichoice SET question = '$id' WHERE answers = '$answers';");
                }

                if (!isset($multianswers[$i+1]) || $parent != $multianswers[$i+1]->parent) {
                    $success = $success && delete_records('quiz_multianswers', 'question', $parent);
                    $multi = new stdClass;
                    $multi->question = $parent;
                    $multi->sequence = implode(',', $sequence);
                    $success = $success && insert_record('quiz_multianswers', $multi);
                    if (isset($multianswers[$i+1])) {
                        $parent   = $multianswers[$i+1]->parent;
                        $sequence = array();
                    }
                }
            }
            $db->debug = $olddebug;
        }

        // Remove redundant fields from quiz_multianswers
        $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP COLUMN answers');
        $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP COLUMN positionkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP COLUMN answertype');
        $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP COLUMN norm');

        // Change numerical from answer to answers
        $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_numerical DROP COLUMN answer');

        if ($success) {
            $success = $success && commit_sql();
        } else {
            rollback_sql();
        }
    }

    if ($success && $oldversion < 2005050300) {
        // length of question determines question numbering. Currently all questions require one
        // question number except for DESCRIPTION questions.
        $success = $success && table_column('quiz_questions', '', 'length', 'integer', '10', 'unsigned', '1', 'not null', 'qtype');
        $success = $success && execute_sql("UPDATE {$CFG->prefix}quiz_questions SET length = 0 WHERE qtype = '7'");
    }

    if ($success && $oldversion < 2005050408) {
        $success = $success && table_column('quiz_questions', '', 'penalty', 'float', '', '', '0.1', 'not null', 'defaultgrade');
    }

    if ($success && $oldversion < 2005051401) {
        // Some earlier changes are undone here, so we need another condition
        if ($oldversion >= 2005042900) {
            // Restore the answer field
            $success = $success && table_column('quiz_numerical', '', 'answer', 'integer', '10', 'unsigned', '0', 'not null', 'answers');
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
                            $success = $success && insert_record('quiz_numerical', $numerical);
                        }
                        // ... and get rid of the old record
                        $success = $success && delete_records('quiz_numerical', 'id', $id);
                    } else {
                        $singleanswer[] = $numerical->id;
                    }
                }
            }

            // Do all of these at once
            if (!empty($singleanswer)) {
                $singleanswer = implode(',', $singleanswer);
                $success = $success && modify_database('', "UPDATE prefix_quiz_numerical SET answer = answers WHERE id IN ($singleanswer);");
            }

            // All answer fields are set, so we can delete the answers field
            $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_numerical DROP answers');

        // If the earlier changes weren't made we can safely do only the
        // bits here.
        } else {
            // Comma separated questionids will be stored as sequence
            $success = $success && table_column('quiz_multianswers', '', 'sequence',  'varchar', '255', '', '', 'not null', 'question');
            // Change the type of positionkey to int, so that the sorting works!
            $success = $success && table_column('quiz_multianswers', 'positionkey', 'positionkey',  'integer', '10', 'unsigned', '0', 'not null', '');
            $success = $success && table_column('quiz_questions', '', 'parent', 'integer', '10', 'unsigned', '0', 'not null', 'category');
            $success = $success && modify_database('', "UPDATE prefix_quiz_questions SET parent = id WHERE qtype ='".RANDOM."';");

            // Each multianswer record is converted to a question object and then
            // inserted as a new question into the quiz_questions table.
            // After that the question fields in the quiz_answers table and the
            // qtype specific tables are updated to point to the new question id.
            // Note: The quiz_numerical table is different as it stores one record
            //       per defined answer (to allow different tolerance values for
            //       different possible answers. (Currently multiple answers are
            //       not supported by the numerical editing interface, but all
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
                // $db->debug = false;
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
                    // $multianswers[$i]->parent is set in the query
                    // $multianswers[$i]->defaultgrade is set in the query
                    // $multianswers[$i]->qtype is set in the query
                    $id = insert_record('quiz_questions', $multianswers[$i]);
                    $success = $success && $id;
                    $sequence[$pos] = $id;

                // Update the quiz_answers table to point to these new questions
                    $success = $success && modify_database('', "UPDATE prefix_quiz_answers SET question = '$id' WHERE id IN ($answers);");
                // Update the questiontype tables to point to these new questions

                    if (SHORTANSWER == $multianswers[$i]->qtype) {
                        $success = $success && modify_database('', "UPDATE prefix_quiz_shortanswer SET question = '$id' WHERE answers = '$answers';");
                    } else if (MULTICHOICE == $multianswers[$i]->qtype) {
                        $success = $success && modify_database('', "UPDATE prefix_quiz_multichoice SET question = '$id' WHERE answers = '$answers';");
                    } else if (NUMERICAL == $multianswers[$i]->qtype) {
                        $success = $success && modify_database('', "UPDATE prefix_quiz_numerical SET question = '$id' WHERE answer IN ($answers);");
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
                                $success = $success && update_record('quiz_states', $state);
                            }
                        }

                        $success = $success && delete_records('quiz_multianswers', 'question', $parent);
                        $multi = new stdClass;
                        $multi->question = $parent;
                        $multi->sequence = implode(',', $sequence);
                        $success = $success && insert_record('quiz_multianswers', $multi);

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
            $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP answers');
            $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP positionkey');
            $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP answertype');
            $success = $success && modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP norm');
        }
    }

    if ($success && $oldversion < 2005051402) {
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT category;",false);
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT {$CFG->prefix}attemptonlast_datasets_category_userid;",false);
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT {$CFG->prefix}quiz_category_userid_unique;",false);
        $success = $success && modify_database('','ALTER TABLE prefix_quiz_attemptonlast_datasets ADD CONSTRAINT prefix_quiz_category_userid_unique UNIQUE (category,userid);');
    }

    if ($success && $oldversion < 2005060300) {
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
                        $success = $success && delete_records('quiz_numerical','id', $id->id);
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
                        $success = $success && delete_records('quiz_shortanswer','id', $id->id);
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
                        $success = $success && delete_records('quiz_multichoice','id', $id->id);
                    }
                }
            }
        }

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
                    require_once("$CFG->libdir/questionlib.php");
                    $success = $success && question_delete_course($course);
                }
            }
        }
        //Reset rebug to its original state
        $db->debug = $olddebug;
    }

    if ($success && $oldversion < 2005060301) {
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_rqp_type RENAME TO '.$CFG->prefix.'quiz_rqp_types');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_rqp_type_id_seq RENAME TO '.$CFG->prefix.'rqp_types_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_rqp_types ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'quiz_rqp_types_id_seq\')');
        $success = $success && execute_sql('DROP INDEX '.$CFG->prefix.'quiz_rqp_type_name_uk');
        $success = $success && execute_sql('CREATE UNIQUE INDEX '.$CFG->prefix.'quiz_rqp_types_name_uk ON '.$CFG->prefix.'quiz_rqp_types (name);');
    }
    
    if ($success && $oldversion < 2005060302) { // Mass cleanup of bad postgres upgrade scripts
        $success = $success && execute_sql('CREATE UNIQUE INDEX '.$CFG->prefix.'quiz_newest_states_attempt_idx ON '.$CFG->prefix.'quiz_newest_states (attemptid, questionid)',false);
        $success = $success && execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_attemptonlast_datasets DROP CONSTRAINT '.$CFG->prefix.'quiz_category_userid_unique',false);
        $success = $success && execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_attemptonlast_datasets ADD CONSTRAINT '.$CFG->prefix.'quiz_attemptonlast_datasets_category_userid UNIQUE (category, userid)',false);
        $success = $success && execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_question_instances DROP CONSTRAINT '.$CFG->prefix.'quiz_question_grades_pkey',false);
        $success = $success && execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_question_instances ADD CONSTRAINT '.$CFG->prefix.'quiz_question_instances_pkey PRIMARY KEY (id)',false);
        $success = $success && execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_question_versions DROP CONSTRAINT '.$CFG->prefix.'quiz_question_version_pkey',false);
        $success = $success && execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_question_versions ADD CONSTRAINT '.$CFG->prefix.'quiz_question_versions_pkey PRIMARY KEY (id)',false);
        $success = $success && execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_states DROP CONSTRAINT '.$CFG->prefix.'quiz_responses_pkey',false);
        $success = $success && execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_states ADD CONSTRAINT '.$CFG->prefix.'quiz_states_pkey PRIMARY KEY (id)',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER decimalpoints SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER optionflags SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER penaltyscheme SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER popup SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER questionsperpage SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER review SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_answers ALTER answer SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_attempts ALTER layout SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_attempts ALTER preview SET NOT NULL',false);

        $success = $success && table_column('quiz_calculated','correctanswerformat','correctanswerformat','integer','16','unsigned','2');

        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_categories ALTER parent SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_categories ALTER sortorder SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_grades ALTER grade SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_multianswers ALTER sequence SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_numerical ALTER tolerance SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_questions ALTER hidden SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_questions ALTER length SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_questions ALTER parent SET NOT NULL',false);

        $success = $success && table_column('quiz_questions','penalty','penalty','real','','UNSIGNED','0.1');

        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER answer SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER event SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER originalquestion SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER penalty SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER raw_grade SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER seq_number SET NOT NULL',false);
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER timestamp SET NOT NULL',false);
    }

    if ($success && $oldversion < 2005100500) {
        // clean up an old mistake. This mistake may not have been made, so don't worry about failures.
        $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_question_version_id_seq RENAME TO '.$CFG->prefix.'quiz_question_versions_id_seq',false);
        $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_question_versions ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'quiz_question_versions_id_seq\')',false);
    }
    if ($success && $oldversion < 2006020801) {
        $success = $success && table_column("quiz", "", "delay1", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "popup");
        $success = $success && table_column("quiz", "", "delay2", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "delay1");
    }

    if ($success && $oldversion < 2006021101) {
        // set defaultgrade field properly (probably not necessary, but better make sure)
        $success && execute_sql("UPDATE {$CFG->prefix}quiz_questions SET defaultgrade = '1' WHERE defaultgrade = '0'", false);
        $success && execute_sql("UPDATE {$CFG->prefix}quiz_questions SET defaultgrade = '0' WHERE qtype = '7'", false);
    }

    if ($success && $oldversion < 2006021103) {
        // add new field to store the question-level shuffleanswers option
        $success = $success && table_column('quiz_match', '', 'shuffleanswers', 'tinyint', '4', 'unsigned', '1', 'not null', 'subquestions');
        $success = $success && table_column('quiz_multichoice', '', 'shuffleanswers', 'tinyint', '4', 'unsigned', '1', 'not null', 'single');
        $success = $success && table_column('quiz_randomsamatch', '', 'shuffleanswers', 'tinyint', '4', 'unsigned', '1', 'not null', 'choose');
    }

    if ($success && $oldversion < 2006021104) {
        // add originalversion field for the new versioning mechanism
        $success = $success && table_column('quiz_question_versions', '', 'originalquestion', 'int', '10', 'unsigned', '0', 'not null', 'newquestion');
    }

    if ($success && $oldversion < 2006021302) {
        $success = $success && table_column('quiz_match_sub', '', 'code', 'int', '10', 'unsigned', '0', 'not null', 'id');
        $success = $success && execute_sql("UPDATE {$CFG->prefix}quiz_match_sub SET code = id", false);
    }

    if ($success && $oldversion < 2006021304) {
        // convert sequence field to text to accomodate very long sequences, see bug 4257
        $success = $success && table_column('quiz_multianswers', 'sequence', 'sequence',  'text', '', '', '', 'not null', 'question');
    }

    if ($success && $oldversion < 2006021400) {
        // modify_database('','CREATE UNIQUE INDEX prefix_quiz_attempts_uniqueid_uk ON prefix_quiz_attempts (uniqueid);');
        // this index will not be created since uniqueid was not added, proper upgrade will be on 2006042801
    }

    if ($success && $oldversion < 2006021501) {
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_newest_states RENAME TO {$CFG->prefix}question_sessions", false);
    }

    if ($success && $oldversion < 2006021900) {
        
        $success = $success && modify_database ('', "
            CREATE TABLE prefix_quiz_essay (
                id SERIAL PRIMARY KEY,
                question integer NOT NULL default '0',
                answer varchar(255) NOT NULL default ''
            ) ");

        $success = $success && modify_database ('', "
            CREATE TABLE prefix_quiz_essay_states (
                id SERIAL PRIMARY KEY,
                stateid integer NOT NULL default '0',
                graded integer NOT NULL default '0',
                fraction varchar(10) NOT NULL default '0.0',
                response text NOT NULL default ''
            );");

        // convert grade fields to real
        $success = $success && table_column('quiz_attempts', 'sumgrades', 'sumgrades',  'real', '', '', '0', 'not null');
        $success = $success && table_column('quiz_answers', 'fraction', 'fraction',  'real', '', '', '0', 'not null');
        $success = $success && table_column('quiz_essay_states', 'fraction', 'fraction',  'real', '', '', '0', 'not null');
        $success = $success && set_field('quiz_states', 'grade', 0, 'grade', ''); // Some values may be wrong, which caused errors in the following table_column calls.
        $success = $success && set_field('quiz_states', 'raw_grade', 0, 'raw_grade', '');
        $success = $success && set_field('quiz_states', 'penalty', 0, 'penalty', '');
        $success = $success && table_column('quiz_states', 'grade', 'grade',  'real', '', '', '0', 'not null');
        $success = $success && table_column('quiz_states', 'raw_grade', 'raw_grade',  'real', '', '', '0', 'not null');
        $success = $success && table_column('quiz_states', 'penalty', 'penalty',  'real', '', '', '0', 'not null');
        $success = $success && table_column('question_sessions', 'sumpenalty', 'sumpenalty',  'real', '', '', '0', 'not null');
    }

    if ($success && $oldversion < 2006030100) {
        // Fix up another table rename :(
        // THIS caused the mistake: execute_sql("ALTER TABLE {$CFG->prefix}quiz_newest_states RENAME TO {$CFG->prefix}question_sessions", false);
        $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_newest_states_id_seq RENAME TO '.$CFG->prefix.'question_sessions_id_seq',false);
        $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_sessions ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_sessions_id_seq\')',false);
    }

    if ($success && $oldversion < 2006030101) {
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_questions RENAME TO {$CFG->prefix}question");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_questions_id_seq RENAME TO '.$CFG->prefix.'question_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_states RENAME TO {$CFG->prefix}question_states");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states_id_seq RENAME TO '.$CFG->prefix.'question_states_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_states ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_states_id_seq\')');
    
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_answers RENAME TO {$CFG->prefix}question_answers");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_answers_id_seq RENAME TO '.$CFG->prefix.'question_answers_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_answers ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_answers_id_seq\')');
    
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_categories RENAME TO {$CFG->prefix}question_categories");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_categories_id_seq RENAME TO '.$CFG->prefix.'question_categories_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_categories ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_categories_id_seq\')');
    }

    if ($success && $oldversion < 2006031202) {
        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_truefalse RENAME TO {$CFG->prefix}question_truefalse");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_truefalse_id_seq RENAME TO '.$CFG->prefix.'question_truefalse_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_truefalse ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_truefalse_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_shortanswer RENAME TO {$CFG->prefix}question_shortanswer");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_shortanswer_id_seq RENAME TO '.$CFG->prefix.'question_shortanswer_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_shortanswer ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_shortanswer_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_multianswers RENAME TO {$CFG->prefix}question_multianswer");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_multianswers_id_seq RENAME TO '.$CFG->prefix.'question_multianswer_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_multianswer ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_multianswer_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_multichoice RENAME TO {$CFG->prefix}question_multichoice");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_multichoice_id_seq RENAME TO '.$CFG->prefix.'question_multichoice_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_multichoice ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_multichoice_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_numerical RENAME TO {$CFG->prefix}question_numerical");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_numerical_id_seq RENAME TO '.$CFG->prefix.'question_numerical_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_numerical ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_numerical_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_numerical_units RENAME TO {$CFG->prefix}question_numerical_units");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_numerical_units_id_seq RENAME TO '.$CFG->prefix.'question_numerical_units_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_numerical_units ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_numerical_units_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_randomsamatch RENAME TO {$CFG->prefix}question_randomsamatch");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_randomsamatch_id_seq RENAME TO '.$CFG->prefix.'question_randomsamatch_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_randomsamatch ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_randomsamatch_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_match RENAME TO {$CFG->prefix}question_match");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_match_id_seq RENAME TO '.$CFG->prefix.'question_match_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_match ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_match_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_match_sub RENAME TO {$CFG->prefix}question_match_sub");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_match_sub_id_seq RENAME TO '.$CFG->prefix.'question_match_sub_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_match_sub ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_match_sub_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_calculated RENAME TO {$CFG->prefix}question_calculated");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_calculated_id_seq RENAME TO '.$CFG->prefix.'question_calculated_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_calculated ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_calculated_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_dataset_definitions RENAME TO {$CFG->prefix}question_dataset_definitions");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_dataset_definitions_id_seq RENAME TO '.$CFG->prefix.'question_dataset_definitions_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_dataset_definitions ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_dataset_definitions_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_dataset_items RENAME TO {$CFG->prefix}question_dataset_items");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_dataset_items_id_seq RENAME TO '.$CFG->prefix.'question_dataset_items_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_dataset_items ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_dataset_items_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_question_datasets RENAME TO {$CFG->prefix}question_datasets");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_question_datasets_id_seq RENAME TO '.$CFG->prefix.'question_datasets_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_datasets ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_datasets_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_rqp RENAME TO {$CFG->prefix}question_rqp");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_rqp_id_seq RENAME TO '.$CFG->prefix.'question_rqp_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_rqp ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_rqp_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_rqp_states RENAME TO {$CFG->prefix}question_rqp_states");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_rqp_states_id_seq RENAME TO '.$CFG->prefix.'question_rqp_states_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_rqp_states ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_rqp_states_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_rqp_types RENAME TO {$CFG->prefix}question_rqp_types");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_rqp_types_id_seq RENAME TO '.$CFG->prefix.'question_rqp_types_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_rqp_types ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_rqp_types_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_essay RENAME TO {$CFG->prefix}question_essay");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_essay_id_seq RENAME TO '.$CFG->prefix.'question_essay_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_essay ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_essay_id_seq\')');

        $success = $success && execute_sql("ALTER TABLE {$CFG->prefix}quiz_essay_states RENAME TO {$CFG->prefix}question_essay_states");
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_essay_states_id_seq RENAME TO '.$CFG->prefix.'question_essay_states_id_seq');
        $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_essay_states ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_essay_states_id_seq\')');

    }

    if ($success && $oldversion < 2006032100) {
        // change from the old questiontype numbers to using the questiontype names
        $success = $success && table_column('question', 'qtype', 'qtype',  'varchar', 20, '', '', 'not null');
        $success = $success && set_field('question', 'qtype', 'shortanswer', 'qtype', 1);
        $success = $success && set_field('question', 'qtype', 'truefalse', 'qtype', 2);
        $success = $success && set_field('question', 'qtype', 'multichoice', 'qtype', 3);
        $success = $success && set_field('question', 'qtype', 'random', 'qtype', 4);
        $success = $success && set_field('question', 'qtype', 'match', 'qtype', 5);
        $success = $success && set_field('question', 'qtype', 'randomsamatch', 'qtype', 6);
        $success = $success && set_field('question', 'qtype', 'description', 'qtype', 7);
        $success = $success && set_field('question', 'qtype', 'numerical', 'qtype', 8);
        $success = $success && set_field('question', 'qtype', 'multianswer', 'qtype', 9);
        $success = $success && set_field('question', 'qtype', 'calculated', 'qtype', 10);
        $success = $success && set_field('question', 'qtype', 'rqp', 'qtype', 11);
        $success = $success && set_field('question', 'qtype', 'essay', 'qtype', 12);
    }

    if ($success && $oldversion < 2006032200) {
        // set version for all questiontypes that already have their tables installed
        $success = $success && set_config('qtype_calculated_version', 2006032100);
        $success = $success && set_config('qtype_essay_version', 2006032100);
        $success = $success && set_config('qtype_match_version', 2006032100);
        $success = $success && set_config('qtype_multianswer_version', 2006032100);
        $success = $success && set_config('qtype_multichoice_version', 2006032100);
        $success = $success && set_config('qtype_numerical_version', 2006032100);
        $success = $success && set_config('qtype_randomsamatch_version', 2006032100);
        $success = $success && set_config('qtype_rqp_version', 2006032100);
        $success = $success && set_config('qtype_shortanswer_version', 2006032100);
        $success = $success && set_config('qtype_truefalse_version', 2006032100);
    }

    if ($success && $oldversion < 2006040600) {
        $success = $success && table_column('question_sessions', '', 'comment', 'text', '', '', '', 'not null', 'sumpenalty');
    }

    if ($success && $oldversion < 2006040900) {
        $success = $success && modify_database('', "UPDATE prefix_question SET parent = id WHERE qtype ='random';");
    }

    if ($success && $oldversion < 2006041000) {
        $success = $success && modify_database('', " INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'continue attempt', 'quiz', 'name');");
    }

    if ($success && $oldversion < 2006041001) {
        $success = $success && table_column('question', 'version', 'version', 'varchar', 255);
    }

    if ($success && $oldversion < 2006042800) {
        // Check we have some un-renamed tables (verified in some servers)
        if ($tables = $db->MetaTables('TABLES')) {
            if (in_array($CFG->prefix.'quiz_randommatch', $tables) &&
                !in_array($CFG->prefix.'question_randomsamatch', $tables)) {
                $success = $success && modify_database ("", "ALTER TABLE prefix_quiz_randommatch RENAME prefix_question_randomsamatch ");
                $success = $success && modify_database ("", "ALTER TABLE prefix_quiz_randommatch_id_seq RENAME prefix_question_randomsamatch_id_seq ");
                $success = $success && execute_sql('ALTER TABLE '.$CFG->prefix.'question_randomsamatch ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'question_randomsamatch_id_seq\')');
            }
            // Check for one possible missing field in one table
            if ($columns = $db->MetaColumnNames($CFG->prefix.'question_randomsamatch')) {
                if (!in_array('shuffleanswers', $columns)) {
                    $success = $success && table_column('question_randomsamatch', '', 'shuffleanswers', 'tinyint', '4', 'unsigned', '1', 'not null', 'choose');
                }
            }
        }
    }

    if ($success && $oldversion < 2006051300) {  // this block also exec'ed by 2006042801 on MOODLE_16_STABLE
        // The newgraded field must always point to a valid state
        $success = $success && modify_database("","UPDATE prefix_question_sessions SET newgraded = newest where newgraded = '0'");

        // Only perform this if hasn't been performed before (in MOODLE_16_STABLE branch - bug 5717)
        $tables = $db->MetaTables('TABLES');
        if (!in_array($CFG->prefix . 'question_attempts', $tables)) {
            // The following table is discussed in bug 5468
            $success = $success && modify_database ("", "CREATE TABLE prefix_question_attempts (
                                     id SERIAL PRIMARY KEY,
                                     modulename varchar(20) NOT NULL default 'quiz'
                                  );");
        }
    }

    if ($success && $oldversion < 2006051700) { // this block also exec'd by 2006042802 on MOODLE_16_STABLE

        notify("The next set of upgrade operations may report an 
                error if you are upgrading from v1.6. 
                This error mesage is normal, and can be ignored.");
        // this block is taken from mysql.php 2005070202
        // add new unique id to prepare the way for lesson module to have its own attempts table
        table_column('quiz_attempts', '', 'uniqueid', 'integer', '10', 'unsigned', '0', 'not null', 'id');
        // create one entry for all the existing quiz attempts
        // initially we can use the id as the unique id because no other modules use attempts yet.
        $success = $success && execute_sql("UPDATE {$CFG->prefix}quiz_attempts SET uniqueid = id");
        // we set $CFG->attemptuniqueid to the next available id
        $record = get_record_sql("SELECT nextval('{$CFG->prefix}quiz_attempts_id_seq')");
        $success = $success && set_config('attemptuniqueid', empty($record->nextid) ? 1 : $record->nextid);
        // the above will be a race condition, see bug 5468

        modify_database('','CREATE UNIQUE INDEX prefix_quiz_attempts_uniqueid_uk ON prefix_quiz_attempts (uniqueid);');

        // create one entry for all the existing quiz attempts
        $success = $success && modify_database ("", "INSERT INTO prefix_question_attempts (id)
                                   SELECT uniqueid
                                   FROM prefix_quiz_attempts;");
    }

    if ($success && $oldversion < 2006042802) {

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
                    if (!($success = $success && set_field('question_sessions', 'comment', $result->essaycomment, 'attemptid', $result->uniqueid, 'questionid', $result->questionid))) {
                        notify("Essay Table Migration: Cannot save comment");
                        break;
                    }
                    $state->event = 9; //QUESTION_EVENTMANUALGRADE;
                } else {
                    // Not graded
                    $state->event = 7; //QUESTION_EVENTSUBMIT;
                }

                // Save the event
                if (!($success = $success && update_record('question_states', $state))) {
                    notify("Essay Table Migration: Cannot update state");
                    break;
                }
            }
        }
    
        // dropping unused tables
        $success = $success && execute_sql('DROP TABLE '.$CFG->prefix.'question_essay_states');
        $success = $success && execute_sql('DROP TABLE '.$CFG->prefix.'question_essay');
        $success = $success && execute_sql('DROP TABLE '.$CFG->prefix.'quiz_attemptonlast_datasets');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question
            ALTER COLUMN qtype SET DEFAULT \'0\'');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question
            ALTER COLUMN version SET DEFAULT \'\'');

        // recreate the indexes that was not moved while quiz was transitioning to question lib
        $success && notify('Errors on indexes not being able to drop or already exists can be ignored as they may have been properly upgraded previously');
        $success && modify_database('','DROP INDEX prefix_quiz_numerical_answer_idx');
        $success && modify_database('','DROP INDEX prefix_quiz_numerical_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_numerical_question_idx ON prefix_question_numerical (question)');
        $success = $success && modify_database('','CREATE INDEX prefix_question_numerical_answer_idx ON prefix_question_numerical (answer)');
        $success && modify_database('','DROP INDEX prefix_quiz_question_datasets_question_datasetdefinition_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_datasets_question_datasetdefinition_idx ON prefix_question_datasets (question, datasetdefinition)');

        $success && modify_database('','DROP INDEX prefix_quiz_multichoice_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_multichoice_question_idx ON prefix_question_multichoice (question)');

        $success && modify_database('','DROP INDEX prefix_quiz_categories_course_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_categories_course_idx ON prefix_question_categories (course)');

        $success && modify_database('','DROP INDEX prefix_quiz_shortanswer_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_shortanswer_question_idx ON prefix_question_shortanswer (question)');

        $success && modify_database('','DROP INDEX prefix_quiz_questions_category_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_category_idx ON prefix_question (category)');

        $success && modify_database('','DROP INDEX prefix_quiz_calculated_answer_idx');
        $success && modify_database('','DROP INDEX prefix_quiz_calculated_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_calculated_question_idx ON prefix_question_calculated (question)');
        $success = $success && modify_database('','CREATE INDEX prefix_question_calculated_answer_idx ON prefix_question_calculated (answer)');

        $success && modify_database('','DROP INDEX prefix_quiz_answers_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_answers_question_idx ON prefix_question_answers (question)');

        $success && modify_database('','DROP INDEX prefix_quiz_dataset_items_definition_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_dataset_items_definition_idx ON prefix_question_dataset_items (definition)');

        $success && modify_database('','DROP INDEX prefix_quiz_numerical_units_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_numerical_units_question_idx ON prefix_question_numerical_units (question)');

        $success && modify_database('','DROP INDEX prefix_quiz_randomsamatch_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_randomsamatch_question_idx ON prefix_question_randomsamatch (question)');

        $success && modify_database('','DROP INDEX prefix_quiz_states_question_idx');
        $success && modify_database('','DROP INDEX prefix_quiz_states_attempt_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_states_question_idx ON prefix_question_states (question)');
        $success = $success && modify_database('','CREATE INDEX prefix_question_states_attempt_idx ON prefix_question_states (attempt)');

        $success && modify_database('','DROP INDEX prefix_quiz_match_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_match_question_idx ON prefix_question_match (question)');

        $success && modify_database('','DROP INDEX prefix_quiz_match_sub_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_match_sub_question_idx ON prefix_question_match_sub (question)');

        $success && modify_database('','DROP INDEX prefix_quiz_multianswers_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_multianswer_question_idx ON prefix_question_multianswer (question)');

        $success && modify_database('','DROP INDEX prefix_quiz_dataset_definitions_category_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_dataset_definitions_category_idx ON prefix_question_dataset_definitions (category)');

        $success = $success && modify_database('','CREATE INDEX prefix_log_timecoursemoduleaction_idx ON prefix_log ("time", course, module, "action")');
        $success = $success && modify_database('','CREATE INDEX prefix_log_coursemoduleaction_idx ON prefix_log (course, module, "action")');

        $success && modify_database('','DROP INDEX prefix_quiz_rqp_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_rqp_question_idx ON prefix_question_rqp (question)');

        $success && modify_database('','DROP INDEX prefix_quiz_truefalse_question_idx');
        $success = $success && modify_database('','CREATE INDEX prefix_question_truefalse_question_idx ON prefix_question_truefalse (question)');
        $success && notify('End of upgrading of indexes');


        $success && notify('Renaming primary key names');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_numerical DROP CONSTRAINT prefix_quiz_numerical_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_numerical ADD CONSTRAINT prefix_question_numerical_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_datasets DROP CONSTRAINT prefix_quiz_question_datasets_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_datasets ADD CONSTRAINT prefix_question_datasets_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_multichoice DROP CONSTRAINT prefix_quiz_multichoice_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_multichoice ADD CONSTRAINT prefix_question_multichoice_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_rqp_states DROP CONSTRAINT prefix_quiz_rqp_states_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_rqp_states ADD CONSTRAINT prefix_question_rqp_states_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_categories DROP CONSTRAINT prefix_quiz_categories_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_categories ADD CONSTRAINT prefix_question_categories_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_shortanswer DROP CONSTRAINT prefix_quiz_shortanswer_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_shortanswer ADD CONSTRAINT prefix_question_shortanswer_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question DROP CONSTRAINT prefix_quiz_questions_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question ADD CONSTRAINT prefix_question_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_sessions DROP CONSTRAINT prefix_quiz_newest_states_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_sessions ADD CONSTRAINT prefix_question_sessions_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_calculated DROP CONSTRAINT prefix_quiz_calculated_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_calculated ADD CONSTRAINT prefix_question_calculated_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_answers DROP CONSTRAINT prefix_quiz_answers_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_answers ADD CONSTRAINT prefix_question_answers_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_dataset_items DROP CONSTRAINT prefix_quiz_dataset_items_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_dataset_items ADD CONSTRAINT prefix_question_dataset_items_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_numerical_units DROP CONSTRAINT prefix_quiz_numerical_units_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_numerical_units ADD CONSTRAINT prefix_question_numerical_units_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_randomsamatch DROP CONSTRAINT prefix_quiz_randomsamatch_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_randomsamatch ADD CONSTRAINT prefix_question_randomsamatch_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_rqp_types DROP CONSTRAINT prefix_quiz_rqp_types_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_rqp_types ADD CONSTRAINT prefix_question_rqp_types_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_states DROP CONSTRAINT prefix_quiz_states_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_states ADD CONSTRAINT prefix_question_states_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_match DROP CONSTRAINT prefix_quiz_match_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_match ADD CONSTRAINT prefix_question_match_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_match_sub DROP CONSTRAINT prefix_quiz_match_sub_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_match_sub ADD CONSTRAINT prefix_question_match_sub_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_multianswer DROP CONSTRAINT prefix_quiz_multianswers_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_multianswer ADD CONSTRAINT prefix_question_multianswer_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_dataset_definitions DROP CONSTRAINT prefix_quiz_dataset_definitions_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_dataset_definitions ADD CONSTRAINT prefix_question_dataset_definitions_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_rqp DROP CONSTRAINT prefix_quiz_rqp_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_rqp ADD CONSTRAINT prefix_question_rqp_pkey PRIMARY KEY (id)');

        $success = $success && modify_database('', 'ALTER TABLE prefix_question_truefalse DROP CONSTRAINT prefix_quiz_truefalse_pkey');
        $success = $success && modify_database('', 'ALTER TABLE prefix_question_truefalse ADD CONSTRAINT prefix_question_truefalse_pkey PRIMARY KEY (id)');
        $success && notify('End of renaming primary keys');

    }

    if ($oldversion < 2006081000) {
        // Add a column to the the question table to store the question general feedback.
        $success = $success && table_column('question', '', 'commentarytext', 'text', '', '', '', 'not null', 'image');

        // Adjust the quiz review options so that general feedback is displayed whenever feedback is.
        $success = $success && execute_sql('UPDATE ' . $CFG->prefix . 'quiz SET review = ' .
                '(review & ~' . QUIZ_REVIEW_GENERALFEEDBACK . ') | ' . // Clear any existing junk from the commenary bits.
                '((review & ' . QUIZ_REVIEW_FEEDBACK . ') * 8)'); // Set the general feedback bits to be the same as the feedback ones.

        // Same adjustment to the defaults for new quizzes.
        $success = $success && set_config('quiz_review', ($CFG->quiz_review & ~QUIZ_REVIEW_GENERALFEEDBACK) |
                (($CFG->quiz_review & QUIZ_REVIEW_FEEDBACK) << 3));
    }
    
    if ($success && $oldversion < 2006081400) {
        $success = $success && modify_database('', "
            CREATE TABLE prefix_quiz_feedback (
                id SERIAL PRIMARY KEY,
                quizid integer NOT NULL default '0',
                feedbacktext text NOT NULL default '',
                maxgrade real NOT NULL default '0',
                mingrade real NOT NULL default '0'
            );
        ");
        $success = $success && modify_database('',
            "CREATE INDEX prefix_quiz_feedback_quizid_idx ON prefix_quiz_feedback (quizid);");
            
        $success = $success && execute_sql("
            INSERT INTO {$CFG->prefix}quiz_feedback (quizid, feedbacktext, maxgrade, mingrade)
            SELECT id, '', grade + 1, 0 FROM {$CFG->prefix}quiz;
        ");
    }
    
    if ($success && $oldversion < 2006082400) {
        $success = $success && table_column('question_sessions', 'comment', 'manualcomment', 'text', '', '', '');
    }

    if ($success && $oldversion < 2006091900) {
        $success = $success && table_column('question_dataset_items', 'number', 'itemnumber', 'integer');
    }

    if ($success && $oldversion < 2006091901) {
        $success = $success && table_column('question', 'commentarytext', 'generalfeedback', 'text', '', '', '');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return $success;
}

?>
