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
 * Forum summary report filters renderable.
 *
 * @package    forumreport_summary
 * @copyright  2019 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace forumreport_summary\output;

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die();

/**
 * Forum summary report filters renderable.
 *
 * @copyright  2019 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filters implements renderable, templatable {

    /**
     * Course module the report is being run within.
     *
     * @var stdClass $cm
     */
    protected $cm;

    /**
     * Moodle URL used as the form action on the generate button.
     *
     * @var moodle_url $actionurl
     */
    protected $actionurl;

    /**
     * Details of groups available for filtering.
     * Stored in the format groupid => groupname.
     *
     * @var array $groupsavailable
     */
    protected $groupsavailable = [];

    /**
     * IDs of groups selected for filtering.
     *
     * @var array $groupsselected
     */
    protected $groupsselected = [];

    /**
     * Builds renderable filter data.
     *
     * @param stdClass $cm The course module object.
     * @param moodle_url $actionurl The form action URL.
     * @param array $filterdata (optional) Associative array of data that has been set on available filters, if any,
     *                                      in the format filtertype => [values]
     */
    public function __construct(stdClass $cm, moodle_url $actionurl, array $filterdata = []) {
        $this->cm = $cm;
        $this->actionurl = $actionurl;

        // Prepare groups filter data.
        $groupsdata = $filterdata['groups'] ?? [];
        $this->prepare_groups_data($groupsdata);
    }

    /**
     * Prepares groups data and sets relevant property values.
     *
     * @param array $groupsdata Groups selected for filtering.
     * @return void.
     */
    protected function prepare_groups_data(array $groupsdata): void {
        $groupsavailable = [];
        $groupsselected = [];

        // Only fetch groups user has access to.
        $groups = groups_get_activity_allowed_groups($this->cm);

        // Include a 'no groups' option if groups exist.
        if (!empty($groups)) {
            $nogroups = new stdClass();
            $nogroups->id = -1;
            $nogroups->name = get_string('groupsnone');
            array_push($groups, $nogroups);
        }

        foreach ($groups as $group) {
            $groupsavailable[$group->id] = $group->name;

            // Select provided groups if they are available.
            if (in_array($group->id, $groupsdata)) {
                $groupsselected[] = $group->id;
            }
        }

        // Overwrite groups properties.
        $this->groupsavailable = $groupsavailable;
        $this->groupsselected = $groupsselected;
    }


    /**
     * Export data for use as the context of a mustache template.
     *
     * @param renderer_base $renderer The renderer to be used to display report filters.
     * @return array Data in a format compatible with a mustache template.
     */
    public function export_for_template(renderer_base $renderer): stdClass {
        $output = new stdClass();

        // Set formaction URL.
        $output->actionurl = $this->actionurl->out(false);

        // Set groups filter data.
        if (!empty($this->groupsavailable)) {
            $output->hasgroups = true;

            $groupscount = count($this->groupsselected);

            if (count($this->groupsavailable) <= $groupscount) {
                $output->filtergroupsname = get_string('filter:groupscountall', 'forumreport_summary');
            } else if (!empty($this->groupsselected)) {
                $output->filtergroupsname = get_string('filter:groupscountnumber', 'forumreport_summary', $groupscount);
            } else {
                $output->filtergroupsname = get_string('filter:groupsname', 'forumreport_summary');
            }

            // Set groups filter.
            $groupsdata = [];

            foreach ($this->groupsavailable as $groupid => $groupname) {
                $groupsdata[] = [
                    'groupid' => $groupid,
                    'groupname' => $groupname,
                    'checked' => in_array($groupid, $this->groupsselected),
                ];
            }

            $output->filtergroups = $groupsdata;
        } else {
            $output->hasgroups = false;
        }

        return $output;
    }
}
