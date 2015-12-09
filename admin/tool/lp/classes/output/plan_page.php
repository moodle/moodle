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
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use templatable;
use stdClass;
use tool_lp\api;
use tool_lp\plan;
use tool_lp\external\competency_exporter;
use tool_lp\external\plan_exporter;

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

        $planexporter = new plan_exporter($this->plan, array('template' => $this->plan->get_template()));

        $data = new stdClass();
        $data->plan = $planexporter->export($output);
        $data->competencies = array();
        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(false);
        $data->contextid = $this->plan->get_context()->id;

        if ($data->plan->iscompleted) {
            $ucproperty = 'usercompetencyplan';
            $ucexporter = 'tool_lp\\external\\user_competency_plan_exporter';
        } else {
            $ucproperty = 'usercompetency';
            $ucexporter = 'tool_lp\\external\\user_competency_exporter';
        }

        $pclist = api::list_plan_competencies($this->plan);
        foreach ($pclist as $pc) {
            $comp = $pc->competency;
            $usercomp = $pc->$ucproperty;

            // Get the framework.
            if (!isset($frameworks[$comp->get_competencyframeworkid()])) {
                $frameworks[$comp->get_competencyframeworkid()] = $comp->get_framework();
            }
            $framework = $frameworks[$comp->get_competencyframeworkid()];

            // Get the scale.
            $scaleid = $comp->get_scaleid();
            if ($scaleid === null) {
                $scaleid = $framework->get_scaleid();
            }
            if (!isset($scales[$framework->get_scaleid()])) {
                $scales[$framework->get_scaleid()] = $framework->get_scale();
            }
            $scale = $scales[$framework->get_scaleid()];

            // Prepare the data.
            $record = new stdClass();
            $exporter = new competency_exporter($comp, array('context' => $framework->get_context()));
            $record->competency = $exporter->export($output);
            $exporter = new $ucexporter($usercomp, array('scale' => $scale));
            $record->$ucproperty = $exporter->export($output);

            $data->competencies[] = $record;
        }
        return $data;
    }
}
