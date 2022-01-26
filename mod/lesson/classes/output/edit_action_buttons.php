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
 * Output the action buttons for this activity.
 *
 * @package   mod_lesson
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lesson\output;

use core\output\notification;
use moodle_url;
use templatable;
use renderable;
use single_button;

/**
 * Output the action buttons for this activity.
 *
 * @package   mod_lesson
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_action_buttons implements templatable, renderable {

    /** @var \lesson The lesson object. */
    protected $lesson;
    /** @var int The currently viewed lesson page id. */
    protected $currentpage;

    /**
     * Constructor for this object.
     *
     * @param \lesson $lesson The lesson object.
     * @param int|null $currentpage The current lesson page that is being viewed
     */
    public function __construct(\lesson $lesson, ?int $currentpage = null) {
        $this->lesson = $lesson;
        $this->currentpage = $currentpage;
    }

    /**
     * Sets the current page being viewed.
     *
     * @param int|null $page
     */
    public function set_currentpage(?int $page) {
        $this->currentpage = $page;
    }

    /**
     * Data for use with a template.
     *
     * @param \renderer_base $output Renderer information.
     * @return array Said data.
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;

        $data = [];

        // A shortcut to edit the lesson's question page.
        if (has_capability('mod/lesson:edit', $this->lesson->context) &&
                !empty($this->currentpage) && $this->currentpage != LESSON_EOL) {
            $url = new moodle_url('/mod/lesson/editpage.php', [
                'id'       => $this->lesson->get_cm()->id,
                'pageid'   => $this->currentpage,
                'edit'     => 1,
                'returnto' => $PAGE->url->out_as_local_url(false)
            ]);
            $editcontent = new single_button($url, get_string('editpagecontent', 'lesson'));
            $data['editcontents']['button'] = $editcontent->export_for_template($output);
        }

        if ($this->lesson->can_manage()) {
            $url = new moodle_url('/mod/lesson/edit.php', ['id' => $this->lesson->get_cm()->id]);
            $editbutton = new single_button($url, get_string('editlesson', 'mod_lesson'), 'get', true);
            $url = new moodle_url('/mod/lesson/essay.php', ['id' => $this->lesson->get_cm()->id]);
            $essaybutton = new single_button($url, get_string('manualgrading', 'mod_lesson'), 'get');
            $data += [
                'edit' => [
                    'button' => $editbutton->export_for_template($output),
                ],
                'gradeessays' => [
                    'button' => $essaybutton->export_for_template($output),
                ]
            ];
        }

        // Standard notification to indicate the lesson is being previewed.
        if ($data) {
            $message = new notification(get_string('lessonbeingpreviewed', 'mod_lesson'), notification::NOTIFY_INFO);
            $data['notification'] = $message->export_for_template($output);
        }
        return $data;
    }
}
