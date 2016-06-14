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
 * Class for exporting a course summary from an stdClass.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;
use moodle_url;

/**
 * Class for exporting a course summary from an stdClass.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_summary_exporter extends \core_competency\external\exporter {

    protected static function define_related() {
        // We cache the context so it does not need to be retrieved from the course.
        return array('context' => '\\context');
    }

    protected function get_other_values(renderer_base $output) {
        return array(
            'viewurl' => (new moodle_url('/course/view.php', array('id' => $this->data->id)))->out(false)
        );
    }

    public static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
            ),
            'fullname' => array(
                'type' => PARAM_TEXT,
            ),
            'shortname' => array(
                'type' => PARAM_TEXT,
            ),
            'idnumber' => array(
                'type' => PARAM_RAW,
            )
        );
    }

    public static function define_other_properties() {
        return array(
            'viewurl' => array(
                'type' => PARAM_URL,
            )
        );
    }
}
