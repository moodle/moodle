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
 * Internal library of functions for module tincanlaunch
 *
 * All the tincanlaunch specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->dirroot/mod/tincanlaunch/lib.php");

/**
 * Send a statement that the activity was launched.
 * This is useful for debugging - if the 'launched' statement is present in the LRS, you know the activity was at least launched.
 *
 * @param string/UUID $registrationid The Tin Can Registration UUID associated with the launch.
 * @return TinCan LRS Response
 */
function tincan_launched_statement($registrationid) {
    global $tincanlaunch, $course, $CFG;
    $tincanlaunchsettings = tincanlaunch_settings($tincanlaunch->id);

    $version = $tincanlaunchsettings['tincanlaunchlrsversion'];
    $url = $tincanlaunchsettings['tincanlaunchlrsendpoint'];
    $basiclogin = $tincanlaunchsettings['tincanlaunchlrslogin'];
    $basicpass = $tincanlaunchsettings['tincanlaunchlrspass'];

    $tincanphputil = new \TinCan\Util();
    $statementid = $tincanphputil->getUUID();

    $lrs = new \TinCan\RemoteLRS($url, $version, $basiclogin, $basicpass);

    $parentdefinition = array();
    if (isset($course->summary) && $course->summary !== "") {
        $parentdefinition["description"] = array(
            "en-US" => $course->summary
        );
    }

    if (isset($course->fullname) && $course->fullname !== "") {
        $parentdefinition["name"] = array(
            "en-US" => $course->fullname
        );
    }

    $statement = new \TinCan\Statement(
        array(
            'id' => $statementid,
            'actor' => tincanlaunch_getactor($tincanlaunch->id),
            'verb' => array(
                'id' => 'http://adlnet.gov/expapi/verbs/launched',
                'display' => array(
                    'en-US' => 'launched'
                )
            ),

            'object' => array(
                'id' => $tincanlaunch->tincanactivityid,
                'objectType' => "Activity"
            ),

            "context" => array(
                "registration" => $registrationid,
                "contextActivities" => array(
                    "parent" => array(
                        array(
                            "id" => $CFG->wwwroot . '/course/view.php?id=' . $course->id,
                            "objectType" => "Activity",
                            "definition" => $parentdefinition
                        )
                    ),
                    "grouping"  => array(
                        array(
                            "id" => $CFG->wwwroot,
                            "objectType" => "Activity"
                        )
                    ),
                    "category"  => array(
                        array(
                            "id" => "https://moodle.org",
                            "objectType" => "Activity",
                            "definition" => array(
                                "type" => "http://id.tincanapi.com/activitytype/source"
                            )
                        )
                    )
                ),
                "language" => tincanlaunch_get_moodle_language()
            ),
            "timestamp" => date(DATE_ATOM)
        )
    );

    $response = $lrs->saveStatement($statement);
    return $response;
}

/**
 * Builds a Tin Can launch link for the current module and a given registration
 *
 * @param string $registrationuuid The Tin Can Registration UUID associated with the launch.
 * @return string Launch link including querystring.
 */
