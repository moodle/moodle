<?php

// This file defines settingpages and externalpages under the "mnet" category

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

$ADMIN->add('mnet', new admin_externalpage('net', new lang_string('settings', 'mnet'),
                                           "$CFG->wwwroot/$CFG->admin/mnet/index.php",
                                           'moodle/site:config'));



$ADMIN->add('mnet', new admin_externalpage('mnetpeers', new lang_string('managemnetpeers', 'mnet'),
                                           "$CFG->wwwroot/$CFG->admin/mnet/peers.php",
                                           'moodle/site:config'));


$ADMIN->add('mnet', new admin_category('mnetpeercat', new lang_string('mnetpeers', 'mnet')));

if (isset($CFG->mnet_dispatcher_mode) and $CFG->mnet_dispatcher_mode !== 'off') {
    require_once($CFG->dirroot.'/mnet/lib.php');

    $hosts = mnet_get_hosts();
    foreach ($hosts as $host) {
        if ($host->id == $CFG->mnet_all_hosts_id) {
            $host->name = new lang_string('allhosts', 'core_mnet');
        }
        $ADMIN->add('mnetpeercat',
            new admin_externalpage(
                'mnetpeer' . $host->id,
                $host->name,
                $CFG->wwwroot . '/'.$CFG->admin.'/mnet/peers.php?step=update&hostid=' . $host->id,
                'moodle/site:config'
            )
        );
    }
}

$ADMIN->add('mnet', new admin_externalpage('ssoaccesscontrol', new lang_string('ssoaccesscontrol', 'mnet'),
                                           "$CFG->wwwroot/$CFG->admin/mnet/access_control.php",
                                           'moodle/site:config'));
$ADMIN->add('mnet', new admin_externalpage('mnetenrol', new lang_string('clientname', 'mnetservice_enrol'),
                                           "$CFG->wwwroot/mnet/service/enrol/index.php",
                                           'moodle/site:config'));
$ADMIN->add('mnet', new admin_externalpage('trustedhosts', new lang_string('trustedhosts', 'mnet'),
                                           "$CFG->wwwroot/$CFG->admin/mnet/trustedhosts.php",
                                           'moodle/site:config'));

if (isset($CFG->mnet_dispatcher_mode) and $CFG->mnet_dispatcher_mode !== 'off') {
    $profilefields = new admin_settingpage('mnetprofilefields', new lang_string('profilefields', 'mnet'),
                                               'moodle/site:config');
    $ADMIN->add('mnet', $profilefields);

    $fields = mnet_profile_field_options();
    $forced = implode(', ', $fields['forced']);

    $profilefields->add(new admin_setting_configmultiselect('mnetprofileexportfields', new lang_string('profileexportfields', 'mnet'), new lang_string('profilefielddesc', 'mnet', $forced), $fields['legacy'], $fields['optional']));
    $profilefields->add(new admin_setting_configmultiselect('mnetprofileimportfields', new lang_string('profileimportfields', 'mnet'), new lang_string('profilefielddesc', 'mnet', $forced), $fields['legacy'], $fields['optional']));
}


} // end of speedup
