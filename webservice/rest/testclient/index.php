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
 * REST web service test client.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
require_once("$CFG->dirroot/webservice/webservice_test_client_form.php");
require_once("$CFG->dirroot/webservice/rest/locallib.php");

$function = optional_param('function', '', PARAM_SAFEDIR);

$PAGE->set_url('webservice/rest/testclient/index.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM)); // TODO: do we need some new capability?

$functions = array('moodle_group_get_groups');
$functions = array_combine($functions, $functions);

if (!isset($functions[$function])) {
    $function = '';
}

if (!$function) {
    $mform = new webservice_test_client_form(null, $functions);
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'webservice_rest'));
    $mform->display();
    echo $OUTPUT->footer();
    die;
}

$class = $function.'_form';
require_once("$CFG->dirroot/webservice/rest/testclient/$class.php");

$mform = new $class();

if ($mform->is_cancelled()) {
    redirect('index.php');

} else if ($data = $mform->get_data()) {
    $serverurl = "$CFG->wwwroot/webservice/rest/simpleserver.php";
    $serverurl .= '?wsusername='.urlencode($data->wsusername);
    $serverurl .= '&wspassword='.urlencode($data->wspassword);
    $serverurl .= '&wsfunction='.urlencode($data->function);
    for ($i=0; $i<10; $i++) {
        if (empty($data->groupids[$i])) {
            continue;
        }
        $serverurl .= "&groupids[$i]=".urlencode($data->groupids[$i]);
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'webservice_rest').': '.$function);

    echo 'URL: '.s($serverurl);
    echo $OUTPUT->box_start();
    echo '<code>';
    $response = download_file_content($serverurl);
    echo str_replace("\n", '<br />', s($response));
    echo '</code>';
    echo $OUTPUT->box_end();
    $mform->display();
    echo $OUTPUT->footer();
    die;

} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'webservice_rest').': '.$function);
    $mform->display();
    echo $OUTPUT->footer();
    die;
}