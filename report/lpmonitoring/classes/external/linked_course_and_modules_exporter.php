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
 * Class for exporting data for a course and its modules for a competency.
 *
 * @package    report_lpmonitoring
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use renderer_base;

/**
 * Class for exporting data for a course and its modules for a competency.
 *
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréalal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class linked_course_and_modules_exporter extends \core\external\exporter {

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    protected static function define_other_properties() {
        return [
            'course' => [
                'type' => linked_course_exporter::read_properties_definition(),
            ],
            'modules' => [
                'type' => linked_cm_exporter::read_properties_definition(),
                'multiple' => true,
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
        return ['plan' => 'core_competency\\plan'];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $plan = $this->related['plan'];

        $result = new \stdClass();
        $result->modules = [];

        $relatedinfo = new \stdClass();
        $relatedinfo->userid = $plan->get('userid');
        $exporter = new linked_course_exporter($this->data['courseinfo'], ['relatedinfo' => $relatedinfo]);
        $result->course = $exporter->export($output);

        foreach ($this->data['modulesinfo'] as $cmid) {
            $cmdata = new \stdClass();
            $cmdata->cmid = $cmid;
            $relatedinfo = new \stdClass();
            $relatedinfo->userid = $plan->get('userid');
            $exporter = new linked_cm_exporter($cmdata, ['relatedinfo' => $relatedinfo]);
            $result->modules[] = $exporter->export($output);
        }

        return (array) $result;
    }
}
