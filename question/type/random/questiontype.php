<?php
/**
 * Class for the random question type.
 *
 * The random question type does not have any options. When the question is
 * attempted, it picks a question at random from the category it is in (and
 * optionally its subcategories). For details see create_session_and_responses.
 * Then all other method calls as delegated to that other question.
 *
 * @package questionbank
 * @subpackage questiontypes
 */
class random_qtype extends default_questiontype {
    protected $excludedqtypes = null;
    protected $manualqtypes = null;

    // Caches questions available as randoms sorted by category
    // This is a 2-d array. The first key is question category, and the
    // second is whether to include subcategories.
    private $catrandoms = array();

    function name() {
        return 'random';
    }

    function menu_name() {
        // Don't include this question type in the 'add new question' menu.
        return false;
    }

    function show_analysis_of_responses() {
        return true;
    }

    function is_manual_graded() {
        return true;
    }

    function is_question_manual_graded($question, $otherquestionsinuse) {
        global $DB;
        // We take our best shot at working whether a particular question is manually
        // graded follows: We look to see if any of the questions that this random
        // question might select if of a manually graded type. If a category contains
        // a mixture of manual and non-manual questions, and if all the attempts so
        // far selected non-manual ones, this will give the wrong answer, but we
        // don't care. Even so, this is an expensive calculation!
        $this->init_qtype_lists();
        if (!$this->manualqtypes) {
            return false;
        }
        if ($question->questiontext) {
            $categorylist = question_categorylist($question->category);
        } else {
            $categorylist = $question->category;
        }
        return $DB->record_exists_select('question',
                "category IN ($categorylist)
                     AND parent = 0
                     AND hidden = 0
                     AND id NOT IN ($otherquestionsinuse)
                     AND qtype IN ($this->manualqtypes)");
    }

    function is_usable_by_random() {
        return false;
    }

    /**
     * This method needs to be called before the ->excludedqtypes and
     *      ->manualqtypes fields can be used.
     */
    function init_qtype_lists() {
        global $QTYPES;
        if (is_null($this->excludedqtypes)) {
            $excludedqtypes = array();
            $manualqtypes = array();
            foreach ($QTYPES as $qtype) {
                $quotedname = "'" . $qtype->name() . "'";
                if (!$qtype->is_usable_by_random()) {
                    $excludedqtypes[] = $quotedname;
                } else if ($qtype->is_manual_graded()) {
                    $manualqtypes[] = $quotedname;
                }
            }
            $this->excludedqtypes = implode(',', $excludedqtypes);
            $this->manualqtypes = implode(',', $manualqtypes);
        }
    }

    function display_question_editing_page(&$mform, $question, $wizardnow){
        global $OUTPUT;
        $heading = $this->get_heading(empty($question->id));
        echo $OUTPUT->heading_with_help($heading, $this->name(), $this->plugin_name());
        $mform->display();
    }

    function get_question_options(&$question) {
        // Don't do anything here, because the random question has no options.
        // Everything is handled by the create- or restore_session_and_responses
        // functions.
        return true;
    }

    /**
     * Random questions always get a question name that is Random (cateogryname).
     * This function is a centralised place to calculate that, given the category.
     * @param object $category the category this question picks from. (Only $category->name is used.)
     * @param boolean $includesubcategories whether this question also picks from subcategories.
     * @return string the name this question should have.
     */
    function question_name($category, $includesubcategories) {
        if ($includesubcategories) {
            $string = 'randomqplusname';
        } else {
            $string = 'randomqname';
        }
        return get_string($string, 'qtype_random', shorten_text($category->name, 100));
    }

    function save_question($question, $form) {
        $form->name = '';
        // Name is not a required field for random questions, but parent::save_question
        // Assumes that it is.
        return parent::save_question($question, $form);
    }

    function save_question_options($question) {
        global $DB;

        // No options, as such, but we set the parent field to the question's
        // own id. Setting the parent field has the effect of hiding this
        // question in various places.
        $updateobject = new stdClass;
        $updateobject->id = $question->id;
        $updateobject->parent = $question->id;

        // We also force the question name to be 'Random (categoryname)'.
        if (!$category = $DB->get_record('question_categories', array('id' => $question->category))) {
            print_error('cannotretrieveqcat', 'question');
        }
        $updateobject->name = $this->question_name($category, !empty($question->questiontext));
        return $DB->update_record('question', $updateobject);
    }

    /**
     * Get all the usable questions from a particular question category.
     *
     * @param integer $categoryid the id of a question category.
     * @param boolean whether to include questions from subcategories.
     * @param string $questionsinuse comma-separated list of question ids to exclude from consideration.
     * @return array of question records.
     */
    function get_usable_questions_from_category($categoryid, $subcategories, $questionsinuse) {
        global $DB;
        $this->init_qtype_lists();
        if ($subcategories) {
            $categorylist = question_categorylist($categoryid);
        } else {
            $categorylist = $categoryid;
        }
        if (!$catrandoms = $DB->get_records_select('question',
                "category IN ($categorylist)
                     AND parent = 0
                     AND hidden = 0
                     AND id NOT IN ($questionsinuse)
                     AND qtype NOT IN ($this->excludedqtypes)", null, '', 'id')) {
            $catrandoms = array();
        }
        return $catrandoms;
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        global $QTYPES, $DB;
        // Choose a random question from the category:
        // We need to make sure that no question is used more than once in the
        // quiz. Therfore the following need to be excluded:
        // 1. All questions that are explicitly assigned to the quiz
        // 2. All random questions
        // 3. All questions that are already chosen by an other random question
        // 4. Deleted questions
        if (!isset($cmoptions->questionsinuse)) {
            $cmoptions->questionsinuse = $attempt->layout;
        }

        if (!isset($this->catrandoms[$question->category][$question->questiontext])) {
            $catrandoms = $this->get_usable_questions_from_category($question->category,
                    $question->questiontext == "1", $cmoptions->questionsinuse);
            $this->catrandoms[$question->category][$question->questiontext] = swapshuffle_assoc($catrandoms);
        }

        while ($wrappedquestion = array_pop(
                $this->catrandoms[$question->category][$question->questiontext])) {
            if (!preg_match("~(^|,)$wrappedquestion->id(,|$)~", $cmoptions->questionsinuse)) {
                /// $randomquestion is not in use and will therefore be used
                /// as the randomquestion here...
                $wrappedquestion = $DB->get_record('question', array('id' => $wrappedquestion->id));
                global $QTYPES;
                $QTYPES[$wrappedquestion->qtype]
                        ->get_question_options($wrappedquestion);
                $QTYPES[$wrappedquestion->qtype]
                        ->create_session_and_responses($wrappedquestion,
                        $state, $cmoptions, $attempt);
                $wrappedquestion->name_prefix = $question->name_prefix;
                $wrappedquestion->maxgrade = $question->maxgrade;
                $cmoptions->questionsinuse .= ",$wrappedquestion->id";
                $state->options->question = &$wrappedquestion;
                return true;
            }
        }
        $question->questiontext = '<span class="notifyproblem">'.
                get_string('toomanyrandom', 'quiz'). '</span>';
        $question->qtype = 'description';
        $state->responses = array('' => '');
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        /// The raw response records for random questions come in two flavours:
        /// ---- 1 ----
        /// For responses stored by Moodle version 1.5 and later the answer
        /// field has the pattern random#-* where the # part is the numeric
        /// question id of the actual question shown in the quiz attempt
        /// and * represents the student response to that actual question.
        /// ---- 2 ----
        /// For responses stored by older Moodle versions - the answer field is
        /// simply the question id of the actual question. The student response
        /// to the actual question is stored in a separate response record.
        /// -----------------------
        /// This means that prior to Moodle version 1.5, random questions needed
        /// two response records for storing the response to a single question.
        /// From version 1.5 and later the question type random works like all
        /// the other question types in that it now only needs one response
        /// record per question.
        global $QTYPES, $DB, $OUTPUT;
        if (!preg_match('~^random([0-9]+)-~', $state->responses[''], $matches)) {
            if (empty($state->responses[''])) {
                // This is the case if there weren't enough questions available in the category.
                $question->questiontext = '<span class="notifyproblem">'.
                 get_string('toomanyrandom', 'quiz'). '</span>';
                $question->qtype = 'description';
                return true;
            }
            // this must be an old-style state which stores only the id for the wrapped question
            if (!$wrappedquestion = $DB->get_record('question', array('id' => $state->responses['']))) {
                echo $OUTPUT->notification("Can not find wrapped question {$state->responses['']}");
            }
            // In the old model the actual response was stored in a separate entry in
            // the state table and fortunately there was only a single state per question
            if (!$state->responses[''] = $DB->get_field('question_states', 'answer', array('attempt' => $state->attempt, 'question' => $wrappedquestion->id))) {
                echo $OUTPUT->notification("Wrapped state missing");
            }
        } else {
            $questionid = $matches[1];
            if (!$wrappedquestion = $DB->get_record('question', array('id' => $questionid))) {
                // The teacher must have deleted this question by mistake
                // Convert it into a description type question with an explanation to the student
                $wrappedquestion = clone($question);
                $wrappedquestion->id = $questionid;
                $wrappedquestion->questiontext = get_string('questiondeleted', 'quiz');
                $wrappedquestion->qtype = 'missingtype';
            }
            $state->responses[''] = substr($state->responses[''], strlen('random' . $questionid . '-'));
            if ($state->responses[''] === false) {
                // In PHP, if $response === $prefix, then
                // substr($response, strlen($prefix)) returns false, not '',
                // which is stupid, and caused MDL-26520. Fix up that case here.
                $state->responses[''] = '';
            }
        }

        if (!$QTYPES[$wrappedquestion->qtype]
         ->get_question_options($wrappedquestion)) {
            return false;
        }

        if (!$QTYPES[$wrappedquestion->qtype]
         ->restore_session_and_responses($wrappedquestion, $state)) {
            return false;
        }
        $wrappedquestion->name_prefix = $question->name_prefix;
        $wrappedquestion->maxgrade    = $question->maxgrade;
        $state->options->question = &$wrappedquestion;
        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        global $QTYPES, $DB;
        $wrappedquestion = &$state->options->question;

        // Trick the wrapped question into pretending to be the random one.
        $realqid = $wrappedquestion->id;
        $wrappedquestion->id = $question->id;
        $QTYPES[$wrappedquestion->qtype]
         ->save_session_and_responses($wrappedquestion, $state);

        // Read what the wrapped question has just set the answer field to
        // (if anything)
        $response = $DB->get_field('question_states', 'answer', array('id' => $state->id));
        if(false === $response) {
            return false;
        }

        // Prefix the answer field...
        $response = "random$realqid-$response";

        // ... and save it again.
        $DB->set_field('question_states', 'answer', $response, array('id' => $state->id));

        // Restore the real id
        $wrappedquestion->id = $realqid;
        return true;
    }

    function get_correct_responses(&$question, &$state) {
        global $QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QTYPES[$wrappedquestion->qtype]
         ->get_correct_responses($wrappedquestion, $state);
    }

    // ULPGC ecastro
    function get_all_responses(&$question, &$state){
        global $QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QTYPES[$wrappedquestion->qtype]
         ->get_all_responses($wrappedquestion, $state);
    }

    // ULPGC ecastro
    function get_actual_response(&$question, &$state){
        global $QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QTYPES[$wrappedquestion->qtype]
         ->get_actual_response($wrappedquestion, $state);
    }

    function get_html_head_contributions(&$question, &$state) {
        global $QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QTYPES[$wrappedquestion->qtype]
                ->get_html_head_contributions($wrappedquestion, $state);
    }

    function print_question(&$question, &$state, &$number, $cmoptions, $options) {
        global $QTYPES;
        $wrappedquestion = &$state->options->question;
        $wrappedquestion->randomquestionid = $question->id;
        $QTYPES[$wrappedquestion->qtype]
         ->print_question($wrappedquestion, $state, $number, $cmoptions, $options);
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        global $QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QTYPES[$wrappedquestion->qtype]
         ->grade_responses($wrappedquestion, $state, $cmoptions);
    }

    function get_texsource(&$question, &$state, $cmoptions, $type) {
        global $QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QTYPES[$wrappedquestion->qtype]
         ->get_texsource($wrappedquestion, $state, $cmoptions, $type);
    }

    function compare_responses(&$question, $state, $teststate) {
        global $QTYPES;
        $wrappedquestion = &$teststate->options->question;
        return $QTYPES[$wrappedquestion->qtype]
         ->compare_responses($wrappedquestion, $state, $teststate);
    }

    /**
     * For random question type return empty string which means won't calculate.
     * @param object $question
     * @return mixed either a integer score out of 1 that the average random
     * guess by a student might give or an empty string which means will not
     * calculate.
     */
    function get_random_guess_score($question) {
        return '';
    }

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new random_qtype());
