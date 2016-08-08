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
 * Contains class used to prepare a contact for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output\messagearea;

use renderable;
use templatable;

/**
 * Class to prepare a contact for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contact implements templatable, renderable {

    /**
     * Maximum length of message to show in left panel.
     */
    const MAX_MSG_LENGTH = 60;

    /**
     * The contact.
     */
    protected $contact;

    /**
     * Constructor.
     *
     * @param \stdClass $contact
     */
    public function __construct($contact) {
        $this->contact = $contact;
    }

    public function export_for_template(\renderer_base $output) {
        $contact = new \stdClass();
        $contact->userid = $this->contact->userid;
        $contact->fullname = $this->contact->fullname;
        $contact->profileimageurl = $this->contact->profileimageurl;
        $contact->profileimageurlsmall = $this->contact->profileimageurlsmall;
        if ($this->contact->lastmessage) {
            $contact->lastmessage = shorten_text($this->contact->lastmessage, self::MAX_MSG_LENGTH);
        } else {
            $contact->lastmessage = null;
        }
        $contact->isonline = $this->contact->isonline;
        $contact->isread = $this->contact->isread;

        return $contact;
    }
}
