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
 * Email failed event.
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Email failed event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string subject: the message subject.
 *      - string message: the message text.
 *      - string errorinfo: the error info.
 * }
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_failed extends base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventemailfailed');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "Failed to send an email from the user with id '$this->userid' to the user with id '$this->relateduserid'
            due to the following error: \"{$this->other['errorinfo']}\".";
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return array(SITEID, 'library', 'mailer', qualified_me(), 'ERROR: ' . $this->other['errorinfo']);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
        if (!isset($this->other['subject'])) {
            throw new \coding_exception('The \'subject\' value must be set in other.');
        }
        if (!isset($this->other['message'])) {
            throw new \coding_exception('The \'message\' value must be set in other.');
        }
        if (!isset($this->other['errorinfo'])) {
            throw new \coding_exception('The \'errorinfo\' value must be set in other.');
        }
    }

    public static function get_other_mapping() {
        return false;
    }
}
