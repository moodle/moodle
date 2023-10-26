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
defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use core_competency\api;
use tool_lp\external\user_competency_summary_in_plan_exporter;

/**
 * User competency page class.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_summary_in_plan implements renderable, templatable {

    /** @var competencyid */
    protected $competencyid;

    /** @var planid */
    protected $planid;

    /**
     * Construct.
     *
     * @param int $competencyid
     * @param int $planid
     */
    public function __construct($competencyid, $planid) {
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
        global $DB;

        $plan = api::read_plan($this->planid);
        $pc = api::get_plan_competency($plan, $this->competencyid);
        $competency = $pc->competency;
        $usercompetency = $pc->usercompetency;
        $usercompetencyplan = $pc->usercompetencyplan;

        if (empty($competency)) {
            throw new \invalid_parameter_exception('Invalid params. The competency does not belong to the plan.');
        }

        $relatedcompetencies = api::list_related_competencies($competency->get('id'));
        $userid = $plan->get('userid');
        $user = $DB->get_record('user', array('id' => $userid));
        $evidence = api::list_evidence($userid, $this->competencyid, $plan->get('id'));

        $params = array(
            'competency' => $competency,
            'usercompetency' => $usercompetency,
            'usercompetencyplan' => $usercompetencyplan,
            'evidence' => $evidence,
            'user' => $user,
            'plan' => $plan,
            'relatedcompetencies' => $relatedcompetencies
        );
        $exporter = new user_competency_summary_in_plan_exporter(null, $params);
        $data = $exporter->export($output);

        return $data;
    }
}
