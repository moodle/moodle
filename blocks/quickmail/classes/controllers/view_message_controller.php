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

namespace block_quickmail\controllers;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\controllers\support\base_controller;
use block_quickmail\controllers\support\controller_request;
use block_quickmail\filemanager\attachment_appender;

class view_message_controller extends base_controller {

    public static $baseuri = '/blocks/quickmail/message.php';

    public static $views = [
        'view_message' => [],
    ];

    /**
     * Returns the query string which this controller's forms will append to target URLs
     *
     * NOTE: this overrides the base controller method
     *
     * @return array
     */
    public function get_form_url_params() {
        return ['id' => $this->props->message->get('id')];
    }

    /**
     * View a queued, sent, or sending message's details
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function view_message(controller_request $request) {
        $userprops = 'email,firstname,lastname';

        // Get sent message recipients as array of user objects.
        $sentrecipientusers = $this->props->message->get_message_recipient_users('sent', $userprops);

        // Get unsent message recipients as array of user objects.
        $unsentrecipientusers = $this->props->message->get_message_recipient_users('unsent', $userprops);

        // Get message additional emails as array.
        $additionalemails = $this->props->message->get_additional_emails(true);

        // Get message file attachments.
        $attachments = $this->props->message->get_message_attachments();

        // Get message file attachment links.
        $attachmentlinks = attachment_appender::add_individual_links($this->props->message, $attachments);

        $this->render_component('view_message', [
            'message' => $this->props->message,
            'user' => $this->props->user,
            'sent_recipient_users' => $sentrecipientusers,
            'unsent_recipient_users' => $unsentrecipientusers,
            'additional_emails' => $additionalemails,
            'attachments' => $attachments,
            'attachmentlinks' => $attachmentlinks
        ]);
    }

}
