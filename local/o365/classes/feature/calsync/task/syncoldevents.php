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
 * AdHoc task to sync existing Moodle calendar events with Microsoft 365.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\calsync\task;

use local_o365\utils;
use moodle_exception;

/**
 * AdHoc task to sync existing Moodle calendar events with Microsoft 365.
 *
 * This is run when users subscribe or unsubscribe from calendars.
 */
class syncoldevents extends \core\task\adhoc_task {
    /**
     * Get subscribers for a given calendar type and (optionally) id.
     *
     * @param string $caltype The calendar type.
     * @param int $caltypeid The calendar type ID.
     * @return array A list of arrays subscribers using their primary and non-primary calendars.
     */
    protected function get_subscribers($caltype, $caltypeid = null) {
        global $DB;
        $subscribersprimary = [];
        $subscribersnotprimary = [];
        $sql = 'SELECT u.id,
                       u.email,
                       u.firstname,
                       u.lastname,
                       sub.isprimary as subisprimary,
                       sub.o365calid as subo365calid
                  FROM {user} u
                  JOIN {local_o365_calsub} sub ON sub.user_id = u.id
                 WHERE sub.caltype = ? AND (sub.syncbehav = ? OR sub.syncbehav = ?)';
        $params = [$caltype, 'out', 'both'];
        if (!empty($caltypeid)) {
            $sql .= ' AND sub.caltypeid = ? ';
            $params[] = $caltypeid;
        }
        $allsubscribers = $DB->get_records_sql($sql, $params);
        foreach ($allsubscribers as $userid => $subscriber) {
            if (isset($subscriber->subisprimary) && $subscriber->subisprimary == '0') {
                $subscribersnotprimary[$userid] = $subscriber;
            } else {
                $subscribersprimary[$userid] = $subscriber;
            }
        }
        unset($allsubscribers);
        return [$subscribersprimary, $subscribersnotprimary];
    }

