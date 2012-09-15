<?php

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    $optionalsubsystems->add(new admin_setting_configcheckbox('enableoutcomes', new lang_string('enableoutcomes', 'grades'), new lang_string('enableoutcomes_help', 'grades'), 0));
    $optionalsubsystems->add(new admin_setting_configcheckbox('usecomments', new lang_string('enablecomments', 'admin'), new lang_string('configenablecomments', 'admin'), 1));

    $optionalsubsystems->add(new admin_setting_configcheckbox('usetags', new lang_string('usetags','admin'),new lang_string('configusetags', 'admin'), '1'));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enablenotes', new lang_string('enablenotes', 'notes'), new lang_string('configenablenotes', 'notes'), 1));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enableportfolios', new lang_string('enabled', 'portfolio'), new lang_string('enableddesc', 'portfolio'), 0));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enablewebservices', new lang_string('enablewebservices', 'admin'), new lang_string('configenablewebservices', 'admin'), 0));

    $optionalsubsystems->add(new admin_setting_configcheckbox('messaging', new lang_string('messaging', 'admin'), new lang_string('configmessaging','admin'), 1));

    $optionalsubsystems->add(new admin_setting_configcheckbox('messaginghidereadnotifications', new lang_string('messaginghidereadnotifications', 'admin'), new lang_string('configmessaginghidereadnotifications','admin'), 0));

    $options = array(DAYSECS=>new lang_string('secondstotime86400'), WEEKSECS=>new lang_string('secondstotime604800'), 2620800=>new lang_string('nummonths', 'moodle', 1), 15724800=>new lang_string('nummonths', 'moodle', 6),0=>new lang_string('never'));
    $optionalsubsystems->add(new admin_setting_configselect('messagingdeletereadnotificationsdelay', new lang_string('messagingdeletereadnotificationsdelay', 'admin'), new lang_string('configmessagingdeletereadnotificationsdelay', 'admin'), 604800, $options));

    $optionalsubsystems->add(new admin_setting_configcheckbox('messagingallowemailoverride', new lang_string('messagingallowemailoverride', 'admin'), new lang_string('configmessagingallowemailoverride','admin'), 0));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enablestats', new lang_string('enablestats', 'admin'), new lang_string('configenablestats', 'admin'), 0));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enablerssfeeds', new lang_string('enablerssfeeds', 'admin'), new lang_string('configenablerssfeeds', 'admin'), 0));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enableblogs', new lang_string('enableblogs', 'admin'), new lang_string('configenableblogs', 'admin'), 1));

    $options = array('off'=>new lang_string('off', 'mnet'), 'strict'=>new lang_string('on', 'mnet'));
    $optionalsubsystems->add(new admin_setting_configselect('mnet_dispatcher_mode', new lang_string('net', 'mnet'), new lang_string('configmnet', 'mnet'), 'off', $options));

    // Conditional activities: completion and availability
    $optionalsubsystems->add(new admin_setting_configcheckbox('enablecompletion',
        new lang_string('enablecompletion','completion'),
        new lang_string('configenablecompletion','completion'), 0));
    $optionalsubsystems->add($checkbox = new admin_setting_configcheckbox('enableavailability',
        new lang_string('enableavailability','condition'),
        new lang_string('configenableavailability','condition'), 0));
    $checkbox->set_affects_modinfo(true);

    $optionalsubsystems->add(new admin_setting_configcheckbox('enableplagiarism', new lang_string('enableplagiarism','plagiarism'), new lang_string('configenableplagiarism','plagiarism'), 0));
}
