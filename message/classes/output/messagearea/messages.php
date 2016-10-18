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
 * Contains class used to prepare the messages for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output\messagearea;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;

/**
 * Class to prepare the messages for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class messages implements templatable, renderable {

    /**
     * @var array The messages.
     */
    public $messages;

    /**
     * @var int The current user id.
     */
    public $currentuserid;

    /**
     * @var int The other user id.
     */
    public $otheruserid;

    /**
     * @var \stdClass The other user.
     */
    public $otheruser;

    /**
     * Constructor.
     *
     * @param int $currentuserid The current user we are wanting to view messages for
     * @param int $otheruserid The other user we are wanting to view messages for
     * @param array $messages
     */
    public function __construct($currentuserid, $otheruserid, $messages) {
        $ufields = get_all_user_name_fields(true) . ', lastaccess';

        $this->currentuserid = $currentuserid;
        $this->otheruserid = $otheruserid;
        $this->otheruser = \core_user::get_user($otheruserid, $ufields);
        $this->messages = $messages;
    }

    public function export_for_template(\renderer_base $output) {
        global $USER;

        $data = new \stdClass();
        $data->iscurrentuser = $USER->id == $this->currentuserid;
        $data->currentuserid = $this->currentuserid;
        $data->otheruserid = $this->otheruserid;
        $data->otheruserfullname = fullname($this->otheruser);

        if (empty($this->otheruser)) {
            $data->isonline = false;
        } else {
            $data->isonline = \core_message\helper::is_online($this->otheruser->lastaccess);
        }

        $data->messages = array();
        foreach ($this->messages as $message) {
            $message = new message($message);
            $data->messages[] = $message->export_for_template($output);
        }

        return $data;
    }
}
