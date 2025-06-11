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
 * Apply default values for ratings in learning plan templates.
 *
 * @package    report_lpmonitoring
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Apply default values for ratings in learning plan templates.
 *
 * @package    report_lpmonitoring
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bulkrating implements renderable, templatable {

    /** @var int $templateid */
    protected $templateid;

    /**
     * Construct.
     *
     * @param int $templateid
     */
    public function __construct($templateid) {
        $this->templateid = $templateid;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $comps = \core_competency\template_competency::list_competencies($this->templateid);
        $data->submitdisabled = \report_lpmonitoring\api::rating_task_exist($this->templateid);
        $data->hascompetencies = count($comps) == 0 ? 0 : 1;
        $data->templateid = $this->templateid;
        $datascalecompetencies = [];
        foreach ($comps as $comp) {
            $compscale = [];
            $compscale['compid'] = $comp->get('id');
            $compscale['compshortname'] = $comp->get('shortname');
            $compscale['idnumber'] = $comp->get('idnumber');
            $compscale['scaleid'] = $comp->get_scale()->id;
            $scale = $comp->get_scale();
            $scale->load_items();
            $scaleitems = $scale->scale_items;
            foreach ($scaleitems as $key => $name) {
                $s = [];
                $s['value'] = $key + 1;
                $s['name'] = $name;
                $s['proficient'] = $comp->get_proficiency_of_grade($key + 1);
                $s['default'] = $comp->get_default_grade()[0] == ($key + 1) ? 1 : 0;
                $compscale['scalevalues'][] = $s;
            }
            $datascalecompetencies[] = $compscale;
        }
        $data->datascalecompetencies = $datascalecompetencies;

        return $data;
    }
}
