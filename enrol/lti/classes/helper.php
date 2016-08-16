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
 * LTI enrolment plugin helper.
 *
 * @package enrol_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_lti;

defined('MOODLE_INTERNAL') || die();

/**
 * LTI enrolment plugin helper class.
 *
 * @package enrol_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /*
     * The value used when we want to enrol new members and unenrol old ones.
     */
    const MEMBER_SYNC_ENROL_AND_UNENROL = 1;

    /*
     * The value used when we want to enrol new members only.
     */
    const MEMBER_SYNC_ENROL_NEW = 2;

    /*
     * The value used when we want to unenrol missing users.
     */
    const MEMBER_SYNC_UNENROL_MISSING = 3;

    /**
     * Code for when an enrolment was successful.
     */
    const ENROLMENT_SUCCESSFUL = true;

    /**
     * Error code for enrolment when max enrolled reached.
     */
    const ENROLMENT_MAX_ENROLLED = 'maxenrolledreached';

    /**
     * Error code for enrolment has not started.
     */
    const ENROLMENT_NOT_STARTED = 'enrolmentnotstarted';

    /**
     * Error code for enrolment when enrolment has finished.
     */
    const ENROLMENT_FINISHED = 'enrolmentfinished';

    /**
     * Error code for when an image file fails to upload.
     */
    const PROFILE_IMAGE_UPDATE_SUCCESSFUL = true;

    /**
     * Error code for when an image file fails to upload.
     */
    const PROFILE_IMAGE_UPDATE_FAILED = 'profileimagefailed';

    /**
     * Creates a unique username.
     *
     * @param string $consumerkey Consumer key
     * @param string $ltiuserid External tool user id
     * @return string The new username
     */
    public static function create_username($consumerkey, $ltiuserid) {
        if (!empty($ltiuserid) && !empty($consumerkey)) {
            $userkey = $consumerkey . ':' . $ltiuserid;
        } else {
            $userkey = false;
        }

        return 'enrol_lti' . sha1($consumerkey . '::' . $userkey);
    }

    /**
     * Adds default values for the user object based on the tool provided.
     *
     * @param \stdClass $tool
     * @param \stdClass $user
     * @return \stdClass The $user class with added default values
     */
    public static function assign_user_tool_data($tool, $user) {
        global $CFG;

        $user->city = (!empty($tool->city)) ? $tool->city : "";
        $user->country = (!empty($tool->country)) ? $tool->country : "";
        $user->institution = (!empty($tool->institution)) ? $tool->institution : "";
        $user->timezone = (!empty($tool->timezone)) ? $tool->timezone : "";
        if (isset($tool->maildisplay)) {
            $user->maildisplay = $tool->maildisplay;
        } else if (isset($CFG->defaultpreference_maildisplay)) {
            $user->maildisplay = $CFG->defaultpreference_maildisplay;
        } else {
            $user->maildisplay = 2;
        }
        $user->mnethostid = $CFG->mnet_localhost_id;
        $user->confirmed = 1;
        $user->lang = $tool->lang;

        return $user;
    }

    /**
     * Compares two users.
     *
     * @param \stdClass $newuser The new user
     * @param \stdClass $olduser The old user
     * @return bool True if both users are the same
     */
    public static function user_match($newuser, $olduser) {
        if ($newuser->firstname != $olduser->firstname) {
            return false;
        }
        if ($newuser->lastname != $olduser->lastname) {
            return false;
        }
        if ($newuser->email != $olduser->email) {
            return false;
        }
        if ($newuser->city != $olduser->city) {
            return false;
        }
        if ($newuser->country != $olduser->country) {
            return false;
        }
        if ($newuser->institution != $olduser->institution) {
            return false;
        }
        if ($newuser->timezone != $olduser->timezone) {
            return false;
        }
        if ($newuser->maildisplay != $olduser->maildisplay) {
            return false;
        }
        if ($newuser->mnethostid != $olduser->mnethostid) {
            return false;
        }
        if ($newuser->confirmed != $olduser->confirmed) {
            return false;
        }
        if ($newuser->lang != $olduser->lang) {
            return false;
        }

        return true;
    }

    /**
     * Updates the users profile image.
     *
     * @param int $userid the id of the user
     * @param string $url the url of the image
     * @return bool|string true if successful, else a string explaining why it failed
     */
    public static function update_user_profile_image($userid, $url) {
        global $CFG, $DB;

        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->libdir . '/gdlib.php');

        $fs = get_file_storage();

        $context = \context_user::instance($userid, MUST_EXIST);
        $fs->delete_area_files($context->id, 'user', 'newicon');

        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'user',
            'filearea' => 'newicon',
            'itemid' => 0,
            'filepath' => '/'
        );

        $urlparams = array(
            'calctimeout' => false,
            'timeout' => 5,
            'skipcertverify' => true,
            'connecttimeout' => 5
        );

        try {
            $fs->create_file_from_url($filerecord, $url, $urlparams);
        } catch (\file_exception $e) {
            return get_string($e->errorcode, $e->module, $e->a);
        }

        $iconfile = $fs->get_area_files($context->id, 'user', 'newicon', false, 'itemid', false);

        // There should only be one.
        $iconfile = reset($iconfile);

        // Something went wrong while creating temp file - remove the uploaded file.
        if (!$iconfile = $iconfile->copy_content_to_temp()) {
            $fs->delete_area_files($context->id, 'user', 'newicon');
            return self::PROFILE_IMAGE_UPDATE_FAILED;
        }

        // Copy file to temporary location and the send it for processing icon.
        $newpicture = (int) process_new_icon($context, 'user', 'icon', 0, $iconfile);
        // Delete temporary file.
        @unlink($iconfile);
        // Remove uploaded file.
        $fs->delete_area_files($context->id, 'user', 'newicon');
        // Set the user's picture.
        $DB->set_field('user', 'picture', $newpicture, array('id' => $userid));
        return self::PROFILE_IMAGE_UPDATE_SUCCESSFUL;
    }

    /**
     * Enrol a user in a course.
     *
     * @param \stdclass $tool The tool object (retrieved using self::get_lti_tool() or self::get_lti_tools())
     * @param int $userid The user id
     * @return bool|string returns true if successful, else an error code
     */
    public static function enrol_user($tool, $userid) {
        global $DB;

        // Check if the user enrolment exists.
        if (!$DB->record_exists('user_enrolments', array('enrolid' => $tool->enrolid, 'userid' => $userid))) {
            // Check if the maximum enrolled limit has been met.
            if ($tool->maxenrolled) {
                if ($DB->count_records('user_enrolments', array('enrolid' => $tool->enrolid)) >= $tool->maxenrolled) {
                    return self::ENROLMENT_MAX_ENROLLED;
                }
            }
            // Check if the enrolment has not started.
            if ($tool->enrolstartdate && time() < $tool->enrolstartdate) {
                return self::ENROLMENT_NOT_STARTED;
            }
            // Check if the enrolment has finished.
            if ($tool->enrolenddate && time() > $tool->enrolenddate) {
                return self::ENROLMENT_FINISHED;
            }

            $timeend = 0;
            if ($tool->enrolperiod) {
                $timeend = time() + $tool->enrolperiod;
            }

            // Finally, enrol the user.
            $instance = new \stdClass();
            $instance->id = $tool->enrolid;
            $instance->courseid = $tool->courseid;
            $instance->enrol = 'lti';
            $instance->status = $tool->status;
            $ltienrol = enrol_get_plugin('lti');

            // Hack - need to do this to workaround DB caching hack. See MDL-53977.
            $timestart = intval(substr(time(), 0, 8) . '00') - 1;
            $ltienrol->enrol_user($instance, $userid, null, $timestart, $timeend);
        }

        return self::ENROLMENT_SUCCESSFUL;
    }

    /**
     * Returns the LTI tool.
     *
     * @param int $toolid
     * @return \stdClass the tool
     */
    public static function get_lti_tool($toolid) {
        global $DB;

        $sql = "SELECT elt.*, e.name, e.courseid, e.status, e.enrolstartdate, e.enrolenddate, e.enrolperiod
                  FROM {enrol_lti_tools} elt
                  JOIN {enrol} e
                    ON elt.enrolid = e.id
                 WHERE elt.id = :tid";

        return $DB->get_record_sql($sql, array('tid' => $toolid), MUST_EXIST);
    }

    /**
     * Returns the LTI tools requested.
     *
     * @param array $params The list of SQL params (eg. array('columnname' => value, 'columnname2' => value)).
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records in total
     * @return array of tools
     */
    public static function get_lti_tools($params = array(), $limitfrom = 0, $limitnum = 0) {
        global $DB;

        $sql = "SELECT elt.*, e.name, e.courseid, e.status, e.enrolstartdate, e.enrolenddate, e.enrolperiod
                  FROM {enrol_lti_tools} elt
                  JOIN {enrol} e
                    ON elt.enrolid = e.id";
        if ($params) {
            $where = "WHERE";
            foreach ($params as $colname => $value) {
                $sql .= " $where $colname = :$colname";
                $where = "AND";
            }
        }
        $sql .= " ORDER BY elt.timecreated";

        return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    }

    /**
     * Returns the number of LTI tools.
     *
     * @param array $params The list of SQL params (eg. array('columnname' => value, 'columnname2' => value)).
     * @return int The number of tools
     */
    public static function count_lti_tools($params = array()) {
        global $DB;

        $sql = "SELECT COUNT(*)
                  FROM {enrol_lti_tools} elt
                  JOIN {enrol} e
                    ON elt.enrolid = e.id";
        if ($params) {
            $where = "WHERE";
            foreach ($params as $colname => $value) {
                $sql .= " $where $colname = :$colname";
                $where = "AND";
            }
        }

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Create a IMS POX body request for sync grades.
     *
     * @param string $source Sourceid required for the request
     * @param float $grade User final grade
     * @return string
     */
    public static function create_service_body($source, $grade) {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <imsx_POXEnvelopeRequest xmlns="http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
              <imsx_POXHeader>
                <imsx_POXRequestHeaderInfo>
                  <imsx_version>V1.0</imsx_version>
                  <imsx_messageIdentifier>' . (time()) . '</imsx_messageIdentifier>
                </imsx_POXRequestHeaderInfo>
              </imsx_POXHeader>
              <imsx_POXBody>
                <replaceResultRequest>
                  <resultRecord>
                    <sourcedGUID>
                      <sourcedId>' . $source . '</sourcedId>
                    </sourcedGUID>
                    <result>
                      <resultScore>
                        <language>en-us</language>
                        <textString>' . $grade . '</textString>
                      </resultScore>
                    </result>
                  </resultRecord>
                </replaceResultRequest>
              </imsx_POXBody>
            </imsx_POXEnvelopeRequest>';
    }
}
