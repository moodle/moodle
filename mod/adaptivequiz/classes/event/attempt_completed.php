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
 * Event which is triggered when a user completes an attempt on adaptive quiz.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\event;

use core\event\base;
use moodle_exception;
use moodle_url;

class attempt_completed extends base {

    /**
     * @inheritDoc
     */
    public static function get_name() {
        return get_string('eventattemptcompleted', 'adaptivequiz');
    }

    /**
     * @inheritDoc
     */
    public function get_description() {
        return "The user with id '$this->userid' has completed the attempt with id '$this->objectid' for the " .
            "adaptive quiz with course module id '$this->contextinstanceid'.";
    }

    /**
     * Returns related URL where result of the event can be observed.
     *
     * @throws moodle_exception
     * @return moodle_url
     */
    public function get_url() {
        return new moodle_url('/mod/adaptivequiz/reviewattempt.php', ['attempt' => $this->objectid]);
    }

    /**
     * @inheritDoc
     */
    public static function get_objectid_mapping() {
        return ['db' => 'adaptivequiz_attempt', 'restore' => 'adaptiveattempts'];
    }

    /**
     * @inheritDoc
     */
    protected function init() {
        $this->data['objecttable'] = 'adaptivequiz_attempt';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }
}
