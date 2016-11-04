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
 * Page to allow the administrator to configure networked hosts, and add new ones
 *
 * @package    core
 * @subpackage mnet
 * @copyright  2007 Donal McMullan
 * @copyright  2007 Martin Langhoff
 * @copyright  2010 Penny Leach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mnet/lib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/mnet/peer_forms.php');

require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context, $USER->id, true, 'nopermissions');

/// Initialize variables.
$hostid = optional_param('hostid', 0, PARAM_INT);
$updra = optional_param('updateregisterall', 0, PARAM_INT);

// first process the register all hosts setting if required
if (!empty($updra)) {
    set_config('mnet_register_allhosts', optional_param('registerallhosts', 0, PARAM_INT));
    redirect(new moodle_url('/admin/mnet/peers.php'), get_string('changessaved'));
}

$adminsection = 'mnetpeers';
if ($hostid && $DB->get_field('mnet_host', 'deleted', array('id' => $hostid)) != 1) {
    $adminsection = 'mnetpeer' . $hostid;
}

$PAGE->set_url('/admin/mnet/peers.php');
admin_externalpage_setup($adminsection);

if (!extension_loaded('openssl')) {
    print_error('requiresopenssl', 'mnet');
}

if (!function_exists('curl_init') ) {
    print_error('nocurl', 'mnet');
}

if (!function_exists('xmlrpc_encode_request')) {
    print_error('xmlrpc-missing', 'mnet');
}

if (!isset($CFG->mnet_dispatcher_mode)) {
    set_config('mnet_dispatcher_mode', 'off');
}

$mnet_peer = new mnet_peer();
$simpleform = new mnet_simple_host_form(); // the one that goes on the bottom of the main page
$reviewform = null; // set up later in different code branches, so mnet_peer can be passed to the constructor

// if the first form has been submitted, bootstrap the peer and load up the review form
if ($formdata = $simpleform->get_data()) {
    // ensure we remove trailing slashes
    $formdata->wwwroot = trim($formdata->wwwroot);
    $formdata->wwwroot = rtrim($formdata->wwwroot, '/');

    // ensure the wwwroot starts with a http or https prefix
    if (strtolower(substr($formdata->wwwroot, 0, 4)) != 'http') {
        $formdata->wwwroot = 'http://'.$formdata->wwwroot;
    }

    $mnet_peer->set_applicationid($formdata->applicationid);
    $application = $DB->get_field('mnet_application', 'name', array('id'=>$formdata->applicationid));
    $mnet_peer->bootstrap($formdata->wwwroot, null, $application);
    // bootstrap the second form straight with the data from the first form
    $reviewform = new mnet_review_host_form(null, array('peer' => $mnet_peer)); // the second step (also the edit host form)
    $formdata->oldpublickey = $mnet_peer->public_key; // set this so we can confirm on form post without having to recreate the mnet_peer object
    $reviewform->set_data($mnet_peer);
    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    $reviewform->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
} else if ($simpleform->is_submitted()) { // validation failed
    $noreviewform = true;
}

// editing a host - load up the review form
if (!empty($hostid)) {
    // TODO print a nice little heading
    $mnet_peer->set_id($hostid);
    echo $OUTPUT->header();
    $currenttab = 'mnetdetails';
    require_once($CFG->dirroot . '/' . $CFG->admin . '/mnet/tabs.php');

    if ($hostid != $CFG->mnet_all_hosts_id) {
        $mnet_peer->currentkey = mnet_get_public_key($mnet_peer->wwwroot, $mnet_peer->application);
        if ($mnet_peer->currentkey == $mnet_peer->public_key) {
            unset($mnet_peer->currentkey);
        } else {
            error_log($mnet_peer->currentkey);
            error_log($mnet_peer->public_key);
            error_log(md5($mnet_peer->currentkey));
            error_log(md5($mnet_peer->public_key));
        }
        $credentials = $mnet_peer->check_credentials($mnet_peer->public_key);
        $reviewform = new mnet_review_host_form(null, array('peer' => $mnet_peer)); // the second step (also the edit host form)
        $mnet_peer->oldpublickey = $mnet_peer->public_key; // set this so we can confirm on form post without having to recreate the mnet_peer object
        $reviewform->set_data((object)$mnet_peer);
        echo $OUTPUT->box_start();
        $reviewform->display();
        echo $OUTPUT->box_end();
    } else {
        // no options for allhosts host - just let the tabs display and print a notification
        echo $OUTPUT->notification(get_string('allhosts_no_options', 'mnet'));
    }
    echo $OUTPUT->footer();
    exit;
}

