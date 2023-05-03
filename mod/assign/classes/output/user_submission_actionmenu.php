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
 * Output the user submission actionbar for this activity.
 *
 * @package   mod_assign
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assign\output;

use templatable;
use renderable;
use moodle_url;
use help_icon;
use single_button;
use stdClass;

/**
 * Output the user submission actionbar for this activity.
 *
 * @package   mod_assign
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_submission_actionmenu implements templatable, renderable {

    /** @var int The course module ID. */
    protected $cmid;
    /** @var bool Whether to show the submit button. */
    protected $showsubmit;
    /** @var bool Whether to show the edit button. */
    protected $showedit;
    /** @var stdClass A submission for this activity. */
    protected $submission;
    /** @var stdClass A team submission for this activity. */
    protected $teamsubmission;
    /** @var int The time limit for the submission. 0 = no time limit. */
    protected $timelimit;

    /**
     * Constructor for this object.
     *
     * @param int $cmid The course module ID.
     * @param bool $showsubmit Whether to show the submit button.
     * @param bool $showedit Whether to show the edit button.
     * @param stdClass|null $submission A submission for this activity.
     * @param stdClass|null $teamsubmission A team submission for this activity.
     * @param int $timelimit The time limit for completing this activity.
     */
    public function __construct(int $cmid, bool $showsubmit, bool $showedit, stdClass $submission = null,
            stdClass $teamsubmission = null, int $timelimit = 0) {

        $this->cmid = $cmid;
        $this->showsubmit = $showsubmit;
        $this->showedit = $showedit;
        $this->submission = $submission;
        $this->teamsubmission = $teamsubmission;
        $this->timelimit = $timelimit;
    }

    /**
     * Get the submission status.
     *
     * @return string The status of the submission.
     */
    protected function get_current_status(): string {
        if (!is_null($this->teamsubmission) && isset($this->teamsubmission->status)) {
            return $this->teamsubmission->status;
        } else if (!empty((array)$this->submission)) {
            return $this->submission->status;
        } else {
            return ASSIGN_SUBMISSION_STATUS_NEW;
        }
    }

    /**
     * Export the submission buttons for the page.
     *
     * @param  \renderer_base $output renderer base output.
     * @return array The data to be rendered.
     */
    public function export_for_template(\renderer_base $output): array {
        $data = ['edit' => false, 'submit' => false, 'remove' => false, 'previoussubmission' => false];
        if ($this->showedit) {
            $url = new moodle_url('/mod/assign/view.php', ['id' => $this->cmid, 'action' => 'editsubmission']);
            $button = new single_button($url, get_string('editsubmission', 'mod_assign'), 'get');
            $data['edit'] = [
                'button' => $button->export_for_template($output),
            ];
            $status = $this->get_current_status();
            if ($status !== ASSIGN_SUBMISSION_STATUS_NEW && $status !== ASSIGN_SUBMISSION_STATUS_REOPENED) {
                $url = new moodle_url('/mod/assign/view.php', ['id' => $this->cmid, 'action' => 'removesubmissionconfirm']);
                $button = new single_button($url, get_string('removesubmission', 'mod_assign'), 'get');
                $data['remove'] = ['button' => $button->export_for_template($output)];
            }
            if ($status === ASSIGN_SUBMISSION_STATUS_REOPENED) {
                $params = ['id' => $this->cmid, 'action' => 'editprevioussubmission', 'sesskey' => sesskey()];
                $url = new moodle_url('/mod/assign/view.php', $params);
                $button = new single_button($url, get_string('addnewattemptfromprevious', 'mod_assign'), 'get');
                $help = new help_icon('addnewattemptfromprevious', 'mod_assign');
                $data['previoussubmission'] = [
                    'button' => $button->export_for_template($output),
                    'help' => $help->export_for_template($output)
                ];
                $url = new moodle_url('/mod/assign/view.php', ['id' => $this->cmid, 'action' => 'editsubmission']);
                $newattemptbutton = new single_button(
                    $url,
                    get_string('addnewattempt', 'mod_assign'),
                    'get',
                    single_button::BUTTON_PRIMARY
                );
                $newattempthelp = new help_icon('addnewattempt', 'mod_assign');
                $data['edit']['button'] = $newattemptbutton->export_for_template($output);
                $data['edit']['help'] = $newattempthelp->export_for_template($output);
            }
            if ($status === ASSIGN_SUBMISSION_STATUS_NEW) {

                if ($this->timelimit && empty($this->submission->timestarted)) {
                    $confirmation = new \confirm_action(
                        get_string('confirmstart', 'assign', format_time($this->timelimit)),
                        null,
                        get_string('beginassignment', 'assign')
                    );
                    $urlparams = array('id' => $this->cmid, 'action' => 'editsubmission');
                    $beginbutton = new \action_link(
                        new moodle_url('/mod/assign/view.php', $urlparams),
                            get_string('beginassignment', 'assign'),
                            $confirmation,
                            ['class' => 'btn btn-primary']
                    );
                    $data['edit']['button'] = $beginbutton->export_for_template($output);
                    $data['edit']['begin'] = true;
                    $data['edit']['help'] = '';
                } else {
                    $newattemptbutton = new single_button(
                        $url,
                        get_string('addsubmission', 'mod_assign'),
                        'get',
                        single_button::BUTTON_PRIMARY
                    );
                    $data['edit']['button'] = $newattemptbutton->export_for_template($output);
                    $data['edit']['help'] = '';
                }
            }
        }
        if ($this->showsubmit) {
            $url = new moodle_url('/mod/assign/view.php', ['id' => $this->cmid, 'action' => 'submit']);
            $button = new single_button($url, get_string('submitassignment', 'mod_assign'), 'get', single_button::BUTTON_PRIMARY);
            $help = new help_icon('submitassignment', 'mod_assign');
            $data['submit'] = [
                'button' => $button->export_for_template($output),
                'help' => $help->export_for_template($output)
            ];
        }
        return $data;
    }
}
