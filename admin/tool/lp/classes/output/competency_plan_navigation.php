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
 * User competency plan page class.
 *
 * @package    tool_lp
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use renderable;
use renderer_base;
use templatable;
use context_course;
use \core_competency\external\competency_exporter;
use stdClass;

/**
 * User competency plan navigation class.
 *
 * @package    tool_lp
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_plan_navigation implements renderable, templatable {

    /** @var userid */
    protected $userid;

    /** @var competencyid */
    protected $competencyid;

    /** @var planid */
    protected $planid;

    /** @var baseurl */
    protected $baseurl;

    /**
     * Construct.
     *
     * @param int $userid
     * @param int $competencyid
     * @param int $planid
     * @param string $baseurl
     */
    public function __construct($userid, $competencyid, $planid, $baseurl) {
        $this->userid = $userid;
        $this->competencyid = $competencyid;
        $this->planid = $planid;
        $this->baseurl = $baseurl;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $data = new stdClass();
        $data->userid = $this->userid;
        $data->competencyid = $this->competencyid;
        $data->planid = $this->planid;
        $data->baseurl = $this->baseurl;

        $plancompetencies = \core_competency\api::list_plan_competencies($data->planid);
        $data->competencies = array();
        $contextcache = array();
        foreach ($plancompetencies as $plancompetency) {
            $frameworkid = $plancompetency->competency->get_competencyframeworkid();
            if (!isset($contextcache[$frameworkid])) {
                $contextcache[$frameworkid] = $plancompetency->competency->get_context();
            }
            $context = $contextcache[$frameworkid];
            $exporter = new competency_exporter($plancompetency->competency, array('context' => $context));
            $competency = $exporter->export($output);
            if ($competency->id == $this->competencyid) {
                $competency->selected = true;
            }
            $data->competencies[] = $competency;
        }
        $data->hascompetencies = count($data->competencies);
        return $data;
    }
}
