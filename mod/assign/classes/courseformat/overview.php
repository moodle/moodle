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

namespace mod_assign\courseformat;

use assign;
use cm_info;
use core_courseformat\local\overview\overviewitem;
use core\output\action_link;
use core\output\local\properties\text_align;
use core\output\local\properties\button;
use core\url;
use mod_assign\dates;

/**
 * Assignment overview integration.
 *
 * @package    mod_assign
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {
    /** @var assign $assign the assign instance. */
    private assign $assign;

    /**
     * Constructor.
     *
     * @param cm_info $cm the course module instance.
     * @param \core\output\renderer_helper $rendererhelper the renderer helper.
     */
    public function __construct(
        cm_info $cm,
        /** @var \core\output\renderer_helper $rendererhelper the renderer helper */
        protected readonly \core\output\renderer_helper $rendererhelper,
    ) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        parent::__construct($cm);
        $this->assign = new assign($this->context, $this->cm, $this->cm->get_course());
    }

    #[\Override]
    public function get_due_date_overview(): ?overviewitem {
        global $USER;

        $dates = new dates($this->cm, $USER->id);
        $duedate = $dates->get_due_date();

        if (empty($duedate)) {
            return new overviewitem(
                name: get_string('duedate', 'assign'),
                value: null,
                content: '-',
            );
        }

        $content = userdate($duedate);

        return new overviewitem(
            name: get_string('duedate', 'assign'),
            value: $duedate,
            content: $content,
        );
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        if (!has_capability('mod/assign:grade', $this->context)) {
            return null;
        }
        $needgrading = $this->assign->count_submissions_need_grading();

        $alertlabel = get_string('numberofsubmissionsneedgrading', 'assign');
        $name = get_string('gradeverb');

        $badge = '';
        if ($needgrading > 0) {
            $renderer = $this->rendererhelper->get_core_renderer();
            $badge = $renderer->notice_badge(
                contents: $needgrading,
                title: $alertlabel,
            );
        }

        $content = new action_link(
            url: new url('/mod/assign/view.php', ['id' => $this->cm->id, 'action' => 'grading']),
            text: $name . $badge,
            attributes: ['class' => button::SECONDARY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: get_string('actions'),
            value: $name,
            content: $content,
            textalign: text_align::CENTER,
            alertcount: $needgrading,
            alertlabel: $alertlabel,
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'submissions' => $this->get_extra_submissions_overview(),
            'submissionstatus' => $this->get_extra_submission_status_overview(),
        ];
    }

    /**
     * Retrieves an overview of submissions for the assignment.
     *
     * @return overviewitem|null An overview item c, or null if the user lacks the required capability.
     */
    private function get_extra_submissions_overview(): ?overviewitem {
        global $USER;

        if (!has_capability('mod/assign:grade', $this->cm->context)) {
            return null;
        }

        $activitygroup = groups_get_activity_group($this->cm);
        $submissions = $this->assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED);
        $total = $this->assign->count_participants($activitygroup);

        $content = new action_link(
            url: new url('/mod/assign/view.php', ['id' => $this->cm->id, 'action' => 'grading']),
            text: get_string(
                'count_of_total',
                'core',
                ['count' => $submissions, 'total' => $total]
            ),
            attributes: ['class' => button::SECONDARY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: get_string('submissions', 'assign'),
            value: $submissions,
            content: $content,
            textalign: text_align::CENTER,
        );
    }

    /**
     * Retrieves the submission status overview for the current user.
     *
     * @return overviewitem|null The overview item, or null if the user does not have the required capabilities.
     */
    private function get_extra_submission_status_overview(): ?overviewitem {
        global $USER;

        if (
            !has_capability('mod/assign:submit', $this->context)
            || has_capability('moodle/site:config', $this->context)
        ) {
            return null;
        }

        if ($this->assign->get_instance()->teamsubmission) {
            $usersubmission = $this->assign->get_group_submission($USER->id, 0, false);
        } else {
            $usersubmission = $this->assign->get_user_submission($USER->id, false);
        }

        if (!empty($usersubmission->status)) {
            $submittedstatus = get_string('submissionstatus_' . $usersubmission->status, 'assign');
        } else {
            $submittedstatus = get_string('submissionstatus_', 'assign');
        }

        return new overviewitem(
            name: get_string('submissionstatus', 'assign'),
            value: $submittedstatus,
            content: $submittedstatus,
        );
    }
}
