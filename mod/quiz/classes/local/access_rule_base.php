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

namespace mod_quiz\local;

use mod_quiz\form\preflight_check_form;
use mod_quiz_mod_form;
use moodle_page;
use MoodleQuickForm;
use mod_quiz\quiz_settings;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');


/**
 * Base class for rules that restrict the ability to attempt a quiz.
 *
 * Quiz access rule plugins must sublclass this one to form their main 'rule' class.
 * Most of the methods are defined in a slightly unnatural way because we either
 * want to say that access is allowed, or explain the reason why it is block.
 * Therefore instead of is_access_allowed(...) we have prevent_access(...) that
 * return false if access is permitted, or a string explanation (which is treated
 * as true) if access should be blocked. Slighly unnatural, but actually the easiest
 * way to implement this.
 *
 * @package   mod_quiz
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.2
 */
abstract class access_rule_base {
    /** @var stdClass the quiz settings. */
    protected $quiz;
    /** @var quiz_settings the quiz object. */
    protected $quizobj;
    /** @var int the time to use as 'now'. */
    protected $timenow;

    /**
     * Create an instance of this rule for a particular quiz.
     *
     * @param quiz_settings $quizobj information about the quiz in question.
     * @param int $timenow the time that should be considered as 'now'.
     */
    public function __construct($quizobj, $timenow) {
        $this->quizobj = $quizobj;
        $this->quiz = $quizobj->get_quiz();
        $this->timenow = $timenow;
    }

    /**
     * Return an appropriately configured instance of this rule, if it is applicable
     * to the given quiz, otherwise return null.
     *
     * @param quiz_settings $quizobj information about the quiz in question.
     * @param int $timenow the time that should be considered as 'now'.
     * @param bool $canignoretimelimits whether the current user is exempt from
     *      time limits by the mod/quiz:ignoretimelimits capability.
     * @return self|null the rule, if applicable, else null.
     */
    public static function make(quiz_settings $quizobj, $timenow, $canignoretimelimits) {
        return null;
    }

    /**
     * Whether a user should be allowed to start a new attempt at this quiz now.
     *
     * @param int $numprevattempts the number of previous attempts this user has made.
     * @param stdClass $lastattempt information about the user's last completed attempt.
     * @return string false if access should be allowed, a message explaining the
     *      reason if access should be prevented.
     */
    public function prevent_new_attempt($numprevattempts, $lastattempt) {
        return false;
    }

    /**
     * Whether the user should be blocked from starting a new attempt or continuing
     * an attempt now.
     * @return string false if access should be allowed, a message explaining the
     *      reason if access should be prevented.
     */
    public function prevent_access() {
        return false;
    }

    /**
     * Does this rule require a UI check with the user before an attempt is started?
     *
     * @param int|null $attemptid the id of the current attempt, if there is one,
     *      otherwise null.
     * @return bool whether a check is required before the user starts/continues
     *      their attempt.
     */
    public function is_preflight_check_required($attemptid) {
        return false;
    }

    /**
     * Add any field you want to pre-flight check form. You should only do
     * something here if {@see is_preflight_check_required()} returned true.
     *
     * @param preflight_check_form $quizform the form being built.
     * @param MoodleQuickForm $mform The wrapped MoodleQuickForm.
     * @param int|null $attemptid the id of the current attempt, if there is one,
     *      otherwise null.
     */
    public function add_preflight_check_form_fields(preflight_check_form $quizform,
            MoodleQuickForm $mform, $attemptid) {
        // Do nothing by default.
    }

    /**
     * Validate the pre-flight check form submission. You should only do
     * something here if {@see is_preflight_check_required()} returned true.
     *
     * If the form validates, the user will be allowed to continue.
     *
     * @param array $data the submitted form data.
     * @param array $files any files in the submission.
     * @param array $errors the list of validation errors that is being built up.
     * @param int|null $attemptid the id of the current attempt, if there is one,
     *      otherwise null.
     * @return array the update $errors array;
     */
    public function validate_preflight_check($data, $files, $errors, $attemptid) {
        return $errors;
    }

    /**
     * The pre-flight check has passed. This is a chance to record that fact in
     * some way.
     * @param int|null $attemptid the id of the current attempt, if there is one,
     *      otherwise null.
     */
    public function notify_preflight_check_passed($attemptid) {
        // Do nothing by default.
    }

    /**
     * This is called when the current attempt at the quiz is finished. This is
     * used, for example by the password rule, to clear the flag in the session.
     */
    public function current_attempt_finished() {
        // Do nothing by default.
    }

    /**
     * Return a brief summary of this rule, to show to users, if required.
     *
     * This information is show shown, for example, on the quiz view page, to explain this
     * restriction. There is no obligation to return anything. If it is not appropriate to
     * tell students about this rule, then just return ''.
     *
     * @return string a message, or array of messages, explaining the restriction
     *         (may be '' if no message is appropriate).
     */
    public function description() {
        return '';
    }

    /**
     * Is the current user unable to start any more attempts in future, because of this rule?
     *
     * If this rule can determine that this user will never be allowed another attempt at
     * this quiz, for example because the last possible start time is past, or all attempts
     * have been used up, then return true. This is used to know whether to display a
     * final grade on the view page. This will only be called if there is not a currently
     * active attempt for this user.
     *
     * @param int $numprevattempts the number of previous attempts this user has made.
     * @param stdClass $lastattempt information about the user's last completed attempt.
     * @return bool true if this rule means that this user will never be allowed another
     * attempt at this quiz.
     */
    public function is_finished($numprevattempts, $lastattempt) {
        return false;
    }

