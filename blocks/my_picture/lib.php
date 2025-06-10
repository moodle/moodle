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
 * Connects to LSU web service for downloading and updating user photos
 *
 * @package    block_my_picture
 * @copyright  2008, Adam Zapletal, 2017, Robert Russo, Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Set and get the config variable.
global $CFG;

require_once($CFG->libdir . '/gdlib.php');
require_once($CFG->libdir . '/filelib.php');

/**
 *
 * @global stdClass $DB
 * @param int $limit how many to fetch
 * @return stdClass[]
 */
function mypic_get_users_without_pictures($limit=0) {
    global $DB;
    $select = "picture IN (0,2) AND idnumber LIKE '89%' AND deleted = 0 AND suspended = 0 ORDER BY RAND() LIMIT " . $limit;
    return $DB->get_records_select('user', $select);
}


/**
 * For a given array of idnumbers, return only the subset that are valid in the database
 * @global type $DB
 * @param int[] $idnumbers array of idnumber keys to fetch users with
 * @return stdClass[] user row objects from the DB
 */
function mypic_WebserviceIntersectMoodle ($idnumbers = array()) {
    global $DB;
    return array_values($DB->get_records_list('user', 'idnumber', $idnumbers, '', 'id, firstname, lastname, idnumber'));
}

/**
 * For a given array of users and photo paths, insert the photo into Moodle
 * @global type $DB, $CFG
 * @param $userid array of Moodle user id keys for users to update
 * @param $picturepath array of Moodle temp photos downloaded to update
 */
function mypic_insert_picture($userid, $picturepath) {
    global $DB, $CFG;

    $context = context_user::instance($userid);

    $pathparts  = explode('/', $picturepath);
    $file       = array_pop($pathparts);
    $dir        = array_pop($pathparts);
    $shortpath  = $dir.'/'.$file;

    if (!file_exists($picturepath)) {
        return false;
    } else if ($picturepath == $CFG->dirroot . '/blocks/my_picture/images/nopic.png') {
        try {
            process_new_icon($context, 'user', 'icon', 0, $picturepath);
            return $DB->set_field('user', 'picture', 2, array('id' => $userid));
        } catch (Exception $e) {
            // No code here in original version.
        }
    } else {
        try {
            process_new_icon($context, 'user', 'icon', 0, $picturepath);
            return $DB->set_field('user', 'picture', 1, array('id' => $userid));
        } catch (Exception $e) {
            // No code here in original version.
        }
    }
}

/**
 * For a given array of user ids, insert the "nopic" photo for users without photos in the ID system
 * @global type $CFG
 * @param $userid array of Moodle user id keys to fetch users with
 * @return stdClass[] userids and the path for the "nopic" image
 */
function mypic_insert_nopic($userid) {
    global $CFG;

    $nopicpath = $CFG->dirroot . '/blocks/my_picture/images/nopic.png';

    return mypic_insert_picture($userid, $nopicpath);
}

/**
 * For a given array of user ids, insert the "badid" photo for users without idnumbers
 * @global type $CFG
 * @param $userid array of Moodle user id keys to fetch users with
 * @return stdClass[] userids and the path for the temporary "badid" image
 */
function mypic_insert_badid($userid) {
    global $CFG;

    $badidpath = $CFG->dirroot . '/blocks/my_picture/images/badid.jpg';

    return mypic_insert_picture($userid, $badidpath);
}

/**
 * This method calls the webservice show() method, requests return as json
 * @param type $idnumber 89-number
 * @param type $hash
 * @deprecated no longer need update functionality
 * @return boolean
 */
function mypic_force_update_picture($idnumber, $hash = null) {
    throw new coding_exception("There is no longer any need to 'update' photos; please do not call this function");

    $url = get_config('block_my_picture', 'update_url');

    if (empty($url)) {
        return true;
    }

    if (empty($hash)) {
        $hash = $idnumber;
    }

    $curl = new curl();
    $json = $curl->post(sprintf($url, $hash));

    $obj = json_decode($json);

    return (
        isset($obj->success) and
        $obj->success->message == 'Photo update scheduled' and
        $obj->success->status == 1
    );
}

