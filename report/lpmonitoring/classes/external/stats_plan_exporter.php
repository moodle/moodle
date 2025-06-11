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
 * Class for exporting stats data for plan
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use core\external\exporter;
use renderer_base;
use core_tag_tag;
use core_comment\external\comment_area_exporter;

/**
 * Class for exporting stats data for plan.
 *
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stats_plan_exporter extends exporter {

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    public static function define_other_properties() {
        return [
            'nbcompetenciesnotrated' => [
                'type' => PARAM_INT,
            ],
            'nbcompetenciesproficient' => [
                'type' => PARAM_INT,
            ],
            'nbcompetenciesnotproficient' => [
                'type' => PARAM_INT,
            ],
            'nbcompetenciestotal' => [
                'type' => PARAM_INT,
            ],
            'nbcompetenciesrated' => [
                'type' => PARAM_INT,
            ],
            'nbtags' => [
                'type' => PARAM_INT,
            ],
            'commentarea' => [
                'type' => comment_area_exporter::read_properties_definition(),
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
        // We cache the scale so it does not need to be retrieved from the framework every time.
        return ['plan' => 'core_competency\\plan'];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {

        $result = new \stdClass();
        $planid = $this->related['plan']->get('id');
        $usercompetencies = $this->data->usercompetencies;
        $nbcompetenciestotal = count($usercompetencies);
        $nbcompetenciesnotproficient = 0;
        $nbcompetenciesproficient = 0;
        $nbcompetenciesnotrated = 0;
        $nbcompetenciesrated = 0;
        $shoulddisplay = \report_lpmonitoring\api::has_to_display_rating($this->related['plan']);
        if ($shoulddisplay) {
            foreach ($usercompetencies as $r) {
                $usercompetency = (isset($r->usercompetency)) ? $r->usercompetency : $r->usercompetencyplan;
                $proficiency = $usercompetency->get('proficiency');
                if (!isset($proficiency)) {
                    $nbcompetenciesnotrated++;
                } else {
                    if ($proficiency) {
                        $nbcompetenciesproficient++;
                    } else {
                        $nbcompetenciesnotproficient++;
                    }
                }
            }
        } else {
            $nbcompetenciesnotrated = $nbcompetenciestotal;
        }

        $result->nbcompetenciestotal = $nbcompetenciestotal;
        $result->nbcompetenciesnotproficient = $nbcompetenciesnotproficient;
        $result->nbcompetenciesproficient = $nbcompetenciesproficient;
        $result->nbcompetenciesnotrated = $nbcompetenciesnotrated;
        $result->nbcompetenciesrated = $nbcompetenciestotal - $nbcompetenciesnotrated;
        $result->nbtags = count(core_tag_tag::get_item_tags('report_lpmonitoring', 'competency_plan', $planid));

        $commentareaexporter = new comment_area_exporter($this->related['plan']->get_comment_object());
        $result->commentarea = $commentareaexporter->export($output);

        return (array) $result;
    }
}
