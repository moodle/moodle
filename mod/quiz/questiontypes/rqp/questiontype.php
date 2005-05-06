<?php  // $Id$

/**
* This file defines the RQP question type class
*
* @version $Id$
* @author Alex Smith and other members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

require_once($CFG->dirroot . '/mod/quiz/questiontypes/rqp/lib.php');
require_once('remote.php');

/**
* RQP question type class
*/
class quiz_rqp_qtype extends quiz_default_questiontype {

    /**
    * Name of the rqp question type
    *
    * @ return string 'rqp'
    */
    function name() {
        return 'rqp';
    }

    /**
    * Save the type-specific options
    *
    * This also saves additional information that it receives from
    * an RQP_itemInformation call to the RQP server
    */
    function save_question_options($form) {
        global $CFG;

        // Check source type
        if (!$type = get_record('quiz_rqp_type', 'id', $form->type)) {
            $result->notice = get_string('invalidsourcetype', 'quiz');
            return $result;
        }

        // Create the object to be stored in quiz_rqp table
        $options = new object;
        $options->question = $form->id;
        $options->type = $form->type;
        $options->type_name = $type->name;
        $options->source = $form->source;
        $options->format = $form->format;
        $options->rendering_server = $type->rendering_server;
        $options->cloning_server = $type->cloning_server;

        // Check source file
        $item = remote_item_info($options);
        if (false === $item || is_soap_fault($item)) {
            $result->notice = get_string('invalidsource', 'quiz', $options);
            return $result;
        }
        if ($item->sourceErrors) {
            array_walk($item->sourceErrors,
             create_function('&$val', '$val = $val->message;'));
            $result->notice = get_string('invalidsource', 'quiz', $options) .
             '<br />' . implode('<br />', array_values($item->sourceErrors));
            return $result;
        }
        // Time dependent items are not supported by the quiz module yet
        if ($item->timeDependent) {
            $result->noticeyesno = get_string('notimedependentitems', 'quiz');
            return $result;
        }

        // Set the format and item specific flags
        $options->format = $item->format;
        $options->maxscore = $item->maxScore;
        $options->flags = 0;
        $options->flags |= $item->template ? REMOTE_TEMPLATE : 0;
        $options->flags |= $item->adaptive ? REMOTE_ADAPTIVE : 0;

        // Check for cloning support if item is a template
        if ($item->template && !($type->flags & REMOTE_TEMPLATES_SUPPORTED)) {
            $result->notice = get_string('itemrequirescloning', 'quiz');
            return $result;
        }
        // Save the options
        if ($old = get_record('quiz_rqp', 'question', $form->id)) {
            $old->type   = $options->type;
            $old->source = $options->source;
            $old->format = $options->format;
            $old->flags  = $options->flags;
            $old->maxscore  = $options->maxscore;
            if (!update_record('quiz_rqp', $old)) {
                $result->error = "Could not update quiz rqp options! (id=$old->id)";
                return $result;
            }
        } else {
            if (!insert_record('quiz_rqp', $options)) {
                $result->error = 'Could not insert quiz rqp options!';
                return $result;
            }
        }
        return true;
    }

    /**
    * Loads the question type specific options for the question.
    *
    * This function loads all question type specific options for the
    * question from the database into the $question->options field.
    * @return bool            Indicates success or failure.
    * @param object $question The question object for the question.
    */
    function get_question_options(&$question) {

        $options =& $question->options;
        if (! ($options = get_record('quiz_rqp', 'question', $question->id))) {
            return false;
        }
        if (!$type = get_record('quiz_rqp_type', 'id', $options->type)) {
            return false;
        }
        $options->type_name = $type->name;
        $options->rendering_server = $type->rendering_server;
        $options->cloning_server = $type->cloning_server;
        $options->flags &= $type->flags;
        return true;
    }