/**
 * This method calls webservice show() method requesting response as jpg
 * @global type $CFG
 * @param type $idnumber 89-number
 * @param type $updating trigger the external service to mark the user photo as updated
 * @return boolean|string
 */
function mypic_fetch_picture($idnumber, $updating = false) {
    global $CFG, $USER;

    $hash = $idnumber;
    $name = $idnumber . '.jpg';
    $path = $CFG->dataroot . '/temp/' . $name;
    $url  = sprintf(get_config('block_my_picture', 'webservice_url'), $hash);
    $curl = new curl();
    $file = fopen($path, 'w');
    $curl->download(array(array('url' => $url, 'file' => $file)));
    $contenttype = isset($curl->response['Content-Type']) ? $curl->response['Content-Type'] : null;
    $responsecode = isset($curl->response['HTTP/1.1']) ? $curl->response['HTTP/1.1'] : null;
    if ((is_siteadmin($USER) && $CFG->debugdeveloper == true) && php_sapi_name() != "cli") {
        echo'Raw Response Code: ';
        var_dump($responsecode);
        echo'<br>';
        echo'Waw Content Type: ';
        var_dump($contenttype);
        echo'<br>';
    }

    $responsecode = (int)substr($responsecode, 0, 3);
    fclose($file);
    if ((is_siteadmin($USER) && $CFG->debugdeveloper == true) && php_sapi_name() != "cli") {
        echo'Adjusted Response Code: ';
        var_dump($responsecode);
        echo'<br>';
    }

    if ($responsecode == '200' && $contenttype == 'image/jpeg') {
        if (php_sapi_name() != "cli") {
            echo'<p style="text-align: center;"><img src="data:image/jpeg;base64,'
                . base64_encode(file_get_contents($url))
                . '" alt="photo" style="border: 1px solid #666666; margin: 0 auto; border-radius: 10px;" '
                . 'width="auto" height="100px" /></p>';
        }
        return $path;
    } elseif ($responsecode != 404) {
        if ((is_siteadmin($USER) && $CFG->debugdeveloper == true) OR php_sapi_name() == "cli") {
            echo(get_string('cron_webservice_response', 'block_my_picture', ['response' => $responsecode, 'content' => $contenttype, 'idnumber' => $idnumber]));
            echo(get_string('cron_webservice_err', 'block_my_picture'));
        }
        return false;
    } else {
        if ((is_siteadmin($USER) && $CFG->debugdeveloper == true) OR php_sapi_name() == "cli") {
            echo(get_string('cron_webservice_response', 'block_my_picture', ['response' => $responsecode, 'content' => $contenttype, 'idnumber' => $idnumber]));
        }
        unlink($path);
        return false;
    }
}

/**
 * For a given array of user idnumbers, determine if they are valid or not
 * @param $idnumber array of Moodle user idnumbers
 * @return boolean true or false depending on result
 */
function mypic_is_lsuid($idnumber) {
    return preg_match('/^89\d{7}$/', $idnumber);
}

/**
 * For a given array of user ids, insert the "badid" photo for users without idnumbers
 * @global type $CFG
 * @param $user array of Moodle users
 * @param $updating overwritten with path if one exists, if not false
 * @return int[] webservice result
 * 0 - Error
 * 1 - Bad idnumber, 'contact moodle admin' picture inserted
 * 2 - Success, tiger card office picture inserted
 * 3 - Picture not found, 'visit tiger card office' picture inserted
 */
function mypic_update_picture($user, $updating=false) {

    if (!mypic_is_lsuid($user->idnumber)) {
        return (int) mypic_insert_badid($user->id);
    }

    if ($path = mypic_fetch_picture($user->idnumber, $updating)) {
        return (int) mypic_insert_picture($user->id, $path) * 2;
    }

    return (int) mypic_insert_nopic($user->id) * 3;
    return 0;
}

