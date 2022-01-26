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
 * Output the actionbar for this activity.
 *
 * @package   mod_lesson
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lesson\output;

use moodle_url;
use templatable;
use renderable;

/**
 * Output the actionbar for this activity.
 *
 * @package   mod_lesson
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_action_area implements templatable, renderable {

    /** @var int The course module ID. */
    protected $cmid;
    /** @var moodle_url The current url for the page. */
    protected $currenturl;

    /**
     * Constructor for this object.
     *
     * @param int        $cmid       The course module ID.
     * @param moodle_url $currenturl The current url for the page.
     */
    public function __construct(int $cmid, moodle_url $currenturl) {
        $this->cmid = $cmid;
        $this->currenturl = $currenturl;
    }

    /**
     * Data for use with a template.
     *
     * @param \renderer_base $output render base output.
     * @return array Said data.
     */
    public function export_for_template(\renderer_base $output): array {

        $viewurl = new moodle_url('/mod/lesson/edit.php', ['id' => $this->cmid, 'mode' => 'collapsed']);
        $fullviewurl = new moodle_url('/mod/lesson/edit.php', ['id' => $this->cmid, 'mode' => 'full']);
        $menu = [
            $viewurl->out(false) => get_string('collapsed', 'mod_lesson'),
            $fullviewurl->out(false) => get_string('full', 'mod_lesson')
        ];

        $selectmenu = new \url_select($menu, $this->currenturl->out(false), null, 'mod_lesson_navigation_select');

        return [
            'back' => [
                'text' => get_string('back', 'core'),
                'link' => (new moodle_url('/mod/lesson/view.php', ['id' => $this->cmid]))->out(false)
            ],
            'viewselect' => $selectmenu->export_for_template($output),
            'heading' => get_string('editinglesson', 'mod_lesson')
        ];
    }
}
