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

namespace mod_lesson\courseformat;

use core\output\action_link;
use core\output\local\properties\button;
use core\output\local\properties\text_align;
use core\url;
use core_courseformat\output\local\overview\overviewdialog;
use lesson;
use core_calendar\output\humandate;
use core_courseformat\local\overview\overviewitem;
use cm_info;

/**
 * Class overview
 *
 * @package    mod_lesson
 * @copyright  2025 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {
    /** @var array $deadlines user's deadline for all lessons in the course. */
    private array $deadlines;

    /** @var lesson $lesson the lesson instance. */
    private lesson $lesson;

    /**
     * Constructor.
     *
     * @param cm_info $cm the course module instance.
     * @param \core\output\renderer_helper $rendererhelper the renderer helper.
     * @param \core_string_manager $stringmanager the string manager.
     */
    public function __construct(
        cm_info $cm,
        /** @var \core\output\renderer_helper $rendererhelper the renderer helper */
        protected readonly \core\output\renderer_helper $rendererhelper,
        /** @var \core_string_manager $sm the string manager */
        protected readonly \core_string_manager $stringmanager,
    ) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lesson/locallib.php');
        parent::__construct($cm);
        $this->lesson = new lesson($this->cm->get_instance_record());
        $this->deadlines = lesson_get_user_deadline($this->cm->get_course()->id);
    }

    #[\Override]
    public function get_due_date_overview(): ?overviewitem {
        $duedate = null;
        if (isset($this->deadlines[$this->lesson->id])) {
            $duedate = $this->deadlines[$this->lesson->id]->userdeadline;
        }

        return new overviewitem(
            name: $this->stringmanager->get_string('duedate', 'mod_lesson'),
            value: $duedate,
            content: $duedate ? humandate::create_from_timestamp($duedate) : '-',
        );
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        if (!has_capability('mod/lesson:manage', $this->context)) {
            return null;
        }

        $content = new action_link(
            url: new url('/mod/lesson/report.php', ['id' => $this->cm->id, 'action' => 'reportoverview']),
            text: $this->stringmanager->get_string('view', 'mod_lesson'),
            attributes: ['class' => button::BODY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: $this->stringmanager->get_string('actions'),
            value: '',
            content: $content,
            textalign: text_align::CENTER,
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'attemptedstudents' => $this->get_extra_attemptedstudents_overview(),
            'totalattempts' => $this->get_extra_totalattempts_overview(),
        ];
    }

    /**
     * Get the extra overview for attempted students.
     *
     * @return overviewitem|null The overview item (or null if the user cannot manage lesson activity).
     */
    protected function get_extra_attemptedstudents_overview(): ?overviewitem {
        if (!has_capability('mod/lesson:manage', $this->context)) {
            return null;
        }

        $groups = array_map(fn($group) => $group->id, $this->get_groups_for_filtering());

        $attemptedusers = $this->lesson->count_submitted_participants($groups);
        $totalusers = $this->lesson->count_all_participants($groups);

        return new overviewitem(
            name: $this->stringmanager->get_string('studentswhoattempted', 'mod_lesson'),
            value: $attemptedusers,
            content: $this->stringmanager->get_string(
                'count_of_total',
                'core',
                ['count' => $attemptedusers, 'total' => $totalusers]
            ),
            textalign: text_align::END,
        );
    }

    /**
     * Get the extra overview for attempted students.
     *
     * @return overviewitem|null The overview item (or null if the user cannot manage lesson activity).
     */
    protected function get_extra_totalattempts_overview(): ?overviewitem {
        if (!has_capability('mod/lesson:manage', $this->context)) {
            return null;
        }

        $groups = array_map(fn($group) => $group->id, $this->get_groups_for_filtering());

        $totalattempts = $this->lesson->count_all_submissions($groups);

        if ($this->lesson->retake) {
            $attemptedusers = $this->lesson->count_submitted_participants($groups);

            $overviewdialog = new overviewdialog(
                buttoncontent: $totalattempts,
                description: $this->stringmanager->get_string('retakesallowedinfo', 'mod_lesson'),
                definition: ['buttonclasses' => button::BODY_OUTLINE->classes() . ' dropdown-toggle'],
            );

            $averageattempts = $totalattempts ? round($totalattempts / $attemptedusers, 1) : 0;
            $overviewdialog->add_item(
                $this->stringmanager->get_string('averageattempts', 'mod_lesson'),
                $averageattempts
            );
        }

        // If the lesson does not allow retakes, do not show the dialog, only the total attepmts number.
        // And also, set the value to null, that will hide the whole column if every lesson does not allow retakes.
        return new overviewitem(
            name: $this->stringmanager->get_string('totalattepmts', 'mod_lesson'),
            value: !empty($overviewdialog) ? $totalattempts : null,
            content: $overviewdialog ?? $totalattempts,
        );
    }
}
