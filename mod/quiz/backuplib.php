<?php
    //This php script contains all the stuff to backup quizzes

//This is the "graphical" structure of the quiz mod:
    //To see, put your terminal to 160cc

    //
    //                           quiz
    //                        (CL,pk->id)
    //                            |
    //           -------------------------------------------------------------------
    //           |               |                |                                |
    //           |          quiz_grades           |                                |
    //           |      (UL,pk->id,fk->quiz)      |                                |
    //           |                                |                                |
    //      quiz_attempts             quiz_question_instances                quiz_feedback
    //  (UL,pk->id,fk->quiz)       (CL,pk->id,fk->quiz,question)         (CL,pk->id,fk->quiz)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          SL->site level info
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files
    //
    //-----------------------------------------------------------

    // When we backup a quiz we also need to backup the questions and possibly
    // the data about student interaction with the questions. The functions to do
    // that are included with the following library
    require_once("$CFG->dirroot/question/backuplib.php");

    /*
     * Insert necessary category ids to backup_ids table. Called during backup_check.html.
     * This backs up ids for quiz module. It backs up :
     *     all categories and questions in course
     *     all categories and questions in contexts of quiz module instances which have been selected for backup
     *     all categories and questions in contexts above course level that are used by quizzes that have been selected for backup
     */
    function quiz_insert_category_and_question_ids($course, $backup_unique_code, $instances = null) {
        global $CFG, $DB;
        $status = true;

        // Create missing categories and reasign orphaned questions.
        quiz_fix_orphaned_questions($course);

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course);
        $status = $status && question_insert_c_and_q_ids_for_course($coursecontext, $backup_unique_code);

        // then, all categories and questions from this course's modules' contexts.
        $status = $status && question_insert_c_and_q_ids_for_module($backup_unique_code, $course, 'quiz', $instances);


        // Then categories from parent contexts used by the quizzes we are backing up.
        $parentcontexts = get_parent_contexts($coursecontext);
        $params = array($course);
        $from = "{quiz} quiz,";
        $where = "AND quiz.course = ?
                     AND qqi.quiz = quiz.id";
        if (!empty($instances) && is_array($instances) && count($instances)) {
            $questionselectsqlfrom = '';
            list($quiz_usql, $quiz_params) = $DB->get_in_or_equal(array_keys($instances));
            $questionselectsqlwhere = "AND qqi.quiz $quiz_usql";
            $params = array_merge($params, $quiz_params);
        } else {
            $questionselectsqlfrom = "{quiz} quiz,";
            $questionselectsqlwhere = "AND quiz.course = ? AND qqi.quiz = quiz.id";
            $params[] = $course;
        }

        list($usql, $context_params) = $DB->get_in_or_equal($parentcontexts);
        $params = array_merge($context_params, $params);
        $categories = $DB->get_records_sql("
                SELECT id, parent, 0 AS childrendone
                FROM {question_categories}
                WHERE contextid $usql
                  AND id IN (
                    SELECT DISTINCT question.category
                    FROM {question} question,
                         $questionselectsqlfrom
                         {quiz_question_instances} qqi
                    WHERE qqi.question = question.id
                      $questionselectsqlwhere
                )", $params);
        if (!$categories) {
            $categories = array();
        } else {
            //put the ids of the used questions from all these categories into the db.
            $status = $status && $DB->execute("INSERT INTO {backup_ids}
                                       (backup_code, table_name, old_id, info)
                                       SELECT DISTINCT $backup_unique_code, 'question', q.id, ''
                                       FROM {question} q,
                                       $from
                                       {question_categories} qc,
                                       {quiz_question_instances} qqi
                                       WHERE (qqi.question = q.id
                                       OR qqi.question = q.parent)
                                       AND q.category = qc.id
                                       AND qc.contextid $usql $where", array_merge($context_params, array($course)));

            // Add the parent categories, of these categories up to the top of the category tree.
            // not backing up the questions in these categories.
            foreach ($categories as $category) {
                while ($category->parent != 0) {
                    if (array_key_exists($category->parent, $categories)) {
                        // Parent category already on the list.
                        break;
                    }
                    $currentid = $category->id;
                    $category = $DB->get_record('question_categories', array('id' => $category->parent), 'id, parent, 0 AS childrendone');
                    if ($category) {
                        $categories[$category->id] = $category;
                    } else {
                        // Parent not found: this indicates an error, but just fix it.
                        $DB->set_field('question_categories', 'parent', 0, array('id' => $currentid));
                        break;
                    }
                }
            }

            // Now we look for categories from other courses containing random questions
            // in our quizzes that select from the category and its subcategories. That implies
            // those subcategories also need to be backed up. (The categories themselves
            // and their parents will already have been included.)
            $categorieswithrandom = $DB->get_records_sql("
                    SELECT question.category AS id, SUM(" .
                            $DB->sql_cast_char2int('questiontext', true) . ") AS numqsusingsubcategories
                    FROM {quiz_question_instances} qqi,
                         $from
                         {question} question
                    WHERE question.id = qqi.question
                      AND question.qtype = '" . RANDOM . "'
                      $where
                    GROUP BY question.category
                    ", array($course));
            $randomselectedquestions = array();
            if ($categorieswithrandom) {
                foreach ($categorieswithrandom as $category) {
                    if ($category->numqsusingsubcategories > 0) {
                        $status = $status && quiz_backup_add_sub_categories($categories, $randomselectedquestions, $category->id);
                    }
                }
                list($usql, $params) = $DB->get_in_or_equal(array_keys($categorieswithrandom));
                $returnval = $DB->get_records_sql("
                    SELECT question.id
                    FROM {question} question
                    WHERE question.category $usql", $params);
                if ($returnval) {
                    $randomselectedquestions += $returnval;
                }
            }

            // Finally, add all these extra categories to the backup_ids table.
            foreach ($categories as $category) {
                $status = $status && backup_putid($backup_unique_code, 'question_categories', $category->id, 0);
            }
            // Finally, add all these extra categories to the backup_ids table.
            foreach ($randomselectedquestions as $question) {
                $status = $status && backup_putid($backup_unique_code, 'question', $question->id, 0);
            }
        }
        return $status;
    }

    /**
     * Helper function adding the id of all the subcategories of a category to an array.
     */
    function quiz_backup_add_sub_categories(&$categories, &$questions, $categoryid) {
        global $CFG, $DB;
        $status = true;
        if ($categories[$categoryid]->childrendone) {
            return $status;
        }
        if ($subcategories = $DB->get_records('question_categories', array('parent' => $categoryid), '', 'id, 0 AS childrendone')) {
            foreach ($subcategories as $subcategory) {
                if (!array_key_exists($subcategory->id, $categories)) {
                    $categories[$subcategory->id] = $subcategory;
                }
                $status = $status && quiz_backup_add_sub_categories($categories, $questions, $subcategory->id);
            }
            list($usql, $params) = $DB->get_in_or_equal(array_keys($subcategories));
            $returnval = $DB->get_records_sql("
                SELECT question.id
                FROM {question} question
                WHERE question.category $usql", $params);
            if ($returnval) {
                $questions += $returnval;
            }
        }
        $categories[$categoryid]->childrendone = 1;
        return $status;
    }


    //This function is used to detect orphaned questions (pointing to a
    //non existing category) and to recreate such category. This function
    //is used by the backup process, to ensure consistency and should be
    //executed in the upgrade process and, perhaps in the health center.
    function quiz_fix_orphaned_questions ($course) {

        global $CFG, $DB;

        $categories = $DB->get_records_sql("SELECT DISTINCT t.category, t.category
                                           FROM {question} t,
                                                {quiz_question_instances} g,
                                                {quiz} q
                                           WHERE q.course = ? AND
                                                 g.quiz = q.id AND
                                                 g.question = t.id", array($course));
        if ($categories) {
            foreach ($categories as $key => $category) {
                $exist = $DB->get_record('question_categories', array('id' => $key));
                //If the category doesn't exist
                if (!$exist) {
                    //Build a new category
                    $db_cat = new stdClass;
                    // always create missing categories in course context
                    $db_cat->contextid = get_context_instance(CONTEXT_COURSE, $course);
                    $db_cat->name = get_string('recreatedcategory','',$key);
                    $db_cat->info = get_string('recreatedcategory','',$key);
                    $db_cat->stamp = make_unique_id_code();
                    //Insert the new category
                    $catid = $DB->insert_record('question_categories',$db_cat);
                    unset ($db_cat);
                    if ($catid) {
                        //Reasign orphaned questions to their new category
                        $DB->set_field ('question','category',$catid,array('category' =>$key));
                    }
                }
            }
        }
    }


//STEP 2. Backup quizzes and associated structures
    //    (course dependent)

    function quiz_backup_one_mod($bf,$preferences,$quiz) {
        global $DB;
        $status = true;

        if (is_numeric($quiz)) {
            $quiz = $DB->get_record('quiz', array('id' => $quiz));
        }

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print quiz data
        fwrite ($bf,full_tag("ID",4,false,$quiz->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"quiz"));
        fwrite ($bf,full_tag("NAME",4,false,$quiz->name));
        fwrite ($bf,full_tag("INTRO",4,false,$quiz->intro));
        fwrite ($bf,full_tag("TIMEOPEN",4,false,$quiz->timeopen));
        fwrite ($bf,full_tag("TIMECLOSE",4,false,$quiz->timeclose));
        fwrite ($bf,full_tag("OPTIONFLAGS",4,false,$quiz->optionflags));
        fwrite ($bf,full_tag("PENALTYSCHEME",4,false,$quiz->penaltyscheme));
        fwrite ($bf,full_tag("ATTEMPTS_NUMBER",4,false,$quiz->attempts));
        fwrite ($bf,full_tag("ATTEMPTONLAST",4,false,$quiz->attemptonlast));
        fwrite ($bf,full_tag("GRADEMETHOD",4,false,$quiz->grademethod));
        fwrite ($bf,full_tag("DECIMALPOINTS",4,false,$quiz->decimalpoints));
        fwrite ($bf,full_tag("REVIEW",4,false,$quiz->review));
        fwrite ($bf,full_tag("QUESTIONSPERPAGE",4,false,$quiz->questionsperpage));
        fwrite ($bf,full_tag("SHUFFLEQUESTIONS",4,false,$quiz->shufflequestions));
        fwrite ($bf,full_tag("SHUFFLEANSWERS",4,false,$quiz->shuffleanswers));
        fwrite ($bf,full_tag("QUESTIONS",4,false,$quiz->questions));
        fwrite ($bf,full_tag("SUMGRADES",4,false,$quiz->sumgrades));
        fwrite ($bf,full_tag("GRADE",4,false,$quiz->grade));
        fwrite ($bf,full_tag("TIMECREATED",4,false,$quiz->timecreated));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$quiz->timemodified));
        fwrite ($bf,full_tag("TIMELIMIT",4,false,round($quiz->timelimit/60)));
        fwrite ($bf,full_tag("TIMELIMITSECS",4,false,$quiz->timelimit));
        fwrite ($bf,full_tag("PASSWORD",4,false,$quiz->password));
        fwrite ($bf,full_tag("SUBNET",4,false,$quiz->subnet));
        fwrite ($bf,full_tag("POPUP",4,false,$quiz->popup));
        fwrite ($bf,full_tag("DELAY1",4,false,$quiz->delay1));
        fwrite ($bf,full_tag("DELAY2",4,false,$quiz->delay2));
        //Now we print to xml question_instances (Course Level)
        $status = backup_quiz_question_instances($bf,$preferences,$quiz->id);
        //Now we print to xml quiz_feedback (Course Level)
        $status = backup_quiz_feedback($bf,$preferences,$quiz->id);
        //Now we print to xml quiz_overrides (Course Level)
        $status = backup_quiz_overrides($bf,$preferences,$quiz->id);
        //if we've selected to backup users info, then execute:
        //    - backup_quiz_grades
        //    - backup_quiz_attempts
        if (backup_userdata_selected($preferences,'quiz',$quiz->id) && $status) {
            $status = backup_quiz_grades($bf,$preferences,$quiz->id);
            if ($status) {
                $status = backup_quiz_attempts($bf,$preferences,$quiz->id);
            }
        }
        //End mod
        $status = fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }


    function quiz_backup_mods($bf,$preferences) {
        global $DB;

        global $CFG;

        $status = true;

        //Iterate over quiz table
        $quizzes = $DB->get_records('quiz',array('course' => $preferences->backup_course),'id');
        if ($quizzes) {
            foreach ($quizzes as $quiz) {
                if (backup_mod_selected($preferences,'quiz',$quiz->id)) {
                    $status = quiz_backup_one_mod($bf,$preferences,$quiz);
                }
            }
        }
        return $status;
    }

    //Backup quiz_question_instances contents (executed from quiz_backup_mods)
    function backup_quiz_question_instances ($bf,$preferences,$quiz) {
        global $DB;
        $status = true;

        $quiz_question_instances = $DB->get_records('quiz_question_instances',array('quiz' =>$quiz),'id');
        //If there are question_instances
        if ($quiz_question_instances) {
            //Write start tag
            $status = fwrite ($bf,start_tag("QUESTION_INSTANCES",4,true));
            //Iterate over each question_instance
            foreach ($quiz_question_instances as $que_ins) {
                //Start question instance
                $status = fwrite ($bf,start_tag("QUESTION_INSTANCE",5,true));
                //Print question_instance contents
                fwrite ($bf,full_tag("ID",6,false,$que_ins->id));
                fwrite ($bf,full_tag("QUESTION",6,false,$que_ins->question));
                fwrite ($bf,full_tag("GRADE",6,false,$que_ins->grade));
                //End question instance
                $status = fwrite ($bf,end_tag("QUESTION_INSTANCE",5,true));
            }
            //Write end tag
            $status = fwrite ($bf,end_tag("QUESTION_INSTANCES",4,true));
        }
        return $status;
    }

    //Backup quiz_overrides contents (executed from quiz_backup_mods)
    function backup_quiz_overrides ($bf,$preferences,$quiz) {
        global $DB;
        $status = true;
        $douserdata = backup_userdata_selected($preferences,'quiz',$quiz);

        $quiz_overrides = $DB->get_records('quiz_overrides',array('quiz' =>$quiz),'id');
        //If there are quiz_overrides
        if ($quiz_overrides) {
            //Write start tag
            $status = fwrite ($bf,start_tag("OVERRIDES",4,true));
            //Iterate over each quiz_override
            foreach ($quiz_overrides as $quiz_override) {
                // Only backup user overrides if we are backing up user data
                if ($douserdata || $quiz_override->userid == 0) {
                    //Start quiz override
                    $status = fwrite ($bf,start_tag("OVERRIDE",5,true));
                    //Print quiz_override contents
                    fwrite ($bf,full_tag("ID",6,false,$quiz_override->id));
                    fwrite ($bf,full_tag("USERID",6,false,$quiz_override->userid));
                    fwrite ($bf,full_tag("GROUPID",6,false,$quiz_override->groupid));
                    fwrite ($bf,full_tag("TIMEOPEN",6,false,$quiz_override->timeopen));
                    fwrite ($bf,full_tag("TIMECLOSE",6,false,$quiz_override->timeclose));
                    fwrite ($bf,full_tag("TIMELIMIT",6,false,$quiz_override->timelimit));
                    fwrite ($bf,full_tag("ATTEMPTS",6,false,$quiz_override->attempts));
                    fwrite ($bf,full_tag("PASSWORD",6,false,$quiz_override->password));
                    //End quiz override
                    $status = fwrite ($bf,end_tag("OVERRIDE",5,true));
                }
            }
            //Write end tag
            $status = fwrite ($bf,end_tag("OVERRIDES",4,true));
        }
        return $status;
    }

    //Backup quiz_question_instances contents (executed from quiz_backup_mods)
    function backup_quiz_feedback ($bf,$preferences,$quiz) {
        global $DB;
        $status = true;

        $quiz_feedback = $DB->get_records('quiz_feedback', array('quizid' => $quiz), 'id');
        // If there are question_instances ...
        if ($quiz_feedback) {
            // Write start tag.
            $status = $status & fwrite($bf,start_tag('FEEDBACKS', 4, true));

            // Iterate over each question_instance.
            foreach ($quiz_feedback as $feedback) {

                //Start feedback instance
                $status = $status & fwrite($bf, start_tag('FEEDBACK',5,true));

                //Print question_instance contents.
                $status = $status & fwrite($bf, full_tag('ID', 6, false, $feedback->id));
                $status = $status & fwrite($bf, full_tag('QUIZID', 6, false, $feedback->quizid));
                $status = $status & fwrite($bf, full_tag('FEEDBACKTEXT', 6, false, $feedback->feedbacktext));
                $status = $status & fwrite($bf, full_tag('MINGRADE', 6, false, $feedback->mingrade));
                $status = $status & fwrite($bf, full_tag('MAXGRADE', 6, false, $feedback->maxgrade));

                // End feedback instance.
                $status = $status & fwrite($bf, end_tag('FEEDBACK', 5, true));
            }

            // Write end tag.
            $status = $status & fwrite($bf, end_tag('FEEDBACKS', 4, true));
        }
        return $status;
    }

    //Backup quiz_grades contents (executed from quiz_backup_mods)
    function backup_quiz_grades ($bf,$preferences,$quiz) {
        global $DB;
        $status = true;

        $quiz_grades = $DB->get_records('quiz_grades',array('quiz' => $quiz),'id');
        //If there are grades
        if ($quiz_grades) {
            //Write start tag
            $status = fwrite ($bf,start_tag("GRADES",4,true));
            //Iterate over each grade
            foreach ($quiz_grades as $gra) {
                //Start grade
                $status = fwrite ($bf,start_tag("GRADE",5,true));
                //Print grade contents
                fwrite ($bf,full_tag("ID",6,false,$gra->id));
                fwrite ($bf,full_tag("USERID",6,false,$gra->userid));
                fwrite ($bf,full_tag("GRADEVAL",6,false,$gra->grade));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$gra->timemodified));
                //End question grade
                $status = fwrite ($bf,end_tag("GRADE",5,true));
            }
            //Write end tag
            $status = fwrite ($bf,end_tag("GRADES",4,true));
        }
        return $status;
    }

    //Backup quiz_attempts contents (executed from quiz_backup_mods)
    function backup_quiz_attempts ($bf,$preferences,$quiz) {
        global $DB;
        $status = true;

        $quiz_attempts = $DB->get_records('quiz_attempts', array('quiz' => $quiz),'id');
        //If there are attempts
        if ($quiz_attempts) {
            //Write start tag
            $status = fwrite ($bf,start_tag("ATTEMPTS",4,true));
            //Iterate over each attempt
            foreach ($quiz_attempts as $attempt) {
                //Start attempt
                $status = fwrite ($bf,start_tag("ATTEMPT",5,true));
                //Print attempt contents
                fwrite ($bf,full_tag("ID",6,false,$attempt->id));
                fwrite ($bf,full_tag("UNIQUEID",6,false,$attempt->uniqueid));
                fwrite ($bf,full_tag("USERID",6,false,$attempt->userid));
                fwrite ($bf,full_tag("ATTEMPTNUM",6,false,$attempt->attempt));
                fwrite ($bf,full_tag("SUMGRADES",6,false,$attempt->sumgrades));
                fwrite ($bf,full_tag("TIMESTART",6,false,$attempt->timestart));
                fwrite ($bf,full_tag("TIMEFINISH",6,false,$attempt->timefinish));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$attempt->timemodified));
                fwrite ($bf,full_tag("LAYOUT",6,false,$attempt->layout));
                fwrite ($bf,full_tag("PREVIEW",6,false,$attempt->preview));
                //Now write to xml the states (in this attempt)
                $status = backup_question_states ($bf,$preferences,$attempt->uniqueid);
                //Now write to xml the sessions (in this attempt)
                $status = backup_question_sessions ($bf,$preferences,$attempt->uniqueid);
                //End attempt
                $status = fwrite ($bf,end_tag("ATTEMPT",5,true));
            }
            //Write end tag
            $status = fwrite ($bf,end_tag("ATTEMPTS",4,true));
        }
        return $status;
    }

    function quiz_check_backup_mods_instances($instance,$backup_unique_code) {
        // the keys in this array need to be unique as they get merged...
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';

        //Categories
        $info[$instance->id.'1'][0] = get_string("categories","quiz");
        if ($ids = question_category_ids_by_backup ($backup_unique_code)) {
            $info[$instance->id.'1'][1] = count($ids);
        } else {
            $info[$instance->id.'1'][1] = 0;
        }
        //Questions
        $info[$instance->id.'2'][0] = get_string("questionsinclhidden","quiz");
        if ($ids = question_ids_by_backup ($backup_unique_code)) {
            $info[$instance->id.'2'][1] = count($ids);
        } else {
            $info[$instance->id.'2'][1] = 0;
        }

        //Now, if requested, the user_data
        if (!empty($instance->userdata)) {
            //Grades
            $info[$instance->id.'3'][0] = get_string("grades");
            if ($ids = quiz_grade_ids_by_instance ($instance->id)) {
                $info[$instance->id.'3'][1] = count($ids);
            } else {
                $info[$instance->id.'3'][1] = 0;
            }
        }
        return $info;
    }

   ////Return an array of info (name,value)
