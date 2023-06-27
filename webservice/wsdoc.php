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
 * Web services auto-generated documentation
 *
 * @package    core_webservice
 * @copyright  2009 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require($CFG->dirroot . '/webservice/lib.php');

require_login();
require_sesskey();

$usercontext = context_user::instance($USER->id);
$tokenid = required_param('id', PARAM_INT);

// PAGE settings
$PAGE->set_context($usercontext);
$PAGE->set_url('/user/wsdoc.php');
$PAGE->set_title(get_string('wsdocumentation', 'webservice'));
$PAGE->set_heading(get_string('wsdocumentation', 'webservice'));
$PAGE->set_pagelayout('standard');

// nav bar
$PAGE->navbar->ignore_active(true);
$PAGE->navbar->add(get_string('preferences'), new moodle_url('/user/preferences.php'));
$PAGE->navbar->add(get_string('useraccount'));
$PAGE->navbar->add(get_string('securitykeys', 'webservice'),
        new moodle_url('/user/managetoken.php',
                array('id' => $tokenid, 'sesskey' => sesskey())));
$PAGE->navbar->add(get_string('wsdocumentation', 'webservice'));

// check web service are enabled
if (empty($CFG->enablewsdocumentation)) {
    echo get_string('wsdocumentationdisable', 'webservice');
    die;
}

// check that the current user is the token user
$webservice = new webservice();
$token = $webservice->get_token_by_id($tokenid);
if (empty($token) or empty($token->userid) or empty($USER->id)
        or ($token->userid != $USER->id)) {
    throw new moodle_exception('docaccessrefused', 'webservice');
}

// get the list of all functions related to the token
$functions = $webservice->get_external_functions(array($token->externalserviceid));

// get all the function descriptions
$functiondescs = array();
foreach ($functions as $function) {
    $functiondescs[$function->name] = external_api::external_function_info($function);
}

// get activated protocol
$activatedprotocol = array();
$activatedprotocol['rest'] = webservice_protocol_is_enabled('rest');
$activatedprotocol['xmlrpc'] = webservice_protocol_is_enabled('xmlrpc');

// Check if we are in printable mode
$printableformat = optional_param('print', false, PARAM_BOOL);

// OUTPUT
echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('core', 'webservice');
echo $renderer->documentation_html($functiondescs,
        $printableformat, $activatedprotocol, array('id' => $tokenid));

// trigger browser print operation
if (!empty($printableformat)) {
    $PAGE->requires->js_function_call('window.print', array());
}

echo $OUTPUT->footer();
