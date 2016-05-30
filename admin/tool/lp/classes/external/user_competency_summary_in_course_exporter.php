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

use core_competency\api;
use core_competency\user_competency;
use context_course;
use renderer_base;
use stdClass;

/**
 * Class for exporting user competency data with additional related data in a plan.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_summary_in_course_exporter extends \core_competency\external\exporter {

    protected static function define_related() {
        // We cache the context so it does not need to be retrieved from the framework every time.
        return array('competency' => '\\core_competency\\competency',
                     'relatedcompetencies' => '\\core_competency\\competency[]',
                     'user' => '\\stdClass',
                     'course' => '\\stdClass',
                     'usercompetencycourse' => '\\core_competency\\user_competency_course?',
                     'evidence' => '\\core_competency\\evidence[]',
                     'scale' => '\\grade_scale');
    }

    protected static function define_other_properties() {
        return array(
            'usercompetencysummary' => array(
                'type' => user_competency_summary_exporter::read_properties_definition()
            ),
            'course' => array(
                'type' => course_summary_exporter::read_properties_definition(),
            ),
            'coursemodules' => array(
                'type' => course_module_summary_exporter::read_properties_definition(),
                'multiple' => true
            )
        );
    }

    protected function get_other_values(renderer_base $output) {
        // Arrays are copy on assign.
        $related = $this->related;
        $result = new stdClass();
        // Remove course from related as it is not wanted by the user_competency_summary_exporter.
        unset($related['course']);
        $related['usercompetencyplan'] = null;
        $related['usercompetency'] = null;
        $exporter = new user_competency_summary_exporter(null, $related);
        $result->usercompetencysummary = $exporter->export($output);
        $result->usercompetencysummary->cangrade = user_competency::can_grade_user_in_course($this->related['user']->id,
            $this->related['course']->id);

        $context = context_course::instance($this->related['course']->id);
        $exporter = new course_summary_exporter($this->related['course'], array('context' => $context));
        $result->course = $exporter->export($output);

        $coursemodules = api::list_course_modules_using_competency($this->related['competency']->get_id(),
                                                                   $this->related['course']->id);

        $fastmodinfo = get_fast_modinfo($this->related['course']->id);
        $exportedmodules = array();
        foreach ($coursemodules as $cm) {
            $cminfo = $fastmodinfo->cms[$cm];
            $cmexporter = new course_module_summary_exporter(null, array('cm' => $cminfo));
            $exportedmodules[] = $cmexporter->export($output);
        }
        $result->coursemodules = $exportedmodules;

        return (array) $result;
    }
}
