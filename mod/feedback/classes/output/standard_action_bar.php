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

namespace mod_feedback\output;

use action_link;
use moodle_url;

/**
 * Class standard_action_bar
 *
 * The default tertiary nav on the module landing page
 *
 * @copyright 2021 Peter Dias
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */
class standard_action_bar extends base_action_bar {
    /** @var int $startpage The page to resume with. */
    private $startpage;
    /** @var int $viewcompletion Whether or not the user can finish the feedback */
    private $viewcompletion;

    /**
     * standard_action_bar constructor.
     *
     * @param int $cmid
     * @param bool $viewcompletion Whether or not the user can finish the feedback
     * @param int|null $startpage The page to resume with.
     * @param int|null $courseid The course that the feedback is being accessed from. If null, courseid will be
     *                              set via the $cmid relationship
     */
    public function __construct(int $cmid, bool $viewcompletion, ?int $startpage = null, ?int $courseid = null) {
        parent::__construct($cmid);
        $this->startpage = $startpage;
        $this->viewcompletion = $viewcompletion;
        if ($courseid && $courseid != $this->course->id) {
            $this->course = get_course($courseid);
        }
        $this->urlparams['courseid'] = $this->course->id;
    }

    /**
     * Return the items to be used in the tertiary nav
     *
     * @return array
     */
    public function get_items(): array {
        $items = [];

        if (has_capability('mod/feedback:edititems', $this->context)) {
            $editurl = new moodle_url('/mod/feedback/edit.php', $this->urlparams);
            $items['left'][]['actionlink'] = new action_link($editurl, get_string('edit_items', 'feedback'),
                null, ['class' => 'btn btn-secondary']);
        }

        // The preview icon should be displayed only to users with capability to edit or view reports (to include
        // non-editing teachers too).
        $capabilities = [
            'mod/feedback:edititems',
            'mod/feedback:viewreports',
        ];
        if (has_any_capability($capabilities, $this->context)) {
            $previewlnk = new moodle_url('/mod/feedback/print.php', array('id' => $this->cmid));
            if ($this->course->id) {
                $previewlnk->param('courseid', $this->course->id);
            }
            $items['left'][]['actionlink'] = new action_link($previewlnk, get_string('previewquestions', 'feedback'),
            null, ['class' => 'btn btn-secondary']);
        }

        if ($this->viewcompletion) {
            // Display a link to complete feedback or resume.
            $completeurl = new moodle_url('/mod/feedback/complete.php',
                ['id' => $this->cmid, 'courseid' => $this->course->id]);
            if ($this->startpage) {
                $completeurl->param('gopage', $this->startpage);
                $label = get_string('continue_the_form', 'feedback');
            } else {
                $label = get_string('complete_the_form', 'feedback');
            }
            $items['left'][]['actionlink'] = new action_link($completeurl, $label, null, ['class' => 'btn btn-secondary']);
        }

        return $items;
    }
}
