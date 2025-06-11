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
 * Class for exporting user competency data with all the evidence in a course.
 *
 * @package    report_lpmonitoring
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use core_competency\api;
use core_competency\course_module_competency;
use core_competency\user_competency;
use core_competency\external\plan_exporter;
use core_course\external\course_module_summary_exporter;
use core_course\external\course_summary_exporter;
use context_course;
use renderer_base;
use stdClass;
use moodle_url;
use tool_lp\external\user_competency_summary_exporter;
use tool_lp\external\user_competency_summary_in_course_exporter;

/**
 * Class for exporting user competency data with all the evidence in a course.
 *
 * @package    report_lpmonitoring
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lpmonitoring_user_competency_summary_in_course_exporter extends user_competency_summary_in_course_exporter {
    /**
     * This function redefines the parent function by adding a try catch around list_course_modules_using_competency,
     * to avoid the course validation (we want to list course modules even if the course is hidden).
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output) {
        // Arrays are copy on assign.
        $related = $this->related;
        $result = new stdClass();
        // Remove course from related as it is not wanted by the user_competency_summary_exporter.
        unset($related['course']);
        $related['usercompetencyplan'] = null;
        $related['usercompetency'] = null;
        $exporter = new user_competency_summary_exporter(null, $related);
        $result->usercompetencysummary = $exporter->export($output);
        $result->usercompetencysummary->cangrade = user_competency::can_grade_user_in_course($this->related['user']->id,
            $this->related['course']->id);

        $context = context_course::instance($this->related['course']->id);
        $exporter = new course_summary_exporter($this->related['course'], ['context' => $context]);
        $result->course = $exporter->export($output);

        // This is the block different from the parent, to avoid some validations.
        try {
            $coursemodules = api::list_course_modules_using_competency($this->related['competency']->get('id'),
                $this->related['course']->id);
        } catch (\Exception $e) {
            // Special case for hidden courses.
            $coursemodules = course_module_competency::list_course_modules($this->related['competency']->get('id'),
                $this->related['course']->id);
        }

        $fastmodinfo = get_fast_modinfo($this->related['course']->id);
        $exportedmodules = [];
        foreach ($coursemodules as $cm) {
            $cminfo = $fastmodinfo->cms[$cm];
            $cmexporter = new course_module_summary_exporter(null, ['cm' => $cminfo]);
            $exportedmodules[] = $cmexporter->export($output);
        }
        $result->coursemodules = $exportedmodules;

        // User learning plans.
        $plans = api::list_plans_with_competency($this->related['user']->id, $this->related['competency']);
        $exportedplans = [];
        foreach ($plans as $plan) {
            $planexporter = new plan_exporter($plan, ['template' => $plan->get_template()]);
            $exportedplans[] = $planexporter->export($output);
        }
        $result->plans = $exportedplans;
        $result->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(true);

        return (array) $result;
    }
}
