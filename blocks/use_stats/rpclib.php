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
 * Form for editing HTML block instances.
 *
 * @package    block_use_stats
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright  Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

define('USE_STATS_SITE_SCOPE', 1);
define('USE_STATS_COURSE_SCOPE', 2);
define('USE_STATS_MODULE_SCOPE', 3);

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/mnet/xmlrpc/client.php');

/*
 * Constants.
 */
if (!defined('RPC_SUCCESS')) {
    define('RPC_TEST', 100);
    define('RPC_SUCCESS', 200);
    define('RPC_FAILURE', 500);
    define('RPC_FAILURE_USER', 501);
    define('RPC_FAILURE_CONFIG', 502);
    define('RPC_FAILURE_DATA', 503);
    define('RPC_FAILURE_CAPABILITY', 510);
    define('MNET_FAILURE', 511);
    define('RPC_FAILURE_RECORD', 520);
    define('RPC_FAILURE_RUN', 521);
}

/**
 * Invoke the local user who make the RPC call and check his rights.
 * @param object $user The calling user.
 * @param string $capability The capability to check.
 * @param object $context The capability's context (optional / CONTEXT_SYSTEM by default).
 */
function use_stats_invoke_local_user($user, $capability, $context = null) {
    global $USER, $DB;

    // Creating response.
    $response = new stdclass;
    $response->status = RPC_SUCCESS;

    // Checking user.
    if (!array_key_exists('username', $user) ||
            !array_key_exists('remoteuserhostroot', $user) ||
                    !array_key_exists('remotehostroot', $user)) {
        $response->status = RPC_FAILURE_USER;
        $response->errors[] = 'Bad client user format.';
        return(json_encode($response));
    }

    if (empty($user['username'])) {
        $response->status = RPC_FAILURE_USER;
        $response->errors[] = 'Empty username.';
        return(json_encode($response));
    }

    // Get local identity.
    if (!$remotehost = $DB->get_record('mnet_host', array('wwwroot' => $user['remotehostroot']))) {
        $response->status = RPC_FAILURE;
        $response->errors[] = 'Calling host is not registered. Check MNET configuration';
        return(json_encode($response));
    }

    $userhost = $DB->get_record('mnet_host', array('wwwroot' => $user['remoteuserhostroot']));

    if (!$localuser = $DB->get_record('user', array('username' => $user['username'], 'mnethostid' => $userhost->id))) {
        $response->status = RPC_FAILURE_USER;
        $response->errors[] = "Calling user has no local account. Register remote user first";
        return(json_encode($response));
    }
    // Replacing current user by remote user.

    $USER = $localuser;

    // Checking capabilities.
    if (is_null($context)) {
        $context = context_system::instance();
    }

    if ((is_string($capability) && !has_capability($capability, $context)) ||
            (is_string($capability) && !has_one_capability($capability, $context))) {
        $response->status = RPC_FAILURE_CAPABILITY;
        $response->errors[] = 'Local user\'s identity has no capability to run';
        return(json_encode($response));
    }

    return '';
}

/**
 * get a complete report of user stats for a single user.
 *
 * @param array $callinguser The calling user descriptor
 * @param string $targetuser Who stats are required for
 * @param string $whereroot Where the user comes from
 * @param string $statsscope Scope of the stats
 * @param string $timefrom Scope of the stats
 */