    /**
     * Time by which, according to this rule, the user has to finish their attempt.
     *
     * @param stdClass $attempt the current attempt
     * @return int|false the attempt close time, or false if there is no close time.
     */
    public function end_time($attempt) {
        return false;
    }

    /**
     * If the user should be shown a different amount of time than $timenow - $this->end_time(), then
     * override this method.  This is useful if the time remaining is large enough to be omitted.
     * @param stdClass $attempt the current attempt
     * @param int $timenow the time now. We don't use $this->timenow, so we can
     * give the user a more accurate indication of how much time is left.
     * @return mixed the time left in seconds (can be negative) or false if there is no limit.
     */
    public function time_left_display($attempt, $timenow) {
        $endtime = $this->end_time($attempt);
        if ($endtime === false) {
            return false;
        }
        return $endtime - $timenow;
    }

    /**
     * Does this rule requires the attempt (and review) to be displayed in a pop-up window?
     *
     * @return bool true if it does.
     */
    public function attempt_must_be_in_popup() {
        return false;
    }

    /**
     * Any options required when showing the attempt in a pop-up.
     *
     * @return array any options that are required for showing the attempt page
     *      in a popup window.
     */
    public function get_popup_options() {
        return [];
    }

    /**
     * Sets up the attempt (review or summary) page with any special extra
     * properties required by this rule. securewindow rule is an example of where
     * this is used.
     *
     * @param moodle_page $page the page object to initialise.
     */
    public function setup_attempt_page($page) {
        // Do nothing by default.
    }

    /**
     * It is possible for one rule to override other rules.
     *
     * The aim is that third-party rules should be able to replace sandard rules
     * if they want. See, for example MDL-13592.
     *
     * @return array plugin names of other rules that this one replaces.
     *      For example ['ipaddress', 'password'].
     */
    public function get_superceded_rules() {
        return [];
    }

    /**
     * Add any fields that this rule requires to the quiz settings form. This
     * method is called from {@see mod_quiz_mod_form::definition()}, while the
     * security seciton is being built.
     * @param mod_quiz_mod_form $quizform the quiz settings form that is being built.
     * @param MoodleQuickForm $mform the wrapped MoodleQuickForm.
     */
    public static function add_settings_form_fields(
            mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        // By default do nothing.
    }

    /**
     * Validate the data from any form fields added using {@see add_settings_form_fields()}.
     * @param array $errors the errors found so far.
     * @param array $data the submitted form data.
     * @param array $files information about any uploaded files.
     * @param mod_quiz_mod_form $quizform the quiz form object.
     * @return array $errors the updated $errors array.
     */
    public static function validate_settings_form_fields(array $errors,
            array $data, $files, mod_quiz_mod_form $quizform) {

        return $errors;
    }

    /**
     * Get any options this rule adds to the 'Browser security' quiz setting.
     *
     * @return array key => lang string any choices to add to the quiz Browser
     *      security settings menu.
     */
    public static function get_browser_security_choices() {
        return [];
    }

    /**
     * Save any submitted settings when the quiz settings form is submitted. This
     * is called from {@see quiz_after_add_or_update()} in lib.php.
     * @param stdClass $quiz the data from the quiz form, including $quiz->id
     *      which is the id of the quiz being saved.
     */
    public static function save_settings($quiz) {
        // By default do nothing.
    }

    /**
     * Delete any rule-specific settings when the quiz is deleted. This is called
     * from {@see quiz_delete_instance()} in lib.php.
     * @param stdClass $quiz the data from the database, including $quiz->id
     *      which is the id of the quiz being deleted.
     * @since Moodle 2.7.1, 2.6.4, 2.5.7
     */
    public static function delete_settings($quiz) {
        // By default do nothing.
    }

    /**
     * Return the bits of SQL needed to load all the settings from all the access
     * plugins in one DB query. The easiest way to understand what you need to do
     * here is probably to read the code of {@see access_manager::load_settings()}.
     *
     * If you have some settings that cannot be loaded in this way, then you can
     * use the {@see get_extra_settings()} method instead, but that has
     * performance implications.
     *
     * @param int $quizid the id of the quiz we are loading settings for. This
     *     can also be accessed as quiz.id in the SQL. (quiz is a table alisas for {quiz}.)
     * @return array with three elements:
     *     1. fields: any fields to add to the select list. These should be alised
     *        if neccessary so that the field name starts the name of the plugin.
     *     2. joins: any joins (should probably be LEFT JOINS) with other tables that
     *        are needed.
     *     3. params: array of placeholder values that are needed by the SQL. You must
     *        used named placeholders, and the placeholder names should start with the
     *        plugin name, to avoid collisions.
     */
    public static function get_settings_sql($quizid) {
        return ['', '', []];
    }

    /**
     * You can use this method to load any extra settings your plugin has that
     * cannot be loaded efficiently with get_settings_sql().
     * @param int $quizid the quiz id.
     * @return array setting value name => value. The value names should all
     *      start with the name of your plugin to avoid collisions.
     */
    public static function get_extra_settings($quizid) {
        return [];
    }
}
