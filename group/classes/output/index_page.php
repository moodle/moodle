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
 * Group index page.
 *
 * @package    core_group
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_group\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Group index page class.
 *
 * @package    core_group
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index_page implements renderable, templatable {

    /** @var int $courseid The course ID. */
    public $courseid;

    /** @var array The array of groups to be rendered. */
    public $groups;

    /** @var string The name of the currently selected group. */
    public $selectedgroupname;

    /** @var array The array of group members to be rendered, if a group is selected. */
    public $selectedgroupmembers;

    /** @var bool Whether to disable the add members/edit group buttons. */
    public $disableaddedit;

    /** @var bool Whether to disable the delete group button. */
    public $disabledelete;

    /** @var array Groups that can't be deleted by the user. */
    public $undeletablegroups;

    /** @var bool Whether to show/hide the messaging setting buttons. */
    public $messagingsettingsvisible;

    /**
     * index_page constructor.
     *
     * @param int $courseid The course ID.
     * @param array $groups The array of groups to be rendered.
     * @param string $selectedgroupname The name of the currently selected group.
     * @param array $selectedgroupmembers The array of group members to be rendered, if a group is selected.
     * @param bool $disableaddedit Whether to disable the add members/edit group buttons.
     * @param bool $disabledelete Whether to disable the delete group button.
     * @param array $undeletablegroups Groups that can't be deleted by the user.
     * @param bool $messagingsettingsvisible If the messaging settings buttons should be visible.
     */
    public function __construct($courseid, $groups, $selectedgroupname, $selectedgroupmembers, $disableaddedit, $disabledelete,
                                $undeletablegroups, $messagingsettingsvisible) {
        $this->courseid = $courseid;
        $this->groups = $groups;
        $this->selectedgroupname = $selectedgroupname;
        $this->selectedgroupmembers = $selectedgroupmembers;
        $this->disableaddedit = $disableaddedit;
        $this->disabledelete = $disabledelete;
        $this->undeletablegroups = $undeletablegroups;
        $this->messagingsettingsvisible = $messagingsettingsvisible;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $data = new stdClass();

        // Variables that will be passed to the JS helper.
        $data->courseid = $this->courseid;
        $data->wwwroot = $CFG->wwwroot;
        // To be passed to the JS init script in the template. Encode as a JSON string.
        $data->undeletablegroups = json_encode($this->undeletablegroups);

        // Some buttons are enabled if single group selected.
        $data->addmembersdisabled = $this->disableaddedit;
        $data->editgroupsettingsdisabled = $this->disableaddedit;
        $data->deletegroupdisabled = $this->disabledelete;
        $data->groups = $this->groups;
        $data->members = $this->selectedgroupmembers;
        $data->selectedgroup = $this->selectedgroupname;
        $data->messagingsettingsvisible = $this->messagingsettingsvisible;

        return $data;
    }
}
