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
 * Contains class used to prepare the contacts for display.
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
 * Class to prepare the contacts for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contacts implements templatable, renderable {

    /**
     * @var int The id of the user that has been selected.
     */
    public $contactuserid;

    /**
     * @var array The contacts.
     */
    public $contacts;

    /**
     * Constructor.
     *
     * @param int|null $contactuserid The id of the user that has been selected
     * @param array $contacts
     */
    public function __construct($contactuserid, $contacts) {
        $this->contactuserid = $contactuserid;
        $this->contacts = $contacts;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        $data->contacts = array();
        $userids = array();
        foreach ($this->contacts as $contact) {
            $contact = new contact($contact);
            $contactdata = $contact->export_for_template($output);
            $userids[$contactdata->userid] = $contactdata->userid;
            // Check if the contact was selected.
            if ($this->contactuserid == $contactdata->userid) {
                $contactdata->selected = true;
            }
            $data->contacts[] = $contactdata;
        }
        // Check if the other user is not part of the contacts. We may be sending a message to someone
        // we have not had a conversation with, so we want to add a new item to the contacts array.
        if ($this->contactuserid && !isset($userids[$this->contactuserid])) {
            $user = \core_user::get_user($this->contactuserid);
            // Set an empty message so that we know we are messaging the user, and not viewing their profile.
            $user->smallmessage = '';
            $user->useridfrom = $user->id;
            $contact = \core_message\helper::create_contact($user);
            $contact = new contact($contact);
            $contactdata = $contact->export_for_template($output);
            $contactdata->selected = true;
            // Put the contact at the front.
            array_unshift($data->contacts, $contactdata);
        }

        return $data;
    }
}
