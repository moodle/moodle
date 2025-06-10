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
 * Webservice client tester for MHAAIRS Gradebook Integration.
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once("$CFG->libdir/adminlib.php");
require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/blocks/mhaairs/admin/testclient_forms.php");

$function = optional_param('function', '', PARAM_PLUGIN);
$protocol = optional_param('protocol', 'rest', PARAM_ALPHA);
$authmethod = optional_param('authmethod', '', PARAM_ALPHA);

$PAGE->set_url('/blocks/mhaairs/admin/testclient.php');

admin_externalpage_setup('blockmhaairs_testclient');

$heading = get_string('testclient', 'webservice');

// Get all functions.
$functions = array();
$params = array('component' => 'block_mhaairs');
$allfunctions = $DB->get_records('external_functions', $params, 'name ASC');
$testclienturl = '/blocks/mhaairs/admin/testclient.php';
foreach ($allfunctions as $f) {
    $class = $f->name.'_form';
    if (class_exists($class)) {
        $functions[$f->name] = $testclienturl. '?function='. $f->name;
    }
}

// White-listing security.
if (!isset($functions[$function])) {
    $function = $selected = '';
} else {
    $selected = $functions[$function];
}

$functionstr = get_string('function', 'webservice');
$functionselect = $OUTPUT->url_select(
    array_flip($functions),
    $selected,
    array('' => $functionstr. '...'),
    'function-selector'
);

if (!$function) {

    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading);

    echo $functionselect;

    echo $OUTPUT->footer();
    die;
}

$PAGE->navbar->add($function);

$formclass = $function.'_form';
$formurlparams = array('function' => $function, 'protocol' => $protocol);
$formurl = new \moodle_url('/blocks/mhaairs/admin/testclient.php', $formurlparams);
$mform = new $formclass($formurl);
$mform->set_data(array('function' => $function, 'protocol' => $protocol));

// If form is cancelled go back to select a service.
if ($mform->is_cancelled()) {
    redirect('testclient.php');
}

// We have a service to display.
echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

echo $functionselect;
echo html_writer::tag('h3', $function);

// Process a request if any.
if ($data = $mform->get_data()) {
    $functioninfo = external_api::external_function_info($function);

    // First load lib of selected protocol.
    require_once("$CFG->dirroot/webservice/$protocol/locallib.php");

    $testclientclass = "webservice_{$protocol}_test_client";
    if (!class_exists($testclientclass)) {
        throw new coding_exception('Missing WS test class in protocol '.$protocol);
    }
    $testclient = new $testclientclass();

    // Server url.
    $server = 'simpleserver.php';
    $requestparams = array();

    if (!empty($data->moodlewsrestformat)) {
        $requestparams['moodlewsrestformat'] = $data->moodlewsrestformat;
    }
    if ($authmethod == 'simple') {
        $requestparams['wsusername'] = urlencode($data->wsusername);
        $requestparams['wspassword'] = urlencode($data->wspassword);
    } else if ($authmethod == 'token') {
        $server = 'server.php';
        $requestparams['wstoken'] = urlencode($data->token);
    }
    $serverurl = new \moodle_url("/webservice/$protocol/$server", $requestparams);

    // Get and test the function parameters.
    $params = $mform->get_params();
    $params = external_api::validate_parameters($functioninfo->parameters_desc, $params);

    // Display the url.
    $urlparams = array_merge($params, array('wsfunction' => $function));
    $fullurl = new \moodle_url($serverurl, $urlparams);
    echo html_writer::tag('h3', 'URL');
    echo $OUTPUT->box_start();
    echo $fullurl->out(false);
    echo $OUTPUT->box_end();

    // Display the result.
    echo html_writer::tag('h3', 'Result');
    echo $OUTPUT->box_start();

    try {
        $response = $testclient->simpletest($serverurl->out(false), $function, $params);
        echo str_replace("\n", '<br />', s(var_export($response, true)));
    } catch (Exception $ex) {
        // TODO: handle exceptions and faults without exposing of the sensitive information such as debug traces!
        echo str_replace("\n", '<br />', s($ex));
    }

    echo $OUTPUT->box_end();
}

$mform->display();
echo $OUTPUT->footer();
