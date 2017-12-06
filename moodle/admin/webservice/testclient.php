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
 * Web service test client.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @author    Petr Skoda (skodak)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . "/" . $CFG->admin . "/webservice/testclient_forms.php");

$function = optional_param('function', '', PARAM_PLUGIN);
$protocol = optional_param('protocol', '', PARAM_ALPHA);
$authmethod = optional_param('authmethod', '', PARAM_ALPHA);

$PAGE->set_url('/' . $CFG->admin . '/webservice/testclient.php');
$PAGE->navbar->ignore_active(true);
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('development', 'admin'));
$PAGE->navbar->add(get_string('testclient', 'webservice'),
        new moodle_url('/' . $CFG->admin . '/webservice/testclient.php'));
if (!empty($function)) {
    $PAGE->navbar->add($function);
}

admin_externalpage_setup('testclient');

// list of all available functions for testing
$allfunctions = $DB->get_records('external_functions', array(), 'name ASC');
$functions = array();
foreach ($allfunctions as $f) {
    $finfo = external_api::external_function_info($f);
    if (!empty($finfo->testclientpath) and file_exists($CFG->dirroot.'/'.$finfo->testclientpath)) {
        //some plugins may want to have own test client forms
        include_once($CFG->dirroot.'/'.$finfo->testclientpath);
    }
    $class = $f->name.'_form';
    if (class_exists($class)) {
        $functions[$f->name] = $f->name;
        continue;
    }
}

// whitelisting security
if (!isset($functions[$function])) {
    $function = '';
}

// list all enabled webservices
$available_protocols = core_component::get_plugin_list('webservice');
$active_protocols = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);
$protocols = array();
foreach ($active_protocols as $p) {
    if (empty($available_protocols[$p])) {
        continue;
    }
    include_once($available_protocols[$p].'/locallib.php');
    if (!class_exists('webservice_'.$p.'_test_client')) {
        // test client support not implemented
        continue;
    }
    $protocols[$p] = get_string('pluginname', 'webservice_'.$p);
}
if (!isset($protocols[$protocol])) { // whitelisting security
    $protocol = '';
}

if (!$function or !$protocol) {
    $mform = new webservice_test_client_form(null, array($functions, $protocols));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('testclient', 'webservice'));
    echo $OUTPUT->box_start();
    $url = new moodle_url('/' . $CFG->admin . '/settings.php?section=debugging');
    $atag =html_writer::start_tag('a', array('href' => $url)).get_string('debug', 'admin').html_writer::end_tag('a');
    $descparams = new stdClass();
    $descparams->atag = $atag;
    $descparams->mode = get_string('debugnormal', 'admin');
    echo get_string('testclientdescription', 'webservice', $descparams);
    echo $OUTPUT->box_end();

    $mform->display();
    echo $OUTPUT->footer();
    die;
}

$class = $function.'_form';

$mform = new $class(null, array('authmethod' => $authmethod));
$mform->set_data(array('function'=>$function, 'protocol'=>$protocol));

if ($mform->is_cancelled()) {
    redirect('testclient.php');

} else if ($data = $mform->get_data()) {

    $functioninfo = external_api::external_function_info($function);

    // first load lib of selected protocol
    require_once("$CFG->dirroot/webservice/$protocol/locallib.php");

    $testclientclass = "webservice_{$protocol}_test_client";
    if (!class_exists($testclientclass)) {
        throw new coding_exception('Missing WS test class in protocol '.$protocol);
    }
    $testclient = new $testclientclass();

    $serverurl = "$CFG->wwwroot/webservice/$protocol/";
    if ($authmethod == 'simple') {
        $serverurl .= 'simpleserver.php';
        $serverurl .= '?wsusername='.urlencode($data->wsusername);
        $serverurl .= '&wspassword='.urlencode($data->wspassword);
    } else if ($authmethod == 'token') {
        $serverurl .= 'server.php';
        $serverurl .= '?wstoken='.urlencode($data->token);
    }

    // now get the function parameters
    $params = $mform->get_params();

    // now test the parameters, this also fixes PHP data types
    $params = external_api::validate_parameters($functioninfo->parameters_desc, $params);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'webservice_'.$protocol).': '.$function);

    echo 'URL: '.s($serverurl);
    echo $OUTPUT->box_start();

    try {
        $response = $testclient->simpletest($serverurl, $function, $params);
        echo str_replace("\n", '<br />', s(var_export($response, true)));
    } catch (Exception $ex) {
        //TODO: handle exceptions and faults without exposing of the sensitive information such as debug traces!
        echo str_replace("\n", '<br />', s($ex));
    }

    echo $OUTPUT->box_end();
    $mform->display();
    echo $OUTPUT->footer();
    die;

} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'webservice_'.$protocol).': '.$function);
    $mform->display();
    echo $OUTPUT->footer();
    die;
}
