<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //quiz mods

//This is the "graphical" structure of the quiz mod:
    //To see, put your terminal to 160cc

    //
    //                           quiz                                                      quiz_categories
    //                        (CL,pk->id)                                                   (CL,pk->id)
    //                            |                                                              |
    //           -------------------------------------------------------------------             |
    //           |                        |                    |                   |             |.......................................
    //           |               quiz_grades                   |        quiz_question_versions   |                                      .
    //           |           (UL,pk->id,fk->quiz)              |         (CL,pk->id,fk->quiz)    |                                      .
    //           |                                             |                         .       |    ----quiz_question_datasets----    .
    //      quiz_attempts                          quiz_question_instances               .       |    |  (CL,pk->id,fk->question,  |    .
    //  (UL,pk->id,fk->quiz)                    (CL,pk->id,fk->quiz,question)            .       |    |   fk->dataset_definition)  |    .
    //             |                                              |                      .       |    |                            |    .
    //             |               quiz_newest_states             |                      .       |    |                            |    .
    //             |---------(UL,pk->id,fk->attempt,question)-----|                      .       |    |                            |    .
    //             |                        .                     |                      .       |    |                       quiz_dataset_definitions
    //             |                        .                     |                      .       |    |                      (CL,pk->id,fk->category)
    //             |                    quiz_states               |                      quiz_questions                                 |
    //             ----------(UL,pk->id,fk->attempt,question)--------------------------(CL,pk->id,fk->category,files)                   |
    //                                      |                                                    |                             quiz_dataset_items
    //                                      |                                                    |                          (CL,pk->id,fk->definition)
    //                              ---------                                                    |
    //                              |                                                            |
    //                        quiz_rqp_states                                                    |
    //                    (UL,pk->id,fk->stateid)                                                |                                   quiz_rqp_type
    //                                                                                           |                                    (SL,pk->id)
    //                                                                                           |                                         |
    //             --------------------------------------------------------------------------------------------------------------          |
    //             |             |              |              |                       |                  |                     |        quiz_rqp
    //             |             |              |              |                       |                  |                     |--(CL,pk->id,fk->question)
    //             |             |              |              |                 quiz_calculated          |                     |
    //      quiz_truefalse       |       quiz_multichoice      |             (CL,pl->id,fk->question)     |                     |
    // (CL,pk->id,fk->question)  |   (CL,pk->id,fk->question)  |                       .                  |                     |    quiz_randomsamatch
    //             .             |              .              |                       .                  |                     |--(CL,pk->id,fk->question)
    //             .      quiz_shortanswer      .       quiz_numerical                 .            quiz_multianswer.           |
    //             .  (CL,pk->id,fk->question)  .  (CL,pk->id,fk->question)            .        (CL,pk->id,fk->question)        |
    //             .             .              .              .                       .                  .                     |         quiz_match
    //             .             .              .              .                       .                  .                     |--(CL,pk->id,fk->question)
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |       quiz_match_sub
    //             ........................................................................................                     |--(CL,pk->id,fk->question)
    //                                                   .                                                                      |
    //                                                   .                                                                      |
    //                                                   .                                                                      |    quiz_numerical_units
    //                                                quiz_answers                                                              |--(CL,pk->id,fk->question)
    //                                         (CL,pk->id,fk->question)----------------------------------------------------------
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

// Comments:
    //THIS MOD BACKUP NEEDS TO USE THE mdl_backup_ids TABLE

    //This module is special, because we make the backup in two steps:
    // 1.-We backup every category and their questions (complete structure). It includes this tables:
    //     - quiz_categories
    //     - quiz_questions
    //     - quiz_rqp
    //     - quiz_truefalse
    //     - quiz_shortanswer
    //     - quiz_multianswer
    //     - quiz_multichoice
    //     - quiz_numerical
    //     - quiz_randomsamatch
    //     - quiz_match
    //     - quiz_match_sub
    //     - quiz_calculated
    //     - quiz_answers
    //     - quiz_numerical_units
    //     - quiz_question_datasets
    //     - quiz_dataset_definitions
    //     - quiz_dataset_items
    //    All this backup info have its own section in moodle.xml (QUESTION_CATEGORIES) and it's generated
    //    before every module backup standard invocation. And only if to backup quizzes has been selected !!
    //    It's invoked with quiz_backup_question_categories. (course independent).

    // 2.-Standard module backup (Invoked via quiz_backup_mods). It includes this tables:
    //     - quiz
    //     - quiz_question_versions
    //     - quiz_question_instances
    //     - quiz_attempts
    //     - quiz_grades
    //     - quiz_states
    //     - quiz_newest_states
    //    This step is the standard mod backup. (course dependent).

