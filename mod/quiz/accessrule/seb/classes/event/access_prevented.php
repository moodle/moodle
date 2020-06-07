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
 * Event for when access to a quiz is prevented by this subplugin.
 *
 * @package    quizaccess_seb
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb\event;

use core\event\base;
use quizaccess_seb\access_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Event for when access to a quiz is prevented by this subplugin.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access_prevented extends base {

    /**
     * Create event with strict parameters.
     *
     * Define strict parameters to create event with instead of relying on internal validation of array. Better code practice.
     * Easier for consumers of this class to know what data must be supplied and observers can have more trust in event data.
     *
     * @param access_manager $accessmanager Access manager.
     * @param string $reason Reason that access was prevented.
     * @return base
     */
    public static function create_strict(access_manager $accessmanager, string $reason) : base {
        global $USER;

        $other = [];
        $other['reason'] = $reason;
        $other['savedconfigkey'] = $accessmanager->get_valid_config_key();
        $other['receivedconfigkey'] = $accessmanager->get_received_config_key();
        $other['receivedbrowserexamkey'] = $accessmanager->get_received_browser_exam_key();

        return self::create([
            'userid' => $USER->id,
            'objectid' => $accessmanager->get_quiz()->get_quizid(),
            'courseid' => $accessmanager->get_quiz()->get_courseid(),
            'context' => $accessmanager->get_quiz()->get_context(),
            'other' => $other,
        ]);
    }

    /**
     * Initialize the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'quiz';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Get the name of the event.
     *
     * @return string Name of event.
     */
    public static function get_name() {
        return get_string('event:accessprevented', 'quizaccess_seb');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string Description.
     */
    public function get_description() {
        $description = "The user with id '$this->userid' has been prevented from accessing quiz with id '$this->objectid' by the "
                . "Safe Exam Browser access plugin. The reason was '{$this->other['reason']}'. "
            . "Expected config key: '{$this->other['savedconfigkey']}'. "
            . "Received config key: '{$this->other['receivedconfigkey']}'. "
            . "Received browser exam key: '{$this->other['receivedbrowserexamkey']}'.";

        return $description;
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the objectid to it's new value in the new course.
     *
     * @return array Mapping of object id.
     */
    public static function get_objectid_mapping() : array {
        return array('db' => 'quiz', 'restore' => 'quiz');
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the information in 'other' to it's new value in the new course.
     *
     * @return array List of mapping of other ids.
     */
    public static function get_other_mapping() : array {
        return [
            'cmid' => ['db' => 'course_modules', 'restore' => 'course_modules']
        ];
    }
}