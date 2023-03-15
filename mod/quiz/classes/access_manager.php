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

namespace mod_quiz;

use core_component;
use mod_quiz\form\preflight_check_form;
use mod_quiz\local\access_rule_base;
use mod_quiz\output\renderer;
use mod_quiz\question\display_options;
use mod_quiz_mod_form;
use moodle_page;
use moodle_url;
use MoodleQuickForm;
use stdClass;

/**
 * This class aggregates the access rules that apply to a particular quiz.
 *
 * This provides a convenient API which other parts of the quiz code can use
 * to interact with the access rules.
 *
 * @package   mod_quiz
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.2
 */
class access_manager {
    /** @var quiz_settings the quiz settings object. */
    protected $quizobj;

    /** @var int the time to be considered as 'now'. */
    protected $timenow;

    /** @var access_rule_base instances of the active rules for this quiz. */
    protected $rules = [];

    /**
     * Create an instance for a particular quiz.
     *
     * @param quiz_settings $quizobj the quiz settings.
     *      The quiz we will be controlling access to.
     * @param int $timenow The time to use as 'now'.
     * @param bool $canignoretimelimits Whether this user is exempt from time
     *      limits (has_capability('mod/quiz:ignoretimelimits', ...)).
     */
    public function __construct(quiz_settings $quizobj, int $timenow, bool $canignoretimelimits) {
        $this->quizobj = $quizobj;
        $this->timenow = $timenow;
        $this->rules = $this->make_rules($quizobj, $timenow, $canignoretimelimits);
    }

    /**
     * Make all the rules relevant to a particular quiz.
     *
     * @param quiz_settings $quizobj information about the quiz in question.
     * @param int $timenow the time that should be considered as 'now'.
     * @param bool $canignoretimelimits whether the current user is exempt from
     *      time limits by the mod/quiz:ignoretimelimits capability.
     * @return access_rule_base[] rules that apply to this quiz.
     */
    protected function make_rules(quiz_settings $quizobj, int $timenow, bool $canignoretimelimits): array {

        $rules = [];
        foreach (self::get_rule_classes() as $ruleclass) {
            $rule = $ruleclass::make($quizobj, $timenow, $canignoretimelimits);
            if ($rule) {
                $rules[$ruleclass] = $rule;
            }
        }

        $superceededrules = [];
        foreach ($rules as $rule) {
            $superceededrules += $rule->get_superceded_rules();
        }

        foreach ($superceededrules as $superceededrule) {
            unset($rules['quizaccess_' . $superceededrule]);
        }

        return $rules;
    }

    /**
     * Get that names of all the installed rule classes.
     *
     * @return array of class names.
     */
    protected static function get_rule_classes(): array {
        return core_component::get_plugin_list_with_class('quizaccess', '', 'rule.php');
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
            mod_quiz_mod_form $quizform, MoodleQuickForm $mform): void {

        foreach (self::get_rule_classes() as $rule) {
            $rule::add_settings_form_fields($quizform, $mform);
        }
    }

    /**
     * The the options for the Browser security settings menu.
     *
     * @return array key => lang string.
     */
    public static function get_browser_security_choices(): array {
        $options = ['-' => get_string('none', 'quiz')];
        foreach (self::get_rule_classes() as $rule) {
            $options += $rule::get_browser_security_choices();
        }
        return $options;
    }

    /**
     * Validate the data from any form fields added using {@see add_settings_form_fields()}.
     *
     * @param array $errors the errors found so far.
     * @param array $data the submitted form data.
     * @param array $files information about any uploaded files.
     * @param mod_quiz_mod_form $quizform the quiz form object.
     * @return array $errors the updated $errors array.
     */
    public static function validate_settings_form_fields(array $errors,
            array $data, array $files, mod_quiz_mod_form $quizform): array {

        foreach (self::get_rule_classes() as $rule) {
            $errors = $rule::validate_settings_form_fields($errors, $data, $files, $quizform);
        }

        return $errors;
    }

    /**
     * Save any submitted settings when the quiz settings form is submitted.
     *
     * Note that the standard plugins do not use this mechanism because their
     * settings are stored in the quiz table.
     *
     * @param stdClass $quiz the data from the quiz form, including $quiz->id
     *      which is the id of the quiz being saved.
     */
    public static function save_settings(stdClass $quiz): void {

        foreach (self::get_rule_classes() as $rule) {
            $rule::save_settings($quiz);
        }
    }

    /**
     * Delete any rule-specific settings when the quiz is deleted.
     *
     * Note that the standard plugins do not use this mechanism because their
     * settings are stored in the quiz table.
     *
     * @param stdClass $quiz the data from the database, including $quiz->id
     *      which is the id of the quiz being deleted.
     * @since Moodle 2.7.1, 2.6.4, 2.5.7
     */
    public static function delete_settings(stdClass $quiz): void {

        foreach (self::get_rule_classes() as $rule) {
            $rule::delete_settings($quiz);
        }
    }

