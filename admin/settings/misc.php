<?php // $Id$

// * Miscellaneous settings (still to be sorted)

$ADMIN->add('misc', new admin_externalpage('xmldbeditor', get_string('xmldbeditor'), "$CFG->wwwroot/$CFG->admin/xmldb/"));

// hidden scripts linked from elsewhere
$ADMIN->add('misc', new admin_externalpage('oacleanup', 'Online Assignment Cleanup', $CFG->wwwroot.'/'.$CFG->admin.'/oacleanup.php', 'moodle/site:config', true));
$ADMIN->add('misc', new admin_externalpage('upgradeforumread', 'Upgrade forum', $CFG->wwwroot.'/'.$CFG->admin.'/upgradeforumread.php', 'moodle/site:config', true));
$ADMIN->add('misc', new admin_externalpage('upgradelogs', 'Upgrade logs', $CFG->wwwroot.'/'.$CFG->admin.'/upgradelogs.php', 'moodle/site:config', true));

?>
