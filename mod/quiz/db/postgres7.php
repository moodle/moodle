<?php // $Id$

function quiz_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;
    include_once("$CFG->dirroot/mod/quiz/locallib.php");

    if ($oldversion < 2003010100) {
        execute_sql(" ALTER TABLE {$CFG->prefix}quiz ADD review integer DEFAULT '0' NOT NULL AFTER `grademethod` ");
    }

    if ($oldversion < 2003010301) {
        table_column("quiz_truefalse", "true", "trueanswer", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
        table_column("quiz_truefalse", "false", "falseanswer", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
        table_column("quiz_questions", "type", "qtype", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
    }

    if ($oldversion < 2003022303) {
        modify_database ("", "CREATE TABLE prefix_quiz_randommatch (
                                  id SERIAL PRIMARY KEY,
                                  question integer NOT NULL default '0',
                                  choose integer NOT NULL default '4'
                              );");
    }
    if ($oldversion < 2003030303) {
        table_column("quiz_questions", "", "defaultgrade", "INTEGER", "6", "UNSIGNED", "1", "NOT NULL", "image");
    }

    if ($oldversion < 2003033100) {
        modify_database ("", "ALTER TABLE prefix_quiz_randommatch RENAME prefix_quiz_randomsamatch ");
        modify_database ("", "CREATE TABLE prefix_quiz_match_sub (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 questiontext text NOT NULL default '',
                                 answertext varchar(255) NOT NULL default ''
                              );");
        modify_database ("", "CREATE INDEX prefix_quiz_match_sub_question_idx ON prefix_quiz_match_sub (question);");

        modify_database ("", "CREATE TABLE prefix_quiz_multichoice (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 layout integer NOT NULL default '0',
                                 answers varchar(255) NOT NULL default '',
                                 single integer NOT NULL default '0'
                               );");
        modify_database ("", "CREATE INDEX prefix_quiz_multichoice_question_idx ON prefix_quiz_multichoice (question);");
    }

    if ($oldversion < 2003040901) {
        table_column("quiz", "", "shufflequestions", "INTEGER", "5", "UNSIGNED", "0", "NOT NULL", "review");
        table_column("quiz", "", "shuffleanswers", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "shufflequestions");
    }
    if ($oldversion < 2003042702) {
        modify_database ("", "CREATE TABLE prefix_quiz_match (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 subquestions varchar(255) NOT NULL default ''
                               );");
        modify_database ("", "CREATE INDEX prefix_quiz_match_question_idx ON prefix_quiz_match (question);");
    }
    if ($oldversion < 2003071001) {
        modify_database ("", " CREATE TABLE prefix_quiz_numerical (
                               id SERIAL PRIMARY KEY,
                               question integer NOT NULL default '0',
                               answer integer NOT NULL default '0',
                               min varchar(255) NOT NULL default '',
                               max varchar(255) NOT NULL default ''
                               ); ");
        modify_database ("", "CREATE INDEX prefix_quiz_numerical_answer_idx ON prefix_quiz_numerical (answer);");
    }

    if ($oldversion < 2003072400) {
        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('quiz', 'review', 'quiz', 'name') ");
    }

    if ($oldversion < 2003082300) {
        modify_database ("", " CREATE TABLE prefix_quiz_multianswers (
                               id SERIAL PRIMARY KEY,
                               question integer NOT NULL default '0',
                               answers varchar(255) NOT NULL default '',
                               positionkey varchar(255) NOT NULL default '',
                               answertype integer NOT NULL default '0',
                               norm integer NOT NULL default '1'
                              ); ");
        modify_database ("", "CREATE INDEX prefix_quiz_multianswers_question_idx ON prefix_quiz_multianswers (question);");

        table_column("quiz", "", "attemptonlast", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "attempts");

        table_column("quiz_questions", "", "stamp", "varchar", "255", "", "qtype");
    }

    if ($oldversion < 2003082301) {
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
        table_column("quiz", "", "timelimit", "integer", "", "", "0", "NOT NULL", "");
    }

    if ($oldversion < 2004070700) {
        table_column("quiz", "", "password", "varchar", "255", "", "", "not null", "");
        table_column("quiz", "", "subnet", "varchar", "255", "", "", "not null", "");
    }

    if ($oldversion < 2004073001) {
        // Six new tables:


        modify_database ( "", "BEGIN;");

        // One table for handling units for numerical questions
        modify_database ("", " CREATE TABLE prefix_quiz_numerical_units (
                               id SERIAL8 PRIMARY KEY,
                               question INT8  NOT NULL default '0',
                               multiplier decimal(40,20) NOT NULL default '1.00000000000000000000',
                               unit varchar(50) NOT NULL default ''
                );" );


        // Four tables for handling distribution and storage of
        // individual data for dataset dependent question types
        modify_database ("", " CREATE TABLE prefix_quiz_attemptonlast_datasets (
                               id SERIAL8 PRIMARY KEY,
                               category INT8  NOT NULL default '0',
                               userid INT8  NOT NULL default '0',
                               datasetnumber INT8  NOT NULL default '0',
                               CONSTRAINT prefix_quiz_attemptonlast_datasets_category_userid UNIQUE (category,userid)
            ) ;");

        modify_database ("", " CREATE TABLE prefix_quiz_dataset_definitions (
                               id SERIAL8 PRIMARY KEY,
                               category INT8  NOT NULL default '0',
                               name varchar(255) NOT NULL default '',
                               type INT8 NOT NULL default '0',
                               options varchar(255) NOT NULL default '',
                               itemcount INT8  NOT NULL default '0'
            ) ; ");

        modify_database ("", " CREATE TABLE prefix_quiz_dataset_items (
                               id SERIAL8 PRIMARY KEY,
                               definition INT8  NOT NULL default '0',
                               number INT8  NOT NULL default '0',
                               value varchar(255) NOT NULL default ''
                             ) ; ");

        modify_database ("", "CREATE INDEX prefix_quiz_dataset_items_definition_idx ON prefix_quiz_dataset_items (definition);");

        modify_database ("", " CREATE TABLE prefix_quiz_question_datasets (
                               id SERIAL8 PRIMARY KEY,
                               question INT8  NOT NULL default '0',
                               datasetdefinition INT8  NOT NULL default '0'
            ) ; ");

        modify_database ("", "CREATE INDEX prefix_quiz_question_datasets_question_datasetdefinition_idx ON prefix_quiz_question_datasets (question,datasetdefinition);");

        // One table for new question type calculated
        //  - the first dataset dependent question type
        modify_database ("", " CREATE TABLE prefix_quiz_calculated (
                               id SERIAL8 PRIMARY KEY,
                               question INT8  NOT NULL default '0',
                               answer INT8  NOT NULL default '0',
                               tolerance varchar(20) NOT NULL default '0.0',
                               tolerancetype INT8 NOT NULL default '1',
                               correctanswerlength INT8 NOT NULL default '2'
                ) ; ");

        modify_database ("", "CREATE INDEX prefix_quiz_calculated_question_idx ON  prefix_quiz_calculated (question);");

        modify_database ( "", "COMMIT;");
    }

    if ($oldversion < 2004111400) {
        table_column("quiz_responses", "answer", "answer", "text", "", "", "", "not null");
    }

    if ($oldversion < 2004111700) {
        execute_sql("DROP INDEX {$CFG->prefix}quiz_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_answers_question_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_attempts_quiz_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_attempts_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_calculated_answer_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_categories_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_dataset_definitions_category_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_grades_quiz_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_grades_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_numerical_question_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_numerical_units_question_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_question_grades_quiz_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_question_grades_question_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_questions_category_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_randomsamatch_question_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_responses_attempt_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}quiz_responses_question_idx;",false);

        modify_database('','CREATE INDEX prefix_quiz_course_idx ON prefix_quiz (course);');
        modify_database('','CREATE INDEX prefix_quiz_answers_question_idx ON prefix_quiz_answers (question);');
        modify_database('','CREATE INDEX prefix_quiz_attempts_quiz_idx ON prefix_quiz_attempts (quiz);');
        modify_database('','CREATE INDEX prefix_quiz_attempts_userid_idx ON prefix_quiz_attempts (userid);');
        modify_database('','CREATE INDEX prefix_quiz_calculated_answer_idx ON prefix_quiz_calculated (answer);');
        modify_database('','CREATE INDEX prefix_quiz_categories_course_idx ON prefix_quiz_categories (course);');
        modify_database('','CREATE INDEX prefix_quiz_dataset_definitions_category_idx ON prefix_quiz_dataset_definitions (category);');
        modify_database('','CREATE INDEX prefix_quiz_grades_quiz_idx ON prefix_quiz_grades (quiz);');
        modify_database('','CREATE INDEX prefix_quiz_grades_userid_idx ON prefix_quiz_grades (userid);');
        modify_database('','CREATE INDEX prefix_quiz_numerical_question_idx ON prefix_quiz_numerical (question);');
        modify_database('','CREATE INDEX prefix_quiz_numerical_units_question_idx ON prefix_quiz_numerical_units (question);');
        modify_database('','CREATE INDEX prefix_quiz_question_grades_quiz_idx ON prefix_quiz_question_grades (quiz);');
        modify_database('','CREATE INDEX prefix_quiz_question_grades_question_idx ON prefix_quiz_question_grades (question);');
        modify_database('','CREATE INDEX prefix_quiz_questions_category_idx ON prefix_quiz_questions (category);');
        modify_database('','CREATE INDEX prefix_quiz_randomsamatch_question_idx ON prefix_quiz_randomsamatch (question);');
        modify_database('','CREATE INDEX prefix_quiz_responses_attempt_idx ON prefix_quiz_responses (attempt);');
        modify_database('','CREATE INDEX prefix_quiz_responses_question_idx ON prefix_quiz_responses (question);');
    }

    if ($oldversion < 2004112300) { //try and clean up an old mistake - try and bring us up to what is in postgres7.sql today.
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT category;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT {$CFG->prefix}quiz_attemptonlast_datasets_category_userid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT {$CFG->prefix}quiz_category_userid_unique;",false);
        modify_database('','ALTER TABLE prefix_quiz_attemptonlast_datasets ADD CONSTRAINT prefix_quiz_category_userid_unique UNIQUE (category,userid);');
    }

    if ($oldversion < 2004120501) {
        table_column("quiz_calculated", "", "correctanswerformat", "integer", "10", "", "0", "not null", "correctanswerlength");
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
        modify_database ('', "CREATE TABLE prefix_quiz_question_version (
                              id SERIAL PRIMARY KEY,
                              quiz integer NOT NULL default '0',
                              oldquestion integer NOT NULL default '0',
                              newquestion integer NOT NULL default '0',
                              userid integer NOT NULL default '0',
                              timestamp integer NOT NULL default '0');");
    }

    if ($oldversion < 2005032000) {
        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('quiz', 'editquestions', 'quiz', 'name') ");
    }

    if ($oldversion < 2005032300) {
        modify_database ('', 'ALTER TABLE prefix_quiz_question_version RENAME TO prefix_quiz_question_versions;');
    }

    if ($oldversion < 2005041200) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        $sql = "select course from {$CFG->prefix}quiz_categories, {$CFG->prefix}quiz_questions ";
        $sql .= "where {$CFG->prefix}quiz_category.id = {$CFG->prefix}quiz_questions.category ";
        $sql .= "and {$CFG->prefix}quiz_questions.id = ";
        $wtm->update( 'quiz_questions', 'questiontext', 'questiontextformat', $sql );
    }

    if ($oldversion < 2005041300) {
        modify_database('', "UPDATE prefix_quiz_questions SET hidden = '1' WHERE qtype ='".RANDOM."';");
    }

    if ($oldversion < 2005042002) {
        table_column('quiz_answers', 'answer', 'answer', 'text', '', '', '', 'not null', '');
    }


    if ($oldversion < 2005042400) {
        begin_sql();

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
            foreach ($quizzes as $quiz) {

                // repaginate
                $quiz->questions = ($quiz->questionsperpage) ? quiz_repaginate($quiz->questions, $quiz->questionsperpage) : $quiz->questions;
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
        execute_sql(" ALTER TABLE {$CFG->prefix}quiz DROP COLUMN feedback");
        execute_sql(" ALTER TABLE {$CFG->prefix}quiz DROP COLUMN correctanswers");

        /// Renaming tables

        // rename the quiz_question_grades table to quiz_question_instances
        modify_database ('', 'ALTER TABLE prefix_quiz_question_grades RENAME TO prefix_quiz_question_instances;');
        modify_database ('', 'ALTER TABLE prefix_quiz_question_grades_id_seq RENAME TO prefix_quiz_question_instances_id_seq;');
        modify_database ('', 'ALTER TABLE prefix_quiz_question_instances ALTER COLUMN id SET DEFAULT nextval(\'prefix_quiz_question_instances_id_seq\');');
        modify_database ('', 'DROP INDEX prefix_quiz_question_grades_quiz_idx');
        modify_database ('', 'DROP INDEX prefix_quiz_question_grades_question_idx;');
        modify_database ('', 'CREATE INDEX prefix_quiz_question_instances_quiz_idx ON prefix_quiz_question_instances (quiz);');
        modify_database ('', 'CREATE INDEX prefix_quiz_question_instances_question_idx ON prefix_quiz_question_instances (question);');

        // rename the quiz_responses table quiz_states
        modify_database ('', 'ALTER TABLE prefix_quiz_responses RENAME TO prefix_quiz_states;');
        modify_database ('', 'ALTER TABLE prefix_quiz_responses_id_seq RENAME TO prefix_quiz_states_id_seq;');
        modify_database ('', 'ALTER TABLE prefix_quiz_states ALTER COLUMN id SET DEFAULT nextval(\'prefix_quiz_states_id_seq\');');
        modify_database ('', 'DROP INDEX prefix_quiz_responses_attempt_idx;');
        modify_database ('', 'DROP INDEX prefix_quiz_responses_question_idx;');
        modify_database ('', 'CREATE INDEX prefix_quiz_states_attempt_idx ON prefix_quiz_states (attempt);');
        modify_database ('', 'CREATE INDEX prefix_quiz_states_question_idx ON prefix_quiz_states (question);');


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

        modify_database('', "CREATE TABLE prefix_quiz_newest_states (
                               id SERIAL PRIMARY KEY,
                               attemptid integer NOT NULL default '0',
                               questionid integer NOT NULL default '0',
                               newest integer NOT NULL default '0',
                               newgraded integer NOT NULL default '0',
                               sumpenalty varchar(10) NOT NULL default '0.0'
                             );");
        modify_database('CREATE UNIQUE INDEX prefix_quiz_newest_states_attempt_idx ON prefix_quiz_newest_states (attemptid,questionid);');

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

        modify_database('', " INSERT INTO prefix_log_display VALUES ('quiz', 'preview', 'quiz', 'name');");
        modify_database('', " INSERT INTO prefix_log_display VALUES ('quiz', 'start attempt', 'quiz', 'name');");
        modify_database('', " INSERT INTO prefix_log_display VALUES ('quiz', 'close attempt', 'quiz', 'name');");

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
        table_column('quiz_numerical', '', 'tolerance', 'varchar', '255', '', '0.0', 'not null', 'question');
        execute_sql("UPDATE {$CFG->prefix}quiz_numerical SET tolerance = (max::text::real-min::text::real)/2");
        modify_database('', 'ALTER TABLE prefix_quiz_numerical DROP COLUMN min'); // Replaced by tolerance
        modify_database('', 'ALTER TABLE prefix_quiz_numerical DROP COLUMN max'); // Replaced by tolerance

        /// Tables for Remote Questions
        modify_database ('', "CREATE TABLE prefix_quiz_rqp (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 type integer NOT NULL default '0',
                                 source text NOT NULL,
                                 format varchar(255) NOT NULL default '',
                                 flags integer NOT NULL default '0',
                                 maxscore integer NOT NULL default '1'
                               );");

        modify_database ('', "CREATE INDEX prefix_quiz_rqp_question_idx ON prefix_quiz_rqp (question);");

        modify_database ('', "CREATE TABLE prefix_quiz_rqp_states (
                                 id SERIAL PRIMARY KEY,
                                 stateid integer NOT NULL default '0',
                                 responses text NOT NULL,
                                 persistent_data text NOT NULL,
                                 template_vars text NOT NULL
                               );");

        modify_database ('', "CREATE TABLE prefix_quiz_rqp_types (
                                id SERIAL PRIMARY KEY,
                                name varchar(255) NOT NULL default '',
                                rendering_server varchar(255) NOT NULL default '',
                                cloning_server varchar(255) NOT NULL default '',
                                flags integer NOT NULL default '0'
                              );");

        modify_database ('', "CREATE UNIQUE INDEX prefix_quiz_rqp_types_name_uk ON prefix_quiz_rqp_types (name);");

        commit_sql();
    }

    if ($oldversion < 2005042900 && false) { // We don't want this to be executed any more!!!

        begin_sql();

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
        modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP COLUMN answers');
        modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP COLUMN positionkey');
        modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP COLUMN answertype');
        modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP COLUMN norm');

        // Change numerical from answer to answers
        modify_database('', 'ALTER TABLE prefix_quiz_numerical DROP COLUMN answer');

        commit_sql();
    }

    if ($oldversion < 2005050300) {
        // length of question determines question numbering. Currently all questions require one
        // question number except for DESCRIPTION questions.
        table_column('quiz_questions', '', 'length', 'integer', '10', 'unsigned', '1', 'not null', 'qtype');
        execute_sql("UPDATE {$CFG->prefix}quiz_questions SET length = 0 WHERE qtype = ".DESCRIPTION);
    }

    if ($oldversion < 2005050408) {
        table_column('quiz_questions', '', 'penalty', 'float', '', '', '0.1', 'not null', 'defaultgrade');
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
            modify_database('', 'ALTER TABLE prefix_quiz_numerical DROP answers');

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
                    $sequence[$pos] = $id;

                // Update the quiz_answers table to point to these new questions
                    modify_database('', "UPDATE prefix_quiz_answers SET question = '$id' WHERE id IN ($answers);");
                // Update the questiontype tables to point to these new questions

                    if (SHORTANSWER == $multianswers[$i]->qtype) {
                        modify_database('', "UPDATE prefix_quiz_shortanswer SET question = '$id' WHERE answers = '$answers';");
                    } else if (MULTICHOICE == $multianswers[$i]->qtype) {
                        modify_database('', "UPDATE prefix_quiz_multichoice SET question = '$id' WHERE answers = '$answers';");
                    } else if (NUMERICAL == $multianswers[$i]->qtype) {
                        modify_database('', "UPDATE prefix_quiz_numerical SET question = '$id' WHERE answer IN ($answers);");
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
            modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP answers');
            modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP positionkey');
            modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP answertype');
            modify_database('', 'ALTER TABLE prefix_quiz_multianswers DROP norm');
        }
    }

    if ($oldversion < 2005051402) {
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT category;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT {$CFG->prefix}attemptonlast_datasets_category_userid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}quiz_attemptonlast_datasets DROP CONSTRAINT {$CFG->prefix}quiz_category_userid_unique;",false);
        modify_database('','ALTER TABLE prefix_quiz_attemptonlast_datasets ADD CONSTRAINT prefix_quiz_category_userid_unique UNIQUE (category,userid);');
    }

    if ($oldversion < 2005060300) {
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
                //Process it with quiz_delete_course(). It will do all the hard work.
                if (!record_exists('course', 'id', $course->id)) {
                    require_once("$CFG->dirroot/mod/quiz/lib.php");
                    quiz_delete_course($course);
                }
            }
        }
        //Reset rebug to its original state
        $db->debug = $olddebug;
    }

    if ($oldversion < 2005060301) {
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_rqp_type RENAME TO '.$CFG->prefix.'quiz_rqp_types');
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_rqp_type_id_seq RENAME TO '.$CFG->prefix.'rqp_types_id_seq');
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_rqp_types ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'quiz_rqp_types_id_seq\')');
        execute_sql('DROP INDEX '.$CFG->prefix.'quiz_rqp_type_name_uk');
        execute_sql('CREATE UNIQUE INDEX '.$CFG->prefix.'quiz_rqp_types_name_uk ON '.$CFG->prefix.'quiz_rqp_types (name);');

    }
    
    if ($oldversion < 2005060302) { // Mass cleanup of bad postgres upgrade scripts
        execute_sql('CREATE UNIQUE INDEX '.$CFG->prefix.'quiz_newest_states_attempt_idx ON '.$CFG->prefix.'quiz_newest_states (attemptid, questionid)',false);
        execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_attemptonlast_datasets DROP CONSTRAINT '.$CFG->prefix.'quiz_category_userid_unique',false);
        execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_attemptonlast_datasets ADD CONSTRAINT '.$CFG->prefix.'quiz_attemptonlast_datasets_category_userid UNIQUE (category, userid)',false);
        execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_question_instances DROP CONSTRAINT '.$CFG->prefix.'quiz_question_grades_pkey',false);
        execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_question_instances ADD CONSTRAINT '.$CFG->prefix.'quiz_question_instances_pkey PRIMARY KEY (id)',false);
        execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_question_versions DROP CONSTRAINT '.$CFG->prefix.'quiz_question_version_pkey',false);
        execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_question_versions ADD CONSTRAINT '.$CFG->prefix.'quiz_question_versions_pkey PRIMARY KEY (id)',false);
        execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_states DROP CONSTRAINT '.$CFG->prefix.'quiz_responses_pkey',false);
        execute_sql('ALTER TABLE ONLY '.$CFG->prefix.'quiz_states ADD CONSTRAINT '.$CFG->prefix.'quiz_states_pkey PRIMARY KEY (id)',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER decimalpoints SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER optionflags SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER penaltyscheme SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER popup SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER questionsperpage SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz ALTER review SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_answers ALTER answer SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_attempts ALTER layout SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_attempts ALTER preview SET NOT NULL',false);

        table_column('quiz_calculated','correctanswerformat','correctanswerformat','integer','16','unsigned','2');

        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_categories ALTER parent SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_categories ALTER sortorder SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_grades ALTER grade SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_multianswers ALTER sequence SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_numerical ALTER tolerance SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_questions ALTER hidden SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_questions ALTER length SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_questions ALTER parent SET NOT NULL',false);

        table_column('quiz_questions','penalty','penalty','real','','UNSIGNED','0.1');

        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER answer SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER event SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER originalquestion SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER penalty SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER raw_grade SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER seq_number SET NOT NULL',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_states ALTER timestamp SET NOT NULL',false);
    }

    if ($oldversion < 2005100500) {
        // clean up an old mistake.
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_question_version_id_seq RENAME TO '.$CFG->prefix.'quiz_question_versions_id_seq',false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'quiz_question_versions ALTER COLUMN id SET DEFAULT nextval(\''.$CFG->prefix.'quiz_question_versions_id_seq\')',false);
    }

    return true;
}

?>
