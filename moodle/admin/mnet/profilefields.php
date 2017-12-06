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
 * Allows the admin to configure a list of profile fields that are sent/recieved
 *
 * @package    core
 * @subpackage mnet
 * @copyright  2010 onwards Penny Leach <penny@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin .'/mnet/profilefields_form.php');
$mnet = get_mnet_environment();

require_login();
$hostid = required_param('hostid', PARAM_INT);
$mnet_peer = new mnet_peer();
$mnet_peer->set_id($hostid);

$context = context_system::instance();

require_capability('moodle/site:config', $context, $USER->id, true, 'nopermissions');
admin_externalpage_setup('mnetpeers');
$form = new mnet_profile_form(null, array('hostid' => $hostid));

if ($data = $form->get_data()) {
    if (!isset($data->importdefault)) {
        $data->importdefault = 0;
    }
    if (!isset($data->exportdefault)) {
        $data->exportdefault = 0;
    }
    if (!isset($data->importfields)) {
        $data->importfields = array();
    }
    if (!isset($data->exportfields)) {
        $data->exportfields = array();
    }
    set_config('host' . $hostid . 'importdefault', $data->importdefault, 'mnet');
    set_config('host' . $hostid . 'importfields', implode(',', $data->importfields), 'mnet');
    set_config('host' . $hostid . 'exportdefault', $data->exportdefault, 'mnet');
    set_config('host' . $hostid . 'exportfields', implode(',', $data->exportfields), 'mnet');

    redirect(new moodle_url('/admin/mnet/peers.php'), get_string('changessaved'));
} elseif ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/mnet/peers.php', array('hostid' => $hostid)));
}

echo $OUTPUT->header();

$currenttab = 'mnetprofilefields';
require_once('tabs.php');

echo $OUTPUT->heading(get_string('peerprofilefielddesc', 'mnet'), 4);

$data = new Stdclass;
$data->importdefault = get_config('mnet', 'host' . $hostid . 'importdefault');
$data->exportdefault = get_config('mnet', 'host' . $hostid . 'exportdefault');
$data->importfields = get_config('mnet', 'host' . $hostid . 'importfields');
$data->exportfields = get_config('mnet', 'host' . $hostid . 'exportfields');

if ($data->importfields === false) {
    $data->importdefault = true;
} else {
    $data->importfields = explode(',', $data->importfields);
}
if ($data->exportfields === false) {
    $data->exportdefault = true;
} else {
    $data->exportfields = explode(',', $data->exportfields);
}

$form->set_data($data);
$form->display();

echo $OUTPUT->footer();
