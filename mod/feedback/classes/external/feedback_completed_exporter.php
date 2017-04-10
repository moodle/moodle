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
 * Class for exporting a feedback completion record.
 *
 * @package    mod_feedback
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_feedback\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;

/**
 * Class for exporting a feedback completion record.
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_completed_exporter extends exporter {

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
            'feedback' => array(
                'type' => PARAM_INT,
                'description' => 'The feedback instance id this records belongs to.',
            ),
            'userid' => array(
                'type' => PARAM_INT,
                'description' => 'The user who completed the feedback (0 for anonymous).',
            ),
            'timemodified' => array(
                'type' => PARAM_INT,
                'description' => 'The last time the feedback was completed.',
            ),
            'random_response' => array(
                'type' => PARAM_INT,
                'description' => 'The response number (used when shuffling anonymous responses).',
            ),
            'anonymous_response' => array(
                'type' => PARAM_INT,
                'description' => 'Whether is an anonymous response.',
            ),
            'courseid' => array(
                'type' => PARAM_INT,
                'description' => 'The course id where the feedback was completed.',
            ),
        );
    }
}
