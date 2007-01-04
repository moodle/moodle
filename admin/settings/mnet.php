<?php // $Id$

// This file defines settingpages and externalpages under the "mnet" category

$ADMIN->add('mnet', new admin_externalpage('net', get_string('settings', 'mnet'), $CFG->wwwroot . '/admin/mnet/index.php', 'moodle/site:config'));
$ADMIN->add('mnet', new admin_externalpage('ssoaccesscontrol', get_string('ssoaccesscontrol', 'mnet'), $CFG->wwwroot . '/admin/mnet/access_control.php', 'moodle/site:config'));

$ADMIN->add('mnet', new admin_externalpage('mnetpeers', get_string('mnetpeers', 'mnet'), $CFG->wwwroot . '/admin/mnet/peers.php', 'moodle/site:config'));
$ADMIN->add('mnet', new admin_externalpage('trustedhosts', get_string('trustedhosts', 'mnet'), $CFG->wwwroot . '/admin/mnet/trustedhosts.php', 'moodle/site:config'));
?>
