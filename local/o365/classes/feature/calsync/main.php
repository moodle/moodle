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
 * Calendar sync feature.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\calsync;

use local_o365\oauth2\token;
use local_o365\rest\unified;
use local_o365\utils;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Calendar sync feature.
 */
class main {
    /**
     * @var \local_o365\oauth2\clientdata|null
     */
    protected $clientdata = null;
    /**
     * @var \local_o365\httpclient|null
     */
    protected $httpclient = null;

    /**
     * Constructor.
     *
     * @param \local_o365\oauth2\clientdata|null $clientdata
     * @param \local_o365\httpclient|null $httpclient
     * @throws moodle_exception
     */
    public function __construct(\local_o365\oauth2\clientdata $clientdata = null, \local_o365\httpclient $httpclient = null) {
        $this->clientdata = (!empty($clientdata)) ? $clientdata : \local_o365\oauth2\clientdata::instance_from_oidc();
        $this->httpclient = (!empty($httpclient)) ? $httpclient : new \local_o365\httpclient();
    }

    /**
     * Construct a calendar API client using the system API user.
     *
     * @param int $muserid The userid to get the outlook token for.
     * @param bool $systemfallback
     *
     * @return unified A constructed unified API client, or false if error.
     */
    public function construct_calendar_api($muserid, $systemfallback = true) {
        $tokenresource = unified::get_tokenresource();

        $token = token::instance($muserid, $tokenresource, $this->clientdata, $this->httpclient);
        if (empty($token) && $systemfallback === true) {
            $token = utils::get_app_or_system_token($tokenresource, $this->clientdata, $this->httpclient);
        }
        if (empty($token)) {
            throw new \Exception('No token available for user #'.$muserid);
        }

        $apiclient = new unified($token, $this->httpclient);

        return $apiclient;
    }

    /**
     * Get a token that can be used for calendar syncing.
     *
     * @param int $muserid The ID of a Moodle user to get a token for.
     * @return token|null Either a token for calendar syncing, or null if no token could be retrieved.
     */
    public function get_user_token($muserid) {
        $tokenresource = unified::get_tokenresource();
        $usertoken = token::instance($muserid, $tokenresource, $this->clientdata, $this->httpclient);
        return (!empty($usertoken)) ? $usertoken : null;
    }

    /**
     * Ensures an event is synced for a *single* user.
     *
     * @param int $eventid The ID of the event.
     * @param int $muserid The ID of the user who will own the event.
     * @param string $subject The event's subject.
     * @param string $body The body text of the event.
     * @param int $timestart The timestamp for the event's start.
     * @param int $timeend The timestamp for the event's end.
     * @param string $calid The o365 ID of the calendar to create the event in.
     * @return int The new ID from local_o365_calidmap.
     */
    public function ensure_event_synced_for_user($eventid, $muserid, $subject, $body, $timestart, $timeend, $calid) {
        global $DB;
        $eventsynced = $DB->record_exists('local_o365_calidmap', ['eventid' => $eventid, 'userid' => $muserid]);
        if (!$eventsynced) {
            return $this->create_event_raw($muserid, $eventid, $subject, $body, $timestart, $timeend, [], [], $calid);
        }
    }

    /**
     * Create a calendar event, including all needed local information.
     *
     * @param int $muserid The ID of the Moodle user to communicate as.
     * @param int $eventid The ID of the Moodle event to link to the Outlook event.
     * @param string $subject The event's title/subject.
     * @param string $body The event's body/description.
     * @param int $timestart The timestamp when the event starts.
     * @param int $timeend The timestamp when the event ends.
     * @param array $attendees Array of moodle user objects that are attending the event.
     * @param array $other Other parameters to include.
     * @param string $calid The o365 ID of the calendar to create the event in.
     * @return bool|int The new ID of the calidmap record.
     */
    public function create_event_raw($muserid, $eventid, $subject, $body, $timestart, $timeend, $attendees, array $other = array(),
        $calid) {
        global $DB;
        $apiclient = $this->construct_calendar_api($muserid, true);
        $o365upn = utils::get_o365_upn($muserid);
        if ($o365upn) {
            $response = $apiclient->create_event($subject, $body, $timestart, $timeend, $attendees, $other, $calid, $o365upn);
            $idmaprec = [
                'eventid' => $eventid,
                'outlookeventid' => $response['Id'],
                'userid' => $muserid,
                'origin' => 'moodle',
            ];
            return $DB->insert_record('local_o365_calidmap', (object)$idmaprec);
        } else {
            return false;
        }
    }

