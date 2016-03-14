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
 * Class for exporting a template statistics summary.
 *
 * @package    tool_lp
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;
use moodle_url;

/**
 * Class for exporting a cohort summary from an stdClass.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_statistics_exporter extends exporter {

    public static function define_properties() {
        return array(
            'competencycount' => array(
                'type' => PARAM_INT,
            ),
            'unlinkedcompetencycount' => array(
                'type' => PARAM_INT,
            ),
            'plancount' => array(
                'type' => PARAM_INT,
            ),
            'completedplancount' => array(
                'type' => PARAM_INT,
            ),
            'usercompetencyplancount' => array(
                'type' => PARAM_INT,
            ),
            'proficientusercompetencyplancount' => array(
                'type' => PARAM_INT,
            )
        );
    }

    public static function define_other_properties() {
        return array(
            'linkedcompetencypercentage' => array(
                'type' => PARAM_FLOAT
            ),
            'linkedcompetencycount' => array(
                'type' => PARAM_INT
            ),
            'completedplanpercentage' => array(
                'type' => PARAM_FLOAT
            ),
            'proficientusercompetencyplanpercentage' => array(
                'type' => PARAM_FLOAT
            ),
            'leastproficient' => array(
                'type' => competency_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'leastproficientcount' => array(
                'type' => PARAM_INT
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $linkedcompetencycount = $this->data->competencycount - $this->data->unlinkedcompetencycount;
        if ($linkedcompetencycount < 0) {
            // Should never happen.
            $linkedcompetencycount = 0;
        }
        $linkedcompetencypercentage = 0;
        if ($this->data->competencycount > 0) {
            $linkedcompetencypercentage = format_float(
                ((float) $linkedcompetencycount / (float) $this->data->competencycount) * 100.0);
        }
        $completedplanpercentage = 0;
        if ($this->data->plancount > 0) {
            $completedplanpercentage = format_float(
                ((float) $this->data->completedplancount / (float) $this->data->plancount) * 100.0);
        }
        $proficientusercompetencyplanpercentage = 0;
        if ($this->data->usercompetencyplancount > 0) {
            $proficientusercompetencyplanpercentage = format_float(
                ((float) $this->data->proficientusercompetencyplancount / (float) $this->data->usercompetencyplancount) * 100.0);
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
            'linkedcompetencycount' => $linkedcompetencycount,
            'linkedcompetencypercentage' => $linkedcompetencypercentage,
            'completedplanpercentage' => $completedplanpercentage,
            'proficientusercompetencyplanpercentage' => $proficientusercompetencyplanpercentage,
            'leastproficient' => $competencies,
            'leastproficientcount' => count($competencies)
        );
    }
}