    /**
    * Return a value or array of values which will give full marks if graded as
    * the $state->responses field
    *
    * The correct answers are obtained from the RQP server via the
    * RQP_SessionInformation operation
    * @return mixed           An array of values giving the responses corresponding
    *                         to the (or a) correct answer to the question.
    * @param object $question The question for which the correct answer is to
    *                         be retrieved.
    * @param object $state    The state object that corresponds to the question,
    *                         for which a correct answer is needed.
    */
    function get_correct_responses(&$question, &$state) {
        $info = remote_session_info($question, $state);
        if (false === $info || is_soap_fault($info)) {
            return null;
        }
        return $info->correctResponses;
    }

    /**
    * Creates empty session and response information for the question
    *
    * This function is called to start a question session. Empty question type
    * specific session data and empty response data is added to the state object.
    * @return bool            Indicates success or failure.
    * @param object $question The question for which the session is to be created.
    * @param object $state    The state to create the session for. This is passed by
    *                         reference and will be updated.
    * @param object $quiz     The quiz for which the session is to be started.
    *                         (not used)
    * @param object $attempt  The quiz attempt for which the session is to be
    *                         started. (not used)
    */
    function create_session_and_responses(&$question, &$state, $quiz, $attempt) {
        $state->responses = array('' => '');
        $state->options->persistent_data = '';
        $state->options->template_vars = array();
        return true;
    }

    /**
    * Restores the session data and most recent responses for the given state
    *
    * This function loads any session data associated with the question session
    * in the given state from the quiz_rqp_states table into the state object.
    * @return bool            Indicates success or failure.
    * @param object $question The question object for the question including any
    *                         question type specific information.
    * @param object $state    The saved state to load the session for. This
    *                         object is updated to include the question
    *                         type specific session information and responses
    *                         (it is passed by reference).
    */
    function restore_session_and_responses(&$question, &$state) {
        if (!$options = get_record('quiz_rqp_states', 'stateid', $state->id)) {
            return false;
        }
        $state->responses = quiz_rqp_explode($options->responses);
        $state->options->persistent_data = $options->persistent_data;
        $state->options->template_vars =
         quiz_rqp_explode($options->template_vars, true);
        return true;
    }

    /**
    * Saves the session data and responses for the question in a new state
    *
    * This function saves all session data from the state object into the
    * quiz_rqp_states table
    * @return bool            Indicates success or failure.
    * @param object $question The question object for the question including
    *                         the question type specific information.
    * @param object $state    The state for which the question type specific
    *                         data and responses should be saved.
    */
    function save_session_and_responses(&$question, &$state) {
        $options->stateid = $state->id;
        $options->responses = quiz_rqp_implode($state->responses);
        $options->persistent_data = $state->options->persistent_data;
        $options->template_vars =
         quiz_rqp_implode($state->options->template_vars);
        if (!insert_record('quiz_rqp_states', $options)) {
            return false;
        }
        return true;
    }

