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

namespace mod_scorm\courseformat;

use cm_info;
use core_calendar\output\humandate;
use core_courseformat\local\overview\overviewitem;
use core_courseformat\output\local\overview\overviewdialog;
use core\output\action_link;
use core\output\local\properties\text_align;
use core\output\local\properties\button;
use core\url;
use mod_data\dates;
use mod_scorm\manager;

/**
 * SCORM activity overview integration.
 *
 * @package    mod_scorm
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {
    /**
     * @var manager the SCORM manager instance.
     */
    protected manager $manager;

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
        parent::__construct($cm);
        $this->manager = manager::create_from_coursemodule($cm);
    }

    #[\Override]
    public function get_due_date_overview(): ?overviewitem {
        global $USER;

        $dates = dates::get_dates_for_module($this->cm, $USER->id);
        $duedate = null;
        foreach ($dates as $date) {
            if ($date['dataid'] === 'timeclose') {
                $duedate = $date['timestamp'];
                break;
            }
        }

        if (empty($duedate)) {
            return new overviewitem(
                name: get_string('duedate', 'assign'),
                value: null,
                content: '-',
            );
        }

        $content = humandate::create_from_timestamp($duedate);

        return new overviewitem(
            name: get_string('duedate', 'assign'),
            value: $duedate,
            content: $content,
        );
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        if (!$this->manager->can_view_reports()) {
            return null;
        }

        $viewresults = get_string('view');
        $content = new action_link(
            url: new url('/mod/scorm/report.php', ['id' => $this->cm->id]),
            text: $viewresults,
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
        return [
            'attempted' => $this->get_extra_studentsattempted_overview(),
            'totalattempts' => $this->get_extra_totalattempts_overview(),
        ];
    }

    /**
     * Get the students who attempted.
     *
     * @return overviewitem|null The overview item.
     */
    private function get_extra_studentsattempted_overview(): ?overviewitem {
        if (!$this->manager->can_view_reports()) {
            return null;
        }
        // Get the number of users who attempted the SCORM activity depending on group mode.
        $groups = array_map(fn($group) => $group->id, $this->get_groups_for_filtering());
        $attemptcount = $this->manager->count_users_who_attempted($groups);
        $participantscount = $this->manager->count_participants($groups);
        $params = [
            'count' => $attemptcount,
            'total' => $participantscount,
        ];
        $content = get_string('count_of_total', 'core', $params);
        return new overviewitem(
            name: get_string('studentattempted', 'mod_scorm'),
            value: $attemptcount,
            content: $content,
            textalign: text_align::END,
        );
    }

    /**
     * Get the "Total attempts" colum data.
     *
     * @return overviewitem|null The overview item.
     */
    private function get_extra_totalattempts_overview(): ?overviewitem {
        if (!$this->manager->can_view_reports()) {
            return null;
        }
        $groups = array_map(fn($group) => $group->id, $this->get_groups_for_filtering());
        $maxattempts = $this->manager->get_max_attempts();
        if ($maxattempts === 0) {
            $maxattemptstext = get_string('unlimited');
        } else {
            $maxattemptstext = (string) $maxattempts;
        }
        $totalattempts = $this->manager->count_all_attempts($groups);
        $attemptedusers = $this->manager->count_users_who_attempted($groups);
        $averageattempts = $totalattempts ? round($totalattempts / $attemptedusers, 1) : 0;

        $content = new overviewdialog(
            buttoncontent: $totalattempts,
            title: get_string('totalattempts', 'mod_scorm'),
            definition: ['buttonclasses' => button::SECONDARY_OUTLINE->classes() . ' dropdown-toggle'],
        );
        $gradingmethod = $this->manager->get_grading_method();
        $content->add_item(
            get_string('gradingmethod', 'grading'),
            $gradingmethod ?? '-',
        );

        $content->add_item(get_string('allowedattemptsstudent', 'mod_scorm'), $maxattemptstext);
        $content->add_item(get_string('averageattemptperstudent', 'mod_scorm'), $averageattempts);

        return new overviewitem(
            name: get_string('totalattempts', 'mod_scorm'),
            value: $totalattempts,
            content: $content,
            textalign: text_align::START,
        );
    }
}
