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
 * Class for exporting lpmonitoring_competency_detail data.
 *
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use renderer_base;
use core\external\exporter;
use report_lpmonitoring\external\scale_competency_item_statistics_exporter;
use report_lpmonitoring\external\competency_stats_user_exporter;


/**
 * Class for exporting lpmonitoring_competency_statistics data.
 *
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lpmonitoring_competency_statistics_exporter extends exporter {

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    public static function define_other_properties() {
        return array(
            'competencyid' => array(
                'type' => PARAM_INT
            ),
            'nbuserrated' => array(
                'type' => PARAM_INT
            ),
            'nbusertotal' => array(
                'type' => PARAM_INT
            ),
            'scalecompetencyitems' => array(
                'type' => scale_competency_item_statistics_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'totaluserlist' => array(
                'type' => competency_stats_user_exporter::read_properties_definition(),
                'multiple' => true
            )
        );
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
        $result->nbusertotal = count($data->listusers);
        $result->nbuserrated = 0;
        $usersrated = [];

        // Information for each scale value.
        $result->scalecompetencyitems = array();
        foreach ($data->scale as $id => $scalename) {
            $scaleinfo = new \stdClass();
            $scaleinfo->value = $id;
            $scaleinfo->name = $scalename;
            $scaleinfo->color = $data->reportscaleconfig[$id - 1]->color;

            $scalecompetencyitemexporter = new scale_competency_item_statistics_exporter($scaleinfo,
                    array('users' => $data->listusers));
            $scalecompetencyitem = $scalecompetencyitemexporter->export($output);
            $result->nbuserrated += $scalecompetencyitem->nbusers;
            $result->scalecompetencyitems[] = $scalecompetencyitem;
            foreach ($scalecompetencyitem->listusers as $user) {
                $usersrated[] = $user->userid;
            }
        }

        // List of rated and not rated users in the competency.
        $result->totaluserlist = array();
        foreach ($data->listusers as $user) {
            if (in_array($user->userinfo->id, $usersrated)) {
                $user->userinfo->rateduser = true;
            } else {
                $user->userinfo->rateduser = false;
            }
            $scalecompetencyitemexporter = new competency_stats_user_exporter($user->userinfo);
            $result->totaluserlist[] = $scalecompetencyitemexporter->export($output);
        }
        return (array) $result;
    }

}
