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

use renderable;
use templatable;
use stdClass;
use moodle_url;
use core_competency\api;
use core_competency\external\performance_helper;
use core_competency\plan;
use core_competency\external\competency_exporter;
use core_competency\external\plan_exporter;
use tool_lp\external\competency_path_exporter;

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
        $planexporter = new plan_exporter($this->plan, array('template' => $this->plan->get_template()));

        $data = new stdClass();
        $data->plan = $planexporter->export($output);
        $data->competencies = array();
        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(false);
        $data->contextid = $this->plan->get_context()->id;

        if ($data->plan->iscompleted) {
            $ucproperty = 'usercompetencyplan';
            $ucexporter = 'core_competency\\external\\user_competency_plan_exporter';
        } else {
            $ucproperty = 'usercompetency';
            $ucexporter = 'core_competency\\external\\user_competency_exporter';
        }

        $helper = new performance_helper();
        $pclist = api::list_plan_competencies($this->plan);
        $proficientcount = 0;
        foreach ($pclist as $pc) {
            $comp = $pc->competency;
            $usercomp = $pc->$ucproperty;

            $compcontext = $helper->get_context_from_competency($comp);
            $framework = $helper->get_framework_from_competency($comp);
            $scale = $helper->get_scale_from_competency($comp);

            // Prepare the data.
            $record = new stdClass();
            $exporter = new competency_exporter($comp, array('context' => $compcontext));
            $record->competency = $exporter->export($output);

            // Competency path.
            $exporter = new competency_path_exporter([
                'ancestors' => $comp->get_ancestors(),
                'framework' => $framework,
                'context' => $compcontext
            ]);
            $record->comppath = $exporter->export($output);

            $exporter = new $ucexporter($usercomp, array('scale' => $scale));
            $record->$ucproperty = $exporter->export($output);

            $data->competencies[] = $record;
            if ($usercomp->get('proficiency')) {
                $proficientcount++;
            }
        }
        $data->competencycount = count($data->competencies);
        $data->proficientcompetencycount = $proficientcount;
        if ($data->competencycount) {
            $data->proficientcompetencypercentage = ((float) $proficientcount / (float) $data->competencycount) * 100.0;
        } else {
            $data->proficientcompetencypercentage = 0.0;
        }
        $data->proficientcompetencypercentageformatted = format_float($data->proficientcompetencypercentage);
        return $data;
    }
}