function tincanlaunch_get_launch_url($registrationuuid) {
    global $tincanlaunch;
    $tincanlaunchsettings = tincanlaunch_settings($tincanlaunch->id);
    $expiry = new DateTime('NOW');
    $xapiduration = $tincanlaunchsettings['tincanlaunchlrsduration'];
    $expiry->add(new DateInterval('PT' . $xapiduration . 'M'));

    $url = trim($tincanlaunchsettings['tincanlaunchlrsendpoint']);

    // Call the function to get the credentials from the LRS.
    $basiclogin = trim($tincanlaunchsettings['tincanlaunchlrslogin']);
    $basicpass = trim($tincanlaunchsettings['tincanlaunchlrspass']);

    switch ($tincanlaunchsettings['tincanlaunchlrsauthentication']) {

            // Learning Locker 1.
        case "0":
            $creds = tincanlaunch_get_creds_learninglocker(
                $tincanlaunchsettings['tincanlaunchlrslogin'],
                $tincanlaunchsettings['tincanlaunchlrspass'],
                $url,
                $expiry,
                $registrationuuid
            );
            $basicauth = base64_encode($creds["contents"]["key"] . ":" . $creds["contents"]["secret"]);
            break;

            // Watershed.
        case "2":
            $creds = tincanlaunch_get_creds_watershed(
                $basiclogin,
                $basicpass,
                $url,
                $xapiduration * 60
            );
            $basicauth = base64_encode($creds["key"] . ":" . $creds["secret"]);
            break;

        default:
            $basicauth = base64_encode($basiclogin . ":" . $basicpass);
            break;
    }

    // Build the URL to be returned.
    $rtnstring = $tincanlaunch->tincanlaunchurl . "?" . http_build_query(
        array(
            "endpoint" => $url,
            "auth" => "Basic " . $basicauth,
            "actor" => tincanlaunch_myjson_encode(
                tincanlaunch_getactor($tincanlaunch->id)->asVersion(
                    $tincanlaunchsettings['tincanlaunchlrsversion']
                )
            ),
            "registration" => $registrationuuid,
            "activity_id" => $tincanlaunch->tincanactivityid
        ),
        '',
        '&',
        PHP_QUERY_RFC3986
    );

    return $rtnstring;
}

/**
 * Used with Learning Locker integration to fetch credentials from the LRS.
 * This process is not part of the xAPI specification or the Tin Can launch spec.
 *
 * @param string $basiclogin login/key for the LRS
 * @param string $basicpass pass/secret for the LRS
 * @param string $url LRS endpoint URL
 * @param DateTime $expiry expiry date for the credentials
 * @param string $registrationuuid registration UUID for the launch
 * @return array the response of the LRS (Note: not a TinCan LRS Response object)
 */
function tincanlaunch_get_creds_learninglocker($basiclogin, $basicpass, $url, $expiry, $registrationuuid) {
    global $tincanlaunch;
    $actor = tincanlaunch_getactor($tincanlaunch->id);
    $data = array(
        'scope' => array('all'),
        'expiry' => $expiry->format(DATE_ATOM),
        'historical' => false,
        'actors' => array(
            "objectType" => 'Person',
            "name" => array($actor->getName())
        ),
        'auth' => $actor,
        'activity' => array(
            $tincanlaunch->tincanactivityid,
        ),
        'registration' => $registrationuuid
    );

    if (null !== $actor->getMbox()) {
        $data['actors']['mbox'] = array($actor->getMbox());
    } else if (null !== $actor->getMbox_sha1sum()) {
        $data['actors']['mbox_sha1sum'] = array($actor->getMbox_sha1sum());
    } else if (null !== $actor->getOpenid()) {
        $data['actors']['openid'] = array($actor->getOpenid());
    } else if (null !== $actor->getAccount()) {
        $data['actors']['account'] = array($actor->getAccount());
    }

    $streamopt = array(
        'ssl' => array(
            'verify-peer' => false,
        ),
        'http' => array(
            'method' => 'POST',
            'ignore_errors' => false,
            'header' => array(
                'Authorization: Basic ' . base64_encode(trim($basiclogin) . ':' . trim($basicpass)),
                'Content-Type: application/json',
                'Accept: application/json, */*; q=0.01',
            ),
            'content' => tincanlaunch_myjson_encode($data),
        ),
    );

    $streamparams = array();

    $context = stream_context_create($streamopt);

    $stream = fopen(trim($url) . 'Basic/request' . '?' . http_build_query($streamparams, '', '&'), 'rb', false, $context);

    $returncode = explode(' ', $http_response_header[0]);
    $returncode = (int) $returncode[1];

    switch ($returncode) {
        case 200:
            $ret = stream_get_contents($stream);
            $meta = stream_get_meta_data($stream);

            if ($ret) {
                $ret = json_decode($ret, true);
            }
            break;
        default: // Error!
            $ret = null;
            $meta = $returncode;
            break;
    }

    return array(
        'contents' => $ret,
        'metadata' => $meta
    );
}

