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
 * Class for exporting user competency data with all the evidence
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;

use context_user;
use renderer_base;
use stdClass;

/**
 * Class for exporting user competency data with additional related data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_summary_exporter extends exporter {

    protected static function define_related() {
        // We cache the context so it does not need to be retrieved from the framework every time.
        return array('competency' => '\\tool_lp\\competency',
                     'relatedcompetencies' => '\\tool_lp\\competency[]',
                     'user' => '\\stdClass?',
                     'plan' => '\\tool_lp\\plan?',
                     'usercompetency' => '\\tool_lp\\user_competency',
                     'evidence' => '\\tool_lp\\evidence[]');
    }

    protected static function define_other_properties() {
        return array(
            'showrelatedcompetencies' => array(
                'type' => PARAM_BOOL
            ),
            'cangrade' => array(
                'type' => PARAM_BOOL
            ),
            'cansuggest' => array(
                'type' => PARAM_BOOL
            ),
            'cangradeorsuggest' => array(
                'type' => PARAM_BOOL
            ),
            'competency' => array(
                'type' => competency_summary_exporter::read_properties_definition()
            ),
            'user' => array(
                'type' => user_summary_exporter::read_properties_definition(),
                'optional' => true
            ),
            'plan' => array(
                'type' => plan_exporter::read_properties_definition(),
                'optional' => true
            ),
            'usercompetency' => array(
                'type' => user_competency_exporter::read_properties_definition()
            ),
            'evidence' => array(
                'type' => evidence_exporter::read_properties_definition(),
                'multiple' => true
            )
        );
    }

    protected function get_other_values(renderer_base $output) {
        global $DB;
        $result = new stdClass();
        $result->showrelatedcompetencies = true;

        $competency = $this->related['competency'];
        $exporter = new competency_summary_exporter(null, array(
            'competency' => $competency,
            'context' => $competency->get_context(),
            'framework' => $competency->get_framework(),
            'linkedcourses' => array(),
            'relatedcompetencies' => $this->related['relatedcompetencies']
        ));
        $result->competency = $exporter->export($output);
        $context = context_user::instance($this->related['usercompetency']->get_userid());
        $result->cangrade = has_capability('tool/lp:competencygrade', $context);
        $result->cansuggest = has_capability('tool/lp:competencysuggestgrade', $context);
        $result->cangradeorsuggest = $result->cangrade || $result->cansuggest;
        if ($this->related['user']) {
            $exporter = new user_summary_exporter($this->related['user']);
            $result->user = $exporter->export($output);
        }
        $exporter = new user_competency_exporter($this->related['usercompetency'], array('scale' => $competency->get_scale()));
        $result->usercompetency = $exporter->export($output);

        if ($this->related['plan']) {
            $exporter = new plan_exporter($this->related['plan'], array('template' => $this->related['plan']->get_template()));
            $result->plan = $exporter->export($output);
        }

        $allevidence = array();
        $usercache = array();
        $scale = $competency->get_scale();

        $result->evidence = array();
        if (count($this->related['evidence'])) {
            foreach ($this->related['evidence'] as $evidence) {
                if (!empty($evidence->get_actionuserid())) {
                    $usercache[$evidence->get_actionuserid()] = true;
                }
            }
            $users = array();
            if (!empty($usercache)) {
                list($sql, $params) = $DB->get_in_or_equal(array_keys($usercache));
                $users = $DB->get_records_select('user', 'id ' . $sql, $params);
            }

            foreach ($users as $user) {
                if (can_view_user_details_cap($user)) {
                    $usercache[$user->id] = $user;
                } else {
                    unset($usercache[$user->id]);
                }
            }

            foreach ($this->related['evidence'] as $evidence) {
                $related = array('scale' => $scale);
                if (!empty($usercache[$evidence->get_actionuserid()])) {
                    $related['actionuser'] = $usercache[$evidence->get_actionuserid()];
                } else {
                    $related['actionuser'] = null;
                }
                $exporter = new evidence_exporter($evidence, $related);
                $allevidence[] = $exporter->export($output);
            }
            $result->evidence = $allevidence;
        }

        return (array) $result;
    }
}
