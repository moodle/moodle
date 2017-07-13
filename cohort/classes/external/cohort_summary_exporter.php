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
 * Class for exporting a cohort summary from an stdClass.
 *
 * @package    core_cohort
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_cohort\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;

/**
 * Class for exporting a cohort summary from an stdClass.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort_summary_exporter extends \core\external\exporter {

    protected static function define_related() {
        // Cohorts can exist on a category context.
        return array('context' => '\\context');
    }

    public static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
            ),
            'name' => array(
                'type' => PARAM_TEXT,
            ),
            'idnumber' => array(
                'type' => PARAM_RAW,        // ID numbers are plain text.
                'default' => '',
                'null' => NULL_ALLOWED
            ),
            'visible' => array(
                'type' => PARAM_BOOL,
            )
        );
    }

    public static function define_other_properties() {
        return array(
            'contextname' => array(
                // The method context::get_context_name() already formats the string, and may return HTML.
                'type' => PARAM_RAW
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        return array(
            'contextname' => $this->related['context']->get_context_name()
        );
    }
}