/**
 * By default, PHP escapes slashes when encoding into JSON. This cause problems for Tin Can,
 * so this function unescapes the slashes after encoding.
 *
 * @param object $obj object or array encode to JSON
 * @return string/JSON JSON encoded object or array
 */
function tincanlaunch_myjson_encode($obj) {
    return str_replace('\\/', '/', json_encode($obj));
}

/**
 * Save data to the state. Note: registration is not used as this is a general bucket of data against the activity/learner.
 *
 * @param string $data data to store as document
 * @param string $key id to store the document against
 * @param string $etag etag associated with the document last time it was fetched (may be Null if document is new)
 * @return TinCan LRS Response
 */
function tincanlaunch_get_global_parameters_and_save_state($data, $key, $etag) {
    global $tincanlaunch;
    $tincanlaunchsettings = tincanlaunch_settings($tincanlaunch->id);
    $lrs = new \TinCan\RemoteLRS(
        $tincanlaunchsettings['tincanlaunchlrsendpoint'],
        $tincanlaunchsettings['tincanlaunchlrsversion'],
        $tincanlaunchsettings['tincanlaunchlrslogin'],
        $tincanlaunchsettings['tincanlaunchlrspass']
    );

    return $lrs->saveState(
        new \TinCan\Activity(array("id" => trim($tincanlaunch->tincanactivityid))),
        tincanlaunch_getactor($tincanlaunch->id),
        $key,
        tincanlaunch_myjson_encode($data),
        array(
            'etag' => $etag,
            'contentType' => 'application/json'
        )
    );
}

/**
 * Save data to the agent profile.
 * Note: registration is not used as this is a general bucket of data against the activity/learner.
 * Note: fetches a new etag before storing. Will ALWAYS overwrite existing contents of the document.
 *
 * @param string $key id to store the document against
 * @param string $data data to store as document
 * @return TinCan LRS Response
 */
function tincanlaunch_get_global_parameters_and_save_agentprofile($key, $data) {
    global $tincanlaunch;
    $tincanlaunchsettings = tincanlaunch_settings($tincanlaunch->id);

    $lrs = new \TinCan\RemoteLRS(
        $tincanlaunchsettings['tincanlaunchlrsendpoint'],
        $tincanlaunchsettings['tincanlaunchlrsversion'],
        $tincanlaunchsettings['tincanlaunchlrslogin'],
        $tincanlaunchsettings['tincanlaunchlrspass']
    );

    $getresponse = $lrs->retrieveAgentProfile(tincanlaunch_getactor($tincanlaunch->id), $key);

    $opts = array(
        'contentType' => 'application/json'
    );
    if ($getresponse->success) {
        $opts['etag'] = $getresponse->content->getEtag();
    }

    return $lrs->saveAgentProfile(tincanlaunch_getactor($tincanlaunch->id), $key, tincanlaunch_myjson_encode($data), $opts);
}

/**
 * Get data from the state. Note: registration is not used as this is a general bucket of data against the activity/learner.
 *
 * @param string $key id to store the document against
 * @return TinCan LRS Response containing the response code and data or error message
 */
function tincanlaunch_get_global_parameters_and_get_state($key) {
    global $tincanlaunch;
    $tincanlaunchsettings = tincanlaunch_settings($tincanlaunch->id);

    $lrs = new \TinCan\RemoteLRS(
        $tincanlaunchsettings['tincanlaunchlrsendpoint'],
        $tincanlaunchsettings['tincanlaunchlrsversion'],
        $tincanlaunchsettings['tincanlaunchlrslogin'],
        $tincanlaunchsettings['tincanlaunchlrspass']
    );

    return $lrs->retrieveState(
        new \TinCan\Activity(array("id" => trim($tincanlaunch->tincanactivityid))),
        tincanlaunch_getactor($tincanlaunch->id),
        $key
    );
}


