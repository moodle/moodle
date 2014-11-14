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
 * Library of functions for chat outside of the core api
 */

require_once($CFG->dirroot . '/mod/chat/lib.php');
require_once($CFG->libdir . '/portfolio/caller.php');

/**
 * @package   mod_chat
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chat_portfolio_caller extends portfolio_module_caller_base {
    /** @var object */
    private $chat;
    /** @var int Timestamp */
    protected $start;
    /** @var int Timestamp */
    protected $end;
    /**
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'id'    => true,
            'start' => false,
            'end'   => false,
        );
    }
    /**
     * @global object
     */
    public function load_data() {
        global $DB;

        if (!$this->cm = get_coursemodule_from_id('chat', $this->id)) {
            throw new portfolio_caller_exception('invalidid', 'chat');
        }
        $this->chat = $DB->get_record('chat', array('id' => $this->cm->instance));
        $select = 'chatid = ?';
        $params = array($this->chat->id);
        if ($this->start && $this->end) {
            $select .= ' AND timestamp >= ? AND timestamp <= ?';
            $params[] = $this->start;
            $params[] = $this->end;
        }
        $this->messages = $DB->get_records_select(
                'chat_messages',
                $select,
                $params,
                'timestamp ASC'
            );
        $select .= ' AND userid = ?';
        $params[] = $this->user->id;
        $this->participated = $DB->record_exists_select(
            'chat_messages',
            $select,
            $params
        );
    }
    /**
     * @return array
     */
    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_PLAINHTML);
    }
    /**
     *
     */
    public function expected_time() {
        return portfolio_expected_time_db(count($this->messages));
    }
    /**
     * @return string
     */
    public function get_sha1() {
        $str = '';
        ksort($this->messages);
        foreach ($this->messages as $m) {
            $str .= implode('', (array)$m);
        }
        return sha1($str);
    }

    /**
     * @return bool
     */
    public function check_permissions() {
        $context = context_module::instance($this->cm->id);
        return has_capability('mod/chat:exportsession', $context)
            || ($this->participated
                && has_capability('mod/chat:exportparticipatedsession', $context));
    }

    /**
     * @todo Document this function
     */
    public function prepare_package() {
        $content = '';
        $lasttime = 0;
        $sessiongap = 5 * 60;    // 5 minutes silence means a new session
        foreach ($this->messages as $message) {  // We are walking FORWARDS through messages
            $m = clone $message; // grrrrrr - this causes the sha1 to change as chat_format_message changes what it's passed.
            $formatmessage = chat_format_message($m, $this->cm->course, $this->user);
            if (!isset($formatmessage->html)) {
                continue;
            }
            if (empty($lasttime) || (($message->timestamp - $lasttime) > $sessiongap)) {
                $content .= '<hr />';
                $content .= userdate($message->timestamp);
            }
            $content .= $formatmessage->html;
            $lasttime = $message->timestamp;
        }
        $content = preg_replace('/\<img[^>]*\>/', '', $content);

        $this->exporter->write_new_file($content, clean_filename($this->cm->name . '-session.html'), false);
    }

    /**
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'chat');
    }

    /**
     * @global object
     * @return string
     */
    public function get_return_url() {
        global $CFG;

        return $CFG->wwwroot . '/mod/chat/report.php?id='
            . $this->cm->id . ((isset($this->start)) ? '&start=' . $this->start . '&end=' . $this->end : '');
    }
}

/**
 * A chat event such a user entering or leaving a chat activity
 *
 * @package    mod_chat
 * @copyright  2012 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_message implements renderable {

    /** @var string The URL of the profile of the user who caused the event */
    public $senderprofile;

    /** @var string The ready to display name of the user who caused the event */
    public $sendername;

    /** @var string Ready to display event time */
    public $time;

    /** @var string Event description */
    public $event;

    /** @var string The chat theme name */
    public $theme;

    /**
     * event_message constructor
     *
     * @param string $senderprofile The URL of the profile of the user who caused the event
     * @param string $sendername The ready to display name of the user who caused the event
     * @param string $time Ready to display event time
     * @param string $theme The chat theme name
     */
    public function __construct($senderprofile, $sendername, $time, $event, $theme) {

        $this->senderprofile = $senderprofile;
        $this->sendername = $sendername;
        $this->time = $time;
        $this->event = $event;
        $this->theme = $theme;
    }
}

/**
 * A chat message from a user
 *
 * @package    mod_chat
 * @copyright  2012 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_message implements renderable {

    /** @var string The URL of the profile of the user sending the message */
    public $senderprofile;

    /** @var string The ready to display name of the user sending the message */
    public $sendername;

    /** @var string HTML for the avatar of the user sending the message */
    public $avatar;

    /** @var string Empty or a html class definition to append to the html */
    public $mymessageclass;

    /** @var string Ready to display message time */
    public $time;

    /** @var string The message */
    public $message;

    /** @var string The name of the chat theme to use */
    public $theme;

    /**
     * user_message constructor
     *
     * @param string $senderprofile The URL of the profile of the user sending the message
     * @param string $sendername The ready to display name of the user sending the message
     * @param string $avatar HTML for the avatar of the user sending the message
     * @param string $mymessageclass Empty or a html class definition to append to the html
     * @param string $time Ready to display message time
     * @param string $message The message
     * @param string $theme The name of the chat theme to use
     */
    public function __construct($senderprofile, $sendername, $avatar, $mymessageclass, $time, $message, $theme) {

        $this->sendername = $sendername;
        $this->avatar = $avatar;
        $this->mymessageclass = $mymessageclass;
        $this->time = $time;
        $this->message = $message;
        $this->theme = $theme;
    }
}
