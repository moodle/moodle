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
 * Class for exporting data of course module linked to a competency.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use renderer_base;

/**
 * Class for exporting data of course module linked to a competency.
 *
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class linked_cm_exporter extends \core\external\exporter {

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
        return ['relatedinfo' => '\\stdClass'];
    }

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    protected static function define_other_properties() {
        return [
            'url' => [
                'type' => PARAM_RAW,
            ],
            'rated' => [
                'type' => PARAM_BOOL,
            ],
            'cmname' => [
                'type' => PARAM_RAW,
            ],
            'cmicon' => [
                'type' => PARAM_RAW,
            ],
            'coursename' => [
                'type' => PARAM_RAW,
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
        $cmdata = $this->data;
        $result = new \stdClass();

        $urlparams = ['user' => $this->related['relatedinfo']->userid, 'id' => $cmdata->cmid];
        $url = (new \moodle_url('/report/cmcompetency/index.php', $urlparams))->out();

        $cm = get_coursemodule_from_id('', $cmdata->cmid, 0, true);
        $modinfo = get_fast_modinfo($cm->course);
        $result->coursename = $modinfo->cms[$cmdata->cmid]->get_course()->shortname;
        $result->cmname = $modinfo->cms[$cmdata->cmid]->name;
        $result->cmicon = $modinfo->cms[$cmdata->cmid]->get_icon_url()->out();
        $result->url = $url;
        $result->rated = (isset($cmdata->usecompetencyincm) && !empty($cmdata->usecompetencyincm->get('grade'))) ? true : false;

        return (array) $result;
    }
}
