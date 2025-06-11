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
 * @copyright  2019 Université de Montréalal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_summary_evaluations_exporter extends \core\external\exporter {

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    protected static function define_other_properties() {
        return [
            'competency' => [
                'type' => competency_exporter::read_properties_definition(),
            ],
            'evaluationslist_total' => [
                'type' => summary_evaluations_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'evaluationslist_course' => [
                'type' => summary_evaluations_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'evaluationslist_cm' => [
                'type' => summary_evaluations_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'showasparent' => [
                'type' => PARAM_BOOL,
                'optional' => true,
            ],
            'isassessable' => [
                'type' => PARAM_BOOL,
                'optional' => true,
            ],
        ];
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
        return [
            'plan' => 'core_competency\\plan',
            'scalevalues' => '\\stdClass[]',
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $scalevalues = $this->related['scalevalues'];
        $competencydetailinfos = $this->data->competencydetailinfos;
        $result = new \stdClass();
        $result->competency = $this->data->competencydetailinfos->competency;
        $result->evaluationslist = [];

        $result->evaluationslist_course = [];
        $result->evaluationslist_total = [];
        $result->evaluationslist_cm = [];

        foreach ($scalevalues as $config) {
            $datacourse = new \stdClass();
            $datacourse->empty = false;
            $datacourse->number = 0;
            $datacourse->color = $config->color;
            if (!empty($competencydetailinfos->competencydetail->courses)) {
                foreach ($competencydetailinfos->competencydetail->courses as $course) {
                    $usercmpcourse = $course->usecompetencyincourse;
                    if ($usercmpcourse && $usercmpcourse->get('grade') == $config->value) {
                        $datacourse->number++;
                    }
                }
                $datacourse->empty = ($datacourse->number == 0) ? true : false;
            } else {
                $datacourse->empty = true;
            }

            $exporter = new summary_evaluations_exporter($datacourse);
            $result->evaluationslist_course[] = $exporter->export($output);

            $datacm = new \stdClass();
            $datacm->empty = false;
            $datacm->number = 0;
            $datacm->color = $config->color;
            if (api::is_cm_comptency_grading_enabled()) {
                if (!empty($competencydetailinfos->competencydetail->cms)) {
                    foreach ($competencydetailinfos->competencydetail->cms as $cm) {
                        $usercmpcm = $cm->usecompetencyincm;
                        if ($usercmpcm && $usercmpcm->get('grade') == $config->value) {
                            $datacm->number++;
                        }
                    }
                    $datacm->empty = ($datacm->number == 0) ? true : false;
                } else {
                    $datacm->empty = true;
                }
                $exporter = new summary_evaluations_exporter($datacm);
                $result->evaluationslist_cm[] = $exporter->export($output);
            }
            $datatotal = new \stdClass();
            $datatotal->number = $datacm->number + $datacourse->number;
            $datatotal->color = $config->color;
            $datatotal->empty = ($datatotal->number == 0) ? true : false;
            $exporter = new summary_evaluations_exporter($datatotal);
            $result->evaluationslist_total[] = $exporter->export($output);
        }

        return (array) $result;
    }
}
