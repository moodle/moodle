<?php
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_externalpage('local_coursematrix',
        get_string('coursematrix', 'local_coursematrix'),
        new moodle_url('/local/coursematrix/index.php')
    ));
}
