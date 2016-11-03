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
 * This page is for configuring which services are published/subscribed on a host
 *
 * @package    core
 * @subpackage mnet
 * @copyright  2010 Penny Leach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/mnet/services_form.php');
$mnet = get_mnet_environment();

require_login();
admin_externalpage_setup('mnetpeers');

$context = context_system::instance();
require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

$hostid = required_param('hostid', PARAM_INT);

$mnet_peer = new mnet_peer();
$mnet_peer->set_id($hostid);

$mform = new mnet_services_form(null, array('peer' => $mnet_peer));
if ($formdata = $mform->get_data()) {
    if (!isset($formdata->publish)) {
        $formdata->publish = array();
    }
    if (!isset($formdata->subscribe)) {
        $formdata->subscribe = array();
    }
    foreach($formdata->exists as $key => $value) {
        $host2service   = $DB->get_record('mnet_host2service', array('hostid'=>$hostid, 'serviceid'=>$key));
        $publish        = (array_key_exists($key, $formdata->publish)) ? $formdata->publish[$key] : 0;
        $subscribe      = (array_key_exists($key, $formdata->subscribe)) ? $formdata->subscribe[$key] : 0;

        if ($publish != 1 && $subscribe != 1) {
            if (false == $host2service) {
                // We don't have or need a record - do nothing!
            } else {
                // We don't need the record - delete it
                $DB->delete_records('mnet_host2service', array('hostid' => $hostid, 'serviceid'=>$key));
            }
        } elseif (false == $host2service && ($publish == 1 || $subscribe == 1)) {
            $host2service = new stdClass();
            $host2service->hostid = $hostid;
            $host2service->serviceid = $key;

            $host2service->publish = $publish;
            $host2service->subscribe = $subscribe;

            $host2service->id = $DB->insert_record('mnet_host2service', $host2service);
        } elseif ($host2service->publish != $publish || $host2service->subscribe != $subscribe) {
            $host2service->publish   = $publish;
            $host2service->subscribe = $subscribe;
            $DB->update_record('mnet_host2service', $host2service);
        }
    }
    $redirecturl = new moodle_url('/admin/mnet/services.php?hostid=' . $hostid);
    redirect($redirecturl, get_string('changessaved'));
}

echo $OUTPUT->header();
$currenttab = 'mnetservices';
require_once($CFG->dirroot . '/' . $CFG->admin . '/mnet/tabs.php');
echo $OUTPUT->box_start();
$s = mnet_get_service_info($mnet_peer, false); // basic data only
$mform->set_data($s);
$mform->display();
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