    /**
     * Update an event.
     *
     * @param int $muserid The ID of the Moodle user to communicate as.
     * @param string $outlookeventid The event ID in o365 outlook.
     * @param array $updated Array of updated information. Keys are 'subject', 'body', 'starttime', 'endtime', and 'attendees'.
     * @return void
     * @throws moodle_exception
     */
    public function update_event_raw($muserid, $outlookeventid, $updated) {
        $apiclient = $this->construct_calendar_api($muserid, true);
        $o365upn = utils::get_o365_upn($muserid);
        $apiclient->update_event($outlookeventid, $updated, $o365upn);
    }

    /**
     * Delete an event.
     *
     * @param bool $muserid
     * @param string $outlookeventid The event ID in o365 outlook.
     * @param int|null $idmaprecid
     *
     * @return bool Success/Failure.
     */
    public function delete_event_raw($muserid, $outlookeventid, $idmaprecid = null) {
        global $DB;
        $apiclient = $this->construct_calendar_api($muserid, true);
        $o365upn = utils::get_o365_upn($muserid);
        $apiclient->delete_event($outlookeventid, $o365upn);
        if (!empty($idmaprecid)) {
            $DB->delete_records('local_o365_calidmap', ['id' => $idmaprecid]);
        } else {
            $DB->delete_records('local_o365_calidmap', ['outlookeventid' => $outlookeventid]);
        }
    }