    /**
     * Sync all site events with Outlook.
     *
     * @param int $timecreated The time the task was created.
     */
    protected function sync_siteevents($timecreated) {
        global $DB;
        $timestart = time();
        // Check the last time site events were synced. Using a direct query here so we don't run into static cache issues.
        $lastsitesync = $DB->get_record('config_plugins', ['plugin' => 'local_o365', 'name' => 'cal_site_lastsync']);
        if (!empty($lastsitesync) && (int)$lastsitesync->value > $timecreated) {
            // Site events have been synced since this event was created, so we don't have to do it again.
            return true;
        }

        $clientdata = \local_o365\oauth2\clientdata::instance_from_oidc();
        $httpclient = new \local_o365\httpclient();
        $calsync = new \local_o365\feature\calsync\main($clientdata, $httpclient);

        [$subscribersprimary, $subscribersnotprimary] = $this->get_subscribers('site');

        $sql = 'SELECT ev.id AS eventid,
                       ev.name AS eventname,
                       ev.description AS eventdescription,
                       ev.timestart AS eventtimestart,
                       ev.timeduration AS eventtimeduration,
                       idmap.outlookeventid,
                       ev.userid AS eventuserid,
                       idmap.id AS idmapid
                  FROM {event} ev
             LEFT JOIN {local_o365_calidmap} idmap ON ev.id = idmap.eventid AND idmap.userid = ev.userid
                 WHERE ev.courseid = ?';
        $params = [SITEID];
        $events = $DB->get_recordset_sql($sql, $params);
        foreach ($events as $event) {
            try {
                mtrace('Syncing site event #'.$event->eventid);
                $subject = $event->eventname;
                $body = $event->eventdescription;
                $evstart = $event->eventtimestart;
                $evend = $evstart + $event->eventtimeduration;

                // Sync primary cal users first.
                if (!empty($subscribersprimary)) {
                    mtrace('Syncing primary calendar users.');
                    try {
                        // If there's a stored outlookeventid we've already synced to o365 so update it. Otherwise create it.
                        if (!empty($event->outlookeventid)) {
                            try {
                                $calsync->update_event_raw($event->eventuserid, $event->outlookeventid,
                                    ['attendees' => $subscribersprimary]);
                            } catch (moodle_exception $e) {
                                mtrace('ERROR: ' . $e->getMessage());
                            }
                        } else {
                            $calid = null;
                            if (!empty($subscribersprimary[$event->eventuserid])) {
                                if (!empty($subscribersprimary[$event->eventuserid]->subo365calid)) {
                                    $calid = $subscribersprimary[$event->eventuserid]->subo365calid;
                                } else {
                                    $calid = null;
                                }
                            } else if (isset($subscribersnotprimary[$event->eventuserid])) {
                                if (!empty($subscribersnotprimary[$event->eventuserid]->subo365calid)) {
                                    $calid = $subscribersnotprimary[$event->eventuserid]->subo365calid;
                                } else {
                                    $calid = null;
                                }
                            }
                            $calsync->create_event_raw($event->eventuserid, $event->eventid, $subject, $body, $evstart, $evend,
                                    $subscribersprimary, [], $calid);
                        }
                    } catch (moodle_exception $e) {
                        mtrace('ERROR: ' . $e->getMessage());
                    }
                }

                // Delete event for users who have an idmap record but are no longer subscribed.
                // Users would have an idmap record if they were non-primary calendar users, and are thus not taken care of with
                // the attendee update done above.
                $sql = 'SELECT userid, id, eventid, outlookeventid FROM {local_o365_calidmap} WHERE eventid = ? AND origin = ?';
                $idmapnosub = $DB->get_records_sql($sql, [$event->eventid, 'moodle']);
                $idmapnosub = array_diff_key($idmapnosub, $subscribersnotprimary, $subscribersprimary);
                if (isset($idmapnosub[$event->eventuserid])) {
                    // If the user who created the event is included, remove them. THIS IS VERY IMPORTANT. Otherwise any event
                    // in a calendar that the creator is not subscribed to will be removed, negating the work we did above with
                    // attendees.
                    unset($idmapnosub[$event->eventuserid]);
                }
                if (!empty($idmapnosub)) {
                    mtrace('Removing event for users who have unsubscribed.');
                    foreach ($idmapnosub as $userid => $usercalidmap) {
                        $calsync->delete_event_raw($userid, $usercalidmap->outlookeventid, $usercalidmap->id);
                    }
                }

                // Sync non-primary cal users.
                if (!empty($subscribersnotprimary)) {
                    mtrace('Syncing non-primary calendar users.');
                    foreach ($subscribersnotprimary as $userid => $user) {
                        $calid = (!empty($user->subo365calid)) ? $user->subo365calid : null;
                        $calsync->ensure_event_synced_for_user($event->eventid, $user->id, $subject, $body, $evstart, $evend,
                            $calid);
                    }
                }

            } catch (moodle_exception $e) {
                // Could not sync this site event. Log and continue.
                mtrace('Error syncing site event #'.$event->eventid.': '.$e->getMessage());
            }
        }
        $events->close();
        $existingcalsitelastsyncsetting = get_config('local_o365', 'cal_site_lastsync');
        if ($existingcalsitelastsyncsetting != $timestart) {
            add_to_config_log('cal_site_lastsync', $existingcalsitelastsyncsetting, $timestart, 'local_o365');
        }
        set_config('cal_site_lastsync', $timestart, 'local_o365');
        return true;
    }

