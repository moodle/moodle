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
 * Class for exporting competency_framework data.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency\external;
defined('MOODLE_INTERNAL') || die();

use core_competency\api;
use renderer_base;

/**
 * Class for exporting competency_framework data.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_framework_exporter extends \core\external\persistent_exporter {

    /**
     * Define the name of persistent class.
     *
     * @return string
     */
    protected static function define_class() {
        return \core_competency\competency_framework::class;
    }

    /**
     * Get other values that do not belong to the basic persisent.
     *
     * @param renderer_base $output
     * @return Array
     */
    protected function get_other_values(renderer_base $output) {
        $filters = array('competencyframeworkid' => $this->persistent->get('id'));
        $context = $this->persistent->get_context();
        return array(
            'canmanage' => has_capability('moodle/competency:competencymanage', $context),
            'competenciescount' => api::count_competencies($filters),
            'contextname' => $context->get_context_name(),
            'contextnamenoprefix' => $context->get_context_name(false)
        );
    }

    /**
     * Define other properties that do not belong to the basic persisent.
     *
     * @return Array
     */
    protected static function define_other_properties() {
        return array(
            'canmanage' => array(
                'type' => PARAM_BOOL
            ),
            'competenciescount' => array(
                'type' => PARAM_INT
            ),

            // Both contexts need to be PARAM_RAW because the method context::get_context_name()
            // already applies the formatting and thus could return HTML content.
            'contextname' => array(
                'type' => PARAM_RAW
            ),
            'contextnamenoprefix' => array(
                'type' => PARAM_RAW
            )
        );
    }

}
