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

namespace mod_feedback\courseformat;

use core_courseformat\local\overview\overviewitem;
use core\output\action_link;
use core\output\local\properties\button;
use core\output\local\properties\text_align;
use core\url;
use core\output\pix_icon;

/**
 * Class overview
 *
 * @package    mod_feedback
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {
    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'submitted' => $this->get_extra_submitted_overview(),
        ];
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        global $CFG, $USER;

        if (!has_capability('mod/feedback:viewreports', $this->context)) {
            return null;
        }

        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $submissions = feedback_get_completeds_group_count(
            $this->cm->get_instance_record()
        );
        // Normalize the value.
        if (!$submissions) {
            $submissions = 0;
        }
        $total = $submissions + feedback_count_incomplete_users($this->cm);

        $content = new action_link(
            url: new url('/mod/feedback/show_entries.php', ['id' => $this->cm->id]),
            text: get_string(
                'count_of_total',
                'core',
                ['count' => $submissions, 'total' => $total]
            ),
            attributes: ['class' => button::SECONDARY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: get_string('responses', 'mod_feedback'),
            value: $submissions,
            content: $content,
            textalign: text_align::CENTER,
        );
    }

    #[\Override]
    public function get_due_date_overview(): ?overviewitem {
        $duedate = null;
        if (isset($this->cm->customdata['timeclose'])) {
            $duedate = $this->cm->customdata['timeclose'];
        }

        if (empty($duedate)) {
            return new overviewitem(
                name: get_string('feedbackclose', 'mod_feedback'),
                value: null,
                content: '-',
            );
        }
        return new overviewitem(
            name: get_string('feedbackclose', 'mod_feedback'),
            value: $duedate,
            content: userdate($duedate),
        );
    }

    /**
     * Get the submitted status overview item.
     *
     * @return overviewitem|null The overview item (or null if the user cannot complete the feedback).
     */
    private function get_extra_submitted_overview(): ?overviewitem {
        global $USER;

        if (!has_capability('mod/feedback:complete', $this->context)) {
            return null;
        }

        $structure = new \mod_feedback_structure(
            feedback: $this->cm->get_instance_record(),
            cm: $this->cm,
            courseid: $this->course->id,
            userid: $USER->id,
        );

        $value = false;
        $content = '-';

        if ($structure->is_already_submitted()) {
            $value = true;
            $content = new pix_icon(
                'i/checkedcircle',
                alt: get_string('this_feedback_is_already_submitted', 'mod_feedback'),
                attributes: ['class' => 'text-success'],
            );
        }

        return new overviewitem(
            name: get_string('responded', 'mod_feedback'),
            value: $value,
            content: $content,
            textalign: text_align::CENTER,
        );
    }
}
