<?php

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // "locations" settingpage
    $temp = new admin_settingpage('locationsettings', new lang_string('locationsettings', 'admin'));
    $temp->add(new admin_setting_servertimezone());
    $temp->add(new admin_setting_forcetimezone());
    $temp->add(new admin_settings_country_select('country', new lang_string('country', 'admin'), new lang_string('configcountry', 'admin'), 0));
    $temp->add(new admin_setting_configtext('defaultcity', new lang_string('defaultcity', 'admin'), new lang_string('defaultcity_help', 'admin'), ''));

    $temp->add(new admin_setting_heading('iplookup', new lang_string('iplookup', 'admin'), new lang_string('iplookupinfo', 'admin')));
    $temp->add(new admin_setting_configfile('geoip2file', new lang_string('geoipfile', 'admin'),
        new lang_string('configgeoipfile', 'admin', $CFG->dataroot.'/geoip/'), $CFG->dataroot.'/geoip/GeoLite2-City.mmdb'));
    $temp->add(new admin_setting_configtext('googlemapkey3', new lang_string('googlemapkey3', 'admin'), new lang_string('googlemapkey3_help', 'admin'), '', PARAM_RAW, 60));

    $temp->add(new admin_setting_configtext('allcountrycodes', new lang_string('allcountrycodes', 'admin'), new lang_string('configallcountrycodes', 'admin'), '', '/^(?:\w+(?:,\w+)*)?$/'));

    $ADMIN->add('location', $temp);

} // end of speedup
