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
 * Class for exporting competency data with the set of linked courses.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use context_course;
use renderer_base;
use stdClass;
use core_competency\competency_framework;
use core_competency\external\competency_exporter;
use core_competency\external\competency_framework_exporter;
use core_course\external\course_summary_exporter;

/**
 * Class for exporting competency data with additional related data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_summary_exporter extends \core\external\exporter {

    protected static function define_related() {
        // We cache the context so it does not need to be retrieved from the framework every time.
        return array('context' => '\\context',
                     'competency' => '\\core_competency\\competency',
                     'framework' => '\\core_competency\\competency_framework',
                     'linkedcourses' => '\\stdClass[]',
                     'relatedcompetencies' => '\\core_competency\\competency[]');
    }

    protected static function define_other_properties() {
        return array(
            'linkedcourses' => array(
                'type' => course_summary_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'relatedcompetencies' => array(
                'type' => competency_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'competency' => array(
                'type' => competency_exporter::read_properties_definition()
            ),
            'framework' => array(
                'type' => competency_framework_exporter::read_properties_definition()
            ),
            'hascourses' => array(
                'type' => PARAM_BOOL
            ),
            'hasrelatedcompetencies' => array(
                'type' => PARAM_BOOL
            ),
            'scaleid' => array(
                'type' => PARAM_INT
            ),
            'scaleconfiguration' => array(
                'type' => PARAM_RAW
            ),
            'taxonomyterm' => array(
                'type' => PARAM_TEXT
            ),
            'comppath' => array(
                'type' => competency_path_exporter::read_properties_definition(),
            )
        );
    }

    protected function get_other_values(renderer_base $output) {
        $result = new stdClass();
        $context = $this->related['context'];

        $courses = $this->related['linkedcourses'];
        $linkedcourses = array();
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            $exporter = new course_summary_exporter($course, array('context' => $context));
            $courseexport = $exporter->export($output);
            array_push($linkedcourses, $courseexport);
        }
        $result->linkedcourses = $linkedcourses;
        $result->hascourses = count($linkedcourses) > 0;

        $relatedcompetencies = array();
        foreach ($this->related['relatedcompetencies'] as $competency) {
            $exporter = new competency_exporter($competency, array('context' => $context));
            $competencyexport = $exporter->export($output);
            array_push($relatedcompetencies, $competencyexport);
        }
        $result->relatedcompetencies = $relatedcompetencies;
        $result->hasrelatedcompetencies = count($relatedcompetencies) > 0;

        $competency = $this->related['competency'];
        $exporter = new competency_exporter($competency, array('context' => $context));
        $result->competency = $exporter->export($output);

        $exporter = new competency_framework_exporter($this->related['framework']);
        $result->framework = $exporter->export($output);
        $scaleconfiguration = $this->related['framework']->get('scaleconfiguration');
        $scaleid = $this->related['framework']->get('scaleid');
        if ($competency->get('scaleid')) {
            $scaleconfiguration = $competency->get('scaleconfiguration');
            $scaleid = $competency->get('scaleid');
        }
        $result->scaleconfiguration = $scaleconfiguration;
        $result->scaleid = $scaleid;

        $level = $competency->get_level();
        $taxonomy = $this->related['framework']->get_taxonomy($level);
        $result->taxonomyterm = (string) (competency_framework::get_taxonomies_list()[$taxonomy]);

        // Competency path.
        $exporter = new competency_path_exporter([
            'ancestors' => $competency->get_ancestors(),
            'framework' => $this->related['framework'],
            'context' => $context
        ]);
        $result->comppath = $exporter->export($output);

        return (array) $result;
    }
}
