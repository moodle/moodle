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
 * User competency page class.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use renderable;
use context_user;
use templatable;
use stdClass;
use tool_lp\api;
use tool_lp\external\user_competency_summary_exporter;
use tool_lp\external\plan_exporter;

/**
 * User competency page class.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_plan_page implements renderable, templatable {

    /** @var userid */
    protected $userid;

    /** @var competencyid */
    protected $competencyid;

    /** @var planid */
    protected $planid;

    /**
     * Construct.
     *
     * @param $userid
     * @param $competencyid
     * @param $planid
     */
    public function __construct($userid, $competencyid, $planid) {
        $this->userid = $userid;
        $this->competencyid = $competencyid;
        $this->planid = $planid;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {
        return \tool_lp\external::read_user_competency_summary($this->userid, $this->competencyid, $this->planid);
    }
}
