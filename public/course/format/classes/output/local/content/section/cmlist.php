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
 * Contains the default activity list from a section.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\section;

use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use moodle_url;
use renderable;
use section_info;
use stdClass;

/**
 * Base class to render a section activity list.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cmlist implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /** @var array optional display options */
    protected $displayoptions;

    /** @var string the item output class name */
    protected $itemclass;

    // TODO remove movehereclass as part of MDL-83530.
    /** @var optional move here output class */
    protected $movehereclass;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param array $displayoptions optional extra display options
     */
    public function __construct(course_format $format, section_info $section, array $displayoptions = []) {
        $this->format = $format;
        $this->section = $section;
        $this->displayoptions = $displayoptions;

        // Get the necessary classes.
        $this->itemclass = $format->get_output_classname('content\\section\\cmitem');
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        global $USER;

        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $modinfo = $format->get_modinfo();
        $user = $USER;

        $data = new stdClass();
        $data->cms = [];

        // TODO remove showmovehere and the if clause as part of MDL-83530.
        $showmovehere = ismoving($course->id);
        if ($showmovehere) {
            // By default, non-ajax controls are disabled but in some places like the frontpage
            // it is necessary to display them. This is a temporal solution while JS is still
            // optional for course editing.
            $data->hascms = true;
            $data->showmovehere = true;
            $data->strmovefull = strip_tags(get_string("movefull", "", "'$user->activitycopyname'"));
            $data->movetosectionurl = new moodle_url('/course/mod.php', ['movetosection' => $section->id, 'sesskey' => sesskey()]);
            $data->movingstr = strip_tags(get_string('activityclipboard', '', $user->activitycopyname));
            $data->cancelcopyurl = new moodle_url('/course/mod.php', ['cancelcopy' => 'true', 'sesskey' => sesskey()]);
        }

        if (empty($modinfo->sections[$section->section])) {
            return $data;
        }

        foreach ($modinfo->sections[$section->section] as $modnumber) {
            $mod = $modinfo->cms[$modnumber];
            // TODO remove this if as part of MDL-83530.
            if ($showmovehere && $USER->activitycopy == $mod->id) {
                // If the old non-ajax move is necessary, we do not print the selected cm.
                continue;
            }
            if ($mod->is_visible_on_course_page() && $mod->is_of_type_that_can_display()) {
                $item = new $this->itemclass($format, $section, $mod, $this->displayoptions);
                $data->cms[] = (object)[
                    'cmitem' => $item->export_for_template($output),
                    'moveurl' => new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey())),
                ];
            }
        }

        if (!empty($data->cms)) {
            $data->hascms = true;
        }

        return $data;
    }
}
