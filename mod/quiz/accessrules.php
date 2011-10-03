<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Classes to enforce the various access rules that can apply to a quiz.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * This class keeps track of the various access rules that apply to a particular
 * quiz, with convinient methods for seeing whether access is allowed.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_access_manager {
    protected $quizobj;
    protected $timenow;
    protected $passwordrule = null;
    protected $securewindowrule = null;
    protected $safebrowserrule = null;
    protected $rules = array();

    /**
     * Create an instance for a particular quiz.
     * @param object $quizobj An instance of the class quiz from attemptlib.php.
     *      The quiz we will be controlling access to.
     * @param int $timenow The time to use as 'now'.
     * @param bool $canignoretimelimits Whether this user is exempt from time
     *      limits (has_capability('mod/quiz:ignoretimelimits', ...)).
     */
    public function __construct($quizobj, $timenow, $canignoretimelimits) {
        $this->quizobj = $quizobj;
        $this->timenow = $timenow;
        $this->create_standard_rules($canignoretimelimits);
    }

    protected function create_standard_rules($canignoretimelimits) {
        $rules = get_plugin_list_with_class('quizaccess', '', 'rule.php');

        $quiz = $this->quizobj->get_quiz();
        if ($quiz->attempts > 0) {
            $this->rules[] = new quizaccess_numattempts($this->quizobj, $this->timenow);
        }
        $this->rules[] = new quizaccess_openclosedate($this->quizobj, $this->timenow);
        if (!empty($quiz->timelimit) && !$canignoretimelimits) {
            $this->rules[] = new quizaccess_timelimit($this->quizobj, $this->timenow);
        }
        if (!empty($quiz->delay1) || !empty($quiz->delay2)) {
            $this->rules[] = new quizaccess_delaybetweenattempts($this->quizobj, $this->timenow);
        }
        if (!empty($quiz->subnet)) {
            $this->rules[] = new quizaccess_ipaddress($this->quizobj, $this->timenow);
        }
        if (!empty($quiz->password)) {
            $this->passwordrule = new quizaccess_password($this->quizobj, $this->timenow);
            $this->rules[] = $this->passwordrule;
        }
        if (!empty($quiz->popup)) {
            if ($quiz->popup == 1) {
                $this->securewindowrule = new quizaccess_securewindow(
                        $this->quizobj, $this->timenow);
                $this->rules[] = $this->securewindowrule;
            } else if ($quiz->popup == 2) {
                $this->safebrowserrule = new quizaccess_securebrowser(
                        $this->quizobj, $this->timenow);
                $this->rules[] = $this->safebrowserrule;
            }
        }
    }

    protected function accumulate_messages(&$messages, $new) {
        if (is_array($new)) {
            $messages = array_merge($messages, $new);
        } else if (is_string($new) && $new) {
            $messages[] = $new;
        }
        return $messages;
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
        foreach ($this->rules as $rule) {
            $result = $this->accumulate_messages($result, $rule->description());
        }
        return $result;
    }

    /**
     * Is it OK to let the current user start a new attempt now? If there are
     * any restrictions in force now, return an array of reasons why access
     * should be blocked. If access is OK, return false.
     *
     * @param int $numattempts the number of previous attempts this user has made.
     * @param object|false $lastattempt information about the user's last completed attempt.
     *      if there is not a previous attempt, the false is passed.
     * @return mixed An array of reason why access is not allowed, or an empty array
     *         (== false) if access should be allowed.
     */
    public function prevent_new_attempt($numprevattempts, $lastattempt) {
        $reasons = array();
        foreach ($this->rules as $rule) {
            $reasons = $this->accumulate_messages($reasons,
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
        foreach ($this->rules as $rule) {
            $reasons = $this->accumulate_messages($reasons, $rule->prevent_access());
        }
        return $reasons;
    }

    /**
     * Do any of the rules mean that this student will no be allowed any further attempts at this
     * quiz. Used, for example, to change the label by the grade displayed on the view page from
     * 'your current grade is' to 'your final grade is'.
     *
     * @param int $numattempts the number of previous attempts this user has made.
     * @param object $lastattempt information about the user's last completed attempt.
     * @return bool true if there is no way the user will ever be allowed to attempt
     *      this quiz again.
     */
    public function is_finished($numprevattempts, $lastattempt) {
        foreach ($this->rules as $rule) {
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
    public function setup_secure_page($title, $headtags = null) {
        $this->securewindowrule->setup_secure_page($title, $headtags);
    }

    public function show_attempt_timer_if_needed($attempt, $timenow) {
        global $PAGE;
        $timeleft = false;
        foreach ($this->rules as $rule) {
            $ruletimeleft = $rule->time_left($attempt, $timenow);
            if ($ruletimeleft !== false && ($timeleft === false || $ruletimeleft < $timeleft)) {
                $timeleft = $ruletimeleft;
            }
        }
        if ($timeleft !== false) {
            // Make sure the timer starts just above zero. If $timeleft was <= 0, then
            // this will just have the effect of causing the quiz to be submitted immediately.
            $timerstartvalue = max($timeleft, 1);
            $PAGE->requires->js_init_call('M.mod_quiz.timer.init',
                    array($timerstartvalue), false, quiz_get_js_module());
        }
    }

    /**
     * @return bolean if this quiz should only be shown to students in a secure window.
     */
    public function securewindow_required($canpreview) {
        return !$canpreview && !is_null($this->securewindowrule);
    }

    /**
     * @return bolean if this quiz should only be shown to students with safe browser.
     */
    public function safebrowser_required($canpreview) {
        return !$canpreview && !is_null($this->safebrowserrule);
    }

    /**
     * Print a button to start a quiz attempt, with an appropriate javascript warning,
     * depending on the access restrictions. The link will pop up a 'secure' window, if
     * necessary.
     *
     * @param bool $canpreview whether this user can preview. This affects whether they must
     * use a secure window.
     * @param string $buttontext the label to put on the button.
     * @param bool $unfinished whether the button is to continue an existing attempt,
     * or start a new one. This affects whether a javascript alert is shown.
     */
    //TODO: Add this function to renderer
    public function print_start_attempt_button($canpreview, $buttontext, $unfinished) {
        global $OUTPUT;

        $url = $this->quizobj->start_attempt_url();
        $button = new single_button($url, $buttontext);
        $button->class .= ' quizstartbuttondiv';

        if (!$unfinished) {
            $strconfirmstartattempt = $this->confirm_start_attempt_message();
            if ($strconfirmstartattempt) {
                $button->add_action(new confirm_action($strconfirmstartattempt, null,
                        get_string('startattempt', 'quiz')));
            }
        }

        $warning = '';

        if ($this->securewindow_required($canpreview)) {
            $button->class .= ' quizsecuremoderequired';

            $button->add_action(new popup_action('click', $url, 'quizpopup',
                    securewindow_access_rule::$popupoptions));

            $warning = html_writer::tag('noscript',
                    $OUTPUT->heading(get_string('noscript', 'quiz')));
        }

        return $OUTPUT->render($button) . $warning;
    }

    /**
     * Send the user back to the quiz view page. Normally this is just a redirect, but
     * If we were in a secure window, we close this window, and reload the view window we came from.
     *
     * @param bool $canpreview This affects whether we have to worry about secure window stuff.
     */
    public function back_to_view_page($canpreview, $message = '') {
        global $CFG, $OUTPUT, $PAGE;
        $url = $this->quizobj->view_url();
        if ($this->securewindow_required($canpreview)) {
            $PAGE->set_pagelayout('popup');
            echo $OUTPUT->header();
            echo $OUTPUT->box_start();
            if ($message) {
                echo '<p>' . $message . '</p><p>' . get_string('windowclosing', 'quiz') . '</p>';
                $delay = 5;
            } else {
                echo '<p>' . get_string('pleaseclose', 'quiz') . '</p>';
                $delay = 0;
            }
            $PAGE->requires->js_function_call('M.mod_quiz.secure_window.close',
                    array($url, $delay));
            echo $OUTPUT->box_end();
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
     * @param bool $canpreview This affects whether we have to worry about secure window stuff.
     */
    public function print_finish_review_link($canpreview, $return = false) {
        global $CFG;
        $output = '';
        $url = $this->quizobj->view_url();
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
        return !is_null($this->passwordrule);
    }

    /**
     * Clear the flag in the session that says that the current user is allowed to do this quiz.
     */
    public function clear_password_access() {
        if (!is_null($this->passwordrule)) {
            $this->passwordrule->clear_access_allowed();
        }
    }

    /**
     * Actually ask the user for the password, if they have not already given it this session.
     * This function only returns is access is OK.
     *
     * @param bool $canpreview used to enfore securewindow stuff.
     */
    public function do_password_check($canpreview) {
        if (!is_null($this->passwordrule)) {
            $this->passwordrule->do_password_check($canpreview, $this);
        }
    }

    /**
     * @return string if the quiz policies merit it, return a warning string to be displayed
     * in a javascript alert on the start attempt button.
     */
    public function confirm_start_attempt_message() {
        $quiz = $this->quizobj->get_quiz();
        if ($quiz->timelimit && $quiz->attempts) {
            return get_string('confirmstartattempttimelimit', 'quiz', $quiz->attempts);
        } else if ($quiz->timelimit) {
            return get_string('confirmstarttimelimit', 'quiz');
        } else if ($quiz->attempts) {
            return get_string('confirmstartattemptlimit', 'quiz', $quiz->attempts);
        }
        return '';
    }

    /**
     * Make some text into a link to review the quiz, if that is appropriate.
     *
     * @param string $linktext some text.
     * @param object $attempt the attempt object
     * @return string some HTML, the $linktext either unmodified or wrapped in a
     *      link to the review page.
     */
    public function make_review_link($attempt, $canpreview, $reviewoptions) {
        global $CFG;

        // If review of responses is not allowed, or the attempt is still open, don't link.
        if (!$attempt->timefinish) {
            return '';
        }

        $when = quiz_attempt_state($this->quizobj->get_quiz(), $attempt);
        $reviewoptions = mod_quiz_display_options::make_from_quiz(
                $this->quizobj->get_quiz(), $when);

        if (!$reviewoptions->attempt) {
            $message = $this->cannot_review_message($when, true);
            if ($message) {
                return '<span class="noreviewmessage">' . $message . '</span>';
            } else {
                return '';
            }
        }

        $linktext = get_string('review', 'quiz');

        // It is OK to link, does it need to be in a secure window?
        if ($this->securewindow_required($canpreview)) {
            return $this->securewindowrule->make_review_link($linktext, $attempt->id);
        } else {
            return '<a href="' . $this->quizobj->review_url($attempt->id) . '" title="' .
                    get_string('reviewthisattempt', 'quiz') . '">' . $linktext . '</a>';
        }
    }

    /**
     * If $reviewoptions->attempt is false, meaning that students can't review this
     * attempt at the moment, return an appropriate string explaining why.
     *
     * @param int $when One of the mod_quiz_display_options::DURING,
     *      IMMEDIATELY_AFTER, LATER_WHILE_OPEN or AFTER_CLOSE constants.
     * @param bool $short if true, return a shorter string.
     * @return string an appropraite message.
     */
    public function cannot_review_message($when, $short = false) {
        $quiz = $this->quizobj->get_quiz();
        if ($short) {
            $langstrsuffix = 'short';
            $dateformat = get_string('strftimedatetimeshort', 'langconfig');
        } else {
            $langstrsuffix = '';
            $dateformat = '';
        }
        if ($when == mod_quiz_display_options::DURING ||
                $when == mod_quiz_display_options::IMMEDIATELY_AFTER) {
            return '';
        } else if ($when == mod_quiz_display_options::LATER_WHILE_OPEN && $quiz->timeclose &&
                $quiz->reviewattempt & mod_quiz_display_options::AFTER_CLOSE) {
            return get_string('noreviewuntil' . $langstrsuffix, 'quiz',
                    userdate($quiz->timeclose, $dateformat));
        } else {
            return get_string('noreview' . $langstrsuffix, 'quiz');
        }
    }
}
