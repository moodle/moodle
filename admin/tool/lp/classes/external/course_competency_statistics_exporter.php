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
 * Class for exporting a course competency statistics summary.
 *
 * @package    tool_lp
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;
use moodle_url;
use core_competency\external\competency_exporter;

/**
 * Class for exporting a course competency statistics summary.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competency_statistics_exporter extends \core_competency\external\exporter {

    public static function define_properties() {
        return array(
            'competencycount' => array(
                'type' => PARAM_INT,
            ),
            'proficientcompetencycount' => array(
                'type' => PARAM_INT,
            ),
        );
    }

    public static function define_other_properties() {
        return array(
            'proficientcompetencypercentage' => array(
                'type' => PARAM_FLOAT
            ),
            'proficientcompetencypercentageformatted' => array(
                'type' => PARAM_RAW
            ),
            'leastproficient' => array(
                'type' => competency_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'leastproficientcount' => array(
                'type' => PARAM_INT
            ),
            'canbegradedincourse' => array(
                'type' => PARAM_BOOL
            ),
            'canmanagecoursecompetencies' => array(
                'type' => PARAM_BOOL
            ),
        );
    }

    protected static function define_related() {
        return array('context' => 'context');
    }

    protected function get_other_values(renderer_base $output) {
        $proficientcompetencypercentage = 0;
        $proficientcompetencypercentageformatted = '';
        if ($this->data->competencycount > 0) {
            $proficientcompetencypercentage = ((float) $this->data->proficientcompetencycount
                / (float) $this->data->competencycount) * 100.0;
            $proficientcompetencypercentageformatted = format_float($proficientcompetencypercentage);
        }
        $competencies = array();
        $contextcache = array();
        foreach ($this->data->leastproficientcompetencies as $competency) {
            if (!isset($contextcache[$competency->get_competencyframeworkid()])) {
                $contextcache[$competency->get_competencyframeworkid()] = $competency->get_context();
            }
            $context = $contextcache[$competency->get_competencyframeworkid()];
            $exporter = new competency_exporter($competency, array('context' => $context));
            $competencies[] = $exporter->export($output);
        }
        return array(
            'proficientcompetencypercentage' => $proficientcompetencypercentage,
            'proficientcompetencypercentageformatted' => $proficientcompetencypercentageformatted,
            'leastproficient' => $competencies,
            'leastproficientcount' => count($competencies),
            'canbegradedincourse' => has_capability('moodle/competency:coursecompetencygradable', $this->related['context']),
            'canmanagecoursecompetencies' => has_capability('moodle/competency:coursecompetencymanage', $this->related['context'])
        );
    }
}
