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
        $options = array('context' => $this->plan->get_context());
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
            $competency = $comp->to_record();
            $competency->descriptionformatted = format_text($competency->description, $competency->descriptionformat, $options);
            $usercompetency = $usercomp->to_record();
            $competency->$ucproperty = $usercompetency;

            if ($usercompetency->grade === null) {
                $gradename = '-';
            } else {
                $gradename = format_string($scale->scale_items[$usercompetency->grade - 1], null, $options);
            }

            if ($usercompetency->proficiency === null) {
                $proficiencyname = '-';
            } else {
                $proficiencyname = get_string($usercompetency->proficiency ? 'yes' : 'no');
            }

            // We don't want to show user competency status when plan is completed.
            if (!$data->iscompleted) {
                $statusname = '-';
                if ($usercompetency->status != user_competency::STATUS_IDLE) {
                    $statusname = (string) user_competency::get_status_name($usercompetency->status);
                }
                $usercompetency->statusname = $statusname;
            } else {
                // Assign planid if plan is completed.
                $usercompetency->planid = $this->plan->get_id();
            }

            $usercompetency->gradename = $gradename;
            $usercompetency->proficiencyname = $proficiencyname;

            $data->competencies[] = $competency;
        }
        return $data;
    }
}
