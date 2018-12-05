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
 * TODO: This file should be removed once the related web services go through final deprecation.
 * Followup: MDL-63261
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
 * Class to prepare a contact for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contact implements templatable, renderable {

    /**
     * @var int Maximum length of message to show in panel.
     */
    const MAX_MSG_LENGTH = 60;

    /**
     * @var int The userid.
     */
    public $userid;

    /**
     * @var int The id of the user who sent the last message.
     */
    public $useridfrom;

    /**
     * @var string The fullname.
     */
    public $fullname;

    /**
     * @var string The profile image url.
     */
    public $profileimageurl;

    /**
     * @var string The small profile image url.
     */
    public $profileimageurlsmall;

    /**
     * @var int The message id.
     */
    public $messageid;

    /**
     * @var bool Are we messaging the user?
     */
    public $ismessaging;

    /**
     * @var string The last message sent.
     */
    public $lastmessage;

    /**
     * @var int The last message sent timestamp.
     */
    public $lastmessagedate;

    /**
     * @var bool Is the user online?
     */
    public $isonline;

    /**
     * @var bool Is the user blocked?
     */
    public $isblocked;

    /**
     * @var bool Is the message read?
     */
    public $isread;

    /**
     * @var int The number of unread messages.
     */
    public $unreadcount;

    /**
     * @var int The id of the conversation to which to message belongs.
     */
    public $conversationid;

    /**
     * Constructor.
     *
     * @param \stdClass $contact
     */
    public function __construct($contact) {
        $this->userid = $contact->userid;
        $this->useridfrom = $contact->useridfrom;
        $this->fullname = $contact->fullname;
        $this->profileimageurl = $contact->profileimageurl;
        $this->profileimageurlsmall = $contact->profileimageurlsmall;
        $this->messageid = $contact->messageid;
        $this->ismessaging = $contact->ismessaging;
        $this->lastmessage = $contact->lastmessage;
        $this->lastmessagedate = $contact->lastmessagedate;
        $this->isonline = $contact->isonline;
        $this->isblocked = $contact->isblocked;
        $this->isread = $contact->isread;
        $this->unreadcount = $contact->unreadcount;
        $this->conversationid = $contact->conversationid ?? null;
    }

    public function export_for_template(\renderer_base $output) {
        $contact = new \stdClass();
        $contact->userid = $this->userid;
        $contact->fullname = $this->fullname;
        $contact->profileimageurl = $this->profileimageurl;
        $contact->profileimageurlsmall = $this->profileimageurlsmall;
        $contact->messageid = $this->messageid;
        $contact->ismessaging = $this->ismessaging;
        $contact->sentfromcurrentuser = false;
        if ($this->lastmessage) {
            if ($this->userid !== $this->useridfrom) {
                $contact->sentfromcurrentuser = true;
            }
            $contact->lastmessage = shorten_text($this->lastmessage, self::MAX_MSG_LENGTH);
        } else {
            $contact->lastmessage = null;
        }
        $contact->lastmessagedate = $this->lastmessagedate;
        $contact->showonlinestatus = is_null($this->isonline) ? false : true;
        $contact->isonline = $this->isonline;
        $contact->isblocked = $this->isblocked;
        $contact->isread = $this->isread;
        $contact->unreadcount = $this->unreadcount;
        $contact->conversationid = $this->conversationid;

        return $contact;
    }
}
