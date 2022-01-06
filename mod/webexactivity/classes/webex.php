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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_webexactivity;

use \mod_webexactivity\local\type;
use \mod_webexactivity\local\exception;
use \mod_webexactivity\local\type\base\xml_gen;

defined('MOODLE_INTERNAL') || die();

/**
 * A class that provides general WebEx services and constants.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webex {
    /**
     * Type that represents a Meeting Center meeting.
     */
    const WEBEXACTIVITY_TYPE_BASE = 0;

    /**
     * Type that represents a Meeting Center meeting.
     */
    const WEBEXACTIVITY_TYPE_MEETING = 1;

    /**
     * Type that represents a Training Center meeting.
     */
    const WEBEXACTIVITY_TYPE_TRAINING = 2;

    /**
     * Type that represents a Support Center meeting.
     */
    const WEBEXACTIVITY_TYPE_SUPPORT = 3;

    /**
     * Status that represents a meeting that has never started.
     */
    const WEBEXACTIVITY_STATUS_NEVER_STARTED = 0;

    /**
     * Status that represents a meeting that has stopped.
     */
    const WEBEXACTIVITY_STATUS_STOPPED = 1;

    /**
     * Status that represents a meeting that is in progress.
     */
    const WEBEXACTIVITY_STATUS_IN_PROGRESS = 2;

    /**
     * Time status that represents a meeting that is upcoming.
     */
    const WEBEXACTIVITY_TIME_UPCOMING = 0;

    /**
     * Time status that represents a meeting that is available.
     */
    const WEBEXACTIVITY_TIME_AVAILABLE = 1;

    /**
     * Time status that represents a meeting that is in progress.
     */
    const WEBEXACTIVITY_TIME_IN_PROGRESS = 2;

    /**
     * Time status that represents a meeting that is in the recent past.
     */
    const WEBEXACTIVITY_TIME_PAST = 3;

    /**
     * Time status that represents a meeting that is in the distant past.
     */
    const WEBEXACTIVITY_TIME_LONG_PAST = 4;

    /**
     * The flag for Available for meeting types.
     */
    const WEBEXACTIVITY_TYPE_INSTALLED = 'inst';

    /**
     * The flag for Available to all setting for meeting types.
     */
    const WEBEXACTIVITY_TYPE_ALL = 'all';

    /**
     * The flag for passwords are required meeting types.
     */
    const WEBEXACTIVITY_TYPE_PASSWORD_REQUIRED = 'pwreq';

    /** @var mixed Storage for the latest errors from a connection. */
    private $latesterrors = null;

    // ---------------------------------------------------
    // User Functions.
    // ---------------------------------------------------
    /**
     * Delete unused passwords, since beginning in 0.2.0 we don't need them anymore.
     */
    public static function delete_passwords() {
        global $DB;

        // Clear passwords that we no longer need.
        $sub = 'SELECT COUNT(1) FROM {webexactivity} WHERE creatorwebexid = u.webexid';
        $sql = 'UPDATE {webexactivity_user} AS u SET password = null WHERE ('.$sub.') = 0';
        $DB->execute($sql);
    }

    // ---------------------------------------------------
    // Support Functions.
    // ---------------------------------------------------
    /**
     * Return the base URL for the WebEx server.
     *
     * @return string  The base URL.
     */
    public static function get_base_url() {
        $host = get_config('webexactivity', 'sitename');

        if ($host === false) {
            return false;
        }
        $url = 'https://'.$host.'.webex.com/'.$host;

        return $url;
    }

    /**
     * Returns count info about List type responses.
     *
     * @return array  The total, startFrom, and count.
     */
    public static function get_list_info($response) {
        if (!isset($response['ep:matchingRecords'][0]['#'])) {
            return array(0, 0, 0);
        }
        $records = $response['ep:matchingRecords'][0]['#'];
        $out = array();

        if (isset($records['serv:total'][0]['#'])) {
            $out[] = $records['serv:total'][0]['#'];
        } else {
            $out[] = 0;
        }

        if (isset($records['serv:startFrom'][0]['#'])) {
            $out[] = $records['serv:startFrom'][0]['#'];
        } else {
            $out[] = 0;
        }

        if (isset($records['serv:returned'][0]['#'])) {
            $out[] = $records['serv:returned'][0]['#'];
        } else {
            $out[] = 0;
        }

        return $out;
    }

    /**
     * Generate a password that will pass the WebEx requirements.
     *
     * @return string  The generated password.
     */
    public static function generate_password() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array();
        $length = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $length);
            $pass[] = $alphabet[$n];
        }
        return implode($pass).'!2Da';
    }

    /**
     * Check and update open sessions/meetings from WebEx.
     *
     * @return bool  True on success, false on failure.
     */
    public function update_open_sessions() {
        global $DB;

        $xml = type\base\xml_gen::list_open_sessions();

        $response = $this->get_response($xml);
        if ($response === false) {
            return false;
        }

        $processtime = time();
        $cleartime = $processtime - 60;

        if (is_array($response) && isset($response['ep:services'])) {
            foreach ($response['ep:services'] as $service) {
                foreach ($service['#']['ep:sessions'] as $session) {
                    $session = $session['#'];

                    $meetingkey = $session['ep:sessionKey'][0]['#'];
                    if ($meetingrecord = $DB->get_record('webexactivity', array('meetingkey' => $meetingkey))) {
                        if ($meetingrecord->status !== self::WEBEXACTIVITY_STATUS_IN_PROGRESS) {
                            $meeting = meeting::load($meetingrecord);

                            $meeting->status = self::WEBEXACTIVITY_STATUS_IN_PROGRESS;
                            $meeting->laststatuscheck = $processtime;
                            $meeting->save();
                        }
                    }
                }
            }
        }

        $select = 'laststatuscheck < ? AND status = ?';
        $params = array('lasttime' => $cleartime, 'status' => self::WEBEXACTIVITY_STATUS_IN_PROGRESS);

        if ($meetings = $DB->get_records_select('webexactivity', $select, $params)) {
            foreach ($meetings as $meetingrecord) {
                $meeting = meeting::load($meetingrecord);

                $meeting->status = self::WEBEXACTIVITY_STATUS_STOPPED;
                $meeting->laststatuscheck = $processtime;
                $meeting->save();
            }
        }
    }

    // ---------------------------------------------------
    // Redirect methods.
    // ---------------------------------------------------
    /**
     * Stores the passed URL and redirects to user edit form.
     *
     * @param moodle_url   $url URL object to return to when done.
     */
    public static function password_redirect($url = false) {
        global $SESSION;

        if (!$url) {
            $url = new \moodle_url('/');
        }

        $SESSION->mod_webexactivity_password_redirect = $url;

        $redirurl = new \moodle_url('/mod/webexactivity/useredit.php', array('action' => 'useredit'));
        redirect($redirurl);
    }

    /**
     * Redirects back to the url stored in the session.
     *
     * @param bool   $home If true, send to / instead of wherever we were.
     */
    public static function password_return_redirect($home = false) {
        global $SESSION;

        $url = false;
        if (isset($SESSION->mod_webexactivity_password_redirect)) {
            $url = $SESSION->mod_webexactivity_password_redirect;
            unset($SESSION->mod_webexactivity_password_redirect);
        }

        if (!$url or $home) {
            $url = new \moodle_url('/');
        }

        redirect($url);
    }

    // ---------------------------------------------------
    // Recording Functions.
    // ---------------------------------------------------
    /**
     * Check and update recordings from WebEx.
     *
     * @param int    Number of days to look back. 0 for forever.
     * @return bool  True on success, false on failure.
     */
    public function update_recordings($daysback = 10) {
        global $DB;

        // In WBS 32/XML API 11.0.0 SP7 WebEx set the max listing to 28 days.
        // We use 15 to make sure we don't run into the cap (which throws a fatal error), and to reduce timeouts.
        $maxtime = 15 * 24 * 3600;

        // End time is the most recent time we are looking for. We look into the future in case of timezone issues.
        $endtime = time() + (12 * 3600);

        // Need to determine how far back to go.
        if ($daysback) {
            $starttime = time() - ($daysback * 24 * 3600);
        } else {
            $processall = (boolean)\get_config('webexactivity', 'manageallrecordings');
            if ($processall) {
                // For this, we are going to go back a really long time. We have no way of knowing what the oldest is.
                // Arbitrarily picking Jan 1 2008.
                $starttime = 1199145600;
            } else {
                // In the case where we aren't doing all recordings, we can just use our meetings and recordings
                // to determine how far back to go.
                $sql = "SELECT MIN(cm.added) FROM {webexactivity} w
                          JOIN {course_modules} cm ON w.id = cm.instance
                          JOIN {modules} m ON m.id = cm.module
                         WHERE m.name = 'webexactivity'";
                $oldestmeeting = $DB->get_field_sql($sql);
                $oldestrec = $DB->get_field_sql("SELECT MIN(timecreated) FROM {webexactivity_recording}");

                if (empty($oldestmeeting) && empty($oldestrec)) {
                    // If we don't have an oldest meeting/recording that we can determine, just go back 1 year.
                    $starttime = time() - (365 * 24 * 3600);
                } else {
                    if (empty($oldestmeeting)) {
                        $oldest = $oldestrec;
                    } else if (empty($oldestrec)) {
                        $oldest = $oldestmeeting;
                    } else {
                        $oldest = min($oldestmeeting, $oldestrec);
                    }
                    // Take the oldest meeting and go back an additional 120 days.
                    $starttime = $oldest - (120 * 24 * 3600);
                }
            }

        }

        $originalstart = $starttime;

        $moretime = true;
        do {
            // This outer loop steps through time, getting each $maxtime chunk of time until all chunks are done.
            $params = new \stdClass();
            $params->startdate = $starttime;
            $params->count = 50;

            // Break up the time into chunks.
            if (($endtime - $starttime) > $maxtime) {
                // Use the max time.
                $params->enddate = $starttime + $maxtime - 1;
                // Move the start forward in time for the next loop.
                $starttime += $maxtime;
            } else {
                $moretime = false;
                $params->enddate = $endtime;
            }

            mtrace("Getting WebEx recordings for ".xml_gen::time_to_date_string($params->startdate)." through ".
                    xml_gen::time_to_date_string($params->enddate));

            $found = 0;
            $start = 0;
            $status = true;

            do {
                // This inner loop steps through pages of results.
                $params->start = $start;
                $xml = type\base\xml_gen::list_recordings($params);

                if (!($response = $this->get_response($xml))) {
                    break;
                }

                list($found, $start, $count) = self::get_list_info($response);
                $start += $count;

                $status = $this->proccess_recording_response($response) && $status;

            } while ($found > $start);

        } while ($moretime);


        if ($status && !$daysback) {
            $this->remove_missing_recordings($originalstart);
        }

        return $status;
    }

    /**
     * Process the response of recordings from WebEx.
     *
     * @param array  The response array from WebEx.
     * @return bool  True on success, false on failure.
     */
    private function proccess_recording_response($response) {
        global $DB;

        if (!is_array($response)) {
            return true;
        }

        $recordings = $response['ep:recording'];

        $processall = (boolean)\get_config('webexactivity', 'manageallrecordings');

        foreach ($recordings as $recording) {
            $recording = $recording['#'];

            if (!isset($recording['ep:sessionKey'][0]['#'])) {
                continue;
            }

            $key = $recording['ep:sessionKey'][0]['#'];
            $meeting = $DB->get_record('webexactivity', array('meetingkey' => $key));
            if (!$meeting && !$processall) {
                continue;
            }

            $rec = new \stdClass();
            if ($meeting) {
                $rec->webexid = $meeting->id;
            } else {
                $rec->webexid = null;
            }

            // TODO Convert to use object?
            $rec->meetingkey = $key;
            $rec->recordingid = $recording['ep:recordingID'][0]['#'];
            $rec->hostid = $recording['ep:hostWebExID'][0]['#'];
            $rec->name = $recording['ep:name'][0]['#'];
            $rec->timecreated = strtotime($recording['ep:createTime'][0]['#']);
            $rec->streamurl = $recording['ep:streamURL'][0]['#'];
            $rec->fileurl = $recording['ep:fileURL'][0]['#'];
            $size = $recording['ep:size'][0]['#'];
            $size = floatval($size);
            $size = $size * 1024 * 1024;
            $rec->filesize = (int)$size;
            $rec->duration = $recording['ep:duration'][0]['#'];
            $rec->timemodified = time();
            $rec->visible = 1;
            $rec->deleted = 0;

            if ($existing = $DB->get_record('webexactivity_recording', array('recordingid' => $rec->recordingid))) {
                $update = new \stdClass();
                $update->id = $existing->id;
                $update->name = $rec->name;
                $update->streamurl = $rec->streamurl;
                $update->fileurl = $rec->fileurl;
                $update->timemodified = time();

                $DB->update_record('webexactivity_recording', $update);
            } else {
                $rec->id = $DB->insert_record('webexactivity_recording', $rec);

                if ($meeting) {
                    $cm = get_coursemodule_from_instance('webexactivity', $meeting->id);
                    $context = \context_module::instance($cm->id);
                    $params = array(
                        'context' => $context,
                        'objectid' => $rec->id
                    );
                    $event = \mod_webexactivity\event\recording_created::create($params);
                    $event->add_record_snapshot('webexactivity_recording', $rec);
                    $event->add_record_snapshot('webexactivity', $meeting);
                    $event->trigger();
                }

            }
        }

        return true;
    }

    /**
     * Delete 'deleted' recordings from the WebEx server.
     */
    public function remove_deleted_recordings() {
        global $DB;

        $holdtime = get_config('webexactivity', 'recordingtrashtime');

        $params = array('time' => (time() - ($holdtime * 3600)));
        $rs = $DB->get_recordset_select('webexactivity_recording', 'deleted > 0 AND deleted < :time', $params);

        foreach ($rs as $record) {
            $recording = new recording($record);
            print 'Deleting: '.$recording->name;
            try {
                $recording->true_delete();
                print "\n";
            } catch (\Exception $e) {
                print " : Exception Error\n";
            }
        }

        $rs->close();
    }

    /**
     * Removes records for recordings that we haven't seen in a long time.
     *
     * @param int  $oldest The time
     */
    public function remove_missing_recordings($oldest) {
        global $DB;

        $select = 'timemodified < ? AND timecreated > ?';
        $params = array(time() - (7 * 24 * 3600), $oldest + (7 * 24 * 3600));

        $DB->delete_records_select('webexactivity_recording', $select, $params);
    }


    // ---------------------------------------------------
    // Connection Functions.
    // ---------------------------------------------------
    /**
     * Get the response from WebEx for a XML message.
     *
     * @param string         $xml The XML to send to WebEx.
     * @param user|bool      $webexuser The WebEx user to use for auth. False to use the API user.
     * @return array|bool    XML response (as array). False on failure.
     * @throws webex_xml_exception on XML parse error.
     */
    public function get_response($basexml, $webexuser = false) {
        global $USER;

        if (!$webexuser) {
            $webexuser = user::load_admin_user();
        }

        $xml = type\base\xml_gen::auth_wrap($basexml, $webexuser);

        list($status, $response, $errors) = $this->fetch_response($xml);

        if ($status) {
            return $response;
        } else {
            // Bad user password, reset it and try again.
            if ((!$webexuser->isadmin) && (isset($errors['exception'])) && ($errors['exception'] === '030002')) {
                if ($webexuser->update_password(self::generate_password())) {
                    $xml = type\base\xml_gen::auth_wrap($basexml, $webexuser);
                    list($status, $response, $errors) = $this->fetch_response($xml);
                    if ($status) {
                        return $response;
                    }
                }

                throw new exception\bad_password();
            }

            // Handling of special cases.
            if ((isset($errors['exception'])) && ($errors['exception'] === '000015')) {
                // No records found (000015), which is not really a failure, return empty array.
                return array();
            }

            if ((isset($errors['exception'])) && ($errors['exception'] === '030001')) {
                // No user found (030001), which is not really a failure, return empty array.
                return array();
            }

            if ((isset($errors['exception'])) && (($errors['exception'] === '030004') || ($errors['exception'] === '030005'))) {
                // Username or email already exists.
                throw new exception\webex_user_collision();
            }

            if ((isset($errors['exception'])) && (($errors['exception'] === '060021'))) {
                // The passed user cannot schedule meetings for the WebEx Host ID passed.
                throw new exception\host_scheduling();
            }

            if ((isset($errors['exception'])) && (($errors['exception'] === '060019'))) {
                // The WebEx Host ID doesn't exist.
                throw new exception\unknown_hostwebexid();
            }

            // Generic exception for other cases.
            throw new exception\webex_xml_exception($errors['exception'], $errors['message'], $xml);
        }
    }

    /**
     * Connects to WebEx and gets a response for the given, full, XML.
     *
     * To be used by get_response().
     *
     * @param string  $xml The XML message to retrieve.
     * @return array  status bool    True on success, false on failure.
     *                response array The XML response in array form.
     *                errors array   An array of errors.
     */
    private function fetch_response($xml) {
        $connector = new service_connector();
        $status = $connector->retrieve($xml);

        if ($status) {
            $response = $connector->get_response_array();
            if (isset($response['serv:message']['#']['serv:body']['0']['#']['serv:bodyContent']['0']['#'])) {
                $response = $response['serv:message']['#']['serv:body']['0']['#']['serv:bodyContent']['0']['#'];
            } else {
                $response = false;
                $status = false;
            }
        } else {
            $response = false;
        }
        $errors = $connector->get_errors();
        $this->latesterrors = $errors;

        return array($status, $response, $errors);
    }

    /**
     * Expose latesterrors to the outside world for use.
     *
     * @return array  The latest errors.
     */
    public function get_latest_errors() {
        return $this->latesterrors;
    }
}
