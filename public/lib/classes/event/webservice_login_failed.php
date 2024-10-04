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
 * Web service login failed event.
 *
 * @package    core
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Web service login failed event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string method: authentication method.
 *      - string reason: failure reason.
 *      - string tokenid: id of token.
 * }
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_login_failed extends base {

    /**
     * Legacy log data.
     *
     * @var null|array
     */
    protected $legacylogdata;

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "Web service authentication failed with code: \"{$this->other['reason']}\".";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventwebserviceloginfailed', 'webservice');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = \context_system::instance();
    }

    /**
     * Custom validation.
     *
     * It is recommended to set the properties:
     * - $other['tokenid']
     * - $other['username']
     *
     * However they are not mandatory as they are not always known.
     *
     * Please note that the token CANNOT be specified, it is considered
     * as a password and should never be displayed.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['reason'])) {
           throw new \coding_exception('The \'reason\' value must be set in other.');
        } else if (!isset($this->other['method'])) {
           throw new \coding_exception('The \'method\' value must be set in other.');
        } else if (isset($this->other['token'])) {
           throw new \coding_exception('The \'token\' value must not be set in other.');
        }
    }

    public static function get_other_mapping() {
        return false;
    }
}