    /**
    * Prints the main content of the question including any interactions
    *
    * This function prints the main content of the question which it obtains
    * from the RQP server via the Render operation. It also updates
    * $state->options->persistent_data and $state->options->template_vars
    * with the values returned by the RQP server.
    * @param object $question The question to be rendered.
    * @param object $state    The state to render the question in. The grading
    *                         information is in ->grade, ->raw_grade and
    *                         ->penalty. The current responses are in
    *                         ->responses. The last graded state is in
    *                         ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses).
    * @param object $quiz     The quiz to which the question belongs.
    * @param object $options  An object describing the rendering options.
    *                         The fields are:
    *                         ->readonly          Review / interactive mode
    *                         ->feedback          Show feedback for the graded
    *                                             responses
    *                         ->validation        Show how the current responses
    *                                             responses were interpreted
    *                         ->correct_responses Show solutions
    *                         These are all boolean values.
    */
    function print_question_formulation_and_controls(&$question, &$state,
     $quiz, $options) {

        // Use the render output created during grading if it exists
        if (isset($state->options->renderoutput)) {
            $output =& $state->options->renderoutput;
        } else {
            // Otherwise perform a render operation
            $output = remote_render($question, $state, false,
             $options->readonly ? 'readonly' : 'normal');
            if (false === $output || is_soap_fault($output)) {
                unset($output);
                notify('Error: Rendering failed!');
                exit;
            }
        }
        $state->options->persistent_data = $output->persistentData;
        $state->options->template_vars = $output->templateVars;
        // Print the head (this may not work, really it should be in the head
        // section of the html document but moodle doesn't allow question types
        // to put things there)
        if (isset($output->output[RQP_URI_COMPONENT . 'head'])) {
            echo $output->output[RQP_URI_COMPONENT . 'head']->output;
        }
        // Print the title
        if (isset($output->output[RQP_URI_COMPONENT . 'title'])) {
            echo '<h2>' . $output->output[RQP_URI_COMPONENT . 'title']->output
             . "</h2>\n";
        }
        // Print the stem
        if (isset($output->output[RQP_URI_COMPONENT . 'stem'])) {
            echo '<div class="RQPstem">';
            echo $output->output[RQP_URI_COMPONENT . 'stem']->output;
            echo '</div>';
        }
        // Print the interactions
        if (isset($output->output[RQP_URI_COMPONENT . 'interactions'])) {
            echo '<div class="RQPinteractions" align="right">';
            echo $output->output[RQP_URI_COMPONENT . 'interactions']->output;
            echo '</div>';
        }
        // Print the last answer
        if (isset($output->output[RQP_URI_COMPONENT . 'lastAnswer'])) {
            echo '<div class="RQPlastAnswer">';
            echo $output->output[RQP_URI_COMPONENT . 'lastAnswer']->output;
            echo '</div>';
        }
        // Print the validation when required
        if ($options->validation) {
            if (isset($output->output[RQP_URI_COMPONENT . 'validation'])) {
                echo '<div class="RQPvalidation">';
                echo $output->output[RQP_URI_COMPONENT . 'validation']->output;
                echo '</div>';
            }
        }
        // Print the feedback when required
        if ($options->feedback) {
            if (isset($output->output[RQP_URI_COMPONENT . 'feedback'])) {
                echo '<div class="RQPfeedback">';
                echo $output->output[RQP_URI_COMPONENT . 'feedback']->output;
                echo '</div>';
            }
        }
        // Print the solution when required
        if ($options->correct_responses) {
            if (isset($output->output[RQP_URI_COMPONENT . 'solution'])) {
                echo $output->output[RQP_URI_COMPONENT . 'solution']->output;
                echo '</div>';
            }
        }
        // Note: hint(s) and modal feedback are ignored; moodle does not support
        // them yet.
        // Remove the render output created during grading (if any)
        unset($state->options->renderoutput);
    }

    /**
    * Prints the submit and validate buttons
    * @param object $question The question for which the buttons are to be printed
    * @param object $state    The state the question is in (not used)
    * @param object $quiz     The quiz to which the question belongs.
    * @param object $options  An object describing the rendering options.
    *                         (not used. This function should only have been called
    *                         if the options were such that the buttons are required)
    */
    function print_question_submit_buttons(&$question, &$state, $quiz, $options) {
        echo '<input type="submit" name="';
        echo $question->name_prefix;
        echo 'validate" value="';
        print_string('validate', 'quiz');
        echo '" />&nbsp;';
        if ($quiz->optionflags & QUIZ_ADAPTIVE) {
            echo '<input type="submit" name="';
            echo $question->name_prefix;
            echo 'mark" value="';
            print_string('mark', 'quiz');
            echo '" />';
        }
    }

