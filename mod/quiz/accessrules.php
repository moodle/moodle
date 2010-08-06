<?php
/**
 * This class keeps track of the various access rules that apply to a particular
 * quiz, with convinient methods for seeing whether access is allowed.
 */
class quiz_access_manager {
    private $_quizobj;
    private $_timenow;
    private $_passwordrule = null;
    private $_securewindowrule = null;
    private $_safebrowserrule = null;
    private $_rules = array();

    /**
     * Create an instance for a particular quiz.
     * @param object $quizobj An instance of the class quiz from attemptlib.php.
     *      The quiz we will be controlling access to.
     * @param integer $timenow The time to use as 'now'.
     * @param boolean $canignoretimelimits Whether this user is exempt from time
     *      limits (has_capability('mod/quiz:ignoretimelimits', ...)).
     */
    public function __construct($quizobj, $timenow, $canignoretimelimits) {
        $this->_quizobj = $quizobj;
        $this->_timenow = $timenow;
        $this->create_standard_rules($canignoretimelimits);
    }

    private function create_standard_rules($canignoretimelimits) {
        $quiz = $this->_quizobj->get_quiz();
        if ($quiz->attempts > 0) {
            $this->_rules[] = new num_attempts_access_rule($this->_quizobj, $this->_timenow);
        }
        $this->_rules[] = new open_close_date_access_rule($this->_quizobj, $this->_timenow);
        if (!empty($quiz->timelimit) && !$canignoretimelimits) {
            $this->_rules[] = new time_limit_access_rule($this->_quizobj, $this->_timenow);
        }
        if (!empty($quiz->delay1) || !empty($quiz->delay2)) {
            $this->_rules[] = new inter_attempt_delay_access_rule($this->_quizobj, $this->_timenow);
        }
        if (!empty($quiz->subnet)) {
            $this->_rules[] = new ipaddress_access_rule($this->_quizobj, $this->_timenow);
        }
        if (!empty($quiz->password)) {
            $this->_passwordrule = new password_access_rule($this->_quizobj, $this->_timenow);
            $this->_rules[] = $this->_passwordrule;
        }
        if (!empty($quiz->popup)) {
            if ($quiz->popup == 1) {
                $this->_securewindowrule = new securewindow_access_rule($this->_quizobj, $this->_timenow);
                $this->_rules[] = $this->_securewindowrule;
            } elseif ($quiz->popup == 2) {
                $this->_safebrowserrule = new safebrowser_access_rule($this->_quizobj, $this->_timenow);
                $this->_rules[] = $this->_safebrowserrule;
            }
        }
    }

    private function accumulate_messages(&$messages, $new) {
        if (is_array($new)) {
            $messages = array_merge($messages, $new);
        } else if (is_string($new) && $new) {
            $messages[] = $new;
        }
    }