/// $instances is an array with key = instanceid, value = object (name,id,userdata)
   function quiz_check_backup_mods($course,$user_data= false,$backup_unique_code,$instances=null) {
        //this function selects all the questions / categories to be backed up.
        quiz_insert_category_and_question_ids($course, $backup_unique_code, $instances);
        if ($course != SITEID){
            question_insert_site_file_names($course, $backup_unique_code);
        }
        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += quiz_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        //First the course data
        $info[0][0] = get_string("modulenameplural","quiz");
        if ($ids = quiz_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }
        //Categories
        $info[1][0] = get_string("categories","quiz");
        if ($ids = question_category_ids_by_backup ($backup_unique_code)) {
            $info[1][1] = count($ids);
        } else {
            $info[1][1] = 0;
        }
        //Questions
        $info[2][0] = get_string("questions","quiz");
        if ($ids = question_ids_by_backup ($backup_unique_code)) {
            $info[2][1] = count($ids);
        } else {
            $info[2][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            //Grades
            $info[3][0] = get_string("grades");
            if ($ids = quiz_grade_ids_by_course ($course)) {
                $info[3][1] = count($ids);
            } else {
                $info[3][1] = 0;
            }
        }

        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function quiz_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of quizs
        $buscar="/(".$base."\/mod\/quiz\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@QUIZINDEX*$2@$',$content);

        //Link to quiz view by moduleid
        $buscar="/(".$base."\/mod\/quiz\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@QUIZVIEWBYID*$2@$',$result);

        //Link to quiz view by quizid
        $buscar="/(".$base."\/mod\/quiz\/view.php\?q\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@QUIZVIEWBYQ*$2@$',$result);

        return $result;
    }

// INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of quiz id
    function quiz_ids ($course) {

        global $CFG, $DB;

        return $DB->get_records_sql ("SELECT a.id, a.course
                                     FROM {quiz} a
                                     WHERE a.course = ?", array($course));
    }

    function quiz_grade_ids_by_course ($course) {

        global $CFG, $DB;

        return $DB->get_records_sql ("SELECT g.id, g.quiz
                                     FROM {quiz} a,
                                          {quiz_grades} g
                                     WHERE a.course = ? and
                                           g.quiz = a.id", array($course));
    }

    function quiz_grade_ids_by_instance($instanceid) {

        global $CFG, $DB;

        return $DB->get_records_sql ("SELECT g.id, g.quiz
                                     FROM {quiz_grades} g
                                     WHERE g.quiz = ?", array($instanceid));
    }


