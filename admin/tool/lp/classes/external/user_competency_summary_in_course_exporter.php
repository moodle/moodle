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
 * Class for exporting user competency data with all the evidence in a course
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;

use context_course;
use renderer_base;
use stdClass;

/**
 * Class for exporting user competency data with additional related data in a plan.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_summary_in_course_exporter extends exporter {

    protected static function define_related() {
        // We cache the context so it does not need to be retrieved from the framework every time.
        return array('competency' => '\\tool_lp\\competency',
                     'relatedcompetencies' => '\\tool_lp\\competency[]',
                     'user' => '\\stdClass',
                     'course' => '\\stdClass',
                     'usercompetency' => '\\tool_lp\\user_competency?',
                     'evidence' => '\\tool_lp\\evidence[]');
    }

    protected static function define_other_properties() {
        return array(
            'usercompetencysummary' => array(
                'type' => user_competency_summary_exporter::read_properties_definition()
            ),
            'course' => array(
                'type' => course_summary_exporter::read_properties_definition(),
            )
        );
    }

    protected function get_other_values(renderer_base $output) {
        // Arrays are copy on assign.
        $related = $this->related;
        // Remove course from related as it is not wanted by the user_competency_summary_exporter.
        unset($related['course']);
        $related['usercompetencyplan'] = null;
        $exporter = new user_competency_summary_exporter(null, $related);
        $result = new stdClass();
        $result->usercompetencysummary = $exporter->export($output);

        $context = context_course::instance($this->related['course']->id);
        $exporter = new course_summary_exporter($this->related['course'], array('context' => $context));
        $result->course = $exporter->export($output);

        return (array) $result;
    }
}
