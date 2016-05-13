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
 * Class for exporting evidence data.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency\external;

use renderer_base;
use core_competency\evidence;
use core_competency\user_competency;

/**
 * Class for exporting evidence data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class evidence_exporter extends persistent_exporter {

    protected static function define_related() {
        return array(
            'actionuser' => 'stdClass?',
            'scale' => 'grade_scale',
            'usercompetency' => 'core_competency\\user_competency?',
            'usercompetencyplan' => 'core_competency\\user_competency_plan?',
        );
    }

    protected static function define_class() {
        return 'core_competency\\evidence';
    }

    protected function get_other_values(renderer_base $output) {
        $other = array();

        if (!empty($this->related['actionuser'])) {
            $exporter = new user_summary_exporter($this->related['actionuser']);
            $actionuser = $exporter->export($output);
            $other['actionuser'] = $actionuser;
        }

        $other['description'] = $this->persistent->get_description();

        $other['userdate'] = userdate($this->persistent->get_timecreated());

        if ($this->persistent->get_grade() === null) {
            $gradename = '-';
        } else {
            $gradename = $this->related['scale']->scale_items[$this->persistent->get_grade() - 1];
        }
        $other['gradename'] = $gradename;

        // Try to guess the user from the user competency.
        $userid = null;
        if ($this->related['usercompetency']) {
            $userid = $this->related['usercompetency']->get_userid();
        } else if ($this->related['usercompetencyplan']) {
            $userid = $this->related['usercompetencyplan']->get_userid();
        } else {
            $uc = user_competency::get_record(['id' => $this->persistent->get_usercompetencyid()]);
            $userid = $uc->get_userid();
        }
        $other['candelete'] = evidence::can_delete_user($userid);

        return $other;
    }

    public static function define_other_properties() {
        return array(
            'actionuser' => array(
                'type' => user_summary_exporter::read_properties_definition(),
                'optional' => true
            ),
            'description' => array(
                'type' => PARAM_TEXT,
            ),
            'gradename' => array(
                'type' => PARAM_TEXT,
            ),
            'userdate' => array(
                'type' => PARAM_TEXT
            ),
            'candelete' => array(
                'type' => PARAM_BOOL
            )
        );
    }
}