/**
 * @param $users, array of Moodle users
 * @param $updating, overwritten with path if one exists, if not false
 * @param $sep, seperator for displaying data in mtrace
 * @param $step, number of users per update. Set to 100.
 * @return object showing the count, number of updates, errors, nopics, and badids
 */
function mypic_batch_update($users, $updating=false, $sep='', $step=100) {
    $s = function($k, $a=null) {
        return get_string($k, 'block_my_picture', $a);
    };

    $starttime = microtime();
    $count = 0;
    $numsuccess = 0;
    $numerror = 0;
    $numnopic = 0;
    $numbadid = 0;

    foreach ($users as $user) {
        mtrace('Processing image for (' . $user->idnumber . ') ');

        // Keys are error codes, values are counter variables to increment.
        $resultmap = array(
            0 => 'numerror',
            1 => 'numbadid',
            2 => 'numsuccess',
            3 => 'numnopic'
        );

        $mypicreturncode = mypic_update_picture($user, $updating);

        mtrace($resultmap[$mypicreturncode] . $sep);

        switch ($mypicreturncode)
        {
            case 0: {
                $numerror++;
                break;
            }
            case 1: {
                $numbadid++;
                break;
            }
            case 2: {
                $numsuccess++;
                break;
            }
            case 3: {
                $numnopic++;
                break;
            }
        }

        $count++;

        if (!($count % $step)) {
            mtrace($s('completed', $count) . $sep);
        }
    }

    $timediff = round(microtime_diff($starttime, microtime()), 1);

    mtrace($s('finish', $count) . $sep);

    foreach (array('success', 'nopic', 'error', 'badid') as $report) {
        $num = ${'num' . $report};

        $percent = round($num / $count * 100, 2);
        $str = $s('num' . $report);

        mtrace("$num ($percent%) $str $sep");
    }

    mtrace($s('elapsed', $timediff) . $sep);
    return array(
        'count' => $count,
        'success' => $numsuccess,
        'error' => $numerror,
        'nopic' => $numnopic,
        'badid' => $numbadid
        );
}

/**
 * Verifies the web service exists otherwise exits
 * @return boolean
 */
function mypic_verifyWebserviceExists() {
    $ready = get_config('block_my_picture', 'ready_url');
    $curl  = curl_init($ready);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_exec($curl);
    $isimage = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    $isalive = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

    if (!$isimage == 'image/jpeg') {
        mypic_emailAdminsFailureMsg($ready);
        return false;
    }

    if (!$isalive == '200') {
        mypic_emailAdminsFailureMsg($ready);
        return false;
    }

    return true;
}

/**
 * Simple email routine for messaging to admins
 * @global type $CFG
 * @global stdClass $DB
 * @global type $USER
 * @return int number of errors encountered while sending email
 */
function mypic_emailAdminsFailureMsg($address='<none given>') {
    global $CFG, $DB, $USER;

    $subject = get_string('misconfigured_subject', 'block_my_picture');
    $message = get_string('misconfigured_message', 'block_my_picture', $address);

    mtrace(sprintf('addr arg = %s, message = %s', $address, $message));

    $adminids     = explode(',', $CFG->siteadmins);
    $admins = $DB->get_records_list('user', 'id', $adminids);
    $errors = 0;
    foreach ($admins as $admin) {
        $success = email_to_user(
                $admin, // To
                $USER, // from
                $subject, // subj
                $message, // body in plain text
                $message, // body in HTML
                '', // attachment
                '', // attachment name
                true, // user true address ($USER)
                $CFG->noreplyaddress, // reply-to address
                get_string('pluginname', 'block_my_picture') // reply-to name.
            );
        if (!$success) {
            $errors++;
        }
    }
    return $errors == 0 ? true : false;
}
