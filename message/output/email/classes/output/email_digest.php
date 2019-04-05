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
 * Email digest renderable.
 *
 * @package    message_email
 * @copyright  2019 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_email\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Email digest renderable.
 *
 * @copyright  2019 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_digest implements \renderable, \templatable {

    /**
     * @var array The conversations
     */
    protected $conversations = array();

    /**
     * @var array The messages
     */
    protected $messages = array();

    /**
     * @var \stdClass The user we want to send the digest email to
     */
    protected $userto;

    /**
     * The email_digest constructor.
     *
     * @param \stdClass $userto
     */
    public function __construct(\stdClass $userto) {
        $this->userto = $userto;
    }

    /**
     * Adds another conversation to this digest.
     *
     * @param \stdClass $conversation The conversation from the 'message_conversations' table.
     */
    public function add_conversation(\stdClass $conversation) {
        $this->conversations[$conversation->id] = $conversation;
    }

    /**
     * Adds another message to this digest, using the conversation id it belongs to as a key.
     *
     * @param \stdClass $message The message from the 'messages' table.
     */
    public function add_message(\stdClass $message) {
        $this->messages[$message->conversationid][] = $message;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $renderer The render to be used for formatting the email
     * @return \stdClass The data ready for use in a mustache template
     */
    public function export_for_template(\renderer_base $renderer) {
        // Prepare the data we are going to send to the template.
        $data = new \stdClass();
        $data->conversations = [];

        // Don't do anything if there are no messages.
        foreach ($this->conversations as $conversation) {
            $messages = $this->messages[$conversation->id] ?? [];

            if (empty($messages)) {
                continue;
            }

            $viewallmessageslink = new \moodle_url('/message/index.php', ['convid' => $conversation->id]);

            $conversationformatted = new \stdClass();
            $conversationformatted->groupname = $conversation->name;
            $conversationformatted->coursename = $conversation->coursename;
            $conversationformatted->numberofunreadmessages = count($messages);
            $conversationformatted->messages = [];
            $conversationformatted->viewallmessageslink = \html_writer::link($viewallmessageslink,
                get_string('emaildigestviewallmessages', 'message_email'));

            // We only display the last 3 messages.
            $messages = array_slice($messages, -3, 3, true);
            foreach ($messages as $message) {
                $user = new \stdClass();
                username_load_fields_from_object($user, $message);
                $user->id = $message->useridfrom;
                $messageformatted = new \stdClass();
                $messageformatted->userfullname = fullname($user);
                $messageformatted->message = message_format_message_text($message);

                // Check if the message was sent today.
                $istoday = userdate($message->timecreated, 'Y-m-d') == userdate(time(), 'Y-m-d');
                if ($istoday) {
                    $timesent = userdate($message->timecreated, get_string('strftimetime24', 'langconfig'));
                } else {
                    $timesent = userdate($message->timecreated, get_string('strftimedatefullshort', 'langconfig'));
                }

                $messageformatted->timesent = $timesent;

                $conversationformatted->messages[] = $messageformatted;
            }

            $data->conversations[] = $conversationformatted;
        }

        return $data;
    }
}
