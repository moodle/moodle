<?php // $Id$

// "locations" settingpage
$temp = new admin_settingpage('locationsettings', get_string('settings'));
$options = get_list_of_timezones();
$options[99] = get_string('serverlocaltime');
$temp->add(new admin_setting_configselect('timezone', get_string('timezone','admin'), get_string('configtimezone', 'admin'), 99, $options));
$options = get_list_of_timezones();
$options[99] = get_string('timezonenotforced', 'admin');
$temp->add(new admin_setting_configselect('forcetimezone', get_string('forcetimezone', 'admin'), get_string('helpforcetimezone', 'admin'), 99, $options));
$options = get_list_of_countries();
$options[0] = get_string('choose') .'...';
$temp->add(new admin_setting_configselect('country', get_string('country', 'admin'), get_string('configcountry', 'admin'), 0, $options));
$ADMIN->add('location', $temp);


$ADMIN->add('location', new admin_externalpage('timezoneimport', get_string('updatetimezones', 'admin'), "$CFG->wwwroot/$CFG->admin/timezoneimport.php"));

?>
