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

namespace core_message\output;

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
     * The id of the user that the contacts belong to.
     */
    protected $userid;

    /**
     * The id of the user that has been selected.
     */
    protected $otheruserid;

    /**
     * The contacts.
     */
    protected $contacts;

    /**
     * Determines if the conversations have been selected.
     */
    protected $conversationsselected;

    /**
     * Constructor.
     *
     * @param int $userid The id of the user the contacts belong to
     * @param int $otheruserid The id of the user we are viewing
     * @param \core_message\output\contact[] $contacts
     * @param bool $conversationsselected
     */
    public function __construct($userid, $otheruserid, $contacts, $conversationsselected = true) {
        $this->userid = $userid;
        $this->otheruserid = $otheruserid;
        $this->contacts = $contacts;
        $this->conversationsselected = $conversationsselected;
    }

    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        $data->userid = $this->userid;
        $data->otheruserid = $this->otheruserid;
        $data->contacts = array();
        foreach ($this->contacts as $contact) {
            $contactdata = $contact->export_for_template($output);
            // Check if the contact was selected.
            if ($this->otheruserid == $contactdata->userid) {
                $contactdata->selected = true;
            }
            $data->contacts[] = $contactdata;
        }
        $data->conversationsselected = $this->conversationsselected;

        return $data;
    }
}