// either we're in the second step of setting up a new host
// or editing an existing host
// try our best to set up the mnet_peer object to pass to the form definition
// unless validation on simpleform failed, in which case fall through.
if (empty($noreviewform) && $id = optional_param('id', 0, PARAM_INT)) {
    // we're editing an existing one, so set up the tabs
    $currenttab = 'mnetdetails';
    $mnet_peer->set_id($id);
    require_once($CFG->dirroot . '/' . $CFG->admin . '/mnet/tabs.php');
} else if (empty($noreviewform) && ($wwwroot = optional_param('wwwroot', '', PARAM_URL)) && ($applicationid = optional_param('applicationid', 0, PARAM_INT))) {
    $application = $DB->get_field('mnet_application', 'name', array('id'=>$applicationid));
    $mnet_peer->bootstrap($wwwroot, null, $application);
}
$reviewform = new mnet_review_host_form(null, array('peer' => $mnet_peer));
if ($formdata = $reviewform->get_data()) {

    $mnet_peer->set_applicationid($formdata->applicationid);
    $application = $DB->get_field('mnet_application', 'name', array('id'=>$formdata->applicationid));
    $mnet_peer->bootstrap($formdata->wwwroot, null, $application);

    if (!empty($formdata->name) && $formdata->name != $mnet_peer->name) {
        $mnet_peer->set_name($formdata->name);
    }

    if (empty($formdata->theme)) {
        $mnet_peer->force_theme = 0;
        $mnet_peer->theme = null;
    } else {
        $mnet_peer->force_theme = 1;
        $mnet_peer->theme = $formdata->theme;
    }

    $mnet_peer->deleted             = $formdata->deleted;
    $mnet_peer->public_key          = $formdata->public_key;
    $credentials                    = $mnet_peer->check_credentials($mnet_peer->public_key);
    $mnet_peer->public_key_expires  = $credentials['validTo_time_t'];
    $mnet_peer->sslverification     = $formdata->sslverification;

    if ($mnet_peer->commit()) {
        redirect(new moodle_url('/admin/mnet/peers.php', array('hostid' => $mnet_peer->id)), get_string('changessaved'));
    } else {
        print_error('invalidaction', 'error', 'index.php');
    }
} else if ($reviewform->is_submitted()) { // submitted, but errors
    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    $reviewform->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}


// normal flow - just display all hosts with links
echo $OUTPUT->header();
$hosts = mnet_get_hosts(true);

// print the table to display the register all hosts setting
$table = new html_table();
$table->head = array(get_string('registerallhosts', 'mnet'));

$registerrow = '';
$registerstr = '';
$registerurl = new moodle_url('/admin/mnet/peers.php', array('updateregisterall' => 1));
if (!empty($CFG->mnet_register_allhosts)) {
    $registerrow = get_string('registerhostson', 'mnet');
    $registerurl->param('registerallhosts', 0);
    $registerstr = get_string('turnitoff', 'mnet');
} else {
    $registerrow = get_string('registerhostsoff', 'mnet');
    $registerurl->param('registerallhosts', 1);
    $registerstr = get_string('turniton', 'mnet');
}
$registerrow .= $OUTPUT->single_button($registerurl, $registerstr);

// simple table with two rows of a single cell
$table->data = array(
    array(
        get_string('registerallhostsexplain', 'mnet'),
    ),
    array(
        $registerrow
    ),
);
echo html_writer::table($table);

// print the list of all hosts, with little action links and buttons
$table = new html_table();
$table->head = array(
    get_string('site'),
    get_string('system', 'mnet'),
    get_string('last_connect_time', 'mnet'),
    '',
);
$table->wrap = array('nowrap', 'nowrap', 'nowrap', 'nowrap');
$baseurl = new moodle_url('/admin/mnet/peers.php');
$deleted = array();
foreach($hosts as $host) {
    $hosturl = new moodle_url($baseurl, array('hostid' => $host->id));
    if (trim($host->name) === '') {
        // should not happen but...
        $host->name = '???';
    }
    // process all hosts first since it's the easiest
    if ($host->id == $CFG->mnet_all_hosts_id) {
        $table->data[] = array(html_writer::link($hosturl, get_string('allhosts', 'core_mnet')), '*', '', '');
        continue;
    }

    // populate the list of deleted hosts
    if ($host->deleted) {
        $deleted[] = html_writer::link($hosturl, $host->name);
        continue;
    }

    if ($host->last_connect_time == 0) {
        $last_connect = get_string('never');
    } else {
        $last_connect = userdate($host->last_connect_time, get_string('strftimedatetime', 'core_langconfig'));
    }
    $table->data[] = array(
        html_writer::link($hosturl, $host->name),
        html_writer::link($host->wwwroot, $host->wwwroot),
        $last_connect,
        $OUTPUT->single_button(new moodle_url('/admin/mnet/delete.php', array('hostid' => $host->id)), get_string('delete'))
    );
}
echo html_writer::table($table);

if ($deleted) {
    echo $OUTPUT->box(get_string('deletedhosts', 'core_mnet', join(', ', $deleted)), 'deletedhosts');
}

// finally, print the initial form to add a new host
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('addnewhost', 'mnet'), 3);
$simpleform->display();
echo $OUTPUT->box_end();

// done
echo $OUTPUT->footer();
exit;