function use_stats_rpc_get_stats($callinguser, $targetuser, $whereroot,
                                 $statsscope = USE_STATS_SITE_SCOPE, $timefrom = 0, $jsonresponse = true) {
    global $CFG, $USER, $DB;

    $extresponse = new stdclass;
    $extresponse->status = RPC_SUCCESS;
    $extresponse->errors[] = array();

    // Invoke local user and check his rights.
    $capabilities = array('block/use_stats:seesitedetails', 'block/use_stats:seecoursedetails');
    if ($authresponse = use_stats_invoke_local_user((array)$callinguser, $capabilities)) {
        if ($jsonresponse) {
            return $authresponse;
        } else {
            return json_decode($authresponse);
        }
    }

    if (empty($whereroot) || $whereroot == $CFG->wwwroot) {
        if (!$targetuser = $DB->get_record('user', array('username' => $targetuser))) {
            $extresponse->status = RPC_FAILURE_RECORD;
            $extresponse->errors[] = 'Target user does not exist.';
            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        }

        // Get stats and report answer.
        if (empty($config->threshold)) {
            set_config('threshold', 60, 'block_use_stats');
            $config->threshold = 60;
        }

        $logs = use_stats_extract_logs($timefrom, time(), $targetuser->id);
        $lasttime = $timefrom;
        $totaltime = 0;
        $totaltimecourse = array();
        $totaltimemodule = array();

        if ($logs) {
            foreach ($logs as $alog) {
                $delta = $alog->time - $lasttime;
                if ($delta < $config->threshold * MINSECS) {
                    $totaltime = $totaltime + $delta;

                    if ($statsscope >= USE_STATS_COURSE_SCOPE) {
                        if (!array_key_exists($alog->course, $totaltimecourse)) {
                            $totaltimecourse[$alog->course] = 0;
                        } else {
                            $totaltimecourse[$alog->course] = $totaltimecourse[$alog->course] + $delta;
                        }
                    }

                    if ($statsscope >= USE_STATS_MODULE_SCOPE) {
                        if (!array_key_exists($alog->course, $totaltimemodule)) {
                            $totaltimemodule[$alog->course][$alog->module] = 0;
                        } else if (!array_key_exists($alog->module, $totaltimemodule[$alog->course])) {
                            $totaltimemodule[$alog->course][$alog->module] = 0;
                        } else {
                            $t = $totaltimemodule[$alog->course][$alog->module] + $delta;
                            $totaltimemodule[$alog->course][$alog->module] = $t;
                        }
                    }
                }
                $lasttime = $alog->time;
            }

            $elapsed = floor($totaltime / MINSECS);

            $data .= "\t<USERNAME>{$targetuser->username}</USERNAME>\n";
            $data .= "\t<FIRSTNAME>{$targetuser->firstname}</FIRSTNAME>\n";
            $data .= "\t<LASTNAME>{$course->idnumber}</LASTNAME>\n";
            $data .= "\t<FROM>{$timefrom}</FROM>\n";
            $data .= "\t<ELAPSED>{$elapsed}</ELAPSED>\n";
            $message = "<USER>\n$data\n</USER>";

            if ($statsscope >= USE_STATS_COURSE_SCOPE) {
                $sitedata = '';
                foreach ($totaltimecourse as $courseid => $statvalue) {
                    $courseinfo = $DB->get_record('course', array('id' => $courseid));
                    $elapsed = floor($statvalue / MINSECS);
                    if ($elapsed < 5) {
                        // Cleaning output from unsignificant values.
                        continue;
                    }
                    $coursedata = "\t<NAME>{$courseinfo->fullname}</NAME>\n";
                    $coursedata .= "\t<SHORTNAME>{$courseinfo->shortname}</SHORTNAME>\n";
                    $coursedata .= "\t<IDNUMBER>{$courseinfo->idnumber}</IDNUMBER>\n";
                    $coursedata .= "\t<ELAPSED>{$elapsed}</ELAPSED>\n";
                    if ($statsscope >= USE_STATS_MODULE_SCOPE) {
                        $moddata = '';
                        foreach ($totaltimemodule as $cmid => $statvalue) {
                            $elapsed = floor($statvalue / MINSECS);
                            if ($elapsed < 2) {
                                // Cleaning output from unsignificant values.
                                continue;
                            }
                            $cm = $DB->get_record('course_modules', array('id' => $cmid));
                            if ($cm) {
                                $modulename = $DB->get_field('modules', 'name', array('id' => $cm->module));
                                $modrecname = $DB->get_field($modulename, 'name', array('id' => $cm->instance));
                            } else {
                                $modulename = 'N.C.';
                                $modrecname = 'N.C.';
                            }
                            $data = "\t<NAME>{$modrecname}</NAME>\n";
                            $data .= "\t<TYPE>{$modulename}</TYPE>\n";
                            $data .= "\t<IDNUMBER>{$cm->idnumber}</IDNUMBER>\n";
                            $data .= "\t<ELAPSED>{$elapsed}</ELAPSED>\n";
                            $moddata .= "<MODULE>\n$data\n</MODULE>\n";
                        }
                        $coursedata .= "<MODULES>\n$moddata\n</MODULES>\n";
                    }
                    $sitedata .= "<COURSE>\n{$coursedata}\n</COURSE>\n";
                }
                $message .= "<COURSES>\n{$sitedata}\n</COURSES>\n";
                $extresponse->message = "<USE_STATS>\n{$message}\n</USE_STATS>";
            } else {
                $extresponse->message = "<USE_STATS>\n{$message}\n</USE_STATS>";
            }
        } else {
            $extresponse->message = "<USE_STATS><EMPTYSET /></USE_STATS>";
        }
        $extresponse->status = RPC_SUCCESS;

        if ($jsonresponse) {
            return json_encode($extresponse);
        } else {
            return $extresponse;
        }
    } else {
        // Make remote call.
        $userhostroot = $DB->get_field('mnet_host', 'wwwroot', array('id' => $USER->mnethostid));

        $rpcclient = new mnet_xmlrpc_client();
        $rpcclient->set_method('blocks/use_stats/rpclib.php/use_stats_rpc_get_stats');
        $caller->username = $USER->username;
        $caller->remoteuserhostroot = $userhostroot;
        $caller->remotehostroot = $CFG->wwwroot;
        $rpcclient->add_param($caller, 'struct'); // Caller user.
        $rpcclient->add_param($targetuser, 'string');
        $rpcclient->add_param($wherewwwroot, 'string');
        $rpcclient->add_param($statsscope, 'string');
        $rpcclient->add_param($timefrom, 'int');

        $mnethost = new mnet_peer();
        $mnethost->set_wwwroot($whereroot);
        if (!$rpcclient->send($mnethost)) {
            $extresponse->status = RPC_FAILURE;
            $extresponse->errors[] = 'REMOTE : '.implode("<br/>\n", $rpcclient->errors);
            $extresponse->errors[] = json_encode($rpcclient);
            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        }

        $response = json_decode($rpcclient->response);

        if ($response->status == 100) {
            $extresponse->message = "Remote Test Point : ".$response->teststatus;
            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        }
        if ($response->status == 200) {
            $extresponse->message = $response->message;
            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        } else {
            $extresponse->status = RPC_FAILURE;
            $extresponse->errors[] = 'Remote application error : ';
            $extresponse->errors[] = $response->errors;
            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        }
    }
}

