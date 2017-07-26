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
 * File containing the class activity navigation renderable.
 *
 * @package    core_course
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;

/**
 * The class activity navigation renderable.
 *
 * @package    core_course
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_navigation implements renderable, templatable {

    /**
     * @var string The html for the prev link
     */
    public $prevlink = '';

    /**
     * @var string The html for the next link
     */
    public $nextlink = '';

    /**
     * Constructor.
     *
     * @param \cm_info|null $prevmod The previous module to display, null if none.
     * @param \cm_info|null $nextmod The previous module to display, null if none.
     */
    public function __construct($prevmod, $nextmod) {
        global $OUTPUT;

        // Check if there is a previous module to display.
        if ($prevmod) {
            $linkname = $prevmod->name;
            if (!$prevmod->visible) {
                $linkname .= ' ' . get_string('hiddenwithbrackets');
            }

            $link = new \action_link($prevmod->url, $OUTPUT->larrow() . ' ' . $linkname);
            $this->prevlink = $OUTPUT->render($link);
        }

        // Check if there is a next module to display.
        if ($nextmod) {
            $linkname = $nextmod->name;
            if (!$nextmod->visible) {
                $linkname .= ' ' . get_string('hiddenwithbrackets');
            }

            $link = new \action_link($nextmod->url, $linkname . ' ' . $OUTPUT->rarrow());
            $this->nextlink = $OUTPUT->render($link);
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output Renderer base.
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        $data->prevlink = $this->prevlink;
        $data->nextlink = $this->nextlink;

        return $data;
    }
}
