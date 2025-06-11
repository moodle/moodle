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
 * Class for exporting lpmonitoring_competency_statistics_incourse data.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use renderer_base;
use core\external\exporter;
use report_lpmonitoring\external\scale_competency_incourse_statistics_exporter;


/**
 * Class for exporting lpmonitoring_competency_statistics_incourse data.
 *
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lpmonitoring_competency_statistics_incourse_exporter extends exporter {

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    public static function define_other_properties() {
        return [
            'competencyid' => [
                'type' => PARAM_INT,
            ],
            'nbratingtotal' => [
                'type' => PARAM_INT,
            ],
            'nbratings' => [
                'type' => PARAM_INT,
            ],
            'scalecompetencyitems' => [
                'type' => scale_competency_incourse_statistics_exporter::read_properties_definition(),
                'multiple' => true,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {

        $data = $this->data;
        $result = new \stdClass();

        $result->competencyid = $data->competency->get('id');
        $result->nbratingtotal = count($data->listratings);

        // Information for each scale value.
        $result->scalecompetencyitems = [];
        $result->nbratings = 0;
        foreach ($data->scale as $id => $scalename) {
            $scaleinfo = new \stdClass();
            $scaleinfo->value = $id;
            $scaleinfo->name = $scalename;
            $scaleinfo->color = $data->reportscaleconfig[$id - 1]->color;

            $scalecompetencyitemexporter = new scale_competency_incourse_statistics_exporter($scaleinfo,
                    ['ratings' => $data->listratings]);
            $scalecompetencyitem = $scalecompetencyitemexporter->export($output);
            $result->nbratings += $scalecompetencyitem->nbratings;
            $result->scalecompetencyitems[] = $scalecompetencyitem;
        }
        return (array) $result;
    }

}