function use_stats_rpc_get_stats_wrapped($wrap) {
    return use_stats_rpc_get_stats(@$wrap['callinguser'], @$wrap['targetuser'], @$wrap['whereroot'], @$wrap['statsscope'],
                                   @$wrap['timefrom'], @$wrap['json_response']);
}

/**
 * get a complete report of user scoring for a single user.
 *
 * @param array $callinguser The calling user descriptor
 * @param string $targetuser Who stats are required for
 * @param string $whereroot Where the user comes from
 * @param string $scorescope The grading scope
 * @param string $courseidfield The course identifier field as 'id', 'idnumber' or 'shortname'
 * @param string $courseidentifier The course effective identifier
 * @param string $jsonresponse If true expects a jsonified scalar response
 * @return a response object
 */
function use_stats_rpc_get_scores($callinguser, $targetuser, $whereroot, $scorescope = 'notes/global', $courseidfield,
                                  $courseidentifier, $jsonresponse = true) {
    global $CFG, $USER, $DB;

    $extresponse = new stdclass;
    $extresponse->status = RPC_SUCCESS;
    $extresponse->errors[] = array();

    // Invoke local user and check his rights.

    $capabilities = array('block/use_stats:seesitedetails', 'block/use_stats:seecoursedetails');
    if ($authresponse = use_stats_invoke_local_user((array)$callinguser, $capabilities)) {
        if ($jsonresponse) {
            return $authresponse;
        } else {
            return json_decode($authresponse);
        }
    }

    if (empty($whereroot) || $whereroot == $CFG->wwwroot) {
        // Getting remote_course definition.
        switch ($courseidfield) {
            case 'id':
                $course = $DB->get_record('course', array('id' => $courseidentifier));
                break;

            case 'shortname':
                $course = $DB->get_record('course', array('shortname' => $courseidentifier));
                break;

            case 'idnumber':
                $course = $DB->get_record('course', array('idnumber' => $courseidentifier));
                break;
        }

        if (!$course) {
            $extresponse->status = RPC_FAILURE_RECORD;
            $extresponse->errors[] = 'Unkown course.';

            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        }

        if (!$targetuser = $DB->get_record('user', array('username' => $targetuser))) {
            $extresponse->status = RPC_FAILURE_RECORD;
            $extresponse->errors[] = 'Target user does not exist.';
            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        }

        $data .= "\t<USERNAME>{$targetuser->username}</USERNAME>\n";
        $data .= "\t<FIRSTNAME>{$targetuser->firstname}</FIRSTNAME>\n";
        $data .= "\t<LASTNAME>{$targetuser->idnumber}</LASTNAME>\n";

        if ($statsscope == 'notes/global') {
            $gradeitem = $DB->get_record('grade_items', array('itemtype' => 'course', 'courseid' => $course->id));
            $grade = $DB->get_record('grade_grades', array('itemid' => $gradeitem->id));
            $message = "<USER>\n$data\n</USER>";
            $message .= "<SCORE>$grade->rawgrade</SCORE>";
        } else {
            $message = "<ERROR>Not implemented</ERROR>";
            $extresponse->message = "<USER_SCORES>\n{$message}\n</USER_SCORES>";
        }

        $extresponse->status = RPC_SUCCESS;

        if ($jsonresponse) {
            return json_encode($extresponse);
        } else {
            return $extresponse;
        }
    } else {
        // Make remote call.
        $userhostroot = $DB->get_field('mnet_host', 'wwwroot', array('id' => $USER->mnethostid));

        $rpcclient = new mnet_xmlrpc_client();
        $rpcclient->set_method('blocks/use_stats/rpclib.php/use_stats_rpc_get_scores');
        $caller->username = $USER->username;
        $caller->remoteuserhostroot = $userhostroot;
        $caller->remotehostroot = $CFG->wwwroot;
        $rpcclient->add_param($caller, 'struct'); // Caller user.
        $rpcclient->add_param($targetuser, 'string');
        $rpcclient->add_param($whereroot, 'string');
        $rpcclient->add_param($statsscope, 'string');
        $rpcclient->add_param($courseidfield, 'string');
        $rpcclient->add_param($courseidentifier, 'string');

        $mnethost = new mnet_peer();
        $mnethost->set_wwwroot($whereroot);
        if (!$rpcclient->send($mnethost)) {
            $extresponse->status = RPC_FAILURE;
            $extresponse->errors[] = 'REMOTE : '.implode("<br/>\n", $rpcclient->errors);
            $extresponse->errors[] = json_encode($rpcclient);
            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        }

        $response = json_decode($rpcclient->response);

        if ($response->status == 200) {
            $extresponse->message = $response->message;
            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        } else {
            $extresponse->status = RPC_FAILURE;
            $extresponse->errors[] = 'Remote application error : ';
            $extresponse->errors[] = $response->errors;
            if ($jsonresponse) {
                return json_encode($extresponse);
            } else {
                return $extresponse;
            }
        }
    }
}

function use_stats_rpc_get_scores_wrapped($wrap) {
    return use_stats_rpc_get_scores(@$wrap['callinguser'], @$wrap['targetuser'], @$wrap['whereroot'], @$wrap['scorescope'],
                                    @$wrap['courseidfield'], @$wrap['courseidentifier'], @$wrap['json_response']);
}
