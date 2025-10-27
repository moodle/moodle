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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\event;

defined('MOODLE_INTERNAL') || die();

class alternate_email_added extends \core\event\base {
    protected function init() {
        // Standard CRUD create, read, update, delete.
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns name of the event.
     *
     * @return string
     */
    public static function get_name() {
        return \block_quickmail_string::get('eventalternateemailadded');
    }

    /**
     * Returns info on when a user with ID has viwed a control panel module (tab).
     *
     * @return string
     */
    public function get_description() {
        $a = (object)[];
        $a->user_id = $this->userid;
        $a->email = $this->other['address'];

        return \block_quickmail_string::get('eventalternateemailadded_desc');
    }

    /**
     * Returns URL of the event.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/blocks/quickmail/alternate.php', array(
                    'courseid' => $this->courseid
                ));
    }
}