    /**
     * Build the SQL for loading all the access settings in one go.
     *
     * @param int $quizid the quiz id.
     * @param array $rules list of rule plugins, from {@see get_rule_classes()}.
     * @param string $basefields initial part of the select list.
     * @return array with two elements, the sql and the placeholder values.
     *      If $basefields is '' then you must allow for the possibility that
     *      there is no data to load, in which case this method returns $sql = ''.
     */
    protected static function get_load_sql(int $quizid, array $rules, string $basefields): array {
        $allfields = $basefields;
        $alljoins = '{quiz} quiz';
        $allparams = ['quizid' => $quizid];

        foreach ($rules as $rule) {
            [$fields, $joins, $params] = $rule::get_settings_sql($quizid);
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

        if ($allfields === '') {
            return ['', []];
        }

        return ["SELECT $allfields FROM $alljoins WHERE quiz.id = :quizid", $allparams];
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
    public static function load_settings(int $quizid): array {
        global $DB;

        $rules = self::get_rule_classes();
        [$sql, $params] = self::get_load_sql($quizid, $rules, '');

        if ($sql) {
            $data = (array) $DB->get_record_sql($sql, $params);
        } else {
            $data = [];
        }

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
     * @return stdClass mdl_quiz row with extra fields.
     */
    public static function load_quiz_and_settings(int $quizid): stdClass {
        global $DB;

        $rules = self::get_rule_classes();
        [$sql, $params] = self::get_load_sql($quizid, $rules, 'quiz.*');
        $quiz = $DB->get_record_sql($sql, $params, MUST_EXIST);

        foreach ($rules as $rule) {
            foreach ($rule::get_extra_settings($quizid) as $name => $value) {
                $quiz->$name = $value;
            }
        }

        return $quiz;
    }

    /**
     * Get an array of the class names of all the active rules.
     *
     * Mainly useful for debugging.
     *
     * @return array
     */
    public function get_active_rule_names(): array {
        $classnames = [];
        foreach ($this->rules as $rule) {
            $classnames[] = get_class($rule);
        }
        return $classnames;
    }

    /**
     * Accumulates an array of messages.
     *
     * @param array $messages the current list of messages.
     * @param string|array $new the new messages or messages.
     * @return array the updated array of messages.
     */
    protected function accumulate_messages(array $messages, $new): array {
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
    public function describe_rules(): array {
        $result = [];
        foreach ($this->rules as $rule) {
            $result = $this->accumulate_messages($result, $rule->description());
        }
        return $result;
    }

    /**
     * Whether a user should be allowed to start a new attempt at this quiz now.
     * If there are any restrictions in force now, return an array of reasons why access
     * should be blocked. If access is OK, return false.
     *
     * @param int $numprevattempts the number of previous attempts this user has made.
     * @param stdClass|false $lastattempt information about the user's last completed attempt.
     *      if there is not a previous attempt, the false is passed.
     * @return array an array of reason why access is not allowed. An empty array
     *         (== false) if access should be allowed.
     */
    public function prevent_new_attempt(int $numprevattempts, $lastattempt): array {
        $reasons = [];
        foreach ($this->rules as $rule) {
            $reasons = $this->accumulate_messages($reasons,
                    $rule->prevent_new_attempt($numprevattempts, $lastattempt));
        }
        return $reasons;
    }

    /**
     * Whether the user should be blocked from starting a new attempt or continuing
     * an attempt now. If there are any restrictions in force now, return an array
     * of reasons why access should be blocked. If access is OK, return false.
     *
     * @return array An array of reason why access is not allowed, or an empty array
     *         (== false) if access should be allowed.
     */
    public function prevent_access(): array {
        $reasons = [];
        foreach ($this->rules as $rule) {
            $reasons = $this->accumulate_messages($reasons, $rule->prevent_access());
        }
        return $reasons;
    }

    /**
     * Is a UI check is required before the user starts/continues their attempt.
     *
     * @param int|null $attemptid the id of the current attempt, if there is one,
     *      otherwise null.
     * @return bool whether a check is required.
     */
    public function is_preflight_check_required(?int $attemptid): bool {
        foreach ($this->rules as $rule) {
            if ($rule->is_preflight_check_required($attemptid)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Build the form required to do the pre-flight checks.
     * @param moodle_url $url the form action URL.
     * @param int|null $attemptid the id of the current attempt, if there is one,
     *      otherwise null.
     * @return preflight_check_form the form.
     */
    public function get_preflight_check_form(moodle_url $url, ?int $attemptid): preflight_check_form {
        // This form normally wants POST submissions. However, it also needs to
        // accept GET submissions. Since formslib is strict, we have to detect
        // which case we are in, and set the form property appropriately.
        $method = 'post';
        if (!empty($_GET['_qf__preflight_check_form'])) {
            $method = 'get';
        }
        return new preflight_check_form($url->out_omit_querystring(),
                ['rules' => $this->rules, 'quizobj' => $this->quizobj,
                      'attemptid' => $attemptid, 'hidden' => $url->params()], $method);
    }

    /**
     * The pre-flight check has passed. This is a chance to record that fact in some way.
     *
     * @param int|null $attemptid the id of the current attempt, if there is one,
     *      otherwise null.
     */
    public function notify_preflight_check_passed(?int $attemptid): void {
        foreach ($this->rules as $rule) {
            $rule->notify_preflight_check_passed($attemptid);
        }
    }

    /**
     * Inform the rules that the current attempt is finished.
     *
     * This is use, for example by the password rule, to clear the flag in the session.
     */
    public function current_attempt_finished(): void {
        foreach ($this->rules as $rule) {
            $rule->current_attempt_finished();
        }
    }

    /**
     * Do any of the rules mean that this student will no be allowed any further attempts at this
     * quiz. Used, for example, to change the label by the grade displayed on the view page from
     * 'your current grade is' to 'your final grade is'.
     *
     * @param int $numprevattempts the number of previous attempts this user has made.
     * @param stdClass|false $lastattempt information about the user's last completed attempt.
     * @return bool true if there is no way the user will ever be allowed to attempt
     *      this quiz again.
     */
    public function is_finished(int $numprevattempts, $lastattempt): bool {
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
    public function setup_attempt_page(moodle_page $page): void {
        foreach ($this->rules as $rule) {
            $rule->setup_attempt_page($page);
        }
    }

    /**
     * Compute when the attempt must be submitted.
     *
     * @param stdClass $attempt the data from the relevant quiz_attempts row.
     * @return int|false the attempt close time. False if there is no limit.
     */
    public function get_end_time(stdClass $attempt) {
        $timeclose = false;
        foreach ($this->rules as $rule) {
            $ruletimeclose = $rule->end_time($attempt);
            if ($ruletimeclose !== false && ($timeclose === false || $ruletimeclose < $timeclose)) {
                $timeclose = $ruletimeclose;
            }
        }
        return $timeclose;
    }

    /**
     * Compute what should be displayed to the user for time remaining in this attempt.
     *
     * @param stdClass $attempt the data from the relevant quiz_attempts row.
     * @param int $timenow the time to consider as 'now'.
     * @return int|false the number of seconds remaining for this attempt.
     *      False if no limit should be displayed.
     */
    public function get_time_left_display(stdClass $attempt, int $timenow) {
        $timeleft = false;
        foreach ($this->rules as $rule) {
            $ruletimeleft = $rule->time_left_display($attempt, $timenow);
            if ($ruletimeleft !== false && ($timeleft === false || $ruletimeleft < $timeleft)) {
                $timeleft = $ruletimeleft;
            }
        }
        return $timeleft;
    }

    /**
     * Is this quiz required to be shown in a popup window?
     *
     * @return bool true if a popup is required.
     */
    public function attempt_must_be_in_popup(): bool {
        foreach ($this->rules as $rule) {
            if ($rule->attempt_must_be_in_popup()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get options required for opening the attempt in a popup window.
     *
     * @return array any options that are required for showing the attempt page
     *      in a popup window.
     */
    public function get_popup_options(): array {
        $options = [];
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
     * @param renderer $output the quiz renderer.
     * @param string $message optional message to output while redirecting.
     */
    public function back_to_view_page(renderer $output, string $message = ''): void {
         // Actually return type 'never' on the previous line, once 8.1 is our minimum PHP version.
        if ($this->attempt_must_be_in_popup()) {
            echo $output->close_attempt_popup(new moodle_url($this->quizobj->view_url()), $message);
            die();
        } else {
            redirect($this->quizobj->view_url(), $message);
        }
    }

    /**
     * Make some text into a link to review the quiz, if that is appropriate.
     *
     * @param stdClass $attempt the attempt object
     * @param mixed $nolongerused not used any more.
     * @param renderer $output quiz renderer instance.
     * @return string some HTML, the $linktext either unmodified or wrapped in a
     *      link to the review page.
     */
    public function make_review_link(stdClass $attempt, $nolongerused, renderer $output): string {

        // If the attempt is still open, don't link.
        if (in_array($attempt->state, [quiz_attempt::IN_PROGRESS, quiz_attempt::OVERDUE])) {
            return $output->no_review_message('');
        }

        $when = quiz_attempt_state($this->quizobj->get_quiz(), $attempt);
        $reviewoptions = display_options::make_from_quiz(
                $this->quizobj->get_quiz(), $when);

        if (!$reviewoptions->attempt) {
            return $output->no_review_message($this->quizobj->cannot_review_message($when, true));

        } else {
            return $output->review_link($this->quizobj->review_url($attempt->id),
                    $this->attempt_must_be_in_popup(), $this->get_popup_options());
        }
    }

    /**
     * Run the preflight checks using the given data in all the rules supporting them.
     *
     * @param array $data passed data for validation
     * @param array $files un-used, Moodle seems to not support it anymore
     * @param int|null $attemptid the id of the current attempt, if there is one,
     *      otherwise null.
     * @return array of errors, empty array means no errors
     * @since  Moodle 3.1
     */
    public function validate_preflight_check(array $data, array $files, ?int $attemptid): array {
        $errors = [];
        foreach ($this->rules as $rule) {
            if ($rule->is_preflight_check_required($attemptid)) {
                $errors = $rule->validate_preflight_check($data, $files, $errors, $attemptid);
            }
        }
        return $errors;
    }
}