//STEP 1. Backup categories/questions and associated structures
    //    (course independent)

    //Insert necessary category ids to backup_ids table
    function insert_category_ids ($course,$backup_unique_code) {
        global $CFG;

        //Create missing categories and reasign orphaned questions
        fix_orphaned_questions($course);

        //Detect used categories (by category in questions)
        $status = execute_sql("INSERT INTO {$CFG->prefix}backup_ids
                                   (backup_code, table_name, old_id)
                               SELECT DISTINCT $backup_unique_code,'quiz_categories',t.category
                               FROM {$CFG->prefix}quiz_questions t,
                                    {$CFG->prefix}quiz_question_instances g,
                                    {$CFG->prefix}quiz q
                               WHERE q.course = '$course' AND
                                     g.quiz = q.id AND
                                     g.question = t.id",false);

        //Now, foreach detected category, we look for their parents upto 0 (top category)
        $categories = get_records_sql("SELECT old_id, old_id
                                       FROM {$CFG->prefix}backup_ids
                                       WHERE backup_code = $backup_unique_code AND
                                             table_name = 'quiz_categories'");

        if ($categories) {
            foreach ($categories as $category) {
                if ($dbcat = get_record('quiz_categories','id',$category->old_id)) {
                    //echo $dbcat->name;      //Debug
                    //Go up to 0
                    while ($dbcat->parent != 0) {
                        //echo '->';              //Debug
                        $current = $dbcat->id;
                        if ($dbcat = get_record('quiz_categories','id',$dbcat->parent)) {
                            //Found parent, add it to backup_ids (by using backup_putid
                            //we ensure no duplicates!)
                            $status = backup_putid($backup_unique_code,'quiz_categories',$dbcat->id,0);
                            //echo $dbcat->name;      //Debug
                        } else {
                            //Parent not found, fix it (set its parent to 0)
                            set_field ('quiz_categories','parent',0,'id',$current);
                            //echo 'assigned to top!';          //Debug
                        }
                    }
                    //echo '<br />';          //Debug
                }
            }
        }

        return $status;
    }

    //This function is used to detect orphaned questions (pointing to a
    //non existing category) and to recreate such category. This function
    //is used by the backup process, to ensure consistency and should be
    //executed in the upgrade process and, perhaps in the health center.
    function fix_orphaned_questions ($course) {

        global $CFG;

        $categories = get_records_sql("SELECT DISTINCT t.category, t.category
                                       FROM {$CFG->prefix}quiz_questions t,
                                            {$CFG->prefix}quiz_question_instances g,
                                            {$CFG->prefix}quiz q
                                       WHERE q.course = '$course' AND
                                             g.quiz = q.id AND
                                             g.question = t.id",false);
        if ($categories) {
            foreach ($categories as $key => $category) {
                $exist = get_record('quiz_categories','id', $key);
                //If the category doesn't exist
                if (!$exist) {
                    //Build a new category
                    $db_cat->course = $course;
                    $db_cat->name = get_string('recreatedcategory','',$key);
                    $db_cat->info = get_string('recreatedcategory','',$key);
                    $db_cat->publish = 1;
                    $db_cat->stamp = make_unique_id_code();
                    //Insert the new category
                    $catid = insert_record('quiz_categories',$db_cat);
                    unset ($db_cat);
                    if ($catid) {
                        //Reasign orphaned questions to their new category
                        set_field ('quiz_questions','category',$catid,'category',$key);
                    }
                }
            }
        }
    }

    //Delete category ids from backup_ids table
    function delete_category_ids ($backup_unique_code) {
        global $CFG;
        $status = true;
        $status = execute_sql("DELETE FROM {$CFG->prefix}backup_ids
                               WHERE backup_code = '$backup_unique_code'",false);
        return $status;
    }

    function quiz_backup_question_categories($bf,$preferences) {

        global $CFG;

        $status = true;

        //First, we get the used categories from backup_ids
        $categories = quiz_category_ids_by_backup ($preferences->backup_unique_code);

        //If we've categories
        if ($categories) {
             //Write start tag
             $status = fwrite($bf,start_tag("QUESTION_CATEGORIES",2,true));
             //Iterate over each category
            foreach ($categories as $cat) {
                //Start category
                $status =fwrite ($bf,start_tag("QUESTION_CATEGORY",3,true));
                //Get category data from quiz_categories
                $category = get_record ("quiz_categories","id",$cat->old_id);
                //Print category contents
                fwrite($bf,full_tag("ID",4,false,$category->id));
                fwrite($bf,full_tag("NAME",4,false,$category->name));
                fwrite($bf,full_tag("INFO",4,false,$category->info));
                fwrite($bf,full_tag("PUBLISH",4,false,$category->publish));
                fwrite($bf,full_tag("STAMP",4,false,$category->stamp));
                fwrite($bf,full_tag("PARENT",4,false,$category->parent));
                fwrite($bf,full_tag("SORTORDER",4,false,$category->sortorder));
                //Now, backup their questions
                $status = quiz_backup_question($bf,$preferences,$category->id);
                //End category
                $status =fwrite ($bf,end_tag("QUESTION_CATEGORY",3,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("QUESTION_CATEGORIES",2,true));
        }

        return $status;
    }

    //This function backups all the questions in selected category and their
    //asociated data
    function quiz_backup_question($bf,$preferences,$category) {

        global $CFG;

        $status = true;

        // We'll fetch the questions sorted by parent so that questions with no parents
        // (these are the ones which could be parents themselves) are backed up first. This
        // is important for the recoding of the parent field during the restore process
        $questions = get_records("quiz_questions","category",$category,"parent ASC, id");
        //If there are questions
        if ($questions) {
            //Write start tag
            $status =fwrite ($bf,start_tag("QUESTIONS",4,true));
            $counter = 0;
            //Iterate over each question
            foreach ($questions as $question) {
                //Start question
                $status =fwrite ($bf,start_tag("QUESTION",5,true));
                //Print question contents
                fwrite ($bf,full_tag("ID",6,false,$question->id));
                fwrite ($bf,full_tag("PARENT",6,false,$question->parent));
                fwrite ($bf,full_tag("NAME",6,false,$question->name));
                fwrite ($bf,full_tag("QUESTIONTEXT",6,false,$question->questiontext));
                fwrite ($bf,full_tag("QUESTIONTEXTFORMAT",6,false,$question->questiontextformat));
                fwrite ($bf,full_tag("IMAGE",6,false,$question->image));
                fwrite ($bf,full_tag("DEFAULTGRADE",6,false,$question->defaultgrade));
                fwrite ($bf,full_tag("PENALTY",6,false,$question->penalty));
                fwrite ($bf,full_tag("QTYPE",6,false,$question->qtype));
                fwrite ($bf,full_tag("LENGTH",6,false,$question->length));
                fwrite ($bf,full_tag("STAMP",6,false,$question->stamp));
                fwrite ($bf,full_tag("VERSION",6,false,$question->version));
                fwrite ($bf,full_tag("HIDDEN",6,false,$question->hidden));
                //Now, depending of the qtype, call one function or other
                if ($question->qtype == "1") {
                    $status = quiz_backup_shortanswer($bf,$preferences,$question->id);
                } else if ($question->qtype == "2") {
                    $status = quiz_backup_truefalse($bf,$preferences,$question->id);
                } else if ($question->qtype == "3") {
                    $status = quiz_backup_multichoice($bf,$preferences,$question->id);
                } else if ($question->qtype == "4") {
                    //Random question. Nothing to write.
                } else if ($question->qtype == "5") {
                    $status = quiz_backup_match($bf,$preferences,$question->id);
                } else if ($question->qtype == "6") {
                    $status = quiz_backup_randomsamatch($bf,$preferences,$question->id);
                } else if ($question->qtype == "7") {
                    //Description question. Nothing to write.
                } else if ($question->qtype == "8") {
                    $status = quiz_backup_numerical($bf,$preferences,$question->id);
                } else if ($question->qtype == "9") {
                    $status = quiz_backup_multianswer($bf,$preferences,$question->id);
                } else if ($question->qtype == "10") {
                    $status = quiz_backup_calculated($bf,$preferences,$question->id);
                } else if ($question->qtype == "11") {
                    $status = quiz_backup_rqp($bf,$preferences,$question->id);
                } else if ($question->qtype == "12") {
                    $status = quiz_backup_essay($bf,$preferences,$question->id);
                }
                //End question
                $status =fwrite ($bf,end_tag("QUESTION",5,true));
                //Do some output
                $counter++;
                if ($counter % 10 == 0) {
                    echo ".";
                    if ($counter % 200 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("QUESTIONS",4,true));
        }
        return $status;
    }

    //This function backups the data in a truefalse question (qtype=2) and its
    //asociated data
    function quiz_backup_truefalse($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $truefalses = get_records("quiz_truefalse","question",$question,"id");
        //If there are truefalses
        if ($truefalses) {
            //Iterate over each truefalse
            foreach ($truefalses as $truefalse) {
                $status =fwrite ($bf,start_tag("TRUEFALSE",6,true));
                //Print truefalse contents
                fwrite ($bf,full_tag("TRUEANSWER",7,false,$truefalse->trueanswer));
                fwrite ($bf,full_tag("FALSEANSWER",7,false,$truefalse->falseanswer));
                $status =fwrite ($bf,end_tag("TRUEFALSE",6,true));
            }
            //Now print quiz_answers
            $status = quiz_backup_answers($bf,$preferences,$question);
        }
        return $status;
    }

    //This function backups the data in a shortanswer question (qtype=1) and its
    //asociated data
    function quiz_backup_shortanswer($bf,$preferences,$question,$level=6,$include_answers=true) {

        global $CFG;

        $status = true;

        $shortanswers = get_records("quiz_shortanswer","question",$question,"id");
        //If there are shortanswers
        if ($shortanswers) {
            //Iterate over each shortanswer
            foreach ($shortanswers as $shortanswer) {
                $status =fwrite ($bf,start_tag("SHORTANSWER",$level,true));
                //Print shortanswer contents
                fwrite ($bf,full_tag("ANSWERS",$level+1,false,$shortanswer->answers));
                fwrite ($bf,full_tag("USECASE",$level+1,false,$shortanswer->usecase));
                $status =fwrite ($bf,end_tag("SHORTANSWER",$level,true));
            }
            //Now print quiz_answers
            if ($include_answers) {
                $status = quiz_backup_answers($bf,$preferences,$question);
            }
        }
        return $status;
    }

    //This function backups the data in a multichoice question (qtype=3) and its
    //asociated data
    function quiz_backup_multichoice($bf,$preferences,$question,$level=6,$include_answers=true) {

        global $CFG;

        $status = true;

        $multichoices = get_records("quiz_multichoice","question",$question,"id");
        //If there are multichoices
        if ($multichoices) {
            //Iterate over each multichoice
            foreach ($multichoices as $multichoice) {
                $status =fwrite ($bf,start_tag("MULTICHOICE",$level,true));
                //Print multichoice contents
                fwrite ($bf,full_tag("LAYOUT",$level+1,false,$multichoice->layout));
                fwrite ($bf,full_tag("ANSWERS",$level+1,false,$multichoice->answers));
                fwrite ($bf,full_tag("SINGLE",$level+1,false,$multichoice->single));
                $status =fwrite ($bf,end_tag("MULTICHOICE",$level,true));
            }
            //Now print quiz_answers
            if ($include_answers) {
                $status = quiz_backup_answers($bf,$preferences,$question);
            }
        }
        return $status;
    }

    //This function backups the data in a randomsamatch question (qtype=6) and its
    //asociated data
    function quiz_backup_randomsamatch($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $randomsamatchs = get_records("quiz_randomsamatch","question",$question,"id");
        //If there are randomsamatchs
        if ($randomsamatchs) {
            //Iterate over each randomsamatch
            foreach ($randomsamatchs as $randomsamatch) {
                $status =fwrite ($bf,start_tag("RANDOMSAMATCH",6,true));
                //Print randomsamatch contents
                fwrite ($bf,full_tag("CHOOSE",7,false,$randomsamatch->choose));
                $status =fwrite ($bf,end_tag("RANDOMSAMATCH",6,true));
            }
        }
        return $status;
    }

    //This function backups the data in a match question (qtype=5) and its
    //asociated data
    function quiz_backup_match($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $matchs = get_records("quiz_match_sub","question",$question,"id");
        //If there are matchs
        if ($matchs) {
            $status =fwrite ($bf,start_tag("MATCHS",6,true));
            //Iterate over each match
            foreach ($matchs as $match) {
                $status =fwrite ($bf,start_tag("MATCH",7,true));
                //Print match contents
                fwrite ($bf,full_tag("ID",8,false,$match->id));
                fwrite ($bf,full_tag("QUESTIONTEXT",8,false,$match->questiontext));
                fwrite ($bf,full_tag("ANSWERTEXT",8,false,$match->answertext));
                $status =fwrite ($bf,end_tag("MATCH",7,true));
            }
            $status =fwrite ($bf,end_tag("MATCHS",6,true));
        }
        return $status;
    }

    //This function backups the data in a numerical question (qtype=8) and its
    //asociated data
    function quiz_backup_numerical($bf,$preferences,$question,$level=6,$include_answers=true) {

        global $CFG;

        $status = true;

        $numericals = get_records("quiz_numerical","question",$question,"id");
        //If there are numericals
        if ($numericals) {
            //Iterate over each numerical
            foreach ($numericals as $numerical) {
                $status =fwrite ($bf,start_tag("NUMERICAL",$level,true));
                //Print numerical contents
                fwrite ($bf,full_tag("ANSWER",$level+1,false,$numerical->answer));
                fwrite ($bf,full_tag("TOLERANCE",$level+1,false,$numerical->tolerance));
                //Now backup numerical_units
                $status = quiz_backup_numerical_units($bf,$preferences,$question,7);
                $status =fwrite ($bf,end_tag("NUMERICAL",$level,true));
            }
            //Now print quiz_answers
            if ($include_answers) {
                $status = quiz_backup_answers($bf,$preferences,$question);
            }
        }
        return $status;
    }

    //This function backups the data in a multianswer question (qtype=9) and its
    //asociated data
    function quiz_backup_multianswer($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $multianswers = get_records("quiz_multianswers","question",$question,"id");
        //If there are multianswers
        if ($multianswers) {
            //Print multianswers header
            $status =fwrite ($bf,start_tag("MULTIANSWERS",6,true));
            //Iterate over each multianswer
            foreach ($multianswers as $multianswer) {
                $status =fwrite ($bf,start_tag("MULTIANSWER",7,true));
                //Print multianswer contents
                fwrite ($bf,full_tag("ID",8,false,$multianswer->id));
                fwrite ($bf,full_tag("QUESTION",8,false,$multianswer->question));
                fwrite ($bf,full_tag("SEQUENCE",8,false,$multianswer->sequence));
                $status =fwrite ($bf,end_tag("MULTIANSWER",7,true));
            }
            //Print multianswers footer
            $status =fwrite ($bf,end_tag("MULTIANSWERS",6,true));
            //Now print quiz_answers
            $status = quiz_backup_answers($bf,$preferences,$question);
        }
        return $status;
    }

    //This function backups the data in a calculated question (qtype=10) and its
    //asociated data
    function quiz_backup_calculated($bf,$preferences,$question,$level=6,$include_answers=true) {

        global $CFG;

        $status = true;

        $calculateds = get_records("quiz_calculated","question",$question,"id");
        //If there are calculated-s
        if ($calculateds) {
            //Iterate over each calculateds
            foreach ($calculateds as $calculated) {
                $status =fwrite ($bf,start_tag("CALCULATED",$level,true));
                //Print calculated contents
                fwrite ($bf,full_tag("ANSWER",$level+1,false,$calculated->answer));
                fwrite ($bf,full_tag("TOLERANCE",$level+1,false,$calculated->tolerance));
                fwrite ($bf,full_tag("TOLERANCETYPE",$level+1,false,$calculated->tolerancetype));
                fwrite ($bf,full_tag("CORRECTANSWERLENGTH",$level+1,false,$calculated->correctanswerlength));
                fwrite ($bf,full_tag("CORRECTANSWERFORMAT",$level+1,false,$calculated->correctanswerformat));
                //Now backup numerical_units
                $status = quiz_backup_numerical_units($bf,$preferences,$question,7);
                //Now backup required dataset definitions and items...
                $status = quiz_backup_datasets($bf,$preferences,$question,7);
                //End calculated data
                $status =fwrite ($bf,end_tag("CALCULATED",$level,true));
            }
            //Now print quiz_answers
            if ($include_answers) {
                $status = quiz_backup_answers($bf,$preferences,$question);
            }
        }
        return $status;
    }

    //This function backups the data in an rqp question (qtype=11) and its
    //asociated data
    function quiz_backup_rqp($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $rqp = get_records("quiz_rqp","question",$question,"id");
        //If there are rqps
        if ($rqps) {
            //Iterate over each rqp
            foreach ($rqps as $rqp) {
                $status =fwrite ($bf,start_tag("RQP",6,true));
                //Print rqp contents
                fwrite ($bf,full_tag("TYPE",7,false,$rqp->type));
                fwrite ($bf,full_tag("SOURCE",7,false,$rqp->source));
                fwrite ($bf,full_tag("FORMAT",7,false,$rqp->format));
                fwrite ($bf,full_tag("FLAGS",7,false,$rqp->flags));
                fwrite ($bf,full_tag("MAXSCORE",7,false,$rqp->maxscore));
                $status =fwrite ($bf,end_tag("RQP",6,true));
            }
        }
        return $status;
    }
    
    //This function backups the data in an essay question (qtype=12) and its
    //asociated data
    function quiz_backup_essay($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $essays = get_records('quiz_essay', 'question', $question, "id");
        //If there are essays
        if ($essays) {
            //Iterate over each essay
            foreach ($essays as $essay) {
                $status = fwrite ($bf,start_tag("ESSAY",6,true));
                //Print essay contents
                fwrite ($bf,full_tag("ANSWER",7,false,$essay->answer));                
                $status = fwrite ($bf,end_tag("ESSAY",6,true));
            }
            //Now print quiz_answers
            $status = quiz_backup_answers($bf,$preferences,$question);
        }
        return $status;
    }

    //This function backups the answers data in some question types
    //(truefalse, shortanswer,multichoice,numerical,calculated)
    function quiz_backup_answers($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $answers = get_records("quiz_answers","question",$question,"id");
        //If there are answers
        if ($answers) {
            $status =fwrite ($bf,start_tag("ANSWERS",6,true));
            //Iterate over each answer
            foreach ($answers as $answer) {
                $status =fwrite ($bf,start_tag("ANSWER",7,true));
                //Print answer contents
                fwrite ($bf,full_tag("ID",8,false,$answer->id));
                fwrite ($bf,full_tag("ANSWER_TEXT",8,false,$answer->answer));
                fwrite ($bf,full_tag("FRACTION",8,false,$answer->fraction));
                fwrite ($bf,full_tag("FEEDBACK",8,false,$answer->feedback));
                $status =fwrite ($bf,end_tag("ANSWER",7,true));
            }
            $status =fwrite ($bf,end_tag("ANSWERS",6,true));
        }
        return $status;
    }

    //This function backups quiz_numerical_units from different question types
    function quiz_backup_numerical_units($bf,$preferences,$question,$level=7) {

        global $CFG;

        $status = true;

        $numerical_units = get_records("quiz_numerical_units","question",$question,"id");
        //If there are numericals_units
        if ($numerical_units) {
            $status =fwrite ($bf,start_tag("NUMERICAL_UNITS",$level,true));
            //Iterate over each numerical_unit
            foreach ($numerical_units as $numerical_unit) {
                $status =fwrite ($bf,start_tag("NUMERICAL_UNIT",$level+1,true));
                //Print numerical_unit contents
                fwrite ($bf,full_tag("MULTIPLIER",$level+2,false,$numerical_unit->multiplier));
                fwrite ($bf,full_tag("UNIT",$level+2,false,$numerical_unit->unit));
                //Now backup numerical_units
                $status =fwrite ($bf,end_tag("NUMERICAL_UNIT",$level+1,true));
            }
            $status =fwrite ($bf,end_tag("NUMERICAL_UNITS",$level,true));
        }

        return $status;

    }

    //This function backups dataset_definitions (via question_datasets) from different question types
    function quiz_backup_datasets($bf,$preferences,$question,$level=7) {

        global $CFG;

        $status = true;

        //First, we get the used datasets for this question
        $question_datasets = get_records("quiz_question_datasets","question",$question,"id");
        //If there are question_datasets
        if ($question_datasets) {
            $status =fwrite ($bf,start_tag("DATASET_DEFINITIONS",$level,true));
            //Iterate over each question_dataset
            foreach ($question_datasets as $question_dataset) {
                $def = NULL;
                //Get dataset_definition
                if ($def = get_record("quiz_dataset_definitions","id",$question_dataset->datasetdefinition)) {;
                    $status =fwrite ($bf,start_tag("DATASET_DEFINITION",$level+1,true));
                    //Print question_dataset contents
                    fwrite ($bf,full_tag("CATEGORY",$level+2,false,$def->category));
                    fwrite ($bf,full_tag("NAME",$level+2,false,$def->name));
                    fwrite ($bf,full_tag("TYPE",$level+2,false,$def->type));
                    fwrite ($bf,full_tag("OPTIONS",$level+2,false,$def->options));
                    fwrite ($bf,full_tag("ITEMCOUNT",$level+2,false,$def->itemcount));
                    //Now backup dataset_entries
                    $status = quiz_backup_dataset_items($bf,$preferences,$def->id,$level+2);
                    //End dataset definition
                    $status =fwrite ($bf,end_tag("DATASET_DEFINITION",$level+1,true));
                }
            }
            $status =fwrite ($bf,end_tag("DATASET_DEFINITIONS",$level,true));
        }

        return $status;

    }

    //This function backups datases_items from dataset_definitions
    function quiz_backup_dataset_items($bf,$preferences,$datasetdefinition,$level=9) {

        global $CFG;

        $status = true;

        //First, we get the datasets_items for this dataset_definition
        $dataset_items = get_records("quiz_dataset_items","definition",$datasetdefinition,"id");
        //If there are dataset_items
        if ($dataset_items) {
            $status =fwrite ($bf,start_tag("DATASET_ITEMS",$level,true));
            //Iterate over each dataset_item
            foreach ($dataset_items as $dataset_item) {
                $status =fwrite ($bf,start_tag("DATASET_ITEM",$level+1,true));
                //Print question_dataset contents
                fwrite ($bf,full_tag("NUMBER",$level+2,false,$dataset_item->number));
                fwrite ($bf,full_tag("VALUE",$level+2,false,$dataset_item->value));
                //End dataset definition
                $status =fwrite ($bf,end_tag("DATASET_ITEM",$level+1,true));
            }
            $status =fwrite ($bf,end_tag("DATASET_ITEMS",$level,true));
        }

        return $status;

    }

//STEP 2. Backup quizzes and associated structures
    //    (course dependent)
    function quiz_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over quiz table
        $quizzes = get_records ("quiz","course",$preferences->backup_course,"id");
        if ($quizzes) {
            foreach ($quizzes as $quiz) {
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
                fwrite ($bf,full_tag("TIMELIMIT",4,false,$quiz->timelimit));
                fwrite ($bf,full_tag("PASSWORD",4,false,$quiz->password));
                fwrite ($bf,full_tag("SUBNET",4,false,$quiz->subnet));
                fwrite ($bf,full_tag("POPUP",4,false,$quiz->popup));
                //Now we print to xml question_instances (Course Level)
                $status = backup_quiz_question_instances($bf,$preferences,$quiz->id);
                //Now we print to xml question_versions (Course Level)
                $status = backup_quiz_question_versions($bf,$preferences,$quiz->id);
                //if we've selected to backup users info, then execute:
                //    - backup_quiz_grades
                //    - backup_quiz_attempts
                if ($preferences->mods["quiz"]->userinfo and $status) {
                    $status = backup_quiz_grades($bf,$preferences,$quiz->id);
                    if ($status) {
                        $status = backup_quiz_attempts($bf,$preferences,$quiz->id);
                    }
                }
                //End mod
                $status =fwrite ($bf,end_tag("MOD",3,true));
            }
        }
        return $status;
    }

    //Backup quiz_question_instances contents (executed from quiz_backup_mods)
    function backup_quiz_question_instances ($bf,$preferences,$quiz) {

        global $CFG;

        $status = true;

        $quiz_question_instances = get_records("quiz_question_instances","quiz",$quiz,"id");
        //If there are question_instances
        if ($quiz_question_instances) {
            //Write start tag
            $status =fwrite ($bf,start_tag("QUESTION_INSTANCES",4,true));
            //Iterate over each question_instance
            foreach ($quiz_question_instances as $que_ins) {
                //Start question instance
                $status =fwrite ($bf,start_tag("QUESTION_INSTANCE",5,true));
                //Print question_instance contents
                fwrite ($bf,full_tag("ID",6,false,$que_ins->id));
                fwrite ($bf,full_tag("QUESTION",6,false,$que_ins->question));
                fwrite ($bf,full_tag("GRADE",6,false,$que_ins->grade));
                //End question instance
                $status =fwrite ($bf,end_tag("QUESTION_INSTANCE",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("QUESTION_INSTANCES",4,true));
        }
        return $status;
    }

    //Backup quiz_question_versions contents (executed from quiz_backup_mods)
    function backup_quiz_question_versions ($bf,$preferences,$quiz) {

        global $CFG;

        $status = true;

        $quiz_question_versions = get_records("quiz_question_versions","quiz",$quiz,"id");
        //If there are question_versions
        if ($quiz_question_versions) {
            //Write start tag
            $status =fwrite ($bf,start_tag("QUESTION_VERSIONS",4,true));
            //Iterate over each question_version
            foreach ($quiz_question_versions as $que_ver) {
                //Start question version
                $status =fwrite ($bf,start_tag("QUESTION_VERSION",5,true));
                //Print question_version contents
                fwrite ($bf,full_tag("ID",6,false,$que_ver->id));
                fwrite ($bf,full_tag("OLDQUESTION",6,false,$que_ver->oldquestion));
                fwrite ($bf,full_tag("NEWQUESTION",6,false,$que_ver->newquestion));
                fwrite ($bf,full_tag("USERID",6,false,$que_ver->userid));
                fwrite ($bf,full_tag("TIMESTAMP",6,false,$que_ver->timestamp));
                //End question version
                $status =fwrite ($bf,end_tag("QUESTION_VERSION",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("QUESTION_VERSIONS",4,true));
        }
        return $status;
    }


    //Backup quiz_grades contents (executed from quiz_backup_mods)
    function backup_quiz_grades ($bf,$preferences,$quiz) {

        global $CFG;

        $status = true;

        $quiz_grades = get_records("quiz_grades","quiz",$quiz,"id");
        //If there are grades
        if ($quiz_grades) {
            //Write start tag
            $status =fwrite ($bf,start_tag("GRADES",4,true));
            //Iterate over each grade
            foreach ($quiz_grades as $gra) {
                //Start grade
                $status =fwrite ($bf,start_tag("GRADE",5,true));
                //Print grade contents
                fwrite ($bf,full_tag("ID",6,false,$gra->id));
                fwrite ($bf,full_tag("USERID",6,false,$gra->userid));
                fwrite ($bf,full_tag("GRADEVAL",6,false,$gra->grade));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$gra->timemodified));
                //End question grade
                $status =fwrite ($bf,end_tag("GRADE",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("GRADES",4,true));
        }
        return $status;
    }

    //Backup quiz_attempts contents (executed from quiz_backup_mods)
    function backup_quiz_attempts ($bf,$preferences,$quiz) {

        global $CFG;

        $status = true;

        $quiz_attempts = get_records("quiz_attempts","quiz",$quiz,"id");
        //If there are attempts
        if ($quiz_attempts) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ATTEMPTS",4,true));
            //Iterate over each attempt
            foreach ($quiz_attempts as $attempt) {
                //Start attempt
                $status =fwrite ($bf,start_tag("ATTEMPT",5,true));
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
                $status = backup_quiz_states ($bf,$preferences,$attempt->uniqueid);
                //End attempt
                $status =fwrite ($bf,end_tag("ATTEMPT",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ATTEMPTS",4,true));
        }
        return $status;
    }

    //Backup quiz_states contents (executed from backup_quiz_attempts)
    function backup_quiz_states ($bf,$preferences,$attempt) {

        global $CFG;

        $status = true;

        $quiz_states = get_records("quiz_states","attempt",$attempt,"id");
        //If there are states
        if ($quiz_states) {
            //Write start tag
            $status =fwrite ($bf,start_tag("STATES",6,true));
            //Iterate over each state
            foreach ($quiz_states as $state) {
                //Start state
                $status =fwrite ($bf,start_tag("STATE",7,true));
                //Print state contents
                fwrite ($bf,full_tag("ID",8,false,$state->id));
                fwrite ($bf,full_tag("QUESTION",8,false,$state->question));
                fwrite ($bf,full_tag("ORIGINALQUESTION",8,false,$state->originalquestion));
                fwrite ($bf,full_tag("SEQ_NUMBER",8,false,$state->seq_number));
                fwrite ($bf,full_tag("ANSWER",8,false,$state->answer));
                fwrite ($bf,full_tag("TIMESTAMP",8,false,$state->timestamp));
                fwrite ($bf,full_tag("EVENT",8,false,$state->event));
                fwrite ($bf,full_tag("GRADE",8,false,$state->grade));
                fwrite ($bf,full_tag("RAW_GRADE",8,false,$state->raw_grade));
                fwrite ($bf,full_tag("PENALTY",8,false,$state->penalty));
                // now back up question type specific state information
                $status = backup_quiz_rqp_state ($bf,$preferences,$state->id);
                $status = backup_quiz_essay_state ($bf,$preferences,$state->id);
                //End state
                $status =fwrite ($bf,end_tag("STATE",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("STATES",6,true));
        }
        $quiz_newest_states = get_records("quiz_newest_states","attemptid",$attempt,"id");
        //If there are newest_states
        if ($quiz_newest_states) {
            //Write start tag
            $status =fwrite ($bf,start_tag("NEWEST_STATES",6,true));
            //Iterate over each newest_state
            foreach ($quiz_newest_states as $newest_state) {
                //Start newest_state
                $status =fwrite ($bf,start_tag("NEWEST_STATE",7,true));
                //Print newest_state contents
                fwrite ($bf,full_tag("ID",8,false,$newest_state->id));
                fwrite ($bf,full_tag("QUESTIONID",8,false,$newest_state->questionid));
                fwrite ($bf,full_tag("NEWEST",8,false,$newest_state->newest));
                fwrite ($bf,full_tag("NEWGRADED",8,false,$newest_state->newgraded));
                fwrite ($bf,full_tag("SUMPENALTY",8,false,$newest_state->sumpenalty));
                //End newest_state
                $status =fwrite ($bf,end_tag("NEWEST_STATE",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("NEWEST_STATES",6,true));
        }
        return $status;
    }


    //Backup quiz_rqp_state contents (executed from backup_quiz_states)
    function backup_quiz_rqp_state ($bf,$preferences,$state) {

        global $CFG;

        $status = true;

        $rqp_state = get_record("quiz_rqp_states","stateid",$state);
        //If there is a state
        if ($rqp_state) {
            //Write start tag
            $status =fwrite ($bf,start_tag("RQP_STATE",8,true));
            //Print state contents
            fwrite ($bf,full_tag("RESPONSES",9,false,$rqp_state->responses));
            fwrite ($bf,full_tag("PERSISTENT_DATA",9,false,$rqp_state->persistent_data));
            fwrite ($bf,full_tag("TEMPLATE_VARS",9,false,$rqp_state->template_vars));
            //Write end tag
            $status =fwrite ($bf,end_tag("RQP_STATE",8,true));
        }
        return $status;
    }
    
    //Backup quiz_essay_state contents (executed from backup_quiz_states)
    function backup_quiz_essay_state ($bf,$preferences,$state) {

        global $CFG;

        $status = true;

        $essay_state = get_record("quiz_essay_states", "stateid", $state);
        //If there is a state
        if ($essay_state) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ESSAY_STATE",8,true));
            //Print state contents
            fwrite ($bf,full_tag("GRADED",9,false,$essay_state->graded));
            fwrite ($bf,full_tag("FRACTION",9,false,$essay_state->fraction));
            fwrite ($bf,full_tag("RESPONSE",9,false,$essay_state->response));
            //Write end tag
            $status =fwrite ($bf,end_tag("ESSAY_STATE",8,true));
        }
        return $status;
    }
    
   ////Return an array of info (name,value)
   function quiz_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //Deletes data from mdl_backup_ids (categories section)
        delete_category_ids ($backup_unique_code);
        //Create date into mdl_backup_ids (categories section)
        insert_category_ids ($course,$backup_unique_code);
        //First the course data
        $info[0][0] = get_string("modulenameplural","quiz");
        if ($ids = quiz_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }
        //Categories
        $info[1][0] = get_string("categories","quiz");
        if ($ids = quiz_category_ids_by_backup ($backup_unique_code)) {
            $info[1][1] = count($ids);
        } else {
            $info[1][1] = 0;
        }
        //Questions
        $info[2][0] = get_string("questions","quiz");
        if ($ids = quiz_question_ids_by_backup ($backup_unique_code)) {
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

        return $result;
    }

// INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of quiz id
    function quiz_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}quiz a
                                 WHERE a.course = '$course'");
    }

    //Returns an array of categories id
    function quiz_category_ids_by_backup ($backup_unique_code) {

        global $CFG;

        return get_records_sql ("SELECT a.old_id, a.backup_code
                                 FROM {$CFG->prefix}backup_ids a
                                 WHERE a.backup_code = '$backup_unique_code' AND
                                       a.table_name = 'quiz_categories'");
    }

    function quiz_question_ids_by_backup ($backup_unique_code) {

        global $CFG;

        return get_records_sql ("SELECT q.id, q.category
                                 FROM {$CFG->prefix}backup_ids a,
                                      {$CFG->prefix}quiz_questions q
                                 WHERE a.backup_code = '$backup_unique_code' AND
                                       q.category = a.old_id AND
                                       a.table_name = 'quiz_categories'");
    }

    function quiz_grade_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT g.id, g.quiz
                                 FROM {$CFG->prefix}quiz a,
                                      {$CFG->prefix}quiz_grades g
                                 WHERE a.course = '$course' and
                                       g.quiz = a.id");
    }

?>
