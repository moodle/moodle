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
 * Class for exporting user evidence competency data.
 *
 * @package    tool_lp
 * @copyright  2016 Serge Gauthier - <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderer_base;
use core_competency\external\competency_exporter;
use core_competency\external\user_competency_exporter;

/**
 * Class for exporting user evidence competency data.
 *
 * @copyright  2016 Serge Gauthier - <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_evidence_competency_summary_exporter extends \core\external\exporter {

    protected static function define_related() {
        return array('competency' => '\\core_competency\\competency',
                     'usercompetency' => '\\core_competency\\user_competency',
                     'scale' => 'grade_scale',
                     'context' => '\\context'
                    );
    }

    protected static function define_other_properties() {
        return array(
            'competency' => array(
                'type' => competency_exporter::read_properties_definition()
            ),
            'usercompetency' => array(
                'type' => user_competency_exporter::read_properties_definition(),
            )
        );
    }

    protected function get_other_values(renderer_base $output) {
        $competencyexporter = new competency_exporter($this->related['competency'],
                array('context' => $this->related['context']));
        $usercompetencyexporter = new user_competency_exporter($this->related['usercompetency'],
                array('scale' => $this->related['scale']));

        $values = array(
            'competency' => $competencyexporter->export($output),
            'usercompetency' => $usercompetencyexporter->export($output)
        );

        return $values;
    }

}
