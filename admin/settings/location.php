<?php

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // "locations" settingpage
    $temp = new admin_settingpage('locationsettings', get_string('locationsettings', 'admin'));
    $options = get_list_of_timezones();
    $options[99] = get_string('serverlocaltime');
    $temp->add(new admin_setting_configselect('timezone', get_string('timezone','admin'), get_string('configtimezone', 'admin'), 99, $options));
    $options[99] = get_string('timezonenotforced', 'admin');
    $temp->add(new admin_setting_configselect('forcetimezone', get_string('forcetimezone', 'admin'), get_string('helpforcetimezone', 'admin'), 99, $options));
    $temp->add(new admin_settings_country_select('country', get_string('country', 'admin'), get_string('configcountry', 'admin'), 0));
    $temp->add(new admin_setting_configtext('defaultcity', get_string('defaultcity', 'admin'), get_string('defaultcity_help', 'admin'), ''));

    $temp->add(new admin_setting_heading('iplookup', get_string('iplookup', 'admin'), get_string('iplookupinfo', 'admin')));
    $temp->add(new admin_setting_configfile('geoipfile', get_string('geoipfile', 'admin'), get_string('configgeoipfile', 'admin', $CFG->dataroot.'/geoip/'), $CFG->dataroot.'/geoip/GeoLiteCity.dat'));
    $temp->add(new admin_setting_configtext('googlemapkey', get_string('googlemapkey', 'admin'), get_string('configgooglemapkey', 'admin', $CFG->wwwroot), ''));

    $temp->add(new admin_setting_configtext('allcountrycodes', get_string('allcountrycodes', 'admin'), get_string('configallcountrycodes', 'admin'), '', '/^(?:\w+(?:,\w+)*)?$/'));

    $ADMIN->add('location', $temp);
    $ADMIN->add('location', new admin_externalpage('timezoneimport', get_string('updatetimezones', 'admin'), "$CFG->wwwroot/$CFG->admin/timezoneimport.php"));

} // end of speedup
