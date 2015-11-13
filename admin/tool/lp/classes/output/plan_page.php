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
 * Plan page output.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use renderable;
use templatable;
use stdClass;
use tool_lp\api;
use tool_lp\plan;
use tool_lp\user_competency;
use tool_lp\external\user_competency_exporter;
use tool_lp\external\competency_exporter;

/**
 * Plan page class.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_page implements renderable, templatable {

    /** @var plan */
    protected $plan;

    /**
     * Construct.
     *
     * @param plan $plan
     */
    public function __construct($plan) {
        $this->plan = $plan;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $frameworks = array();
        $scales = array();

        $data = new stdClass();
        $data->competencies = array();
        $data->planid = $this->plan->get_id();
        $data->canmanage = $this->plan->can_manage() && !$this->plan->is_based_on_template();
        $data->contextid = $this->plan->get_context()->id;

        $pclist = api::list_plan_competencies($this->plan);

        $data->iscompleted = $this->plan->get_status() == plan::STATUS_COMPLETE;
        if ($data->iscompleted) {
            $ucproperty = 'usercompetencyplan';
        } else {
            $ucproperty = 'usercompetency';
        }

        foreach ($pclist as $pc) {
            $comp = $pc->competency;
            $usercomp = $pc->$ucproperty;

            if (!isset($frameworks[$comp->get_competencyframeworkid()])) {
                $frameworks[$comp->get_competencyframeworkid()] = $comp->get_framework();
            }
            $framework = $frameworks[$comp->get_competencyframeworkid()];
            if (!isset($scales[$framework->get_scaleid()])) {
                $scales[$framework->get_scaleid()] = $framework->get_scale();
            }
            $scale = $scales[$framework->get_scaleid()];

            // Prepare the data.
            $exporter = new competency_exporter($comp, array('context' => $framework->get_context()));
            $competency = $exporter->export($output);
            $exporter = new user_competency_exporter($usercomp, array('scale' => $scale));
            $competency->usercompetency = $exporter->export($output);

            $data->competencies[] = $competency;
        }
        return $data;
    }
}
