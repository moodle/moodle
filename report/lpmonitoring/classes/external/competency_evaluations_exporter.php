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
 * Class for exporting data for a competency and its evaluations.
 *
 * @package    report_lpmonitoring
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use renderer_base;
use core_competency\external\competency_exporter;
use report_lpmonitoring\api;

/**
 * Class for exporting data for a competency and its evaluations.
 *
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_evaluations_exporter extends \core\external\exporter {

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    protected static function define_other_properties() {
        return array(
            'competency' => array(
                'type' => competency_exporter::read_properties_definition()
            ),
            'competencydetail' => array(
                'type' => lpmonitoring_competency_detail_exporter::read_properties_definition()
            ),
            'evaluationslist' => array(
                'type' => evaluations_exporter::read_properties_definition(),
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
        $plan = $this->related['plan'];

        $competencydetailinfos = $this->data->competencydetailinfos;

        $result = new \stdClass();
        $result->competency = $this->data->competencydetailinfos->competency;
        $result->evaluationslist = array();

        foreach ($this->data->allcourses as $courseid => $course) {
            // Evaluation for the course.
            $data = new \stdClass();
            $data->iscourse = true;
            $data->elementid = $courseid;
            if (isset($competencydetailinfos->tmpevalincourse[$courseid])) {
                $data->grade = $competencydetailinfos->tmpevalincourse[$courseid];
            } else {
                $data->grade = null;
            }
            $data->competencydetail = $competencydetailinfos->competencydetail;
            $exporter = new evaluations_exporter($data);
            $result->evaluationslist[] = $exporter->export($output);

            // Evaluation for the modules in this course.
            if (api::is_cm_comptency_grading_enabled()) {
                foreach ($course['modulesinfo'] as $cmid => $module) {
                    $data = new \stdClass();
                    $data->iscourse = false;
                    $data->elementid = $cmid;
                    if (isset($competencydetailinfos->tmpevalinmodule[$cmid])) {
                        $data->grade = $competencydetailinfos->tmpevalinmodule[$cmid];
                    } else {
                        $data->grade = null;
                    }
                    $data->competencydetail = $competencydetailinfos->competencydetail;
                    $exporter = new evaluations_exporter($data);
                    $result->evaluationslist[] = $exporter->export($output);
                }
            }
        }

        $competencydetailinfos->competencydetail->displayrating = api::has_to_display_rating($plan->get('id'));
        $exporter = new lpmonitoring_competency_detail_exporter($competencydetailinfos->competencydetail);
        $result->competencydetail = $exporter->export($output);

        return (array) $result;
    }
}
