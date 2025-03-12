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

namespace mod_workshop\courseformat;

use cm_info;
use core_courseformat\local\overview\overviewitem;
use core\output\action_link;
use core\output\local\properties\text_align;
use core\output\local\properties\button;
use core\url;
use stdClass;
use workshop;

/**
 * Workshop overview integration.
 *
 * @package    mod_workshop
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {
    /** @var workshop $workshop the workshop instance. */
    private workshop $workshop;

    /** @var stdClass $activephase the active phase. */
    private stdClass $activephase;

    /**
     * Constructor.
     *
     * @param cm_info $cm the course module instance.
     */
    public function __construct(
        cm_info $cm,
    ) {
        global $CFG, $USER;

        require_once($CFG->dirroot . '/mod/workshop/locallib.php');

        parent::__construct($cm);
        $this->workshop = new workshop(
            $cm->get_instance_record(),
            $cm,
            $this->course,
            $this->context,
        );

        $userplan = new \workshop_user_plan($this->workshop, $USER->id);
        foreach ($userplan->phases as $phase) {
            if ($phase->active) {
                $this->activephase = $phase;
            }
        }
    }

    #[\Override]
    protected function get_grade_item_names(array $items): array {
        if (count($items) != 2) {
            return parent::get_grade_item_names($items);
        }
        $names = [];
        foreach ($items as $item) {
            $stridentifier = ($item->itemnumber == 0) ? 'overview_submission_grade' : 'overview_assessment_grade';
            $names[$item->id] = get_string($stridentifier, 'mod_workshop');
        }
        return $names;
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'phase' => $this->get_extra_phase_overview(),
            'deadline' => $this->get_extra_deadline_overview(),
            'submissions' => $this->get_extra_submissions_overview(),
            'assessments' => $this->get_extra_assessments_overview(),
        ];
    }

    /**
     * Get the current phase overview item.
     *
     * @return overviewitem|null An overview item, or null if the user lacks the required capability.
     */
    private function get_extra_phase_overview(): ?overviewitem {
        $currentphasetitle = '-';
        if ($this->activephase) {
            $currentphasetitle = $this->activephase->title;
        }
        return new overviewitem(
            name: get_string('phase', 'workshop'),
            value: $this->workshop->phase,
            content: $currentphasetitle,
        );
    }

    /**
     * Retrieves an overview of the deadline for the workshop.
     *
     * @return overviewitem|null An overview item, or null if the current phase does not have a deadline.
     */
    private function get_extra_deadline_overview(): ?overviewitem {
        $deadline = match ((int)$this->workshop->phase) {
            workshop::PHASE_SUBMISSION => $this->workshop->submissionend ?? 0,
            workshop::PHASE_ASSESSMENT => $this->workshop->assessmentend ?? 0,
            default => 0,
        };

        if (empty($deadline)) {
            return new overviewitem(
                name: get_string('deadline', 'workshop'),
                value: null,
                content: '-',
            );
        }

        return new overviewitem(
            name: get_string('deadline', 'workshop'),
            value: (int) $deadline,
            content: userdate($deadline),
        );
    }

    /**
     * Retrieves an overview of submissions for the workshop.
     *
     * @return overviewitem|null An overview item, or null if the user lacks the required capability.
     */
    private function get_extra_submissions_overview(): ?overviewitem {
        if (!has_capability('mod/workshop:viewallsubmissions', $this->cm->context)) {
            return null;
        }

        $submissions = $this->workshop->count_submissions();
        $total = $this->workshop->count_participants();

        if (!$total) {
            return new overviewitem(
                name: get_string('submissions', 'workshop'),
                value: 0,
                content: '-',
                textalign: text_align::CENTER,
            );
        }

        $content = get_string(
            'count_of_total',
            'core',
            ['count' => $submissions, 'total' => $total]
        );

        // If the current phase is submission, we can add a direct link.
        if ($this->workshop->phase == workshop::PHASE_SUBMISSION) {
            $content = new action_link(
                url: new url(
                    '/mod/workshop/view.php',
                    ['id' => $this->cm->id],
                    'workshop-viewlet-allsubmissions',
                ),
                text: $content,
                attributes: ['class' => button::SECONDARY_OUTLINE->classes()],
            );
        }

        return new overviewitem(
            name: get_string('submissions', 'workshop'),
            value: $submissions,
            content: $content,
            textalign: text_align::CENTER,
        );
    }

    /**
     * Retrieves an overview of assessments for the workshop.
     *
     * @return overviewitem|null An overview item, or null if the user lacks the required capability.
     */
    private function get_extra_assessments_overview(): ?overviewitem {
        global $USER;

        if (!has_capability('mod/workshop:viewallassessments', $this->cm->context)) {
            return null;
        }

        $assessments = $this->workshop->count_assessments(true);
        $total = $this->workshop->count_assessments(false);

        if (!$total) {
            return new overviewitem(
                name: get_string('assessments', 'workshop'),
                value: 0,
                content: '-',
                textalign: text_align::CENTER,
            );
        }

        $content = get_string(
            'count_of_total',
            'core',
            ['count' => $assessments, 'total' => $total]
        );

        // If the current phase is assessment, we can add a direct link.
        if ($this->workshop->phase == workshop::PHASE_ASSESSMENT) {
            $content = new action_link(
                url: new url(
                    '/mod/workshop/view.php',
                    ['id' => $this->cm->id],
                    'workshop-viewlet-gradereport',
                ),
                text: $content,
                attributes: ['class' => button::SECONDARY_OUTLINE->classes()],
            );
        }

        return new overviewitem(
            name: get_string('assessments', 'workshop'),
            value: $assessments,
            content: $content,
            textalign: text_align::CENTER,
        );
    }
}