    /**
     * Print each message in an array, surrounded by &lt;p>, &lt;/p> tags.
     *
     * @param array $messages the array of message strings.
     * @param boolean $return if true, return a string, instead of outputting.
     *
     * @return mixed, if $return is true, return the string that would have been output, otherwise
     * return null.
     */
    public function print_messages($messages, $return=false) {
        $output = '';
        foreach ($messages as $message) {
            $output .= '<p>' . $message . "</p>\n";
        }
        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Provide a description of the rules that apply to this quiz, such
     * as is shown at the top of the quiz view page. Note that not all
     * rules consider themselves important enough to output a description.
     *
     * @return array an array of description messages which may be empty. It
     *         would be sensible to output each one surrounded by &lt;p> tags.
     */
    public function describe_rules() {
        $result = array();
        foreach ($this->_rules as $rule) {
            $this->accumulate_messages($result, $rule->description());
        }
        return $result;
    }

    /**
     * Is it OK to let the current user start a new attempt now? If there are
     * any restrictions in force now, return an array of reasons why access
     * should be blocked. If access is OK, return false.
     *
     * @param integer $numattempts the number of previous attempts this user has made.
     * @param object|false $lastattempt information about the user's last completed attempt.
     *      if there is not a previous attempt, the false is passed.
     * @return mixed An array of reason why access is not allowed, or an empty array
     *         (== false) if access should be allowed.
     */
    public function prevent_new_attempt($numprevattempts, $lastattempt) {
        $reasons = array();
        foreach ($this->_rules as $rule) {
            $this->accumulate_messages($reasons,
                    $rule->prevent_new_attempt($numprevattempts, $lastattempt));
        }
        return $reasons;
    }

    /**
     * Is it OK to let the current user start a new attempt now? If there are
     * any restrictions in force now, return an array of reasons why access
     * should be blocked. If access is OK, return false.
     *
     * @return mixed An array of reason why access is not allowed, or an empty array
     *         (== false) if access should be allowed.
     */
    public function prevent_access() {
        $reasons = array();
        foreach ($this->_rules as $rule) {
            $this->accumulate_messages($reasons, $rule->prevent_access());
        }
        return $reasons;
    }

    /**
     * Do any of the rules mean that this student will no be allowed any further attempts at this
     * quiz. Used, for example, to change the label by the  grade displayed on the view page from
     * 'your current score is' to 'your final score is'.
     *
     * @param integer $numattempts the number of previous attempts this user has made.
     * @param object $lastattempt information about the user's last completed attempt.
     * @return boolean true if there is no way the user will ever be allowed to attempt this quiz again.
     */
    public function is_finished($numprevattempts, $lastattempt) {
        foreach ($this->_rules as $rule) {
            if ($rule->is_finished($numprevattempts, $lastattempt)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Do the printheader call, etc. required for a secure page, including the necessary JS.
     *
     * @param string $title HTML title tag content, passed to printheader.
     * @param string $headtags extra stuff to go in the HTML head tag, passed to printheader.
     */
    public function setup_secure_page($title, $headtags) {
        $this->_securewindowrule->setup_secure_page($title, $headtags);
    }

    public function show_attempt_timer_if_needed($attempt, $timenow) {
        global $PAGE;
        $timeleft = false;
        foreach ($this->_rules as $rule) {
            $ruletimeleft = $rule->time_left($attempt, $timenow);
            if ($ruletimeleft !== false && ($timeleft === false || $ruletimeleft < $timeleft)) {
                $timeleft = $ruletimeleft;
            }
        }
        if ($timeleft !== false) {
        /// Make sure the timer starts just above zero. If $timeleft was <= 0, then
        /// this will just have the effect of causing the quiz to be submitted immediately.
            $timerstartvalue = max($timeleft, 1);
            $PAGE->requires->js_init_call('M.mod_quiz.timer.init',
                    array($timerstartvalue), false, quiz_get_js_module());
        }
    }

    /**
     * @return bolean if this quiz should only be shown to students in a secure window.
     */
    public function securewindow_required($canpreview) {
        return !$canpreview && !is_null($this->_securewindowrule);
    }

    /**
     * @return bolean if this quiz should only be shown to students with safe browser.
    */
    public function safebrowser_required($canpreview) {
        return !$canpreview && !is_null($this->_safebrowserrule);
    }

    /**
     * Print a button to start a quiz attempt, with an appropriate javascript warning,
     * depending on the access restrictions. The link will pop up a 'secure' window, if
     * necessary.
     *
     * @param boolean $canpreview whether this user can preview. This affects whether they must
     * use a secure window.
     * @param string $buttontext the label to put on the button.
     * @param boolean $unfinished whether the button is to continue an existing attempt,
     * or start a new one. This affects whether a javascript alert is shown.
     */
    public function print_start_attempt_button($canpreview, $buttontext, $unfinished) { 
        global $OUTPUT;

        $button = new single_button($this->_quizobj->start_attempt_url(), $buttontext);
        $button->class .= ' quizstartbuttondiv';

        if (!$unfinished) {
            $strconfirmstartattempt = $this->confirm_start_attempt_message();
            if ($strconfirmstartattempt) {
                $button->add_confirm_action($strconfirmstartattempt);
            }
        }

        $warning = '';

        if ($this->securewindow_required($canpreview)) {
            $button->class .= ' quizsecuremoderequired';

            $popupoptions = array(
                'left' => 0,
                'top' => 0,
                'fullscreen' => true,
                'scrollbars' => true,
                'resizeable' => false,
                'directories' => false,
                'toolbar' => false,
                'titlebar' => false,
                'location' => false,
                'status' => false,
                'menubar' => false,
            );
            $button->popup_action(new popup_action('click', $url, 'quizpopup', $popupoptions));

            $warning = html_writer::tag('noscript',
                    $OUTPUT->heading(get_string('noscript', 'quiz')));
        }

        echo $OUTPUT->render($button) . $warning;
    }

    /**
     * Send the user back to the quiz view page. Normally this is just a redirect, but
     * If we were in a secure window, we close this window, and reload the view window we came from.
     *
     * @param boolean $canpreview This affects whether we have to worry about secure window stuff.
     */
    public function back_to_view_page($canpreview, $message = '') {
        global $CFG, $OUTPUT;
        $url = $this->_quizobj->view_url();
        if ($this->securewindow_required($canpreview)) {
            echo $OUTPUT->header();
            echo $OUTPUT->box_start();
            if ($message) {
                echo '<p>' . $message . '</p><p>' . get_string('windowclosing', 'quiz') . '</p>';
                $delay = 5;
            } else {
                echo '<p>' . get_string('pleaseclose', 'quiz') . '</p>';
                $delay = 0;
            }
            echo $OUTPUT->box_end();
            $PAGE->requires->js_function_call('M.mod_quiz.secure_window.close', array($url, $delay));
            echo $OUTPUT->footer();
            die();
        } else {
            redirect($url, $message);
        }
    }

    /**
     * Print a control to finish the review. Normally this is just a link, but if we are
     * in a secure window, it needs to be a button that does M.mod_quiz.secure_window.close.
     *
     * @param boolean $canpreview This affects whether we have to worry about secure window stuff.
     */
    public function print_finish_review_link($canpreview, $return = false) {
        global $CFG;
        $output = '';
        $url = $this->_quizobj->view_url();
        $output .= '<div class="finishreview">';
        if ($this->securewindow_required($canpreview)) {
            $url = addslashes_js(htmlspecialchars($url));
            $output .= '<input type="button" value="' . get_string('finishreview', 'quiz') . '" ' .
                    "onclick=\"M.mod_quiz.secure_window.close('$url', 0)\" />\n";
        } else {
            $output .= '<a href="' . $url . '">' . get_string('finishreview', 'quiz') . "</a>\n";
        }
        $output .= "</div>\n";
        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * @return bolean if this quiz is password protected.
     */
    public function password_required() {
        return !is_null($this->_passwordrule);
    }

    /**
     * Clear the flag in the session that says that the current user is allowed to do this quiz.
     */
    public function clear_password_access() {
        if (!is_null($this->_passwordrule)) {
            $this->_passwordrule->clear_access_allowed();
        }
    }

    /**
     * Actually ask the user for the password, if they have not already given it this session.
     * This function only returns is access is OK.
     *
     * @param boolean $canpreview used to enfore securewindow stuff.
     */
    public function do_password_check($canpreview) {
        if (!is_null($this->_passwordrule)) {
            $this->_passwordrule->do_password_check($canpreview, $this);
        }
    }

    /**
     * @return string if the quiz policies merit it, return a warning string to be displayed
     * in a javascript alert on the start attempt button.
     */
    public function confirm_start_attempt_message() {
        $quiz = $this->_quizobj->get_quiz();
        if ($quiz->timelimit && $quiz->attempts) {
            return get_string('confirmstartattempttimelimit','quiz', $quiz->attempts);
        } else if ($quiz->timelimit) {
            return get_string('confirmstarttimelimit','quiz');
        } else if ($quiz->attempts) {
            return get_string('confirmstartattemptlimit','quiz', $quiz->attempts);
        }
        return '';
    }

    /**
     * Make some text into a link to review the quiz, if that is appropriate.
     *
     * @param string $linktext some text.
     * @param object $attempt the attempt object
     * @return string some HTML, the $linktext either unmodified or wrapped in a link to the review page.
     */
    public function make_review_link($attempt, $canpreview, $reviewoptions) {
        global $CFG;

    /// If review of responses is not allowed, or the attempt is still open, don't link.
        if (!$attempt->timefinish) {
            return '';
        }
        if (!$reviewoptions->responses) {
            $message = $this->cannot_review_message($reviewoptions, true);
            if ($message) {
                return '<span class="noreviewmessage">' . $message . '</span>';
            } else {
                return '';
            }
        }

        $linktext = get_string('review', 'quiz');

    /// It is OK to link, does it need to be in a secure window?
        if ($this->securewindow_required($canpreview)) {
            return $this->_securewindowrule->make_review_link($linktext, $attempt->id);
        } else {
            return '<a href="' . $this->_quizobj->review_url($attempt->id) . '" title="' .
                    get_string('reviewthisattempt', 'quiz') . '">' . $linktext . '</a>';
        }
    }

    /**
     * If $reviewoptions->responses is false, meaning that students can't review this
     * attempt at the moment, return an appropriate string explaining why.
     *
     * @param object $reviewoptions as obtained from quiz_get_reviewoptions.
     * @param boolean $short if true, return a shorter string.
     * @return string an appropraite message.
     */
    public function cannot_review_message($reviewoptions, $short = false) {
        $quiz = $this->_quizobj->get_quiz();
        if ($short) {
            $langstrsuffix = 'short';
            $dateformat = get_string('strftimedatetimeshort', 'langconfig');
        } else {
            $langstrsuffix = '';
            $dateformat = '';
        }
        if ($reviewoptions->quizstate == QUIZ_STATE_IMMEDIATELY) {
            return '';
        } else if ($reviewoptions->quizstate == QUIZ_STATE_OPEN && $quiz->timeclose &&
                    ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_RESPONSES)) {
            return get_string('noreviewuntil' . $langstrsuffix, 'quiz', userdate($quiz->timeclose, $dateformat));
        } else {
            return get_string('noreview' . $langstrsuffix, 'quiz');
        }
    }
}

/**
 * A base class that defines the interface for the various quiz access rules.
 * Most of the methods are defined in a slightly unnatural way because we either
 * want to say that access is allowed, or explain the reason why it is block.
 * Therefore instead of is_access_allowed(...) we have prevent_access(...) that
 * return false if access is permitted, or a string explanation (which is treated
 * as true) if access should be blocked. Slighly unnatural, but acutally the easist
 * way to implement this.
 */
abstract class quiz_access_rule_base {
    protected $_quiz;
    protected $_quizobj;
    protected $_timenow;
    /**
     * Create an instance of this rule for a particular quiz.
     * @param object $quiz the quiz we will be controlling access to.
     */
    public function __construct($quizobj, $timenow) {
        $this->_quizobj = $quizobj;
        $this->_quiz = $quizobj->get_quiz();
        $this->_timenow = $timenow;
    }
    /**
     * Whether or not a user should be allowed to start a new attempt at this quiz now.
     * @param integer $numattempts the number of previous attempts this user has made.
     * @param object $lastattempt information about the user's last completed attempt.
     * @return string false if access should be allowed, a message explaining the reason if access should be prevented.
     */
    public function prevent_new_attempt($numprevattempts, $lastattempt) {
        return false;
    }
    /**
     * Whether or not a user should be allowed to start a new attempt at this quiz now.
     * @return string false if access should be allowed, a message explaining the reason if access should be prevented.
     */
    public function prevent_access() {
        return false;
    }
    /**
     * Information, such as might be shown on the quiz view page, relating to this restriction.
     * There is no obligation to return anything. If it is not appropriate to tell students
     * about this rule, then just return ''.
     * @return mixed a message, or array of messages, explaining the restriction
     *         (may be '' if no message is appropriate).
     */
    public function description() {
        return '';
    }
    /**
     * If this rule can determine that this user will never be allowed another attempt at
     * this quiz, then return true. This is used so we can know whether to display a
     * final score on the view page. This will only be called if there is not a currently
     * active attempt for this user.
     * @param integer $numattempts the number of previous attempts this user has made.
     * @param object $lastattempt information about the user's last completed attempt.
     * @return boolean true if this rule means that this user will never be allowed another
     * attempt at this quiz.
     */
    public function is_finished($numprevattempts, $lastattempt) {
        return false;
    }

    /**
     * If, becuase of this rule, the user has to finish their attempt by a certain time,
     * you should override this method to return the amount of time left in seconds.
     * @param object $attempt the current attempt
     * @param integer $timenow the time now. We don't use $this->_timenow, so we can
     * give the user a more accurate indication of how much time is left.
     * @return mixed false if there is no deadline, of the time left in seconds if there is one.
     */
    public function time_left($attempt, $timenow) {
        return false;
    }
}

/**
 * A rule controlling the number of attempts allowed.
 */
class num_attempts_access_rule extends quiz_access_rule_base {
    public function description() {
        return get_string('attemptsallowedn', 'quiz', $this->_quiz->attempts);
    }
    public function prevent_new_attempt($numprevattempts, $lastattempt) {
        if ($numprevattempts >= $this->_quiz->attempts) {
            return get_string('nomoreattempts', 'quiz');
        }
        return false;
    }
    public function is_finished($numprevattempts, $lastattempt) {
        return $numprevattempts >= $this->_quiz->attempts;
    }
}

/**
 * A rule enforcing open and close dates.
 */
class open_close_date_access_rule extends quiz_access_rule_base {
    public function description() {
        $result = array();
        if ($this->_timenow < $this->_quiz->timeopen) {
            $result[] = get_string('quiznotavailable', 'quiz', userdate($this->_quiz->timeopen));
        } else if ($this->_quiz->timeclose && $this->_timenow > $this->_quiz->timeclose) {
            $result[] = get_string("quizclosed", "quiz", userdate($this->_quiz->timeclose));
        } else {
            if ($this->_quiz->timeopen) {
                $result[] = get_string('quizopenedon', 'quiz', userdate($this->_quiz->timeopen));
            }
            if ($this->_quiz->timeclose) {
                $result[] = get_string('quizcloseson', 'quiz', userdate($this->_quiz->timeclose));
            }
        }
        return $result;
    }
    public function prevent_access() {
        if ($this->_timenow < $this->_quiz->timeopen ||
                    ($this->_quiz->timeclose && $this->_timenow > $this->_quiz->timeclose)) {
            return get_string('notavailable', 'quiz');
        }
        return false;
    }
    public function is_finished($numprevattempts, $lastattempt) {
        return $this->_quiz->timeclose && $this->_timenow > $this->_quiz->timeclose;
    }
    public function time_left($attempt, $timenow) {
        // If this is a teacher preview after the close date, do not show
        // the time.
        if ($attempt->preview && $timenow > $this->_quiz->timeclose) {
            return false;
        }

        // Otherwise, return to the time left until the close date, providing
        // that is less than QUIZ_SHOW_TIME_BEFORE_DEADLINE
        if ($this->_quiz->timeclose) {
            $timeleft = $this->_quiz->timeclose - $timenow;
            if ($timeleft < QUIZ_SHOW_TIME_BEFORE_DEADLINE) {
                return $timeleft;
            }
        }
        return false;
    }
}

/**
 * A rule imposing the delay between attemtps settings.
 */
class inter_attempt_delay_access_rule extends quiz_access_rule_base {
    public function prevent_new_attempt($numprevattempts, $lastattempt) {
        if ($this->_quiz->attempts > 0 && $numprevattempts >= $this->_quiz->attempts) {
        /// No more attempts allowed anyway.
            return false;
        }
        if ($this->_quiz->timeclose != 0 && $this->_timenow > $this->_quiz->timeclose) {
        /// No more attempts allowed anyway.
            return false;
        }
        $nextstarttime = $this->compute_next_start_time($numprevattempts, $lastattempt);
        if ($this->_timenow < $nextstarttime) {
            if ($this->_quiz->timeclose == 0 || $nextstarttime <= $this->_quiz->timeclose) {
                return get_string('youmustwait', 'quiz', userdate($nextstarttime));
            } else {
                return get_string('youcannotwait', 'quiz');
            }
        }
        return false;
    }

    /**
     * Compute the next time a student would be allowed to start an attempt,
     * according to this rule.
     * @param integer $numprevattempts number of previous attempts.
     * @param object $lastattempt information about the previous attempt.
     * @return number the time.
     */
    protected function compute_next_start_time($numprevattempts, $lastattempt) {
        if ($numprevattempts == 0) {
            return 0;
        }

        $lastattemptfinish = $lastattempt->timefinish;
        if ($this->_quiz->timelimit > 0){
            $lastattemptfinish = min($lastattemptfinish,
                    $lastattempt->timestart + $this->_quiz->timelimit);
        }

        if ($numprevattempts == 1 && $this->_quiz->delay1) {
            return $lastattemptfinish + $this->_quiz->delay1;
        } else if ($numprevattempts > 1 && $this->_quiz->delay2) {
            return $lastattemptfinish + $this->_quiz->delay2;
        }
        return 0;
    }

    public function is_finished($numprevattempts, $lastattempt) {
        $nextstarttime = $this->compute_next_start_time($numprevattempts, $lastattempt);
        return $this->_timenow <= $nextstarttime &&
                $this->_quiz->timeclose != 0 && $nextstarttime >= $this->_quiz->timeclose;
    }
}

/**
 * A rule implementing the ipaddress check against the ->submet setting.
 */
class ipaddress_access_rule extends quiz_access_rule_base {
    public function prevent_access() {
        if (address_in_subnet(getremoteaddr(), $this->_quiz->subnet)) {
            return false;
        } else {
            return get_string('subnetwrong', 'quiz');
        }
    }
}

/**
 * A rule representing the password check. It does not actually implement the check,
 * that has to be done directly in attempt.php, but this facilitates telling users about it.
 */
class password_access_rule extends quiz_access_rule_base {
    public function description() {
        return get_string('requirepasswordmessage', 'quiz');
    }
    /**
     * Clear the flag in the session that says that the current user is allowed to do this quiz.
     */
    public function clear_access_allowed() {
        global $SESSION;
        if (!empty($SESSION->passwordcheckedquizzes[$this->_quiz->id])) {
            unset($SESSION->passwordcheckedquizzes[$this->_quiz->id]);
        }
    }
    /**
     * Actually ask the user for the password, if they have not already given it this session.
     * This function only returns is access is OK.
     *
     * @param boolean $canpreview used to enfore securewindow stuff.
     * @param object $accessmanager the accessmanager calling us.
     */
    public function do_password_check($canpreview, $accessmanager) {
        global $CFG, $SESSION, $OUTPUT, $PAGE;

    /// We have already checked the password for this quiz this session, so don't ask again.
        if (!empty($SESSION->passwordcheckedquizzes[$this->_quiz->id])) {
            return;
        }

    /// If the user cancelled the password form, send them back to the view page.
        if (optional_param('cancelpassword', false, PARAM_BOOL)) {
            $accessmanager->back_to_view_page($canpreview);
        }

    /// If they entered the right password, let them in.
        $enteredpassword = optional_param('quizpassword', '', PARAM_RAW);
        $validpassword = false;
        if (strcmp($this->_quiz->password, $enteredpassword) === 0) {
            $validpassword = true;
        } else if (isset($this->_quiz->extrapasswords)) {
            // group overrides may have additional passwords
            foreach ($this->_quiz->extrapasswords as $password) {
                if (strcmp($password, $enteredpassword) === 0) {
                    $validpassword = true;
                    break;
                }
            }
        }
        if ($validpassword) {
            $SESSION->passwordcheckedquizzes[$this->_quiz->id] = true;
            return;
        }

    /// User entered the wrong password, or has not entered one yet, so display the form.
        $output = '';

    /// Start the page and print the quiz intro, if any.
        echo $OUTPUT->header();
        if (trim(strip_tags($this->_quiz->intro))) {
            $cm = get_coursemodule_from_id('quiz', $this->_quiz->id);
            $output .= $OUTPUT->box(format_module_intro('quiz', $this->_quiz, $cm->id), 'generalbox', 'intro');
        }
        $output .= $OUTPUT->box_start('generalbox', 'passwordbox');

    /// If they have previously tried and failed to enter a password, tell them it was wrong.
        if (!empty($enteredpassword)) {
            $output .= '<p class="notifyproblem">' . get_string('passworderror', 'quiz') . '</p>';
        }

    /// Print the password entry form.
        $output .= '<p>' . get_string('requirepasswordmessage', 'quiz') . "</p>\n";
        $output .= '<form id="passwordform" method="post" action="' . $CFG->wwwroot .
                '/mod/quiz/startattempt.php" onclick="this.autocomplete=\'off\'">' . "\n";
        $output .= "<div>\n";
        $output .= '<label for="quizpassword">' . get_string('password') . "</label>\n";
        $output .= '<input name="quizpassword" id="quizpassword" type="password" value=""/>' . "\n";
        $output .= '<input name="cmid" type="hidden" value="' .
                $this->_quizobj->get_cmid() . '"/>' . "\n";
        $output .= '<input name="sesskey" type="hidden" value="' . sesskey() . '"/>' . "\n";
        $output .= '<input type="submit" value="' . get_string('ok') . '" />';
        $output .= '<input type="submit" name="cancelpassword" value="' .
                get_string('cancel') . '" />' . "\n";
        $output .= "</div>\n";
        $output .= "</form>\n";

    /// Finish page.
        $output .= $OUTPUT->box_end();

    /// return or display form.
        echo $output;
        echo $OUTPUT->footer();
        exit;
    }
}

/**
 * A rule representing the time limit. It does not actually restrict access, but we use this
 * class to encapsulate some of the relevant code.
 */
class time_limit_access_rule extends quiz_access_rule_base {
    public function description() {
        return get_string('quiztimelimit', 'quiz', format_time($this->_quiz->timelimit));
    }
    public function time_left($attempt, $timenow) {
        return $attempt->timestart + $this->_quiz->timelimit - $timenow;
    }
}

/**
 * A rule implementing the ipaddress check against the ->submet setting.
 */
class securewindow_access_rule extends quiz_access_rule_base {
    /**
     * Make a link to the review page for an attempt.
     *
     * @param string $linktext the desired link text.
     * @param integer $attemptid the attempt id.
     * @return string HTML for the link.
     */
    public function make_review_link($linktext, $attemptid) {
        global $OUTPUT;
        $button = new single_button($this->_quizobj->review_url($attemptid), $linktext);
        $button->add_action(new popup_action('click', $form->url, 'quizpopup', $this->windowoptions));
        return $OUTPUT->render($button);
    }

    /**
     * Do the printheader call, etc. required for a secure page, including the necessary JS.
     *
     * @param string $title HTML title tag content, passed to printheader.
     * @param string $headtags extra stuff to go in the HTML head tag, passed to printheader.
     *                $headtags has been deprectaed since Moodle 2.0
     */
    public function setup_secure_page($title, $headtags=null) {
        global $OUTPUT, $PAGE;
    /// This prevents the message window coming up.
        define('MESSAGE_WINDOW', true);
        $PAGE->set_title($title);
        $PAGE->set_cacheable(false);
        $PAGE->add_body_class('quiz-secure-window');
        $PAGE->requires->js_init_call('M.mod_quiz.secure_window.init', null, false,
                quiz_get_js_module());
        echo $OUTPUT->header();
    }
}

/**
 * A rule representing the safe browser check.
*/
class safebrowser_access_rule extends quiz_access_rule_base {
    public function prevent_access() {
        if (!$this->_quizobj->is_preview_user() && !quiz_check_safe_browser()) {
            return get_string('safebrowsererror', 'quiz');
        } else {
            return false;
        }
    }

    public function description() {
        return get_string("safebrowsernotice", "quiz");
    }
}

