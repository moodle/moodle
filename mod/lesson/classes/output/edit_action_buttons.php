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

    /** @var int The course module ID. */
    protected $cmid;
    /** @var bool Whether the user can edit this lesson. */
    protected $canmanage;

    /**
     * Constructor for this object.
     *
     * @param int  $cmid      The course module ID.
     * @param bool $canmanage Whether the user can edit this lesson.
     */
    public function __construct(int $cmid, bool $canmanage) {
        $this->cmid = $cmid;
        $this->canmanage = $canmanage;
    }

    /**
     * Data for use with a template.
     *
     * @param \renderer_base $output Renderer information.
     * @return array Said data.
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;

        if (!$this->canmanage || !$PAGE->has_secondary_navigation()) {
            return [];
        }

        $url = new moodle_url('/mod/lesson/edit.php', ['id' => $this->cmid]);
        $editbutton = new single_button($url, get_string('edit', 'mod_lesson'), 'get', true);
        $url = new moodle_url('/mod/lesson/essay.php', ['id' => $this->cmid]);
        $essaybutton = new single_button($url, get_string('manualgrading', 'mod_lesson'), 'get');
        $data = [
            'edit' => [
                'button' => $editbutton->export_for_template($output),
            ],
            'gradeessays' => [
                'button' => $essaybutton->export_for_template($output),
            ]
        ];
        return $data;
    }

}