    /**
    * Renders the question for printing and returns the LaTeX source produced
    *
    * This function calls the RQP_Render operation to render the question
    * suitable for a printed problem or solution sheet in LaTeX and returns
    * the rendered output. It does not advance the state of the question session.
    * @return string          The LaTeX output.
    * @param object $question The question to be rendered.
    * @param object $state    The state to render the question in.
    * @param object $quiz     The quiz to which the question belongs.
    * @param string $type     Indicates if the question or the solution is to be
    *                         rendered. Possible values are 'question' and
    *                         'solution'.
    */
    /*function get_texsource(&$question, &$state, $quiz, $type) {
        // Perform the RQP render operation requesting tex output
        $output = remote_render($question, $state, 'print', RQP_URI_STATE .
         ('solution' === $type ? 'solution' : 'normal'));
        if (false === $output || is_soap_fault($output)) {
            notify('Error: Rendering failed!');
            exit;
        }
        if (isset($output->bodyParts[RQP_URI_FORMAT . 'text/latex2e-parts/body'])) {
            $output = $output->bodyParts[RQP_URI_FORMAT . 'text/latex2e-parts/body']->content;
        }
        else {
            $output = '';
        }
        return $output;
    }*/

    /**
    * Performs response processing and grading
    *
    * This function calls RQP_Render to perform response processing and grading
    * and updates the state accordingly. It also caches the rendering output in case
    * it is needed later.
    * @return boolean         Indicates success or failure.
    * @param object $question The question to be graded.
    * @param object $state    The state to render the question in. The grading
    *                         information is in ->grade, ->raw_grade and
    *                         ->penalty. The currently responses are in
    *                         ->responses. The last graded state is in
    *                         ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is also
    *                         included. The ->raw_grade and ->penalty fields
    *                         are updated. The ->grade field is computed
    *                         automatically. The cumulative penalty is set
    *                         in ->penalty by adding to the penalty from the
    *                         most recently graded state. The method is able to
    *                         close the question session (preventing any further
    *                         attempts at this question) by setting
    *                         $state->event to QUIZ_EVENTCLOSE.
    * @param object $quiz     The quiz to which the question belongs.
    */
    function grade_responses(&$question, &$state, $quiz) {
        // Perform the grading and rendering
        $output = remote_render($question, $state, QUIZ_EVENTGRADE == $state->event
         || QUIZ_EVENTCLOSE == $state->event, 'normal');
        if (false === $output || is_soap_fault($output)) {
            unset($output);
            return false;
        }
        $state->options->persistent_data = $output->persistentData;
        $state->options->template_vars = $output->templateVars;
        // Save the rendering results for later
        $state->options->renderoutput = $output;
        if (isset($output->outcomeVars[RQP_URI_OUTCOME . 'rawScore'])) {
            $state->raw_grade = (float) $output->outcomeVars[RQP_URI_OUTCOME .
             'rawScore'][0];
            if (isset($output->outcomeVars[RQP_URI_OUTCOME . 'penalty'])) {
                $state->penalty = (float) $output->outcomeVars[RQP_URI_OUTCOME .
                 'penalty'][0];
            } else {
                $state->penalty = 0;
            }
        } else if (isset($output->outcomeVars[RQP_URI_OUTCOME . 'grade'])) {
            // This won't work quite as we would like but it is the best we can
            // do given that the server won't tell us the information we need
            $state->raw_grade = (float) $output->outcomeVars[RQP_URI_OUTCOME .
             'grade'][0];
            $state->penalty = 0;
        } else {
            $state->raw_grade = 0;
            $state->penalty = 0;
        }
        $state->raw_grade = ($state->raw_grade * ((float) $question->maxgrade))
         / ((float) $question->options->maxscore);
        return true;
    }

    /**
    * Includes configuration settings for the question type on the quiz admin
    * page
    *
    * Returns an array of objects describing the options for the question type
    * to be included on the quiz module admin page.
    * This is currently only a link to the server setup page types.php
    * @return array    Array of objects describing the configuration options to
    *                  be included on the quiz module admin page.
    */
    function get_config_options() {
        global $CFG;

        $link->name = 'managetypes';
        $link->link = 'types.php';
        return array($link);
    }

}
//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[RQP]= new quiz_rqp_qtype();

?>
