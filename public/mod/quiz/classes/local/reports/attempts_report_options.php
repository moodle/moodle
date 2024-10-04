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

namespace mod_quiz\local\reports;

use context_module;
use mod_quiz\quiz_attempt;
use moodle_url;
use stdClass;

/**
 * Base class for the options that control what is visible in an {@see attempts_report}.
 *
 * @package   mod_quiz
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempts_report_options {

    /** @var string the report mode. */
    public $mode;

    /** @var stdClass the settings for the quiz being reported on. */
    public $quiz;

    /** @var stdClass the course module objects for the quiz being reported on. */
    public $cm;

    /** @var stdClass the course settings for the course the quiz is in. */
    public $course;

    /**
     * @var array form field name => corresponding quiz_attempt:: state constant.
     */
    protected static $statefields = [
        'statenotstarted' => quiz_attempt::NOT_STARTED,
        'stateinprogress' => quiz_attempt::IN_PROGRESS,
        'stateoverdue'    => quiz_attempt::OVERDUE,
        'statesubmitted' => quiz_attempt::SUBMITTED,
        'statefinished'   => quiz_attempt::FINISHED,
        'stateabandoned'  => quiz_attempt::ABANDONED,
    ];

    /**
     * @var string attempts_report::ALL_WITH, attempts_report::ENROLLED_WITH,
     *      attempts_report::ENROLLED_WITHOUT or attempts_report::ENROLLED_ALL
     */
    public $attempts = attempts_report::ENROLLED_WITH;

    /** @var int the currently selected group. 0 if no group is selected. */
    public $group = 0;

    /**
     * @var array|null of quiz_attempt::IN_PROGRESS, etc. constants. null means
     *      no restriction.
     */
    public $states = [
        quiz_attempt::NOT_STARTED,
        quiz_attempt::IN_PROGRESS,
        quiz_attempt::OVERDUE,
        quiz_attempt::SUBMITTED,
        quiz_attempt::FINISHED,
        quiz_attempt::ABANDONED,
    ];

    /**
     * @var bool whether to show all finished attmepts, or just the one that gave
     *      the final grade for the user.
     */
    public $onlygraded = false;

    /** @var int Number of attempts to show per page. */
    public $pagesize = attempts_report::DEFAULT_PAGE_SIZE;

    /** @var string whether the data should be downloaded in some format, or '' to display it. */
    public $download = '';

    /** @var bool whether the current user has permission to see grades. */
    public $usercanseegrades;

    /** @var bool whether the report table should have a column of checkboxes. */
    public $checkboxcolumn = false;

    /**
     * Constructor.
     *
     * @param string $mode which report these options are for.
     * @param stdClass $quiz the settings for the quiz being reported on.
     * @param stdClass $cm the course module objects for the quiz being reported on.
     * @param stdClass $course the course settings for the coures this quiz is in.
     */
    public function __construct($mode, $quiz, $cm, $course) {
        $this->mode   = $mode;
        $this->quiz   = $quiz;
        $this->cm     = $cm;
        $this->course = $course;

        $this->usercanseegrades = quiz_report_should_show_grades($quiz, context_module::instance($cm->id));
    }

    /**
     * Get the URL parameters required to show the report with these options.
     * @return array URL parameter name => value.
     */
    protected function get_url_params() {
        $params = [
            'id'         => $this->cm->id,
            'mode'       => $this->mode,
            'attempts'   => $this->attempts,
            'onlygraded' => $this->onlygraded,
        ];

        if ($this->states) {
            $params['states'] = implode('-', $this->states);
        }

        if (groups_get_activity_groupmode($this->cm, $this->course)) {
            $params['group'] = $this->group;
        }
        return $params;
    }

    /**
     * Get the URL to show the report with these options.
     * @return moodle_url the URL.
     */
    public function get_url() {
        return new moodle_url('/mod/quiz/report.php', $this->get_url_params());
    }

    /**
     * Process the data we get when the settings form is submitted. This includes
     * updating the fields of this class, and updating the user preferences
     * where appropriate.
     * @param stdClass $fromform The data from $mform->get_data() from the settings form.
     */
    public function process_settings_from_form($fromform) {
        $this->setup_from_form_data($fromform);
        $this->resolve_dependencies();
        $this->update_user_preferences();
    }

    /**
     * Set up this preferences object using optional_param (using user_preferences
     * to set anything not specified by the params.
     */
    public function process_settings_from_params() {
        $this->setup_from_user_preferences();
        $this->setup_from_params();
        $this->resolve_dependencies();
    }

    /**
     * Get the current value of the settings to pass to the settings form.
     */
    public function get_initial_form_data() {
        $toform = new stdClass();
        $toform->attempts   = $this->attempts;
        $toform->onlygraded = $this->onlygraded;
        $toform->pagesize   = $this->pagesize;

        if ($this->states) {
            foreach (self::$statefields as $field => $state) {
                $toform->$field = in_array($state, $this->states);
            }
        }

        return $toform;
    }

    /**
     * Set the fields of this object from the form data.
     * @param stdClass $fromform The data from $mform->get_data() from the settings form.
     */
    public function setup_from_form_data($fromform) {
        $this->attempts   = $fromform->attempts;
        $this->group      = groups_get_activity_group($this->cm, true);
        $this->onlygraded = !empty($fromform->onlygraded);
        $this->pagesize   = $fromform->pagesize;

        $this->states = [];
        foreach (self::$statefields as $field => $state) {
            if (!empty($fromform->$field)) {
                $this->states[] = $state;
            }
        }
    }

    /**
     * Set the fields of this object from the URL parameters.
     */
    public function setup_from_params() {
        $this->attempts   = optional_param('attempts', $this->attempts, PARAM_ALPHAEXT);
        $this->group      = groups_get_activity_group($this->cm, true);
        $this->onlygraded = optional_param('onlygraded', $this->onlygraded, PARAM_BOOL);
        $this->pagesize   = optional_param('pagesize', $this->pagesize, PARAM_INT);

        $states = optional_param('states', '', PARAM_ALPHAEXT);
        if (!empty($states)) {
            $this->states = explode('-', $states);
        }

        $this->download   = optional_param('download', $this->download, PARAM_ALPHA);
    }

    /**
     * Set the fields of this object from the user's preferences.
     * (For those settings that are backed by user-preferences).
     */
    public function setup_from_user_preferences() {
        $this->pagesize = get_user_preferences('quiz_report_pagesize', $this->pagesize);
    }

    /**
     * Update the user preferences so they match the settings in this object.
     * (For those settings that are backed by user-preferences).
     */
    public function update_user_preferences() {
        set_user_preference('quiz_report_pagesize', $this->pagesize);
    }

    /**
     * Check the settings, and remove any 'impossible' combinations.
     */
    public function resolve_dependencies() {
        if ($this->group) {
            // Default for when a group is selected.
            if ($this->attempts === null || $this->attempts == attempts_report::ALL_WITH) {
                $this->attempts = attempts_report::ENROLLED_WITH;
            }

        } else if (!$this->group && $this->course->id == SITEID) {
            // Force report on front page to show all, unless a group is selected.
            $this->attempts = attempts_report::ALL_WITH;

        } else if (!in_array($this->attempts, [attempts_report::ALL_WITH, attempts_report::ENROLLED_WITH,
                attempts_report::ENROLLED_WITHOUT, attempts_report::ENROLLED_ALL])) {
            $this->attempts = attempts_report::ENROLLED_WITH;
        }

        $cleanstates = [];
        foreach (self::$statefields as $state) {
            if (in_array($state, $this->states)) {
                $cleanstates[] = $state;
            }
        }
        $this->states = $cleanstates;
        if (count($this->states) == count(self::$statefields)) {
            // If all states have been selected, then there is no constraint
            // required in the SQL, so clear the array.
            $this->states = null;
        }

        if (!quiz_report_can_filter_only_graded($this->quiz)) {
            // A grading mode like 'average' has been selected, so we cannot do
            // the show the attempt that gave the final grade thing.
            $this->onlygraded = false;
        }

        if ($this->attempts == attempts_report::ENROLLED_WITHOUT) {
            $this->states = null;
            $this->onlygraded = false;
        }

        if (!$this->is_showing_finished_attempts()) {
            $this->onlygraded = false;
        }

        if ($this->pagesize < 1) {
            $this->pagesize = attempts_report::DEFAULT_PAGE_SIZE;
        }
    }

    /**
     * Whether the options are such that finished attempts are being shown.
     * @return boolean
     */
    protected function is_showing_finished_attempts() {
        return $this->states === null || in_array(quiz_attempt::FINISHED, $this->states);
    }
}
