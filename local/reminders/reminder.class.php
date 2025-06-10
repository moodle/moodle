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
 * Contains base class for all reminder instances.
 *
 * @package    local_reminders
 * @author     Isuru Weerarathna <uisurumadushanka89@gmail.com>
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Abstract class for reminder object.
 *
 * This abstract reminder class will be used to implement behaviours of different
 * types of event types.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class local_reminder {

    /**
     * @var int number of days in advance to actual event.
     */
    protected $aheaddays;

    /**
     * @var object custom time ahead of the actual event.
     */
    protected $customtime;

    /**
     * @var int indicates immediate sending of message as a notification.
     */
    protected $notification = 1;

    /**
     * @var object event object correspond to this reminder.
     */
    protected $event;
    /**
     * CSS styles for body content.
     *
     * @var string
     */
    protected $tbodycssstyle = 'width:100%;'.
        'font-family:Tahoma,Arial,Sans-serif;'.
        'border-width:1px 2px 2px 1px;'.
        'border:1px solid #ccc;'.
        'font-size:13px';
    /**
     * CSS styles for title content.
     *
     * @var string
     */
    protected $titlestyle = 'padding:10px 0 10px 0;'.
        'margin:0;'.
        'font-family:Arial,Sans-serif;'.
        'font-size:16px;'.
        'font-weight:bold;'.
        'color:#222';
    /**
     * CSS styles for overdue content.
     *
     * @var string
     */
    protected $overduestyle = 'padding:10px 0 10px 10px;'.
        'background-color: #f3e3e3;'.
        'margin:0;'.
        'font-family:Arial,Sans-serif;'.
        'font-size:16px;'.
        'font-weight:bold;'.
        'color:#ec4040';
    /**
     * CSS styles for footer content.
     *
     * @var string
     */
    protected $footerstyle = 'background-color:#f6f6f6;'.
        'color:#888;'.
        'border-top:1px solid #ccc;'.
        'font-family:Arial,Sans-serif;'.
        'font-size:11px;'.
        'padding: 0px;';
    /**
     * CSS styles for default footer content.
     *
     * @var string
     */
    protected $footerdefstyle = 'background-color:#f6f6f6;'.
        'color:#888;'.
        'border-top:1px solid #ccc;'.
        'font-family:Arial,Sans-serif;'.
        'font-size:11px;'.
        'padding: 20px 10px;';
    /**
     * CSS style for description div.
     *
     * @var string
     */
    protected $descstyle = 'border-top:1px solid #eee;'.
        'font-family:Arial,Sans-serif;'.
        'font-size:13px;'.
        'padding: 2px 15px;';
    /**
     * CSS style for title header.
     *
     * @var string
     */
    protected $defheaderstyle = 'padding: 0 15px; color: #888; width: 150px;';
    /**
     * Style for timezone span.
     *
     * @var string
     */
    public $tzshowstyle = 'font-size:13px;color: #888;';

    /**
     *
     * @var object cahced reminder message object. This will be reused for other users too.
     */
    public $eventobject;

    /**
     * Creates a new reminder instance with event and no of days ahead value.
     *
     * @param object $event calendar event.
     * @param integer $aheaddays number of days ahead.
     * @param object $customtime contains the custom time value and unit (if configured).
     */
    public function __construct($event, $aheaddays = 1, $customtime=null) {
        $this->event = $event;
        $this->aheaddays = $aheaddays;
        $this->customtime = $customtime;
    }

    /**
     * Clean up this instance.
     */
    public function cleanup() {
        if (isset($this->eventobject)) {
            unset($this->eventobject);
        }
    }

    /**
     * Filter out users who still does not have completed this activity.
     *
     * @param array $users user array to check.
     * @param string $type type of request. Pre|Post for now.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type=null) {
        return $users;
    }

    /**
     * Writes an email row including header and its value.
     *
     * @param string $headervalue string the content for header
     * @param string $value string value to show
     * @param array $customizedstyle array of style values.
     * @param array $overridestyle boolean to override or not the specified styles if given.
     * @return string generated html row.
     */
    public function write_table_row($headervalue, $value, $customizedstyle=null, $overridestyle=true) {
        $htmltext = html_writer::start_tag('tr');
        $defheadercss = ['style' => $this->defheaderstyle];
        if (isset($customizedstyle)) {
            $finalstyles = $customizedstyle;
            if (!$overridestyle) {
                $finalstyles = array_merge($defheadercss, $customizedstyle);
            }
            $htmltext .= html_writer::tag('td', $headervalue, $finalstyles);
        } else {
            $htmltext .= html_writer::tag('td', $headervalue, $defheadercss);
        }
        $htmltext .= html_writer::tag('td', $value);
        return $htmltext.html_writer::end_tag('tr');
    }

    /**
     * Write location to the email if exists.
     *
     * @param object $event event instance.
     */
    protected function write_location_info($event) {
        if (isset($event->location) && !empty($event->location)) {
            return self::write_table_row(get_string('contenttypelocation', 'local_reminders'), $event->location);
        }
    }

    /**
     * Writes the description to the email.
     *
     * @param string $description event description.
     * @param object $event event instance.
     * @return string description content.
     */
    protected function write_description($description, $event) {
        $htmltext = html_writer::start_tag('tr');
        $columndescstyle = ['style' => $this->descstyle, 'colspan' => 2];
        if (isemptystring($description)) {
            $htmltext .= html_writer::tag('td', "<p>$event->name</p>", $columndescstyle);
        } else {
            if (substr($description, strlen('<p>')) == '<p>') {
                $htmltext .= html_writer::tag('td', $description, $columndescstyle);
            } else {
                $htmltext .= html_writer::tag('td', "<p>$description</p>", $columndescstyle);
            }
        }
        return $htmltext.html_writer::end_tag('tr');
    }

    /**
     * Gets the header content of the e-mail message.
     *
     * @return string return header.
     */
    protected function get_html_header() {
        return html_writer::tag('head', '');
    }

    /**
     * Returns the header content of the reminder email. This part can be use to brand
     * all of these email messages, such as with logo etc.
     *
     */
    protected function get_reminder_header() {
        global $CFG;

        if (isset($CFG->local_reminders_emailheadercustom) && trim($CFG->local_reminders_emailheadercustom) !== '') {
            $htmltext = html_writer::start_tag('div');
            $htmltext .= text_to_html($CFG->local_reminders_emailheadercustom, false, false, true);
            return $htmltext.html_writer::end_tag('div');
        }
        return '';
    }

    /**
     * Returns time zone info for a user in plain text format.
     *
     * @param object $user object.
     * @param object $event reference instance.
     * @return string timezone info as plain text.
     */
    protected function get_tzinfo_plain($user, $event) {
        return format_event_time_duration($user, $event, null, true, 'plain');
    }

    /**
     * Returns number of ahead days as a plain text.
     * The format looks like: "[# days to go]" when aheaddays > 0.
     * For custom scehdules: "[# {timeunit}s to go]".
     *
     * @return string number of days/time units in advance as a text.
     */
    protected function get_aheaddays_plain() {
        if ($this->aheaddays != 0) {
            return '['.$this->pluralize($this->aheaddays, ' day').' to go]';
        } else {
            return '['.$this->pluralize($this->customtime->value, ' ' . $this->customtime->unit).' to go]';
        }
    }

    /**
     * Pluralize given text by appending 's' if number if greater than 1.
     *
     * @param int $number number to check.
     * @param string $text text to append with number.
     * @return string pluralized string if necessary.
     */
    protected function pluralize($number, $text) {
        return $number.($number > 1 ? $text.'s' : $text);
    }

    /**
     * Gets the footer content of the e-mail message.
     *
     * @return string footer content.
     */
    protected function get_html_footer() {
        global $CFG;

        $footer = html_writer::start_tag('tr');
        $moodlecalendarname = get_string('moodlecalendarname', 'local_reminders');
        $calendarlink = html_writer::link($CFG->wwwroot.'/calendar/index.php', $moodlecalendarname, ['target' => '_blank']);

        if (isset($CFG->local_reminders_emailfooterdefaultenabled) && $CFG->local_reminders_emailfooterdefaultenabled) {
            $footer .= html_writer::start_tag('td', ['style' => $this->footerdefstyle, 'colspan' => 2]);
            $footer .= get_string('reminderfrom', 'local_reminders').' ';
            $footer .= $calendarlink;

        } else if (isset($CFG->local_reminders_emailfootercustom) && trim($CFG->local_reminders_emailfootercustom) !== '') {
            $footer .= html_writer::start_tag('td', ['style' => $this->footerstyle, 'colspan' => 2]);
            $footer .= text_to_html($CFG->local_reminders_emailfootercustom, false, false, true);

        } else {
            return '';
        }

        $footer .= html_writer::end_tag('td').html_writer::end_tag('tr');
        return $footer;
    }

    /**
     * Returns the correct link for the calendar event.
     *
     * @return string complete url for the event
     */
    protected function generate_event_link() {
        $params = [
            'view' => 'day', 'cal_d' => date('j', $this->event->timestart),
            'cal_m' => date('n', $this->event->timestart), 'cal_y' => date('Y', $this->event->timestart),
        ];
        $calurl = new moodle_url('/calendar/view.php', $params);
        $calurl->set_anchor('event_'.$this->event->id);

        return $calurl->out(false);
    }

    /**
     * This function setup the corresponding message provider for each
     * reminder type. It would be called everytime at the constructor.
     *
     * @return string Message provider name
     */
    abstract protected function get_message_provider();

    /**
     * Generates a message content as a HTML. Suitable for email messages.
     *
     * @param object $user The user object
     * @param object $changetype change type (add/update/removed)
     * @param stdClass $ctxinfo additional context info needed to process.
     * @return string Message content as HTML text.
     */
    abstract public function get_message_html($user=null, $changetype=null, $ctxinfo=null);

    /**
     * Generates a message content as a plain-text. Suitable for popup messages.
     *
     * @param object $user The user object
     * @param object $changetype change type (add/update/removed)
     * @return string Message content as plain-text.
     */
    abstract public function get_message_plaintext($user=null, $changetype=null);

    /**
     * Generates a message title for the reminder. Used for all message types.
     *
     * @param string $type type of message to be send (null=reminder cron)
     * @return string Message title as a plain-text.
     */
    abstract public function get_message_title($type=null);

    /**
     * Gets an array of custom headers for the reminder message, specially
     * for e-mails. For e-mails they will be easier to track when
     * several e-mail reminders are received for a particular event. <br>
     * If no header is wanted, just simply returns an empty array.
     *
     * @return array array of strings containing header attributes.
     */
    public function get_custom_headers() {
        global $CFG;

        $urlinfo = parse_url($CFG->wwwroot);
        $hostname = $urlinfo['host'];

        return ['Message-ID: <moodlereminder'.$this->event->id.'@'.$hostname.'>'];
    }

    /**
     * Creates the final reminder message object from given information.
     *
     * @param object $admin impersonated user for sending messages. This
     *          name will display in 'from' field in every reminder message.
     *
     * @return object a message object which will be sent to the messaging API
     */
    public function create_reminder_message_object($admin=null) {
        global $CFG;

        if ($admin == null) {
            $admin = get_admin();
        }

        $contenthtml = $this->get_message_html();
        $titlehtml = $this->get_message_title();
        $subjectprefix = get_string('titlesubjectprefix', 'local_reminders');
        if (isset($CFG->local_reminders_messagetitleprefix)) {
            if (!empty($CFG->local_reminders_messagetitleprefix)) {
                $subjectprefix = $CFG->local_reminders_messagetitleprefix;
            } else {
                $subjectprefix = '';
            }
        }

        $msgtitle = '['.$subjectprefix.'] '.$titlehtml;
        if (empty($subjectprefix)) {
            $msgtitle = $titlehtml;
        }

        $cheaders = $this->get_custom_headers();
        if (!empty($cheaders)) {
            $admin->customheaders = $cheaders;
        }

         // BUG FIX: $eventdata must be a new \core\message\message() for Moodle 3.5+.
        $eventdata = new \core\message\message();

        $eventdata->component           = 'local_reminders';
        $eventdata->name                = $this->get_message_provider();
        $eventdata->userfrom            = $admin;
        $eventdata->subject             = $msgtitle;
        $eventdata->fullmessage         = $this->get_message_plaintext();
        $eventdata->fullmessageformat   = FORMAT_PLAIN;
        $eventdata->fullmessagehtml     = $contenthtml;
        $eventdata->smallmessage        = $titlehtml . ' - ' . $contenthtml;
        $eventdata->notification        = $this->notification;

        // Save created object with reminder object.
        $this->eventobject = $eventdata;

        return $eventdata;
    }

    /**
     * Assign user which the reminder message is sent to
     *
     * @param object $user user object (id field must contain)
     * @param boolean $refreshcontent indicates whether content of message should
     * be refresh based on given user
     * @param object $fromuser sending user.
     * @return event object notification instance.
     */
    public function set_sendto_user($user, $refreshcontent=true, $fromuser=null) {
        if (!isset($this->eventobject) || empty($this->eventobject)) {
            $this->create_reminder_message_object();
        }

        $this->eventobject->userto = $user;
        if (isset($fromuser)) {
            $this->eventobject->userfrom = $fromuser;
        }

        if ($refreshcontent) {
            $contenthtml = $this->get_message_html($user);
            $titlehtml = $this->get_message_title();
            $smallmsg = $this->get_message_plaintext($user);

            $this->eventobject->fullmessagehtml = $contenthtml;
            $this->eventobject->smallmessage = $smallmsg;
            $this->eventobject->fullmessage = $smallmsg;
        }

        return $this->eventobject;
    }

    /**
     * Returns the sending notification instance from user to user.
     *
     * @param object $fromuser from user.
     * @param object $touser to user.
     * @return object notification instance.
     */
    public function get_sending_event($fromuser, $touser) {
        return $this->set_sendto_user($touser, true, $fromuser);
    }

    /**
     * Returns appropiate email title prefix based on changed type.
     *
     * @param object $changetype change type.
     * @param stdClass $ctxinfo additional context information.
     * @return string prefix to be appended.
     */
    protected function get_relavant_title_prefix($changetype, $ctxinfo=null) {
        $toreturn = '';
        if ($changetype == REMINDERS_CALL_TYPE_OVERDUE) {
            if (!is_null($ctxinfo) && property_exists($ctxinfo, 'overduetitle') && !isemptystring($ctxinfo->overduetitle)) {
                $toreturn = $ctxinfo->overduetitle;
            }
        } else {
            $toreturn = get_string('calendarevent'.strtolower($changetype).'prefix', 'local_reminders');
        }
        return !empty($toreturn) ? $toreturn.':' : '';
    }

    /**
     * Returns the sending notification instance from user to user with change type.
     *
     * @param string $changetype change type.
     * @param object $admin admin user.
     * @param object $touser to user.
     * @param stdClass $ctxinfo additional context information.
     * @return object notification instance.
     */
    public function get_updating_event_message($changetype, $admin=null, $touser=null, $ctxinfo=null) {
        global $CFG;

        $fromuser = $admin;
        if ($fromuser == null) {
            $fromuser = get_admin();
        }

        $contenthtml = $this->get_message_html($touser, $changetype, $ctxinfo);
        $titleprefixlangstr = $this->get_relavant_title_prefix($changetype, $ctxinfo);
        $titlehtml = $this->get_message_title($changetype);
        $subjectprefix = get_string('titlesubjectprefix', 'local_reminders');
        if (isset($CFG->local_reminders_messagetitleprefix)) {
            if (!empty($CFG->local_reminders_messagetitleprefix)) {
                $subjectprefix = $CFG->local_reminders_messagetitleprefix;
            } else {
                $subjectprefix = '';
            }
        }

        $msgtitle = '['.$subjectprefix.'] '.$titleprefixlangstr.' '.$titlehtml;
        if (empty($subjectprefix)) {
            $msgtitle = $titleprefixlangstr.' '.$titlehtml;
        }

        $cheaders = $this->get_custom_headers();
        if (!empty($cheaders)) {
            $fromuser->customheaders = $cheaders;
        }

        $smallmsg = $this->get_message_plaintext($touser, $changetype);

         // BUG FIX: $eventdata must be a new \core\message\message() for Moodle 3.5+.
        $eventdata = new \core\message\message();

        $eventdata->component           = 'local_reminders';
        $eventdata->name                = $this->get_message_provider();
        $eventdata->userfrom            = $fromuser;
        $eventdata->userto              = $touser;
        $eventdata->subject             = trim($msgtitle);
        $eventdata->fullmessage         = $smallmsg;
        $eventdata->fullmessageformat   = FORMAT_PLAIN;
        $eventdata->fullmessagehtml     = $contenthtml;
        $eventdata->smallmessage        = $smallmsg;
        $eventdata->notification        = $this->notification;

        return $eventdata;
    }

}
