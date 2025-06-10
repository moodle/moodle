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
 * Class for exporting data for the plan competency summary by scale.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use core\external\exporter;
use renderer_base;
use report_lpmonitoring\api;

/**
 * Class for exporting data for the plan competency summary by scale.
 *
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scale_competency_summary_exporter extends exporter {

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    public static function define_other_properties() {
        return array(
            'iscmcompetencygradingenabled' => array(
                'type' => PARAM_BOOL
            ),
            'scale_competency' => array(
                'type' => list_plan_competency_summary_exporter::read_properties_definition(),
                'multiple' => true
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
        return array('plan' => 'core_competency\\plan');
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

        $result = array();
        $result['iscmcompetencygradingenabled'] = api::is_cm_comptency_grading_enabled();
        $result['scale_competency'] = array();
        $helper = new \core_competency\external\performance_helper();

        $scales = [];
        foreach ($resultcompetencies as $key => $r) {
            $competency = new \core_competency\competency($r->competency->id);
            $scale = $helper->get_scale_from_competency($competency);
            if (!in_array($scale->id, $scales)) {
                $reportscaleconfig = api::read_report_competency_config($r->competency->competencyframeworkid, $scale->id);
                $reportscaleconfig = json_decode($reportscaleconfig->get('scaleconfiguration'));
                $scalevalues = [];
                foreach ($reportscaleconfig as $config) {
                    $scaleinfo = new \stdClass();
                    $scaleinfo->value = $config->id;
                    $scaleinfo->name = $scale->scale_items[$config->id - 1];
                    $scaleinfo->color = $config->color;
                    $scalevalues[] = $scaleinfo;
                }

                $exporter = new list_plan_competency_summary_exporter($this->data, ['plan' => $plan, 'scale' => $scale,
                    'scalevalues' => $scalevalues]);
                $exportedcompetency = $exporter->export($output);
                $result['scale_competency'][] = $exportedcompetency;
                $scales[] = $scale->id;
            }
        }
        return $result;
    }
}
