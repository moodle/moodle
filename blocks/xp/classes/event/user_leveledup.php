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
 * User leveled up event.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\event;

/**
 * User leveled up event class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_leveledup extends \core\event\base {

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'The user with ID ' . $this->relateduserid . ' leveled up to level ' . $this->other['level'] . '.';
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_user_leveledup', 'block_xp');
    }

    /**
     * Get URL related to the action.
     *
     * @return block_xp\local\routing\url
     */
    public function get_url() {
        return \block_xp\di::get('url_resolver')->reverse('report', ['courseid' => $this->courseid]);
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Data validation.
     *
     * @return void
     */
    protected function validate_data() {
        if (empty($this->relateduserid)) {
            throw new \coding_exception('The related user ID must be set.');
        }
        if (!isset($this->other['level'])) {
            throw new \coding_exception('The level must be set in $other.');
        }
    }

}
