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
 * Class communication
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\hub;
defined('MOODLE_INTERNAL') || die();

use webservice_xmlrpc_client;
use moodle_exception;
use curl;
use stdClass;
use coding_exception;
use moodle_url;

/**
 * Methods to communicate with moodle.net web services
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /** @var File type: Course screenshot */
    const HUB_SCREENSHOT_FILE_TYPE = 'screenshot';

    /** @var File type: Hub screenshot */
    const HUB_HUBSCREENSHOT_FILE_TYPE = 'hubscreenshot';

    /** @var File type: Backup */
    const HUB_BACKUP_FILE_TYPE = 'backup';

    /**
     * Calls moodle.net WS
     *
     * @param string $function name of WS function
     * @param array $data parameters of WS function
     * @param bool $allowpublic allow request without moodle.net registration
     * @return mixed depends on the function
     * @throws moodle_exception
     */
    protected static function call($function, array $data = [], $allowpublic = false) {

        $token = registration::get_token() ?: 'publichub';
        if (!$allowpublic && $token === 'publichub') {
            // This will throw an exception.
            registration::require_registration();
        }

        return self::call_rest($token, $function, $data);
    }

    /**
     * Performs REST request to moodle.net (using GET method)
     *
     * @param string $token
     * @param string $function
     * @param array $data
     * @return mixed
     * @throws moodle_exception
     */
    protected static function call_rest($token, $function, array $data) {
        $params = [
                'wstoken' => $token,
                'wsfunction' => $function,
                'moodlewsrestformat' => 'json'
            ] + $data;

        $curl = new curl();
        $serverurl = HUB_MOODLEORGHUBURL . "/local/hub/webservice/webservices.php";
        $curloutput = @json_decode($curl->get($serverurl, $params), true);
        $info = $curl->get_info();
        if ($curl->get_errno()) {
            // Connection error.
            throw new moodle_exception('errorconnect', 'hub', '', $curl->error);
        } else if (isset($curloutput['exception'])) {
            // Exception occurred on moodle.net .
            self::process_curl_exception($token, $curloutput);
        } else if ($info['http_code'] != 200) {
            throw new moodle_exception('errorconnect', 'hub', '', $info['http_code']);
        } else {
            return $curloutput;
        }
    }

    /**
     * Analyses exception received from moodle.net
     *
     * @param string $token token used for CURL request
     * @param array $curloutput output from CURL request
     * @throws moodle_exception
     */
    protected static function process_curl_exception($token, $curloutput) {
        if (!isset($curloutput['exception'])) {
            return;
        }
        if ($token === registration::get_token()) {
            // Check if registration token was rejected or there are other problems with registration.
            if (($curloutput['exception'] === 'moodle_exception' && $curloutput['errorcode'] === 'invalidtoken')
                    || $curloutput['exception'] === 'registration_exception') {
                // Force admin to repeat site registration process.
                registration::reset_token();
                throw new moodle_exception('errorwstokenreset', 'hub', '', $curloutput['message']);
            }
        }
        throw new moodle_exception('errorws', 'hub', '', $curloutput['message']);
    }

    /**
     * Update site registration on moodle.net
     *
     * @param array $siteinfo
     * @throws moodle_exception
     */
    public static function update_registration(array $siteinfo) {
        $params = array('siteinfo' => $siteinfo, 'validateurl' => 1);
        self::call('hub_update_site_info', $params);
    }

    /**
     * Returns information about moodle.net
     *
     * Example of the return array:
     * {
     *     "courses": 384,
     *     "description": "Moodle.net connects you with free content and courses shared by Moodle ...",
     *     "downloadablecourses": 190,
     *     "enrollablecourses": 194,
     *     "hublogo": 1,
     *     "language": "en",
     *     "name": "Moodle.net",
     *     "sites": 274175,
     *     "url": "https://moodle.net",
     *     "imgurl": "https://moodle.net/local/hub/webservice/download.php?filetype=hubscreenshot"
     * }
     *
     * @return array
     * @throws moodle_exception
     */
    public static function get_hub_info() {
        $info = self::call('hub_get_info', [], true);
        $info['imgurl'] = new moodle_url(HUB_MOODLEORGHUBURL . '/local/hub/webservice/download.php',
            ['filetype' => self::HUB_HUBSCREENSHOT_FILE_TYPE]);
        return $info;
    }

    /**
     * Calls WS function hub_get_courses
     *
     * @deprecated since Moodle 3.8. Moodle.net has been sunsetted making this function useless.
     *
     * Parameter $options may have any of these fields:
     * [
     *     'ids' => new external_multiple_structure(new external_value(PARAM_INTEGER, 'id of a course in the hub course
     *          directory'), 'ids of course', VALUE_OPTIONAL),
     *     'sitecourseids' => new external_multiple_structure(new external_value(PARAM_INTEGER, 'id of a course in the
     *          site'), 'ids of course in the site', VALUE_OPTIONAL),
     *     'coverage' => new external_value(PARAM_TEXT, 'coverage', VALUE_OPTIONAL),
     *     'licenceshortname' => new external_value(PARAM_ALPHANUMEXT, 'licence short name', VALUE_OPTIONAL),
     *     'subject' => new external_value(PARAM_ALPHANUM, 'subject', VALUE_OPTIONAL),
     *     'audience' => new external_value(PARAM_ALPHA, 'audience', VALUE_OPTIONAL),
     *     'educationallevel' => new external_value(PARAM_ALPHA, 'educational level', VALUE_OPTIONAL),
     *     'language' => new external_value(PARAM_ALPHANUMEXT, 'language', VALUE_OPTIONAL),
     *     'orderby' => new external_value(PARAM_ALPHA, 'orderby method: newest, eldest, publisher, fullname,
     *          ratingaverage', VALUE_OPTIONAL),
     *     'givememore' => new external_value(PARAM_INT, 'next range of result - range size being set by the hub
     *          server ', VALUE_OPTIONAL),
     *     'allsitecourses' => new external_value(PARAM_INTEGER,
     *          'if 1 return all not visible and visible courses whose siteid is the site
     *          matching token. Only courses of this site are returned.
     *          givememore parameter is ignored if this param = 1.
     *          In case of public token access, this param option is ignored', VALUE_DEFAULT, 0),
     * ]
     *
     * Each course in the returned array of courses will have fields:
     * [
     *     'id' => new external_value(PARAM_INTEGER, 'id'),
     *     'fullname' => new external_value(PARAM_TEXT, 'course name'),
     *     'shortname' => new external_value(PARAM_TEXT, 'course short name'),
     *     'description' => new external_value(PARAM_TEXT, 'course description'),
     *     'language' => new external_value(PARAM_ALPHANUMEXT, 'course language'),
     *     'publishername' => new external_value(PARAM_TEXT, 'publisher name'),
     *     'publisheremail' => new external_value(PARAM_EMAIL, 'publisher email', VALUE_OPTIONAL),
     *     'privacy' => new external_value(PARAM_INT, 'privacy: published or not', VALUE_OPTIONAL),
     *     'sitecourseid' => new external_value(PARAM_INT, 'course id on the site', VALUE_OPTIONAL),
     *     'contributornames' => new external_value(PARAM_TEXT, 'contributor names', VALUE_OPTIONAL),
     *     'coverage' => new external_value(PARAM_TEXT, 'coverage', VALUE_OPTIONAL),
     *     'creatorname' => new external_value(PARAM_TEXT, 'creator name'),
     *     'licenceshortname' => new external_value(PARAM_ALPHANUMEXT, 'licence short name'),
     *     'subject' => new external_value(PARAM_ALPHANUM, 'subject'),
     *     'audience' => new external_value(PARAM_ALPHA, 'audience'),
     *     'educationallevel' => new external_value(PARAM_ALPHA, 'educational level'),
     *     'creatornotes' => new external_value(PARAM_RAW, 'creator notes'),
     *     'creatornotesformat' => new external_value(PARAM_INTEGER, 'notes format'),
     *     'demourl' => new external_value(PARAM_URL, 'demo URL', VALUE_OPTIONAL),
     *     'courseurl' => new external_value(PARAM_URL, 'course URL', VALUE_OPTIONAL),
     *     'backupsize' => new external_value(PARAM_INT, 'course backup size in bytes', VALUE_OPTIONAL),
     *     'enrollable' => new external_value(PARAM_BOOL, 'is the course enrollable'),
     *     'screenshots' => new external_value(PARAM_INT, 'total number of screenshots'),
     *     'timemodified' => new external_value(PARAM_INT, 'time of last modification - timestamp'),
     *     'contents' => new external_multiple_structure(new external_single_structure(
     *         array(
     *             'moduletype' => new external_value(PARAM_ALPHA, 'the type of module (activity/block)'),
     *             'modulename' => new external_value(PARAM_TEXT, 'the name of the module (forum, resource etc)'),
     *             'contentcount' => new external_value(PARAM_INT, 'how many time the module is used in the course'),
     *         )), 'contents', VALUE_OPTIONAL),
     *     'rating' => new external_single_structure (
     *         array(
     *              'aggregate' =>  new external_value(PARAM_FLOAT, 'Rating average', VALUE_OPTIONAL),
     *              'scaleid' => new external_value(PARAM_INT, 'Rating scale'),
     *              'count' => new external_value(PARAM_INT, 'Rating count'),
     *         ), 'rating', VALUE_OPTIONAL),
     *     'comments' => new external_multiple_structure(new external_single_structure (
     *          array(
     *              'comment' => new external_value(PARAM_TEXT, 'the comment'),
     *              'commentator' => new external_value(PARAM_TEXT, 'the name of commentator'),
     *              'date' => new external_value(PARAM_INT, 'date of the comment'),
     *         )), 'contents', VALUE_OPTIONAL),
     *     'outcomes' => new external_multiple_structure(new external_single_structure(
     *          array(
     *              'fullname' => new external_value(PARAM_TEXT, 'the outcome fullname')
     *          )), 'outcomes', VALUE_OPTIONAL)
     * ]
     *
     * Additional fields for each course:
     *      'screenshotbaseurl' (moodle_url) URL of the first screenshot, only set if $course['screenshots']>0
     *      'commenturl' (moodle_url) URL for comments
     *
     * @param string $search search string
     * @param bool $downloadable return downloadable courses
     * @param bool $enrollable return enrollable courses
     * @param array|\stdClass $options other options from the list of allowed options:
     *              'ids', 'sitecourseids', 'coverage', 'licenceshortname', 'subject', 'audience',
     *              'educationallevel', 'language', 'orderby', 'givememore', 'allsitecourses'
     * @return array of two elements: [$courses, $coursetotal]
     * @throws \coding_exception
     * @throws moodle_exception
     */
    public static function get_courses($search, $downloadable, $enrollable, $options) {
        debugging("This function has been deprecated as part of the Moodle.net sunsetting process.");
        return [[], 0];
    }

    /**
     * Unregister the site
     *
     * @throws moodle_exception
     */
    public static function unregister_site() {
        global $CFG;
        self::call('hub_unregister_site', ['url' => [$CFG->wwwroot]]);
    }

    /**
     * Unpublish courses
     *
     * @deprecated since Moodle 3.8. Moodle.net has been sunsetted making this function useless.
     *
     * @param int[]|int $courseids
     * @throws moodle_exception
     */
    public static function unregister_courses($courseids) {
        debugging("This function has been deprecated as part of the Moodle.net sunsetting process.");
    }

    /**
     * Publish one course
     *
     * @deprecated since Moodle 3.8. Moodle.net has been sunsetted making this function useless.
     *
     * Expected contents of $courseinfo:
     * [
     *     'sitecourseid' => new external_value(PARAM_INT, 'the id of the course on the publishing site'),
     *     'fullname' => new external_value(PARAM_TEXT, 'course name'),
     *     'shortname' => new external_value(PARAM_TEXT, 'course short name'),
     *     'description' => new external_value(PARAM_TEXT, 'course description'),
     *     'language' => new external_value(PARAM_ALPHANUMEXT, 'course language'),
     *     'publishername' => new external_value(PARAM_TEXT, 'publisher name'),
     *     'publisheremail' => new external_value(PARAM_EMAIL, 'publisher email'),
     *     'contributornames' => new external_value(PARAM_TEXT, 'contributor names'),
     *     'coverage' => new external_value(PARAM_TEXT, 'coverage'),
     *     'creatorname' => new external_value(PARAM_TEXT, 'creator name'),
     *     'licenceshortname' => new external_value(PARAM_ALPHANUMEXT, 'licence short name'),
     *     'subject' => new external_value(PARAM_ALPHANUM, 'subject'),
     *     'audience' => new external_value(PARAM_ALPHA, 'audience'),
     *     'educationallevel' => new external_value(PARAM_ALPHA, 'educational level'),
     *     'creatornotes' => new external_value(PARAM_RAW, 'creator notes'),
     *     'creatornotesformat' => new external_value(PARAM_INTEGER, 'notes format'),
     *     'demourl' => new external_value(PARAM_URL, 'demo URL', VALUE_OPTIONAL),
     *     'courseurl' => new external_value(PARAM_URL, 'course URL', VALUE_OPTIONAL),
     *     'enrollable' => new external_value(PARAM_BOOL, 'is the course enrollable', VALUE_DEFAULT, 0),
     *     'screenshots' => new external_value(PARAM_INT, 'the number of screenhots', VALUE_OPTIONAL),
     *     'deletescreenshots' => new external_value(PARAM_INT, 'ask to delete all the existing screenshot files
     *          (it does not reset the screenshot number)', VALUE_DEFAULT, 0),
     *     'contents' => new external_multiple_structure(new external_single_structure(
     *          array(
     *              'moduletype' => new external_value(PARAM_ALPHA, 'the type of module (activity/block)'),
     *              'modulename' => new external_value(PARAM_TEXT, 'the name of the module (forum, resource etc)'),
     *              'contentcount' => new external_value(PARAM_INT, 'how many time the module is used in the course'),
     *          )), 'contents', VALUE_OPTIONAL),
     *     'outcomes' => new external_multiple_structure(new external_single_structure(
     *         array(
     *              'fullname' => new external_value(PARAM_TEXT, 'the outcome fullname')
     *          )), 'outcomes', VALUE_OPTIONAL)
     * ]
     *
     * @param array|\stdClass $courseinfo
     * @return int id of the published course on the hub
     * @throws moodle_exception if communication to moodle.net failed or course could not be published
     */
    public static function register_course($courseinfo) {
        debugging("This function has been deprecated as part of the Moodle.net sunsetting process.");
        throw new moodle_exception('errorcoursewronglypublished', 'hub');
    }

    /**
     * Uploads a screenshot for the published course
     *
     * @deprecated since Moodle 3.8. Moodle.net has been sunsetted making this function useless.
     *
     * @param int $hubcourseid id of the published course on moodle.net, it must be published from this site
     * @param \stored_file $file
     * @param int $screenshotnumber ordinal number of the screenshot
     */
    public static function add_screenshot($hubcourseid, \stored_file $file, $screenshotnumber) {
        debugging("This function has been deprecated as part of the Moodle.net sunsetting process.");
    }

    /**
     * Downloads course backup
     *
     * @deprecated since Moodle 3.8. Moodle.net has been sunsetted making this function useless.
     *
     * @param int $hubcourseid id of the course on moodle.net
     * @param string $path local path (in tempdir) to save the downloaded backup to.
     */
    public static function download_course_backup($hubcourseid, $path) {
        debugging("This function has been deprecated as part of the Moodle.net sunsetting process.");
    }

    /**
     * Uploads a course backup
     *
     * @deprecated since Moodle 3.8. Moodle.net has been sunsetted making this function useless.
     *
     * @param int $hubcourseid id of the published course on moodle.net, it must be published from this site
     * @param \stored_file $backupfile
     */
    public static function upload_course_backup($hubcourseid, \stored_file $backupfile) {
        debugging("This function has been deprecated as part of the Moodle.net sunsetting process.");
    }
}