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
 * The mod_quiz attempt abandoned event.
 *
 * @package    mod_quiz
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_quiz\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_quiz attempt abandoned event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int submitterid: id of submitter (null when trigged by CLI script).
 *      - int quizid: (optional) id of the quiz.
 * }
 *
 * @package    mod_quiz
 * @since      Moodle 2.6
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_abandoned extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'quiz_attempts';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->relateduserid' has had their attempt with id '$this->objectid' marked as abandoned " .
            "for the quiz with course module id '$this->contextinstanceid'.";
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventquizattemptabandoned', 'mod_quiz');
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/quiz/review.php', ['attempt' => $this->objectid]);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!array_key_exists('submitterid', $this->other)) {
            throw new \coding_exception('The \'submitterid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return ['db' => 'quiz_attempts', 'restore' => 'quiz_attempt'];
    }

    public static function get_other_mapping() {
        $othermapped = [];
        $othermapped['submitterid'] = ['db' => 'user', 'restore' => 'user'];
        $othermapped['quizid'] = ['db' => 'quiz', 'restore' => 'quiz'];

        return $othermapped;
    }
}