/**
 * Get the current language of the current user and return it as an RFC 5646 language tag
 *
 * @return string RFC 5646 language tag
 */
function tincanlaunch_get_moodle_language() {
    $lang = current_language();
    $langarr = explode('_', $lang);
    if (count($langarr) == 2) {
        return $langarr[0] . '-' . strtoupper($langarr[1]);
    } else {
        return $lang;
    }
}


/**
 * Used with Watershed integration to fetch credentials from the LRS.
 * This process is not part of the xAPI specification or the Tin Can launch spec.
 *
 * @param string $login login for Watershed
 * @param string $pass pass for Watershed
 * @param string $endpoint LRS endpoint URL
 * @param int $expiry number of seconds the credentials are required for
 * @return array the response of the LRS (Note: not a TinCan LRS Response object)
 */
function tincanlaunch_get_creds_watershed($login, $pass, $endpoint, $expiry) {

    // Process input parameters.
    $auth = 'Basic ' . base64_encode($login . ':' . $pass);

    $explodedendpoint = explode('/', $endpoint);
    $wsserver = $explodedendpoint[0] . '//' . $explodedendpoint[2];
    $orgid = $explodedendpoint[5];

    // Create a session.
    $sessionresponse = tincanlaunch_send_api_request(
        $auth,
        "POST",
        $wsserver . "/api/organizations/" . $orgid . "/activity-providers/self/sessions",
        [
            "content" => json_encode([
                "expireSeconds" => $expiry,
                "scope" => "xapi:all"
            ])
        ]
    );

    if ($sessionresponse["status"] === 200) {
        return [
            "key" => $sessionresponse["content"]->key,
            "secret" => $sessionresponse["content"]->secret
        ];
    } else {
        $reason = get_string('apCreationFailed', 'tincanlaunch')
            . " Status: " . $sessionresponse["status"] . ". Response: " . $sessionresponse["content"]->message;
        throw new moodle_exception($reason, 'tincanlaunch', '');
    }
}

/**
 * Sends a request to the API.
 *
 * @param string $auth Auth string
 * @param string $method Method of the request e.g. POST.
 * @param string $url URL to request
 * @return array Details of the response
 */
function tincanlaunch_send_api_request($auth, $method, $url) {
    $options = func_num_args() === 4 ? func_get_arg(3) : array();

    if (!isset($options['contentType'])) {
        $options['contentType'] = 'application/json';
    }

    $http = array(
        // We don't expect redirects.
        'max_redirects' => 0,
        // This is here for some proxy handling.
        'request_fulluri' => 1,
        // Switching this to false causes non-2xx/3xx status codes to throw exceptions.
        // but we need to handle the "error" status codes ourselves in some cases.
        'ignore_errors' => true,
        'method' => $method,
        'header' => array()
    );

    array_push($http['header'], 'Authorization: ' . $auth);

    if (($method === 'PUT' || $method === 'POST') && isset($options['content'])) {
        $http['content'] = $options['content'];
        array_push($http['header'], 'Content-length: ' . strlen($options['content']));
        array_push($http['header'], 'Content-Type: ' . $options['contentType']);
    }

    $context = stream_context_create(array('http' => $http));
    $fp = fopen($url, 'rb', false, $context);
    if (!$fp) {
        return array(
            "metadata" => null,
            "content" => null,
            "status" => 0
        );
    }
    $metadata = stream_get_meta_data($fp);
    $content  = stream_get_contents($fp);
    $responsecode = (int) explode(' ', $metadata["wrapper_data"][0])[1];

    fclose($fp);

    if ($options['contentType'] == 'application/json') {
        $content = json_decode($content);
    }

    return array(
        "metadata" => $metadata,
        "content" => $content,
        "status" => $responsecode
    );
}