    /**
     * Create an outlook event for a newly created Moodle event.
     *
     * @param int $moodleventid The ID of the newly created Moodle event.
     * @return bool Success/Failure.
     */
    public function create_outlook_event_from_moodle_event($moodleventid) {
        global $DB, $SITE;

        // Assemble basic event data.
        $event = $DB->get_record('event', ['id' => $moodleventid]);
        $subject = $event->name;
        $body = $event->description;
        $timestart = $event->timestart;
        $timeend = $timestart + $event->timeduration;

        // Update event name.
        if ($event->eventtype === 'site') {
            $subject = $SITE->fullname . ': ' . $subject;
        } else if ($event->eventtype === 'user') {
            $subject = get_string('personal_calendar', 'local_o365') . ': ' . $subject;
        } else if ($event->eventtype === 'course') {
            $course = $DB->get_record('course', ['id' => $event->courseid]);
            $subject = $course->fullname . ': ' . $subject;
        }

        $body .= $this->get_event_link_html($event);

        // Get attendees.
        if (isset($event->courseid) && $event->courseid == SITEID) {
            // Site event.
            $sql = 'SELECT u.id,
                           u.id as userid,
                           u.email,
                           u.firstname,
                           u.lastname,
                           sub.isprimary as subisprimary,
                           sub.o365calid as subo365calid
                      FROM {user} u
                      JOIN {local_o365_calsub} sub ON sub.user_id = u.id
                     WHERE sub.caltype = ? AND (sub.syncbehav = ? OR sub.syncbehav = ?)';
            $params = ['site', 'out', 'both'];
            $attendees = $DB->get_records_sql($sql, $params);
        } else if (isset($event->courseid) && $event->courseid != SITEID && $event->courseid > 0) {
            // Course event - Get subscribed students.
            if (!empty($event->groupid)) {
                $sql = 'SELECT u.id,
                               u.id as userid,
                               u.email,
                               u.firstname,
                               u.lastname,
                               sub.isprimary as subisprimary,
                               sub.o365calid as subo365calid
                          FROM {user} u
                          JOIN {user_enrolments} ue ON ue.userid = u.id
                          JOIN {enrol} e ON e.id = ue.enrolid
                          JOIN {local_o365_calsub} sub ON sub.user_id = u.id
                               AND sub.caltype = ?
                               AND sub.caltypeid = e.courseid
                               AND (sub.syncbehav = ? OR sub.syncbehav = ?)
                          JOIN {groups_members} grpmbr ON grpmbr.userid = u.id
                         WHERE e.courseid = ? AND grpmbr.groupid = ?';
                $params = ['course', 'out', 'both', $event->courseid, $event->groupid];
                $attendees = $DB->get_records_sql($sql, $params);
            } else {
                $sql = 'SELECT u.id,
                               u.id as userid,
                               u.email,
                               u.firstname,
                               u.lastname,
                               sub.isprimary as subisprimary,
                               sub.o365calid as subo365calid
                          FROM {user} u
                          JOIN {user_enrolments} ue ON ue.userid = u.id
                          JOIN {enrol} e ON e.id = ue.enrolid
                          JOIN {local_o365_calsub} sub ON sub.user_id = u.id
                               AND sub.caltype = ?
                               AND sub.caltypeid = e.courseid
                               AND (sub.syncbehav = ? OR sub.syncbehav = ?)
                         WHERE e.courseid = ?';
                $params = ['course', 'out', 'both', $event->courseid];
                $attendees = $DB->get_records_sql($sql, $params);

                // Retrieve the Outlook group objectid.
                $groupobject = $DB->get_record('local_o365_objects',
                    ['moodleid' => $event->courseid, 'type' => 'group', 'subtype' => 'course']);
                $outlookgroupemail = $this->construct_outlook_group_email($event->courseid);
                // Add the Outlook group user as an attendee and organizer to the event.
                if (!empty($groupobject) && !empty($groupobject->o365name) && !empty($outlookgroupemail)) {
                    // Add o365 group as organizer for the event.
                    $outlookeventorganizer = [
                        'organizer' => [
                            'emailAddress' => [
                                'name' => $groupobject->o365name,
                                'address' => $outlookgroupemail,
                            ]
                        ],
                        'responseRequested' => false,
                        'isOrganizer' => true,
                    ];
                    try {
                        $apiclient = $this->construct_calendar_api($event->userid);
                        $response = $apiclient->create_group_event($subject, $body, $timestart, $timeend, [],
                            $outlookeventorganizer, $groupobject->objectid);
                        if (!empty($response)) {
                            $idmaprec = [
                                'eventid' => $event->id,
                                'outlookeventid' => $response['Id'],
                                'userid' => $event->userid,
                                'origin' => 'moodle',
                            ];
                            $DB->insert_record('local_o365_calidmap', (object)$idmaprec);
                        }
                    } catch (\Exception $e) {
                        // No token found, nothing to do.
                    }
                }
            }
        } else {
            // Personal user event. Only sync if user is subscribed to their events.
            $select = 'caltype = ? AND user_id = ? AND (syncbehav = ? OR syncbehav = ?)';
            $params = ['user', $event->userid, 'out', 'both'];
            $calsub = $DB->get_record_select('local_o365_calsub', $select, $params);
            if (!empty($calsub)) {
                // Send event to o365 and store ID.
                $apiclient = $this->construct_calendar_api($event->userid);
                $calid = (!empty($calsub->o365calid) && empty($calsub->isprimary)) ? $calsub->o365calid : null;
                $o365upn = utils::get_o365_upn($event->userid);
                if ($o365upn) {
                    $response = $apiclient->create_event($subject, $body, $timestart, $timeend, [], [], $calid, $o365upn);
                    $idmaprec = [
                        'eventid' => $event->id,
                        'outlookeventid' => $response['Id'],
                        'userid' => $event->userid,
                        'origin' => 'moodle',
                    ];
                    $DB->insert_record('local_o365_calidmap', (object)$idmaprec);
                } else {
                    return false;
                }
            }
            return true;
        }

        // Move users who've subscribed to non-primary calendars.
        $nonprimarycalsubs = [];
        $eventcreatorsub = null;
        foreach ($attendees as $userid => $attendee) {
            if ($userid == $event->userid) {
                $eventcreatorsub = $attendee;
            }
            if (isset($attendee->subisprimary) && $attendee->subisprimary == '0') {
                $nonprimarycalsubs[] = $attendee;
                unset($attendees[$userid]);
            }
        }

        // Sync primary-calendar users as attendees on a single event.
        if (!empty($attendees)) {
            $apiclient = $this->construct_calendar_api($event->userid);
            $calid = (!empty($eventcreatorsub) && !empty($eventcreatorsub->subo365calid)) ? $eventcreatorsub->subo365calid : null;
            if (isset($eventcreatorsub->subisprimary) && $eventcreatorsub->subisprimary == 1) {
                $calid = null;
            }
            $o365upn = utils::get_o365_upn($event->userid);
            if ($o365upn) {
                $response = $apiclient->create_event($subject, $body, $timestart, $timeend, $attendees, [], $calid, $o365upn);
                $idmaprec = [
                    'eventid' => $event->id,
                    'outlookeventid' => $response['Id'],
                    'userid' => $event->userid,
                    'origin' => 'moodle',
                ];
                $DB->insert_record('local_o365_calidmap', (object)$idmaprec);
            }
        }

        // Sync non-primary attendees individually.
        foreach ($nonprimarycalsubs as $attendee) {
            $apiclient = $this->construct_calendar_api($attendee->id);
            $calid = (!empty($attendee->subo365calid)) ? $attendee->subo365calid : null;
            $o365upn = utils::get_o365_upn($attendee->userid);
            if ($o365upn) {
                $response = $apiclient->create_event($subject, $body, $timestart, $timeend, [], [], $calid, $o365upn);
                $idmaprec = [
                    'eventid' => $event->id,
                    'outlookeventid' => $response['Id'],
                    'userid' => $attendee->userid,
                    'origin' => 'moodle',
                ];
                $DB->insert_record('local_o365_calidmap', (object)$idmaprec);
            }
        }

        return true;
    }

