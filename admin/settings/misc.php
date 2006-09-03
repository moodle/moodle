<?php // $Id$

// * Miscellaneous settings (still to be sorted)

$ADMIN->add('misc', new admin_externalpage('stickyblocks', get_string('stickyblocks'), "$CFG->wwwroot/$CFG->admin/stickyblocks.php"));

$ADMIN->add('misc', new admin_externalpage('xmldbeditor', get_string('xmldbeditor'), "$CFG->wwwroot/$CFG->admin/xmldb/"));

?>
