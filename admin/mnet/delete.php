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
 * Page to allow the administrator to delete networked hosts, with a confirm message
 *
 * @package    core
 * @subpackage mnet
 * @copyright  2007 Donal McMullan
 * @copyright  2007 Martin Langhoff
 * @copyright  2010 Penny Leach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$step   = optional_param('step', 'verify', PARAM_ALPHA);
$hostid = required_param('hostid', PARAM_INT);



$context = context_system::instance();

$mnet = get_mnet_environment();

$PAGE->set_url('/admin/mnet/delete.php');
admin_externalpage_setup('mnetpeer' . $hostid);

require_sesskey();

$mnet_peer = new mnet_peer();
$mnet_peer->set_id($hostid);

if ('verify' == $step) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('deleteaserver', 'mnet'));
    if ($live_users = $mnet_peer->count_live_sessions() > 0) {
        echo $OUTPUT->notification(get_string('usersareonline', 'mnet', $live_users));
    }
    $yesurl = new moodle_url('/admin/mnet/delete.php', array('hostid' => $mnet_peer->id, 'step' => 'delete'));
    $nourl = new moodle_url('/admin/mnet/peers.php');
    echo $OUTPUT->confirm(get_string('reallydeleteserver', 'mnet')  . ': ' .  $mnet_peer->name, $yesurl, $nourl);
    echo $OUTPUT->footer();
} elseif ('delete' == $step) {
    $mnet_peer->delete();
    redirect(new moodle_url('/admin/mnet/peers.php'), get_string('hostdeleted', 'mnet'), 5);
}
