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
 * Class for exporting user competency course data.
 *
 * @package    core_competency
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency\external;
defined('MOODLE_INTERNAL') || die();

use context_system;
use renderer_base;
use stdClass;

/**
 * Class for exporting user competency course data.
 *
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_course_exporter extends \core\external\persistent_exporter {

    protected static function define_class() {
        return \core_competency\user_competency_course::class;
    }

    protected static function define_related() {
        // We cache the scale so it does not need to be retrieved from the framework every time.
        return array('scale' => 'grade_scale');
    }

    protected function get_other_values(renderer_base $output) {
        $result = new stdClass();

        if ($this->persistent->get('grade') === null) {
            $gradename = '-';
        } else {
            $gradename = $this->related['scale']->scale_items[$this->persistent->get('grade') - 1];
        }
        $result->gradename = $gradename;

        if ($this->persistent->get('proficiency') === null) {
            $proficiencyname = get_string('no');
        } else {
            $proficiencyname = get_string($this->persistent->get('proficiency') ? 'yes' : 'no');
        }
        $result->proficiencyname = $proficiencyname;

        return (array) $result;
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

    protected static function define_other_properties() {
        return array(
            'gradename' => array(
                'type' => PARAM_TEXT
            ),
            'proficiencyname' => array(
                'type' => PARAM_RAW
            )
        );
    }
}
