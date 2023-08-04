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
 * This file is used to call any registered externallib function in Moodle.
 *
 * It will process more than one request and return more than one response if required.
 * It is recommended to add webservice functions and re-use this script instead of
 * writing any new custom ajax scripts.
 *
 * @since Moodle 2.9
 * @package core
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

define('AJAX_SCRIPT', true);
// Services can declare 'readonlysession' in their config located in db/services.php, if not present will default to false.
define('READ_ONLY_SESSION', true);

if (!empty($_GET['nosessionupdate'])) {
    define('NO_SESSION_UPDATE', true);
}

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/qbassign/externallib.php');
define('PREFERRED_RENDERER_TARGET', RENDERER_TARGET_GENERAL);

class qbtest_external_api extends external_api{ 

    public static function setUser($user = null) { 
        global $CFG, $DB;

        if (is_object($user)) {
            $user = clone($user);
        } else if (!$user) {
            $user = new stdClass();
            $user->id = 0;
            $user->mnethostid = $CFG->mnet_localhost_id;
        } else {
            $user = $DB->get_record('user', array('id'=>$user));
        }
        unset($user->description);
        unset($user->access);
        unset($user->preference);

        // Enusre session is empty, as it may contain caches and user specific info.
        \core\session\manager::init_empty_session();

        \core\session\manager::set_user($user);
    }

    public static function call_external_function($function, $args, $ajaxonly=false) { 
        global $PAGE, $COURSE, $CFG, $SITE;
        if(!$USER){
            self::setUser(2);
        }
        require_once($CFG->libdir . "/pagelib.php");

        $externalfunctioninfo = static::external_function_info($function);

        // Eventually this should shift into the various handlers and not be handled via config.
        $readonlysession = $externalfunctioninfo->readonlysession ?? false;
        if (!$readonlysession || empty($CFG->enable_read_only_sessions)) {
            \core\session\manager::restart_with_write_lock($readonlysession);
        }

        $currentpage = $PAGE;
        $currentcourse = $COURSE;
        $response = array();

        try {
            // Taken straight from from setup.php.
            if (!empty($CFG->moodlepageclass)) {
                if (!empty($CFG->moodlepageclassfile)) {
                    require_once($CFG->moodlepageclassfile);
                }
                $classname = $CFG->moodlepageclass;
            } else {
                $classname = 'moodle_page';
            }
            $PAGE = new $classname();
            $COURSE = clone($SITE);

            if ($ajaxonly && !$externalfunctioninfo->allowed_from_ajax) {
                throw new moodle_exception('servicenotavailable', 'webservice');
            }

            // Validate params, this also sorts the params properly, we need the correct order in the next part.
            $callable = array($externalfunctioninfo->classname, 'validate_parameters');
            $params = call_user_func($callable,
                                     $externalfunctioninfo->parameters_desc,
                                     $args);
            $params = array_values($params);

            // Allow any Moodle plugin a chance to override this call. This is a convenient spot to
            // make arbitrary behaviour customisations. The overriding plugin could call the 'real'
            // function first and then modify the results, or it could do a completely separate
            // thing.
            $callbacks = get_plugins_with_function('override_webservice_execution');
            $result = false;
            foreach ($callbacks as $plugintype => $plugins) {
                foreach ($plugins as $plugin => $callback) {
                    $result = $callback($externalfunctioninfo, $params);
                    if ($result !== false) {
                        break 2;
                    }
                }
            }

            // If the function was not overridden, call the real one.
            if ($result === false) {
                $callable = array($externalfunctioninfo->classname, $externalfunctioninfo->methodname);
                $result = call_user_func_array($callable, $params);
            }

            // Validate the return parameters.
            if ($externalfunctioninfo->returns_desc !== null) {
                $callable = array($externalfunctioninfo->classname, 'clean_returnvalue');
                $result = call_user_func($callable, $externalfunctioninfo->returns_desc, $result);
            }

            $response['error'] = false;
            $response['data'] = $result;
        } catch (Throwable $e) {
            $exception = get_exception_info($e);
            unset($exception->a);
            $exception->backtrace = format_backtrace($exception->backtrace, true);
            if (!debugging('', DEBUG_DEVELOPER)) {
                unset($exception->debuginfo);
                unset($exception->backtrace);
            }
            $response['error'] = true;
            $response['exception'] = $exception;
            // Do not process the remaining requests.
        }

        $PAGE = $currentpage;
        $COURSE = $currentcourse;

        return $response;
    }
}


$arguments = '';
$cacherequest = false;
if (defined('ALLOW_GET_PARAMETERS')) { 
    $arguments = optional_param('args', '', PARAM_RAW);
    $cachekey = optional_param('cachekey', '', PARAM_INT);
    if ($cachekey && $cachekey > 0 && $cachekey <= time()) {
        $cacherequest = true;
    }
}

// Either we are not allowing GET parameters or we didn't use GET because
// we did not pass a cache key or the URL was too long.
if (empty($arguments)) { 
    $arguments = file_get_contents('php://input');
}

$requests = json_decode($arguments, true);

if ($requests === null) { 
    $lasterror = json_last_error_msg();
    throw new coding_exception('Invalid json in request: ' . $lasterror);
}
$responses = array();

// Defines the external settings required for Ajax processing.
$settings = external_settings::get_instance();
$settings->set_file('pluginfile.php');
$settings->set_fileurl(true);
$settings->set_filter(true);
$settings->set_raw(false);

$haserror = false;

$assignid = $requests['qbassignmentid'];
$pdata = $requests['plugindata'];
$response = mod_qbassign_external::save_submission($assignid, $pdata);
    

if ($cacherequest && !$haserror) {
    // 90 days only - based on Moodle point release cadence being every 3 months.
    $lifetime = 60 * 60 * 24 * 90;

    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime . ', immutable');
    header('Accept-Ranges: none');
}

echo json_encode($response);
