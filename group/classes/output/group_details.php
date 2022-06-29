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
 * Group details page.
 *
 * @package    core_group
 * @copyright  2017 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_group\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;
use context_course;
use moodle_url;

/**
 * Group details page class.
 *
 * @package    core_group
 * @copyright  2017 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_details implements renderable, templatable {

    /** @var stdClass $group An object with the group information. */
    protected $group;

    /**
     * group_details constructor.
     *
     * @param  int $groupid Group ID to show details of.
     */
    public function __construct($groupid) {
        $this->group = groups_get_group($groupid, '*', MUST_EXIST);
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        if (!empty($this->group->description) || (!empty($this->group->picture))) {
            $context = context_course::instance($this->group->courseid);
            $description = file_rewrite_pluginfile_urls($this->group->description,
                                                        'pluginfile.php',
                                                        $context->id,
                                                        'group',
                                                        'description',
                                                        $this->group->id);

            $descriptionformat = $this->group->descriptionformat ?? FORMAT_MOODLE;
            $options = [
                'overflowdiv' => true,
                'context'     => $context
            ];

            $data = new stdClass();
            $data->name = format_string($this->group->name, true, ['context' => $context]);
            $data->pictureurl = get_group_picture_url($this->group, $this->group->courseid, true);
            $data->description = format_text($description, $descriptionformat, $options);

            if (has_capability('moodle/course:managegroups', $context)) {
                $url = new moodle_url('/group/group.php', ['id' => $this->group->id, 'courseid' => $this->group->courseid]);
                $data->editurl = $url->out(false);
            }

            return $data;
        } else {
            return;
        }
    }
}
