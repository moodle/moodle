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
defined('MOODLE_INTERNAL') || die();

use context_system;
use renderer_base;
use core_competency\evidence;
use core_competency\user_competency;
use core_user\external\user_summary_exporter;

/**
 * Class for exporting evidence data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class evidence_exporter extends \core\external\persistent_exporter {

    /**
     * Constructor.
     *
     * @param mixed $data The data.
     * @param array $related Array of relateds.
     */
    public function __construct($data, $related = array()) {
        if (!isset($related['context'])) {
            // Previous code was automatically using the system context which was not correct.
            // We let developers know that they must fix their code without breaking anything, and
            // fallback on the previous behaviour. This should be removed at a later stage: Moodle 3.5.
            debugging('Missing related context in evidence_exporter.', DEBUG_DEVELOPER);
            $related['context'] = context_system::instance();
        }
        parent::__construct($data, $related);
    }

    protected static function define_related() {
        return array(
            'actionuser' => 'stdClass?',
            'context' => 'context',
            'scale' => 'grade_scale',
            'usercompetency' => 'core_competency\\user_competency?',
            'usercompetencyplan' => 'core_competency\\user_competency_plan?',
        );
    }

    protected static function define_class() {
        return evidence::class;
    }

    protected function get_other_values(renderer_base $output) {
        $other = array();

        if (!empty($this->related['actionuser'])) {
            $exporter = new user_summary_exporter($this->related['actionuser']);
            $actionuser = $exporter->export($output);
            $other['actionuser'] = $actionuser;
        }

        $other['description'] = $this->persistent->get_description();

        $other['userdate'] = userdate($this->persistent->get('timecreated'));

        if ($this->persistent->get('grade') === null) {
            $gradename = '-';
        } else {
            $gradename = $this->related['scale']->scale_items[$this->persistent->get('grade') - 1];
        }
        $other['gradename'] = $gradename;

        // Try to guess the user from the user competency.
        $userid = null;
        if ($this->related['usercompetency']) {
            $userid = $this->related['usercompetency']->get('userid');
        } else if ($this->related['usercompetencyplan']) {
            $userid = $this->related['usercompetencyplan']->get('userid');
        } else {
            $uc = user_competency::get_record(['id' => $this->persistent->get('usercompetencyid')]);
            $userid = $uc->get('userid');
        }
        $other['candelete'] = evidence::can_delete_user($userid);

        return $other;
    }

    /**
     * Get the format parameters for gradename.
     *
     * @return array
     */
    protected function get_format_parameters_for_gradename() {
        return [
            'context' => context_system::instance(), // The system context is cached, so we can get it right away.
        ];
    }

    public static function define_other_properties() {
        return array(
            'actionuser' => array(
                'type' => user_summary_exporter::read_properties_definition(),
                'optional' => true
            ),
            'description' => array(
                'type' => PARAM_TEXT,   // The description may contain course names, etc.. which may need filtering.
            ),
            'gradename' => array(
                'type' => PARAM_TEXT,
            ),
            'userdate' => array(
                'type' => PARAM_NOTAGS
            ),
            'candelete' => array(
                'type' => PARAM_BOOL
            )
        );
    }
}
