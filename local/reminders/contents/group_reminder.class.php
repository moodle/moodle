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
 * Group event reminder handler.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->dirroot . '/local/reminders/reminder.class.php');
require_once($CFG->libdir . '/accesslib.php');

/**
 * Class to specify the reminder message object for group events.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_reminder extends local_reminder {

    /**
     * group reference.
     *
     * @var object
     */
    private $group;
    /**
     * course reference.
     *
     * @var object
     */
    private $course;
    /**
     * course module context reference.
     *
     * @var object
     */
    private $cm;

    /**
     * activity reference.
     *
     * @var object
     */
    private $activityobj;
    /**
     * module name.
     *
     * @var string
     */
    private $modname;

    /**
     * Creates a new group event instance.
     *
     * @param object $event calendar event.
     * @param object $group group instance.
     * @param integer $aheaddays number of days ahead.
     * @param object $customtime contains the custom time value and unit (if configured).
     */
    public function __construct($event, $group, $aheaddays = 1, $customtime = null) {
        parent::__construct($event, $aheaddays, $customtime);
        $this->group = $group;
        $this->load_course_object();
    }

    /**
     * Cleanup this reminder instance.
     */
    public function cleanup() {
        parent::cleanup();

        if (isset($this->activityobj)) {
            unset($this->activityobj);
        }
    }

    /**
     * Set activity instance if there is any.
     *
     * @param string $modulename module name.
     * @param object $activity activity instance
     */
    public function set_activity($modulename, $activity) {
        $this->activityobj = $activity;
        $this->modname = $modulename;
    }

    /**
     * Loads course reference using provided group reference.
     *
     * @return void.
     */
    private function load_course_object() {
        global $DB;

        $this->course = $DB->get_record('course', ['id' => $this->group->courseid]);
        if (!empty($this->course) && !empty($this->event->instance)) {
            $cmx = get_coursemodule_from_instance($this->event->modulename, $this->event->instance, $this->group->courseid);
            if (!empty($cmx)) {
                $this->cm = get_context_instance(CONTEXT_MODULE, $cmx->id);
            }
        }
    }

    /**
     * Generates a message content as a HTML for group reminder.
     *
     * @param object $user The user object
     * @param object $changetype change type (add/update/removed)
     * @param stdClass $ctxinfo additional context info needed to process.
     * @return string Message content as HTML text.
     */
    public function get_message_html($user=null, $changetype=null, $ctxinfo=null) {
        global $CFG;

        $htmlmail = $this->get_html_header();
        $htmlmail .= html_writer::start_tag('body', ['id' => 'email']);
        $htmlmail .= $this->get_reminder_header();
        $htmlmail .= html_writer::start_tag('div');
        $htmlmail .= html_writer::start_tag('table',
                ['cellspacing' => 0, 'cellpadding' => 8, 'style' => $this->tbodycssstyle]);

        $contenttitle = $this->get_message_title();
        if (!isemptystring($changetype)) {
            $titleprefixlangstr = get_string('calendarevent'.strtolower($changetype).'prefix', 'local_reminders');
            $contenttitle = "[$titleprefixlangstr]: $contenttitle";
        }
        $htmlmail .= html_writer::start_tag('tr');
        $htmlmail .= html_writer::start_tag('td', ['colspan' => 2]);
        $htmlmail .= html_writer::link($this->generate_event_link(),
                html_writer::tag('h3', $contenttitle, ['style' => $this->titlestyle]),
                ['style' => 'text-decoration: none']);
        $htmlmail .= html_writer::end_tag('td').html_writer::end_tag('tr');

        $htmlmail .= $this->write_table_row(get_string('contentwhen', 'local_reminders'),
            format_event_time_duration($user, $this->event));
        $htmlmail .= $this->write_location_info($this->event);

        if (!empty($this->course)) {
            $htmlmail .= $this->write_table_row(get_string('contenttypecourse', 'local_reminders'), $this->course->fullname);
        }

        if (!empty($this->cm)) {
            $cmlink = html_writer::link($this->cm->get_url(), $this->cm->get_context_name());
            $htmlmail .= $this->write_table_row(get_string('contenttypeactivity', 'local_reminders'),
                $cmlink, ['target' => '_blank'], false);
        }

        if (isset($CFG->local_reminders_groupshowname) && $CFG->local_reminders_groupshowname) {
            $htmlmail .= $this->write_table_row(get_string('contenttypegroup', 'local_reminders'), $this->group->name);
        }

        $formattercls = null;
        if (!empty($this->modname) && !empty($this->activityobj)) {
            $clsname = 'local_reminder_'.$this->modname.'_handler';
            if (class_exists($clsname)) {
                $formattercls = new $clsname;
                $formattercls->append_info($htmlmail, $this->modname, $this->activityobj, $user, $this->event);
            }
        }

        $description = isset($formattercls) ? $formattercls->get_description($this->activityobj, $this->event) :
            $this->event->description;
        $htmlmail .= $this->write_description($description, $this->event);

        $htmlmail .= $this->get_html_footer();
        $htmlmail .= html_writer::end_tag('table').html_writer::end_tag('div').html_writer::end_tag('body').
                html_writer::end_tag('html');

        return $htmlmail;
    }

    /**
     * Generates a message content as a plain-text for group reminder.
     *
     * @param object $user The user object
     * @param object $changetype change type (add/update/removed)
     * @return string Message content as plain-text.
     */
    public function get_message_plaintext($user=null, $changetype=null) {
        $text = $this->get_message_title().' '.$this->get_aheaddays_plain()."\n";
        $text .= get_string('contentwhen', 'local_reminders').': '.$this->get_tzinfo_plain($user, $this->event)."\n";
        if (!empty($this->course)) {
            $text .= get_string('contenttypecourse', 'local_reminders').': '.$this->course->fullname."\n";
        }
        if (!empty($this->cm)) {
            $text .= get_string('contenttypeactivity', 'local_reminders').': '.$this->cm->get_context_name()."\n";
        }
        $text .= get_string('contenttypegroup', 'local_reminders').': '.$this->group->name."\n";
        $text .= get_string('contentdescription', 'local_reminders').': '.$this->event->description."\n";

        return $text;
    }

    /**
     * Returns 'reminders_group' name.
     *
     * @return string Message provider name
     */
    protected function get_message_provider() {
        return 'reminders_group';
    }

    /**
     * Generates a message title for the group reminder.
     *
     * @param string $type type of message to be send (null=reminder cron)
     * @return string Message title as a plain-text.
     */
    public function get_message_title($type=null) {
        $title = '';
        if (!empty($this->course)) {
            $title .= '('.$this->course->shortname;
            if (!empty($this->cm)) {
                $title .= '-'.get_string('modulename', $this->event->modulename);
            }
            $title .= ') ';
        }
        $title .= $this->event->name;
        return $title;
    }

    /**
     * Adds group id and activity id (if exists) to header.
     *
     * @return array additional headers.
     */
    public function get_custom_headers() {
        $headers = parent::get_custom_headers();

        $headers[] = 'X-Group-Id: '.$this->group->id;
        if (!empty($this->cm)) {
            $headers[] = 'X-Activity-Id: '.$this->cm->id;
        }
        return $headers;
    }
}