    /**
     * Sync all course events for a given course with Outlook.
     *
     * @param int $courseid The ID of the course to sync.
     * @param int $timecreated The time the task was created.
     */
    protected function sync_courseevents($courseid, $timecreated) {
        global $DB;
        $timestart = time();
        // Check the last time course events for this course were synced.
        // Using a direct query here so we don't run into static cache issues.
        $lastcoursesync = $DB->get_record('config_plugins', ['plugin' => 'local_o365', 'name' => 'cal_course_lastsync']);
        if (!empty($lastcoursesync)) {
            $lastcoursesync = unserialize($lastcoursesync->value);
            if (isset($lastcoursesync[$courseid]) && (int)$lastcoursesync[$courseid] > $timecreated) {
                // Course events for this course have been synced since this event was created, so we don't have to do it again.
                return true;
            }
        }

        $clientdata = \local_o365\oauth2\clientdata::instance_from_oidc();
        $httpclient = new \local_o365\httpclient();
        $calsync = new \local_o365\feature\calsync\main($clientdata, $httpclient);

        [$subscribersprimary, $subscribersnotprimary] = $this->get_subscribers('course', $courseid);

        $sql = 'SELECT ev.id AS eventid,
                       ev.name AS eventname,
                       ev.description AS eventdescription,
                       ev.timestart AS eventtimestart,
                       ev.timeduration AS eventtimeduration,
                       idmap.outlookeventid,
                       ev.userid AS eventuserid,
                       ev.groupid,
                       idmap.id AS idmapid
                  FROM {event} ev
             LEFT JOIN {local_o365_calidmap} idmap ON ev.id = idmap.eventid AND idmap.userid = ev.userid
                 WHERE ev.courseid = ? ';
        $params = [$courseid];
        $events = $DB->get_recordset_sql($sql, $params);
        foreach ($events as $event) {
            try {
                mtrace('Syncing course event #'.$event->eventid);
                $grouplimit = null;
                // If this is a group event, get members and save for limiting later.
                if (!empty($event->groupid)) {
                    $sql = 'SELECT userid
                              FROM {groups_members}
                             WHERE groupid = ?';
                    $params = [$event->groupid];
                    $grouplimit = $DB->get_records_sql($sql, $params);
                }

                $subject = $event->eventname;
                $body = $event->eventdescription;
                $evstart = $event->eventtimestart;
                $evend = $evstart + $event->eventtimeduration;

                // Sync primary cal users first.
                if (!empty($subscribersprimary)) {
                    mtrace('Syncing primary calendar users.');
                    try {
                        // Determine attendees - if this is a group event, limit to group members.
                        if ($grouplimit !== null && is_array($grouplimit)) {
                            $eventattendees = array_intersect_key($subscribersprimary, $grouplimit);
                        } else {
                            $eventattendees = $subscribersprimary;
                        }

                        // If there's a stored outlookeventid the event exists in o365, so update it. Otherwise create it.
                        if (!empty($event->outlookeventid)) {
                            try {
                                $calsync->update_event_raw($event->eventuserid, $event->outlookeventid,
                                    ['attendees' => $eventattendees]);
                            } catch (moodle_exception $e) {
                                // Do nothing.
                                mtrace('Error updating event #' . $event->eventid . ': ' . $e->getMessage());
                            }
                        } else {
                            $calid = null;
                            if (!empty($subscribersprimary[$event->eventuserid])) {
                                if (!empty($subscribersprimary[$event->eventuserid]->subo365calid)) {
                                    $calid = $subscribersprimary[$event->eventuserid]->subo365calid;
                                } else {
                                    $calid = null;
                                }
                            } else if (isset($subscribersnotprimary[$event->eventuserid])) {
                                if (!empty($subscribersnotprimary[$event->eventuserid]->subo365calid)) {
                                    $calid = $subscribersnotprimary[$event->eventuserid]->subo365calid;
                                } else {
                                    $calid = null;
                                }
                            }
                            $calsync->create_event_raw($event->eventuserid, $event->eventid, $subject, $body, $evstart, $evend,
                                    $eventattendees, [], $calid);
                        }
                    } catch (moodle_exception $e) {
                        mtrace('ERROR: '.$e->getMessage());
                    }
                }

                // Delete event for users who have an idmap record but are no longer subscribed.
                // Users would have an idmap record if they were non-primary calendar users, and are thus not taken care of with
                // the attendee update done above.
                $sql = 'SELECT userid, id, eventid, outlookeventid FROM {local_o365_calidmap} WHERE eventid = ? AND origin = ?';
                $idmapnosub = $DB->get_records_sql($sql, [$event->eventid, 'moodle']);
                $idmapnosub = array_diff_key($idmapnosub, $subscribersnotprimary, $subscribersprimary);
                if (isset($idmapnosub[$event->eventuserid])) {
                    // If the user who created the event is included, remove them. THIS IS VERY IMPORTANT. Otherwise any event
                    // in a calendar that the creator is not subscribed to will be removed, negating the work we did above with
                    // attendees.
                    unset($idmapnosub[$event->eventuserid]);
                }
                if (!empty($idmapnosub)) {
                    mtrace('Removing event for users who have unsubscribed.');
                    foreach ($idmapnosub as $userid => $usercalidmap) {
                        $calsync->delete_event_raw($userid, $usercalidmap->outlookeventid, $usercalidmap->id);
                    }
                }

                // Sync non-primary cal users.
                if (!empty($subscribersnotprimary)) {
                    mtrace('Syncing non-primary calendar users.');
                    foreach ($subscribersnotprimary as $userid => $user) {
                        // If we're syncing a group event, only sync users in the group.
                        if ($grouplimit !== null && is_array($grouplimit) && !isset($grouplimit[$user->id])) {
                            continue;
                        }
                        $calid = (!empty($user->subo365calid)) ? $user->subo365calid : null;
                        $calsync->ensure_event_synced_for_user($event->eventid, $user->id, $subject, $body, $evstart, $evend,
                            $calid);
                    }
                }
            } catch (moodle_exception $e) {
                // Could not sync this course event. Log and continue.
                mtrace('Error syncing course event #' . $event->eventid . ': ' . $e->getMessage());
            }
        }
        $events->close();

        if (!empty($lastcoursesync) && is_array($lastcoursesync)) {
            $lastcoursesync[$courseid] = $timestart;
        } else {
            $lastcoursesync = [$courseid => $timestart];
        }
        $lastcoursesync = serialize($lastcoursesync);
        $existingcalcourselastsyncsetting = get_config('local_o365', 'cal_course_lastsync');
        if ($existingcalcourselastsyncsetting != $lastcoursesync) {
            add_to_config_log('cal_course_lastsync', $existingcalcourselastsyncsetting, $lastcoursesync, 'local_o365');
        }
        set_config('cal_course_lastsync', $lastcoursesync, 'local_o365');

        return true;
    }

