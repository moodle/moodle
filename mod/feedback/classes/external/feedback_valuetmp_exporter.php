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
 * Class for exporting a feedback tmp response.
 *
 * @package    mod_feedback
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_feedback\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;

/**
 * Class for exporting a feedback tmp response.
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_valuetmp_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array list of properties
     */
    protected static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'The record id.',
            ),
            'course_id' => array(
                'type' => PARAM_INT,
                'description' => 'The course id this record belongs to.',
            ),
            'item' => array(
                'type' => PARAM_INT,
                'description' => 'The item id that was responded.',
            ),
            'completed' => array(
                'type' => PARAM_INT,
                'description' => 'Reference to the feedback_completedtmp table.',
            ),
            'tmp_completed' => array(
                'type' => PARAM_INT,
                'description' => 'Old field - not used anymore.',
            ),
            'value' => array(
                'type' => PARAM_RAW,
                'description' => 'The response value.',
            ),
        );
    }
}
