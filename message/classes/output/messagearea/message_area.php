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
 * Contains class used to prepare the message area for display.
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
 * Class to prepare the message area for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_area implements templatable, renderable {

    /**
     * @var int The user id.
     */
    public $userid;

    /**
     * @var int The other user id.
     */
    public $otheruserid;

    /**
     * @var array The contacts for the users.
     */
    public $contacts;

    /**
     * @var array The messages for the user.
     */
    public $messages;

    /**
     * @var bool Was a specific conversation requested.
     */
    public $requestedconversation;

    /**
     * Constructor.
     *
     * @param int $userid The ID of the user whose contacts and messages we are viewing
     * @param int|null $otheruserid The id of the user we are viewing, null if none
     * @param array $contacts
     * @param array|null $messages
     * @param bool $requestedconversation
     */
    public function __construct($userid, $otheruserid, $contacts, $messages, $requestedconversation) {
        $this->userid = $userid;
        $this->otheruserid = $otheruserid;
        $this->contacts = $contacts;
        $this->messages = $messages;
        $this->requestedconversation = $requestedconversation;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        $data->userid = $this->userid;
        $contacts = new contacts($this->otheruserid, $this->contacts);
        $data->contacts = $contacts->export_for_template($output);
        $messages = new messages($this->userid, $this->otheruserid, $this->messages);
        $data->messages = $messages->export_for_template($output);
        $data->isconversation = true;
        $data->requestedconversation = $this->requestedconversation;

        return $data;
    }
}
