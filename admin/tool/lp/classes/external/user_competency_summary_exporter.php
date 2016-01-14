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
use tool_lp\user_competency;

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
                     'user' => '\\stdClass',
                     'usercompetency' => '\\tool_lp\\user_competency?',
                     'usercompetencyplan' => '\\tool_lp\\user_competency_plan?',
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
            ),
            'usercompetency' => array(
                'type' => user_competency_exporter::read_properties_definition(),
                'optional' => true
            ),
            'usercompetencyplan' => array(
                'type' => user_competency_exporter::read_properties_definition(),
                'optional' => true
            ),
            'evidence' => array(
                'type' => evidence_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'commentarea' => array(
                'type' => comment_area_exporter::read_properties_definition(),
                'optional' => true
            ),
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

        $context = context_user::instance($this->related['user']->id);
        $result->cangrade = user_competency::can_grade_user($this->related['user']->id, $competency->get_id());
        $result->cansuggest = user_competency::can_suggest_grade_user($this->related['user']->id, $competency->get_id());
        $result->cangradeorsuggest = $result->cangrade || $result->cansuggest;
        if ($this->related['user']) {
            $exporter = new user_summary_exporter($this->related['user']);
            $result->user = $exporter->export($output);
        }
        $related = array('scale' => $competency->get_scale());
        if ($this->related['usercompetency']) {
            $exporter = new user_competency_exporter($this->related['usercompetency'], $related);
            $result->usercompetency = $exporter->export($output);
        }
        if ($this->related['usercompetencyplan']) {
            $exporter = new user_competency_plan_exporter($this->related['usercompetencyplan'], $related);
            $result->usercompetencyplan = $exporter->export($output);
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
                $usercache[$user->id] = $user;
            }

            foreach ($this->related['evidence'] as $evidence) {
                $related = array('scale' => $scale);
                $related['actionuser'] = !empty($evidence->get_actionuserid()) ? $usercache[$evidence->get_actionuserid()] : null;
                $exporter = new evidence_exporter($evidence, $related);
                $allevidence[] = $exporter->export($output);
            }
            $result->evidence = $allevidence;
        }

        $usercompetency = !empty($this->related['usercompetency']) ? $this->related['usercompetency'] : null;

        if (!empty($usercompetency)) {
            $commentareaexporter = new comment_area_exporter($usercompetency->get_comment_object());
            $result->commentarea = $commentareaexporter->export($output);
        }

        return (array) $result;
    }
}
