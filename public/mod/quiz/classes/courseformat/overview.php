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

namespace mod_quiz\courseformat;

use core\output\renderer_helper;
use core\url;
use cm_info;
use core_calendar\output\humandate;
use core_courseformat\local\overview\overviewitem;
use core\output\action_link;
use core\output\local\properties\text_align;
use core\output\local\properties\button;
use core_courseformat\output\local\overview\overviewdialog;
use mod_quiz\dates;
use mod_quiz\quiz_settings;

/**
 * Wiki overview integration.
 *
 * @package    mod_quiz
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {
    /**
     * @var quiz_settings the quiz settings object.
     */
    private quiz_settings $quizsettings;

    /**
     * Constructor.
     *
     * @param cm_info $cm the course module instance.
     * @param renderer_helper $rendererhelper the renderer helper.
     */
    public function __construct(
        cm_info $cm,
        /** @var renderer_helper $rendererhelper the renderer helper */
        protected readonly renderer_helper $rendererhelper,
    ) {
        parent::__construct($cm);
        $this->quizsettings = quiz_settings::create_for_cmid($cm->id);
    }

    #[\Override]
    public function get_due_date_overview(): ?overviewitem {
        global $USER;

        $dates = new dates($this->cm, $USER->id);
        $duedate = $dates->get_due_date();
        $name = get_string('duedate', 'quiz');

        if (empty($duedate)) {
            return new overviewitem(
                name: $name,
                value: null,
                content: '-',
            );
        }

        $content = humandate::create_from_timestamp($duedate);

        return new overviewitem(
            name: $name,
            value: $duedate,
            content: $content,
        );
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        if (!has_capability('mod/quiz:viewreports', $this->cm->context)) {
            return null;
        }
        $content = new action_link(
            url: new url(
                '/mod/quiz/report.php',
                ['id' => $this->cm->id, 'mode' => 'responses'],
            ),
            text: get_string('view'),
            attributes: ['class' => button::SECONDARY_OUTLINE->classes()],
        );
        return new overviewitem(
            name: get_string('actions'),
            value: '',
            content: $content,
            textalign: text_align::CENTER,
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        global $CFG;
        // Some extra items require global quiz functions.
        require_once($CFG->dirroot . '/mod/quiz/lib.php');

        return [
            'studentswhoattempted' => $this->get_extra_students_who_attempted_overview(),
            'totalattempts' => $this->get_extra_total_attempts_overview(),
        ];
    }

    /**
     * Get the "Students who attempted" item.
     *
     * @return overviewitem|null The overview item.
     */
    private function get_extra_students_who_attempted_overview(): ?overviewitem {
        if (!has_capability('mod/quiz:viewreports', $this->cm->context)) {
            return null;
        }
        $groups = array_map(fn($group) => $group->id, $this->get_groups_for_filtering());
        $numstudentattempted = quiz_num_users_who_attempted($this->cm, $groups);
        $numstudentwhocanattempt = quiz_num_users_who_can_attempt($this->cm, $groups);
        $studentattemptedvalue = get_string(
            'count_of_total',
            'core',
            ['count' => $numstudentattempted, 'total' => $numstudentwhocanattempt]
        );

        return new overviewitem(
            name: get_string('studentswhoattempted', 'mod_quiz'),
            value: html_to_text($studentattemptedvalue),
            content: $studentattemptedvalue,
            textalign: text_align::END,
        );
    }

    /**
     * Get the "Total attempts" item.
     *
     * @return overviewitem|null The overview item.
     */
    private function get_extra_total_attempts_overview(): ?overviewitem {
        if (!has_capability('mod/quiz:viewreports', $this->cm->context)) {
            return null;
        }
        $groups = array_map(fn($group) => $group->id, $this->get_groups_for_filtering());

        $total = quiz_num_attempts($this->cm, $groups);
        $overviewdialog = new overviewdialog(
            buttoncontent: $total,
            description: get_string('totalattempts', 'mod_quiz'),
            definition: ['buttonclasses' => button::SECONDARY_OUTLINE->classes() . ' dropdown-toggle'],
        );

        $allowedattempts = $this->quizsettings->get_quiz()->attempts;
        if ($allowedattempts == 0) {
            $allowedattempts = get_string('attemptsunlimited', 'mod_quiz');
        }
        $overviewdialog->add_item(
            get_string('allowedattemptsperstudent', 'mod_quiz'),
            $allowedattempts,
        );

        $totalattempts = quiz_num_attempts(
            $this->cm,
            $groups,
            withcapabilities: ['mod/quiz:attempt', 'mod/quiz:reviewmyattempts']
        );
        // Only attempts completed by students. If we count all attempts, including teachers,
        // the average attempts per student would be misleading.

        $attemptedusers = quiz_num_users_who_attempted($this->cm, $groups);
        $averageattempts = $totalattempts ? round($totalattempts / $attemptedusers, 1) : 0;
        $overviewdialog->add_item(
            get_string('averageattemptsperstudent', 'mod_quiz'),
            $averageattempts
        );
        return new overviewitem(
            name: get_string('totalattempts', 'mod_quiz'),
            value: $total,
            content: $overviewdialog,
            textalign: text_align::START,
        );
    }
}