    /**
     * Get user calendars.
     *
     * @return array Array of user calendars.
     */
    public function get_calendars() {
        global $USER;
        $apiclient = $this->construct_calendar_api($USER->id, false);
        $o365upn = utils::get_o365_upn($USER->id);
        $calendarresults = $apiclient->get_calendars($o365upn);
        $calendars = $calendarresults['value'];
        while (!empty($calendarresults['@odata.nextLink'])) {
            $nextlink = parse_url($calendarresults['@odata.nextLink']);
            $calendarresults = [];
            if (isset($nextlink['query'])) {
                $query = [];
                parse_str($nextlink['query'], $query);
                if (isset($query['$skip'])) {
                    $calendarresults = $apiclient->get_calendars($o365upn, $query['$skip']);
                    $calendars = array_merge($calendars, $calendarresults['value']);
                }
            }
        }

        return (!empty($calendars) && is_array($calendars)) ? $calendars : [];
    }

    /**
     * Get events for a given user in a given calendar.
     *
     * @param int $muserid The ID of the Moodle user to get events as.
     * @param string $o365calid The ID of the o365 calendar to get events from.
     * @param int $since Timestamp to fetch events since.
     * @return array Array of events.
     */
    public function get_events($muserid, $o365calid, $since = null) {
        $apiclient = $this->construct_calendar_api($muserid, false);
        $o365upn = utils::get_o365_upn($muserid);
        $eventresults = $apiclient->get_events($o365calid, $since, $o365upn);
        $events = $eventresults['value'];
        while (!empty($eventresults['@odata.nextLink'])) {
            $nextlink = parse_url($eventresults['@odata.nextLink']);
            $eventresults = [];
            if (isset($nextlink['query'])) {
                $query = [];
                parse_str($nextlink['query'], $query);
                if (isset($query['$skip'])) {
                    $eventresults = $apiclient->get_events($o365calid, $since, $o365upn, $query['$skip']);
                    $events = array_merge($events, $eventresults['value']);
                }
            }
        }

        return $events;
    }

    /**
     * Update an already-synced event with new information.
     *
     * @param int $moodleeventid The ID of an updated Moodle event.
     * @return bool Success/Failure.
     */
    public function update_outlook_event($moodleeventid) {
        global $DB, $SITE;

        // Get o365 event id (and determine if we can sync this event).
        $idmaprecs = $DB->get_records('local_o365_calidmap', ['eventid' => $moodleeventid]);
        if (empty($idmaprecs)) {
            return true;
        }

        // Send updated information to o365.
        $event = $DB->get_record('event', ['id' => $moodleeventid]);
        if (empty($event)) {
            return true;
        }

        $updated = [
            'subject' => $event->name,
            'body' => $event->description,
            'starttime' => $event->timestart,
            'endtime' => $event->timestart + $event->timeduration,
        ];

        // Update event name.
        if ($event->eventtype === 'site') {
            $updated['subject'] = $SITE->fullname . ': ' . $updated['subject'];
        } else if ($event->eventtype === 'user') {
            $updated['subject'] = get_string('personal_calendar', 'local_o365') . ': ' . $updated['subject'];
        } else if ($event->eventtype === 'course') {
            $course = $DB->get_record('course', ['id' => $event->courseid]);
            $updated['subject'] = $course->fullname . ': ' . $updated['subject'];
        }

        $updated['body'] .= $this->get_event_link_html($event);

        foreach ($idmaprecs as $idmaprec) {
            $apiclient = $this->construct_calendar_api($idmaprec->userid);
            $o365upn = utils::get_o365_upn($idmaprec->userid);
            try {
                $apiclient->update_event($idmaprec->outlookeventid, $updated, $o365upn);
            } catch (moodle_exception $e) {
                // Do nothing.
            }
        }
        return true;
    }

