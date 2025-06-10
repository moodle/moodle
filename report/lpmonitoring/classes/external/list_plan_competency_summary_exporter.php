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
 * Class for exporting data for the plan competency summary.
 *
 * @package    report_lpmonitoring
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use context_system;
use core\external\exporter;
use renderer_base;
use report_lpmonitoring\api;

/**
 * Class for exporting data for the plan competency summary.
 *
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_plan_competency_summary_exporter extends exporter {

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    public static function define_other_properties() {
        return array(
            'competencies_list' => array(
                'type' => competency_summary_evaluations_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'scale' => array(
                'type' => scale_competency_item_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'scaleid' => array(
                'type' => PARAM_INT
            ),
            'scalename' => array(
                'type' => PARAM_TEXT
            )
        );
    }

    /**
     * Returns a list of objects that are related to this persistent.
     *
     * Only objects listed here can be cached in this object.
     *
     * The class name can be suffixed:
     * - with [] to indicate an array of values.
     * - with ? to indicate that 'null' is allowed.
     *
     * @return array of 'propertyname' => array('type' => classname, 'required' => true)
     */
    protected static function define_related() {
        // We cache the plan so it does not need to be retrieved every time.
        return array('plan' => 'core_competency\\plan',
                    'scalevalues' => '\\stdClass[]',
                    'scale' => 'grade_scale');
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $resultcompetencies = $this->data;
        $plan = $this->related['plan'];
        $scale = $this->related['scale'];
        $scalevalues = $this->related['scalevalues'];

        $result = array();
        $result['scaleid'] = $scale->id;
        $result['scalename'] = $scale->name;

        $relatedinfo = new \stdClass();
        foreach ($scalevalues as $scalevalue) {
            $scalecompetencyitemexporter = new scale_competency_item_exporter($scalevalue, array('courses' => array(),
                    'relatedinfo' => $relatedinfo, 'cms' => array()));
            $result['scale'][] = $scalecompetencyitemexporter->export($output);
        }
        $result['competencies_list'] = array();
        $helper = new \core_competency\external\performance_helper();
        $parents = [];
        foreach ($resultcompetencies as $key => $r) {
            $comp = new \core_competency\competency($r->competency->id);
            $scalecmp = $helper->get_scale_from_competency($comp);
            // Get only competencies match scale.
            if (empty($r->isparent) && $scalecmp->id !== $scale->id) {
                continue;
            }
            if (isset($r->isparent) && $r->isparent && !$r->isassessable) {
                $compdetail = new \stdClass();
                $compdetail->competency = $comp;
                $compdetail->usercompetency = null;
            } else {
                $usercomp = (isset($r->usercompetency)) ? $r->usercompetency : $r->usercompetencyplan;
                $r->competencydetail = api::get_competency_detail($plan->get('userid'), $usercomp->competencyid, $plan->get('id'));
            }

            $data = new \stdClass();
            $data->allcourses = array();
            $data->competencydetailinfos = $r;

            $data->isassessable = (isset($r->isassessable) && $r->isassessable) ? $r->isassessable : false;
            $data->showasparent = (isset($r->isparent) && $r->isparent) ? $r->isparent : false;
            $exporter = new competency_summary_evaluations_exporter($data, ['plan' => $plan, 'scalevalues' => $scalevalues]);
            $exportedcompetency = $exporter->export($output);
            $result['competencies_list'][] = $exportedcompetency;
            // Get total in parent competency.
            if (empty($r->isparent)) {
                if (!empty($r->competency->firstlevelparentid)) {
                    if (empty($parents[$r->competency->firstlevelparentid])) {
                        $parents[$r->competency->firstlevelparentid] = ['total' => [], 'cm' => [], 'course' => [],
                            'total_self' => [], 'cm_self' => [], 'course_self' => []];
                    }

                    foreach ($exportedcompetency->evaluationslist_total as $key => $value) {
                        $number = (isset($parents[$r->competency->firstlevelparentid]['total'][$key])) ?
                                $parents[$r->competency->firstlevelparentid]['total'][$key] : 0;
                        $number += $value->number;
                        $parents[$r->competency->firstlevelparentid]['total'][$key] = $number;
                    }

                    foreach ($exportedcompetency->evaluationslist_course as $key => $value) {
                        $number = (isset($parents[$r->competency->firstlevelparentid]['course'][$key])) ?
                                $parents[$r->competency->firstlevelparentid]['course'][$key] : 0;
                        $number += $value->number;
                        $parents[$r->competency->firstlevelparentid]['course'][$key] = $number;
                    }
                    if (api::is_cm_comptency_grading_enabled()) {
                        foreach ($exportedcompetency->evaluationslist_cm as $key => $value) {
                            $number = (isset($parents[$r->competency->firstlevelparentid]['cm'][$key])) ?
                                    $parents[$r->competency->firstlevelparentid]['cm'][$key] : 0;
                            $number += $value->number;
                            $parents[$r->competency->firstlevelparentid]['cm'][$key] = $number;
                        }
                    }
                }
            } else {
                // If this is a parent.
                if (empty($parents[$r->competency->id])) {
                    $parents[$r->competency->id] = ['total' => [], 'cm' => [], 'course' => [],
                        'total_self' => [], 'cm_self' => [], 'course_self' => []];
                }
                $isgoodscale = ($scalecmp->id == $scale->id);
                foreach ($exportedcompetency->evaluationslist_total as $key => $value) {
                    $parents[$r->competency->id]['total_self'][$key] = ($isgoodscale) ? $value->number : 0;
                }
                foreach ($exportedcompetency->evaluationslist_course as $key => $value) {
                    $parents[$r->competency->id]['course_self'][$key] = ($isgoodscale) ? $value->number : 0;
                }
                if (api::is_cm_comptency_grading_enabled()) {
                    foreach ($exportedcompetency->evaluationslist_cm as $key => $value) {
                        $parents[$r->competency->id]['cm_self'][$key] = ($isgoodscale) ? $value->number : 0;
                    }
                }
            }
        }
        $result = $this->cleanemptyparent($result, $parents);
        $result = $this->fillparent($result, $parents);
        return $result;
    }

    /**
     * Remove empty parents.
     *
     * @param oject[] $result
     * @param Array $parents
     * @return oject[] $result
     */
    protected function cleanemptyparent($result, $parents) {
        foreach ($result['competencies_list'] as $key => $comp) {
            if ($comp->showasparent === true) {
                if (!in_array($comp->competency->id, array_keys($parents))) {
                    unset($result['competencies_list'][$key]);
                } else {
                    if (count($parents[$comp->competency->id]['total']) == 0) {
                        // This is a parent competency, but not for this scale.
                        unset($result['competencies_list'][$key]);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Fill parents with total children.
     *
     * @param oject[] $result
     * @param Array $parents
     * @return oject[] $result
     */
    protected function fillparent($result, $parents) {
        foreach ($result['competencies_list'] as $key => $comp) {
            if ($comp->showasparent === true) {
                $compid = $comp->competency->id;
                if (array_key_exists($compid, $parents)) {
                    foreach ($comp->evaluationslist_total as $keyeval => $value) {
                        if (isset($parents[$compid]['total'][$keyeval])) {
                            $comp->evaluationslist_total[$keyeval]->number = $parents[$compid]['total'][$keyeval];
                            $comp->evaluationslist_total[$keyeval]->number_self = $parents[$compid]['total_self'][$keyeval];
                        }
                    }
                    foreach ($comp->evaluationslist_course as $keyeval => $value) {
                        if (isset($parents[$compid]['course'][$keyeval])) {
                            $comp->evaluationslist_course[$keyeval]->number = $parents[$compid]['course'][$keyeval];
                            $comp->evaluationslist_course[$keyeval]->number_self = $parents[$compid]['course_self'][$keyeval];
                        }
                    }
                    if (api::is_cm_comptency_grading_enabled()) {
                        foreach ($comp->evaluationslist_cm as $keyeval => $value) {
                            if (isset($parents[$compid]['cm'][$keyeval])) {
                                $comp->evaluationslist_cm[$keyeval]->number = $parents[$compid]['cm'][$keyeval];
                                $comp->evaluationslist_cm[$keyeval]->number_self = $parents[$compid]['cm_self'][$keyeval];
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Get the format parameters for scalename.
     *
     * @return array
     */
    protected function get_format_parameters_for_scalename() {
        return [
            'context' => context_system::instance(), // The system context is cached, so we can get it right away.
        ];
    }
}
