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
                $this->safebrowserrule = new quizaccess_safebrowser(
                        $this->quizobj, $this->timenow);
                $this->rules[] = $this->safebrowserrule;
            }
        }
    }

    /**
     * Add any form fields that the access rules require to the settings form.
     *
     * Note that the standard plugins do not use this mechanism, becuase all their
     * settings are stored in the quiz table.
     *
     * @param mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    public static function add_settings_form_fields(
            mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        $rules = get_plugin_list_with_class('quizaccess', '', 'rule.php');
        foreach ($rules as $rule) {
            $rule::add_settings_form_fields($quizform, $mform);
        }
    }

    /**
     * Save any submitted settings when the quiz settings form is submitted.
     *
     * Note that the standard plugins do not use this mechanism, becuase all their
     * settings are stored in the quiz table.
     *
     * @param object $quiz the data from the quiz form, including $quiz->id
     *      which is the is of the quiz being saved.
     */
    public static function save_settings($quiz) {
        $rules = get_plugin_list_with_class('quizaccess', '', 'rule.php');
        foreach ($rules as $rule) {
            $rule::save_settings($quiz);
        }
    }

    /**
     * Build the SQL for loading all the access settings in one go.
     * @param int $quizid the quiz id.
     * @param string $basefields initial part of the select list.
     * @return array with two elements, the sql and the placeholder values.
     */
    protected static function get_load_sql($quizid, $rules, $basefields) {
        $allfields = $basefields;
        $alljoins = '{quiz} quiz';
        $allparams = array('quizid' => $quizid);

        foreach ($rules as $rule) {
            list($fields, $joins, $params) = $rule::get_settings_sql($quizid);
            if ($fields) {
                if ($allfields) {
                    $allfields .= ', ';
                }
                $allfields .= $fields;
            }
            if ($joins) {
                $alljoins .= ' ' . $joins;
            }
            if ($params) {
                $allparams += $params;
            }
        }

        return array("SELECT $allfields FROM $alljoins WHERE quiz.id = :quizid", $allparams);
    }

    /**
     * Load any settings required by the access rules. We try to do this with
     * a single DB query.
     *
     * Note that the standard plugins do not use this mechanism, becuase all their
     * settings are stored in the quiz table.
     *
     * @param int $quizid the quiz id.
     * @return array setting value name => value. The value names should all
     *      start with the name of the corresponding plugin to avoid collisions.
     */
    public static function load_settings($quizid) {
        global $DB;

        $rules = get_plugin_list_with_class('quizaccess', '', 'rule.php');
        list($sql, $params) = self::get_load_sql($quizid, $rules, '');
        $data = (array) $DB->get_record_sql($sql, $params);

        foreach ($rules as $rule) {
            $data += $rule::get_extra_settings($quizid);
        }

        return $data;
    }

    /**
     * Load the quiz settings and any settings required by the access rules.
     * We try to do this with a single DB query.
     *
     * Note that the standard plugins do not use this mechanism, becuase all their
     * settings are stored in the quiz table.
     *
     * @param int $quizid the quiz id.
     * @return object mdl_quiz row with extra fields.
     */
    public static function load_quiz_and_settings($quizid) {
        global $DB;

        $rules = get_plugin_list_with_class('quizaccess', '', 'rule.php');
        list($sql, $params) = self::get_load_sql($quizid, $rules, 'quiz.*');
        $quiz = $DB->get_record_sql($sql, $params, MUST_EXIST);

        foreach ($rules as $rule) {
            foreach ($rule::get_extra_settings($quizid) as $name => $value) {
                $quiz->$name = $value;
            }
        }

        return $quiz;
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
     * Sets up the attempt (review or summary) page with any properties required
     * by the access rules.
     *
     * @param moodle_page $page the page object to initialise.
     */
    public function setup_attempt_page($page) {
        foreach ($this->rules as $rule) {
            $rule->setup_attempt_page($page);
        }
    }

    /**
     * Will cause the attempt time to start counting down after the page has loaded,
     * if that is necessary.
     *
     * @param object $attempt the data from the relevant quiz_attempts row.
     * @param int $timenow the time to consider as 'now'.
     * @param mod_quiz_renderer $output the quiz renderer.
     */
    public function show_attempt_timer_if_needed($attempt, $timenow, $output) {

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
            $output->initialise_timer($timerstartvalue);
        }
    }

    /**
     * @return bolean if this quiz should only be shown to students in a popup window.
     */
    public function attempt_must_be_in_popup() {
        foreach ($this->rules as $rule) {
            if ($rule->attempt_must_be_in_popup()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array any options that are required for showing the attempt page
     *      in a popup window.
     */
    public function get_popup_options() {
        $options = array();
        foreach ($this->rules as $rule) {
            $options += $rule->get_popup_options();
        }
        return $options;
    }

    /**
     * Send the user back to the quiz view page. Normally this is just a redirect, but
     * If we were in a secure window, we close this window, and reload the view window we came from.
     *
     * This method does not return;
     *
     * @param mod_quiz_renderer $output the quiz renderer.
     * @param string $message optional message to output while redirecting.
     */
    public function back_to_view_page($output, $message = '') {
        if ($this->attempt_must_be_in_popup()) {
            echo $output->close_attempt_popup($message, $this->quizobj->view_url());
            die();
        } else {
            redirect($this->quizobj->view_url(), $message);
        }
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
     * Make some text into a link to review the quiz, if that is appropriate.
     *
     * @param string $linktext some text.
     * @param object $attempt the attempt object
     * @return string some HTML, the $linktext either unmodified or wrapped in a
     *      link to the review page.
     */
    public function make_review_link($attempt, $reviewoptions, $output) {

        // If review of responses is not allowed, or the attempt is still open, don't link.
        if (!$attempt->timefinish) {
            return $output->no_review_message('');
        }

        $when = quiz_attempt_state($this->quizobj->get_quiz(), $attempt);
        $reviewoptions = mod_quiz_display_options::make_from_quiz(
                $this->quizobj->get_quiz(), $when);

        if (!$reviewoptions->attempt) {
            return $output->no_review_message($this->quizobj->cannot_review_message($when, true));

        } else {
            return $output->review_link($this->quizobj->review_url($attempt->id),
                    $this->attempt_must_be_in_popup(), $this->get_popup_options());
        }
    }
}