    /**
     * Delete all synced outlook event for a given Moodle event.
     *
     * @param int $moodleeventid The ID of a Moodle event.
     * @return bool Success/Failure.
     */
    public function delete_outlook_event($moodleeventid) {
        global $DB;

        // Get o365 event ids (and determine if we can sync this event).
        $idmaprecs = $DB->get_records('local_o365_calidmap', ['eventid' => $moodleeventid]);
        if (empty($idmaprecs)) {
            return true;
        }

        foreach ($idmaprecs as $idmaprec) {
            $apiclient = $this->construct_calendar_api($idmaprec->userid);
            $o365upn = utils::get_o365_upn($idmaprec->userid);
            $apiclient->delete_event($idmaprec->outlookeventid, $o365upn);
        }

        // Clean up idmap table.
        $DB->delete_records('local_o365_calidmap', ['eventid' => $moodleeventid]);

        return true;
    }

    /**
     * Construct the o365 group email.
     *
     * @param int $courseid
     *
     * @return string The o365 group email, or an empty string if an error occurred.
     */
    protected function construct_outlook_group_email($courseid) {
        global $DB;
        // Assemble Moodle course data and reconstruct the o365 group email.
        $groupprefix = $DB->get_field('course', 'shortname', ['id' => SITEID]);
        $groupname = $DB->get_field('course', 'shortname', ['id' => $courseid]);
        $tenant = get_config('local_o365', 'aadtenant');
        $groupemail = '';

        // If the course shortname and the Azure AD tenant are not empty.
        if (!empty($groupprefix) && !empty($tenant)) {
            $mailnickprefix = \core_text::strtolower($groupprefix);
            $mailnickprefix = preg_replace('/[^a-z0-9]+/iu', '', $mailnickprefix);
            $groupemail = $mailnickprefix.'_'.$groupname."@{$tenant}";
        }

        return $groupemail;
    }

    /**
     * Get group first and last name.
     * @param string $groupname The o365 group name.
     * @return array The first index is the first name and the second index is the last name.
     */
    protected function group_first_last_name($groupname) {
        $firstname = '';
        $lastname = '';
        if (empty($groupname)) {
            return array($firstname, $lastname);
        }

        $pos = strpos($groupname, ': ');

        if (false === $pos) {
            return array($firstname, $lastname);
        }

        $firstname = substr($groupname, 0, $pos + 1);
        $lastname = substr($groupname, $pos + 1);
        $lastname = trim($lastname);
        return array($firstname, $lastname);
    }

    /**
     * Create a new calendar in the user's o365 calendars.
     *
     * @param string $name The calendar's title.
     * @return array|null Returned response, or null if error.
     */
    public function create_outlook_calendar($name) {
        global $USER;
        $apiclient = $this->construct_calendar_api($USER->id, false);
        $o365upn = utils::get_o365_upn($USER->id);
        return $apiclient->create_calendar($name, $o365upn);
    }

    /**
     * Update a existing o365 calendar.
     *
     * @param string $outlookcalendearid The calendar's title.
     * @param array $updated Array of updated information. Keys are 'name'.
     * @return array|null Returned response, or null if error.
     */
    public function update_outlook_calendar($outlookcalendearid, $updated) {
        global $USER;
        $apiclient = $this->construct_calendar_api($USER->id, false);
        $o365upn = utils::get_o365_upn($USER->id);
        return $apiclient->update_calendar($outlookcalendearid, $updated, $o365upn);
    }

    /**
     * Get Moodle event link and it's HTML.
     *
     * @param object $event The Moodle event database object.
     * @return string Moodle event HTML with link.
     */
    public function get_event_link_html($event) {
        // Update event description.
        if (isset($event->courseid) && $event->courseid == SITEID) {
            $moodleeventurl = new \moodle_url('/calendar/view.php?view=day&time='.$event->timestart.'#event_'.$event->id);
        } else if (isset($event->courseid) && $event->courseid != SITEID && $event->courseid > 0) {
            $moodleeventurl = new \moodle_url('/calendar/view.php?course='.$event->courseid.'&view=day&time='.$event->timestart.
                '#event_'.$event->id);
        } else {
            $moodleeventurl = new \moodle_url('/calendar/view.php?view=day&time='.$event->timestart.'#event_'.$event->id);
        }

        $linkhtml = \html_writer::link($moodleeventurl, get_string('calendar_event', 'local_o365'));
        $fulllinkhtml = \html_writer::link($moodleeventurl, $moodleeventurl);
        $spanhtml = \html_writer::span($linkhtml.\html_writer::empty_tag('br').$fulllinkhtml);
        return \html_writer::empty_tag('br').\html_writer::tag('p', $spanhtml);
    }
}
