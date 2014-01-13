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
 * This file contains an event for an unknown service API call.
 *
 * @package    mod_lti
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lti\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event for when something happens with an unknown lti service API call.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      @type string body raw body.
 *      @type string messageid id of message.
 *      @type string messagetype type of message.
 *      @type string consumerkey key of consumer.
 *      @type string sharedsecret shared secret key.
 * }
 *
 * @package    mod_lti
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unknown_service_api_called extends \core\event\base {

    /** Old data to be used for the legacy event. */
    protected $legacydata;

    /**
     * Set method for legacy data.
     *
     * @param stdClass $data legacy event data.
     */
    public function set_legacy_data($data) {
        $this->legacydata = $data;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'lti';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['context'] = \context_system::instance();
    }

    /**
     * Returns localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'An unknown call to a service api was made.';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('ltiunknownserviceapicall', 'mod_lti');
    }

    /**
     * Does this event replace a legacy event?
     *
     * @return null|string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'lti_unknown_service_api_call';
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return mixed
     */
    protected function get_legacy_eventdata() {
        return $this->legacydata;
    }

}
