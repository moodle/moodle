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
use core_competency\external\competency_exporter;
use core_competency\external\performance_helper;

/**
 * Class for exporting a cohort summary from an stdClass.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_statistics_exporter extends \core\external\exporter {

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
            'linkedcompetencypercentageformatted' => array(
                'type' => PARAM_RAW
            ),
            'linkedcompetencycount' => array(
                'type' => PARAM_INT
            ),
            'completedplanpercentage' => array(
                'type' => PARAM_FLOAT
            ),
            'completedplanpercentageformatted' => array(
                'type' => PARAM_RAW
            ),
            'proficientusercompetencyplanpercentage' => array(
                'type' => PARAM_FLOAT
            ),
            'proficientusercompetencyplanpercentageformatted' => array(
                'type' => PARAM_RAW
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
        $linkedcompetencypercentageformatted = '';
        if ($this->data->competencycount > 0) {
            $linkedcompetencypercentage = ((float) $linkedcompetencycount / (float) $this->data->competencycount) * 100.0;
            $linkedcompetencypercentageformatted = format_float($linkedcompetencypercentage);
        }
        $completedplanpercentage = 0;
        $completedplanpercentageformatted = '';
        if ($this->data->plancount > 0) {
            $completedplanpercentage = ((float) $this->data->completedplancount / (float) $this->data->plancount) * 100.0;
            $completedplanpercentageformatted = format_float($completedplanpercentage);
        }
        $proficientusercompetencyplanpercentage = 0;
        $proficientusercompetencyplanpercentageformatted = '';
        if ($this->data->usercompetencyplancount > 0) {
            $proficientusercompetencyplanpercentage = ((float) $this->data->proficientusercompetencyplancount
                    / (float) $this->data->usercompetencyplancount) * 100.0;
            $proficientusercompetencyplanpercentageformatted = format_float($proficientusercompetencyplanpercentage);
        }
        $competencies = array();
        $helper = new performance_helper();
        foreach ($this->data->leastproficientcompetencies as $competency) {
            $context = $helper->get_context_from_competency($competency);
            $exporter = new competency_exporter($competency, array('context' => $context));
            $competencies[] = $exporter->export($output);
        }
        return array(
            'linkedcompetencycount' => $linkedcompetencycount,
            'linkedcompetencypercentage' => $linkedcompetencypercentage,
            'linkedcompetencypercentageformatted' => $linkedcompetencypercentageformatted,
            'completedplanpercentage' => $completedplanpercentage,
            'completedplanpercentageformatted' => $completedplanpercentageformatted,
            'proficientusercompetencyplanpercentage' => $proficientusercompetencyplanpercentage,
            'proficientusercompetencyplanpercentageformatted' => $proficientusercompetencyplanpercentageformatted,
            'leastproficient' => $competencies,
            'leastproficientcount' => count($competencies)
        );
    }
}
