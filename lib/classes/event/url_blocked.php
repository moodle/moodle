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

namespace core\event;

/**
 * URL blocked event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string url:      blocked url
 *      - string reason:   reason for blocking
 *      - bool   redirect: blocked url was a redirect
 * }
 *
 * @package    core
 * @copyright  2022  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class url_blocked extends base {

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return sprintf(
            'Blocked %s%s: %s',
            $this->other['url'],
            $this->other['redirect'] ? ' (redirect)' : '',
            $this->other['reason']
        );
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventurlblocked', 'core');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        global $USER;

        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = empty($USER->id) ? \context_system::instance() : \context_user::instance($USER->id);
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
        if (!isset($this->other['url'])) {
            throw new \coding_exception("The 'url' value must be set in other.");
        }
    }

    /**
     * Used when restoring course logs.
     *
     */
    public static function get_other_mapping() {
    }

    /**
     * Validate all properties right before triggering the event.
     *
     * Emits debugging.
     *
     * @throws \coding_exception
     */
    protected function validate_before_trigger() {
        parent::validate_before_trigger();

        debugging(
            sprintf('%s [user %d]', $this->get_description(), $this->userid),
            DEBUG_NONE
        );
    }
}
