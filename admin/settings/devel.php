<?php
if ($CFG->debug > 7) {

    if (file_exists($CFG->dirroot . '/admin/mysql/frame.php')) {
        $ADMIN->add('devel', new admin_externalpage('database', get_string('managedatabase'), $CFG->wwwroot . '/' . $CFG->admin . '/mysql/frame.php'));
    }
    $ADMIN->add('devel', new admin_externalpage('xmldbeditor', get_string('xmldbeditor'), $CFG->wwwroot . '/' . $CFG->admin . '/xmldb/'));

}
?>