    /**
     * Sync all user events for a given user with Outlook.
     *
     * @param int $userid The ID of the user to sync.
     * @param int $timecreated The time the task was created.
     */
    protected function sync_userevents($userid, $timecreated) {
        global $DB;
        $timestart = time();
        // Check the last time user events for this user were synced.
        // Using a direct query here so we don't run into static cache issues.
        $lastusersync = $DB->get_record('config_plugins', ['plugin' => 'local_o365', 'name' => 'cal_user_lastsync']);
        if (!empty($lastusersync)) {
            $lastusersync = unserialize($lastusersync->value);
            if (is_array($lastusersync) && isset($lastusersync[$userid]) && (int)$lastusersync[$userid] > $timecreated) {
                // User events for this user have been synced since this event was created, so we don't have to do it again.
                return true;
            }
        }

        $clientdata = \local_o365\oauth2\clientdata::instance_from_oidc();
        $httpclient = new \local_o365\httpclient();
        $calsync = new \local_o365\feature\calsync\main($clientdata, $httpclient);

        $usertoken = $calsync->get_user_token($userid);
        if (empty($usertoken)) {
            // No token, can't sync.
            utils::debug('Could not get user token for calendar sync.', __METHOD__);
            return false;
        }

        $subscription = $DB->get_record('local_o365_calsub', ['user_id' => $userid, 'caltype' => 'user']);

        $sql = 'SELECT ev.id AS eventid,
                       ev.name AS eventname,
                       ev.description AS eventdescription,
                       ev.timestart AS eventtimestart,
                       ev.timeduration AS eventtimeduration,
                       idmap.outlookeventid,
                       idmap.origin AS idmaporigin
                  FROM {event} ev
             LEFT JOIN {local_o365_calidmap} idmap ON ev.id = idmap.eventid AND idmap.userid = ev.userid
                 WHERE ev.courseid = 0
                       AND ev.groupid = 0
                       AND ev.userid = ?';
        $events = $DB->get_recordset_sql($sql, [$userid]);
        foreach ($events as $event) {
            mtrace('Syncing user event #'.$event->eventid);
            if (!empty($subscription)) {
                if (empty($event->outlookeventid)) {
                    // Event not synced, if outward subscription exists sync to o365.
                    if ($subscription->syncbehav === 'out' || $subscription->syncbehav === 'both') {
                        mtrace('Creating event in Outlook.');
                        $subject = $event->eventname;
                        $body = $event->eventdescription;
                        $evstart = $event->eventtimestart;
                        $evend = $event->eventtimestart + $event->eventtimeduration;
                        $calid = (!empty($subscription->o365calid)) ? $subscription->o365calid : null;
                        if (isset($subscription->isprimary) && $subscription->isprimary == 1) {
                            $calid = null;
                        }
                        $calsync->create_event_raw($userid, $event->eventid, $subject, $body, $evstart, $evend, [], [], $calid);
                    } else {
                        mtrace('Not creating event in Outlook. (Sync settings are inward-only.)');
                    }
                } else {
                    // Event synced. If event was created in Moodle and subscription is inward-only, delete o365 event.
                    if ($event->idmaporigin === 'moodle' && $subscription->syncbehav === 'in') {
                        mtrace('Removing event from Outlook (Created in Moodle, sync settings are inward-only.)');
                        $calsync->delete_event_raw($userid, $event->outlookeventid);
                    } else {
                        mtrace('Event already synced.');
                    }
                }
            } else {
                // No subscription exists. Delete relevant events.
                if (!empty($event->outlookeventid)) {
                    if ($event->idmaporigin === 'moodle') {
                        mtrace('Removing event from Outlook.');
                        // Event was created in Moodle, delete o365 event.
                        $calsync->delete_event_raw($userid, $event->outlookeventid);
                    } else {
                        mtrace('Not removing event from Outlook (It was created there.)');
                    }
                } else {
                    mtrace('Did not have an outlookeventid. Event not synced?');
                }
            }
        }
        $events->close();

        if (!empty($lastusersync) && is_array($lastusersync)) {
            $lastusersync[$userid] = $timestart;
        } else {
            $lastusersync = [$userid => $timestart];
        }
        $lastusersync = serialize($lastusersync);
        $existingcaluserlastsyncsetting = get_config('local_o365', 'cal_user_lastsync');
        if ($existingcaluserlastsyncsetting != $lastusersync) {
            add_to_config_log('cal_user_lastsync', $existingcaluserlastsyncsetting, $lastusersync, 'local_o365');
        }
        set_config('cal_user_lastsync', $lastusersync, 'local_o365');

        return true;
    }

    /**
     * Do the job.
     */
    public function execute() {
        $opdata = $this->get_custom_data();
        $timecreated = (isset($opdata->timecreated)) ? $opdata->timecreated : time();

        if (utils::is_connected() !== true) {
            utils::debug(get_string('erroracpauthoidcnotconfig', 'local_o365'), __METHOD__);
            return false;
        }

        // Sync site events.
        if ($opdata->caltype === 'site') {
            $this->sync_siteevents($timecreated);
        } else if ($opdata->caltype === 'course') {
            $this->sync_courseevents($opdata->caltypeid, $timecreated);
        } else if ($opdata->caltype === 'user') {
            $this->sync_userevents($opdata->userid, $timecreated);
        }
    }
}
