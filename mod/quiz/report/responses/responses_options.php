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
 * Class to store the options for a {@link quiz_responses_report}.
 *
 * @package   quiz_responses
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/attemptsreport_options.php');


/**
 * Class to store the options for a {@link quiz_responses_report}.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_responses_options extends mod_quiz_attempts_report_options {

    /** @var bool whether to show the question text columns. */
    public $showqtext = false;

    /** @var bool whether to show the students' reponse columns. */
    public $showresponses = true;

    /** @var bool whether to show the correct response columns. */
    public $showright = false;

    protected function get_url_params() {
        $params = parent::get_url_params();
        $params['qtext'] = $this->showqtext;
        $params['resp']  = $this->showresponses;
        $params['right'] = $this->showright;
        return $params;
    }

    public function get_initial_form_data() {
        $toform = parent::get_initial_form_data();
        $toform->qtext = $this->showqtext;
        $toform->resp  = $this->showresponses;
        $toform->right = $this->showright;

        return $toform;
    }

    public function setup_from_form_data($fromform) {
        parent::setup_from_form_data($fromform);

        $this->showqtext     = $fromform->qtext;
        $this->showresponses = $fromform->resp;
        $this->showright     = $fromform->right;
    }

    public function setup_from_params() {
        parent::setup_from_params();

        $this->showqtext     = optional_param('qtext', $this->showqtext,     PARAM_BOOL);
        $this->showresponses = optional_param('resp',  $this->showresponses, PARAM_BOOL);
        $this->showright     = optional_param('right', $this->showright,     PARAM_BOOL);
    }

    public function setup_from_user_preferences() {
        parent::setup_from_user_preferences();

        $this->showqtext     = get_user_preferences('quiz_report_responses_qtext', $this->showqtext);
        $this->showresponses = get_user_preferences('quiz_report_responses_resp',  $this->showresponses);
        $this->showright     = get_user_preferences('quiz_report_responses_right', $this->showright);
    }

    public function update_user_preferences() {
        parent::update_user_preferences();

        set_user_preference('quiz_report_responses_qtext', $this->showqtext);
        set_user_preference('quiz_report_responses_resp',  $this->showresponses);
        set_user_preference('quiz_report_responses_right', $this->showright);
    }

    public function resolve_dependencies() {
        parent::resolve_dependencies();

        if (!$this->showqtext && !$this->showresponses && !$this->showright) {
            // We have to show at least something.
            $this->showresponses = true;
        }

        // We only want to show the checkbox to delete attempts
        // if the user has permissions and if the report mode is showing attempts.
        $this->checkboxcolumn = has_capability('mod/quiz:deleteattempts', context_module::instance($this->cm->id))
                && ($this->attempts != quiz_attempts_report::ENROLLED_WITHOUT);
    }
}
