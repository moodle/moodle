<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Prints an instance of mod_jupyter.
 *
 * @package     mod_jupyter
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require(__DIR__ . '/vendor/autoload.php');

use Firebase\JWT\JWT;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use mod_jupyter\error_handler;
use mod_jupyter\jupyterhub;
use mod_jupyter\gradeservice;
use \moodle_url;

// Moodle specific config.
global $DB, $PAGE, $USER, $OUTPUT;

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$j = optional_param('j', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('jupyter', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $jupyter = $DB->get_record('jupyter', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $jupyter = $DB->get_record('jupyter', array('id' => $j), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $jupyter->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('jupyter', $jupyter->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$PAGE->set_url('/mod/jupyter/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($jupyter->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

// User interface.
echo $OUTPUT->header();

// Starting point.
echo $OUTPUT->render_from_template('mod_jupyter/loading', []);

$user = mb_strtolower($USER->username, "UTF-8");
$jwt = JWT::encode(["name" => $user], get_config('mod_jupyter', 'jupyterhub_jwt_secret'), 'HS256');

if (!$jupyter->notebook_ready && $jupyter->autograded) {
    try {
        $jupyter->notebook_ready = gradeservice::create_assignment(
            $jupyter,
            $modulecontext->id,
            $jwt
        );
    } catch (RequestException $e) {
        $msg = $e->hasResponse() ? "{$e->getResponse()->getBody()->getContents()}" : "{$e->getCode()}: {$e->getMessage()}";
        error_handler::gradeservice_resp_err($msg, $modulecontext);
    } catch (ConnectException $e) {
        error_handler::gradeservice_connect_err("{$e->getCode()}: {$e->getMessage()}", $modulecontext);
    }
}

if ($jupyter->notebook_ready) {
    try {
        $jupyterhuburl = get_config('mod_jupyter', 'jupyterhub_url');
        if (substr($jupyterhuburl, -1) == "/") {
            $jupyterhuburl = substr($jupyterhuburl, 0, -1);
        }

        $notebookpath = jupyterhub::load_notebook(
            $user,
            $modulecontext->id,
            $course->id,
            $jupyter->id,
            $jupyter->autograded
        );

        if ($jupyter->autograded) {
            // Create link to user's gradebook.
            $gradelink = new moodle_url('grade.php', array('id' => $cm->id, 'userid' => $USER->id));
            $gradelink = $gradelink->__toString();

            $PAGE->requires->js_call_amd('mod_jupyter/submit_notebook', 'init', [[
                'user' => $user,
                'courseid' => $course->id,
                'instanceid' => $jupyter->id,
                'filename' => $jupyter->notebook_filename,
                'token' => $jwt,
                'gradelink' => $gradelink
                ]]
            );
        }

        $PAGE->requires->js_call_amd('mod_jupyter/reset_notebook', 'init', [[
            'user' => $user,
            'contextid' => $modulecontext->id,
            'courseid' => $course->id,
            'instanceid' => $jupyter->id,
            'autograded' => $jupyter->autograded
            ]]
        );

        $PAGE->requires->js_call_amd('mod_jupyter/startup', 'init', [[
            'login' => $jupyterhuburl. $notebookpath . "?auth_token=" . $jwt,
            'autograded' => $jupyter->autograded
            ]]);
    } catch (RequestException $e) {
        $msg = $e->hasResponse() ? "{$e->getResponse()->getBody()->getContents()}" : "{$e->getCode()}: {$e->getMessage()}";
        error_handler::jupyter_resp_err($msg, $modulecontext);
    } catch (ConnectException $e) {
        error_handler::jupyter_connect_err("{$e->getCode()}: {$e->getMessage()}", $modulecontext);
    } finally{
        $PAGE->requires->js_call_amd('mod_jupyter/startup', 'cancelStartup', [['isLoading' => false]]);
    }
}

echo $OUTPUT->footer();
