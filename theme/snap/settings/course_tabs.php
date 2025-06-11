<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die;

$snapsettings = new admin_settingpage('themesnapcoursetabs', get_string('coursetabs', 'theme_snap'));

// Remote courses heading.
$name = 'theme_snap/remotecourses';
$title = new lang_string('remotecourses', 'theme_snap');
$setting = new admin_setting_heading($name, $title, '');
$snapsettings->add($setting);

// Remote courses toggle.
$name = 'theme_snap/remotecoursestoggle';
$title = new lang_string('remotecoursestoggle', 'theme_snap');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Remote courses opt-in.
$name = 'theme_snap/remotecoursesoptin';
$title = new lang_string('remotecoursesoptin', 'theme_snap');
$description = new lang_string('remotecoursesoptin_help', 'theme_snap');
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Remote site setting.
$name = 'theme_snap/remotesite';
$title = new lang_string('remotesite', 'theme_snap');
$description = new lang_string('remotesite_help', 'theme_snap');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, 64);
$snapsettings->add($setting);

// Webservice token setting.
$name = 'theme_snap/wstoken';
$title = new lang_string('wstoken', 'theme_snap');
$description = new lang_string('wstoken_help', 'theme_snap');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, 48);
$snapsettings->add($setting);

// Proxy course setting.
$name = 'theme_snap/localproxy';
$title = new lang_string('localproxy', 'theme_snap');
$description = new lang_string('localproxy_help', 'theme_snap');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT, 8);
$snapsettings->add($setting);

// Cache timeout.
$name = 'theme_snap/cachetimeout';
$title = new lang_string('cachetimeout', 'theme_snap');
$description = new lang_string('cachetimeout_help', 'theme_snap');
$default = '10';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT, 8);
$snapsettings->add($setting);

// Extra tab 1 heading.
$name = 'theme_snap/extratab1head';
$title = new lang_string('extratab1head', 'theme_snap');
$setting = new admin_setting_heading($name, $title, '');
$snapsettings->add($setting);

// Extra tab 1 toggle.
$name = 'theme_snap/extratab1toggle';
$title = new lang_string('extratab1toggle', 'theme_snap');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Extra tab 1 require enrollment.
$name = 'theme_snap/extratab1enrolled';
$title = new lang_string('extratab1enrolled', 'theme_snap');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Extra tab 1 require course enddate and within timeframe.
$name = 'theme_snap/extratab1datelimits';
$title = new lang_string('extratab1datelimits', 'theme_snap');
$description = new lang_string('extratab1datelimits_help', 'theme_snap');
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Extra tab 1 name.
$name = 'theme_snap/extratab1name';
$title = new lang_string('extratab1name', 'theme_snap');
$description = new lang_string('extratab1name_help', 'theme_snap');
$default = 'HRM_';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, 16);
$snapsettings->add($setting);

// Extra tab 1 course field options.
$name = 'theme_snap/extratab1coursefield';
$title = new lang_string('extratab1coursefield', 'theme_snap');
$description = new lang_string('extratab1coursefield_help', 'theme_snap');
$default = '0';
$enabledloginchoices = [
    'fullname' => new lang_string('fullname', 'moodle'),
    'shortname' => new lang_string('shortname', 'moodle'),
    'idnumber' => new lang_string('idnumber', 'moodle')
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices);
$snapsettings->add($setting);

// Extra tab 1 course search options.
$name = 'theme_snap/extratab1searchopts';
$title = new lang_string('extratab1searchopts', 'theme_snap');
$description = new lang_string('extratab1searchopts_help', 'theme_snap');
$default = '0';
$enabledloginchoices = [
    '0' => new lang_string('beginswith', 'theme_snap'),
    '1' => new lang_string('contains', 'theme_snap'),
    '2' => new lang_string('endswith', 'theme_snap'),
    '3' => new lang_string('regex', 'theme_snap')
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices, 40);
$snapsettings->add($setting);

// Extra tab 1 search term.
$name = 'theme_snap/extratab1searchterm';
$title = new lang_string('extratab1searchterm', 'theme_snap');
$description = new lang_string('extratab1searchterm_help', 'theme_snap');
$default = 'HRM_';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, 16);
$snapsettings->add($setting);

// Extra tab 2 heading.
$name = 'theme_snap/extratab2head';
$title = new lang_string('extratab2head', 'theme_snap');
$setting = new admin_setting_heading($name, $title, '');
$snapsettings->add($setting);

// Extra tab 2 toggle.
$name = 'theme_snap/extratab2toggle';
$title = new lang_string('extratab2toggle', 'theme_snap');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Extra tab 2 require enrollment.
$name = 'theme_snap/extratab2enrolled';
$title = new lang_string('extratab2enrolled', 'theme_snap');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Extra tab 2 require course enddate and within timeframe.
$name = 'theme_snap/extratab2datelimits';
$title = new lang_string('extratab2datelimits', 'theme_snap');
$description = new lang_string('extratab2datelimits_help', 'theme_snap');
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Extra tab 2 name.
$name = 'theme_snap/extratab2name';
$title = new lang_string('extratab2name', 'theme_snap');
$description = new lang_string('extratab2name_help', 'theme_snap');
$default = 'Blueprint Courses';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, 16);
$snapsettings->add($setting);

// Extra tab 2 course field options.
$name = 'theme_snap/extratab2coursefield';
$title = new lang_string('extratab2coursefield', 'theme_snap');
$description = new lang_string('extratab2coursefield_help', 'theme_snap');
$default = '0';
$enabledloginchoices = [
    'fullname' => new lang_string('fullname', 'moodle'),
    'shortname' => new lang_string('shortname', 'moodle'),
    'idnumber' => new lang_string('idnumber', 'moodle')
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices);
$snapsettings->add($setting);

// Extra tab 2 course search options.
$name = 'theme_snap/extratab2searchopts';
$title = new lang_string('extratab2searchopts', 'theme_snap');
$description = new lang_string('extratab2searchopts_help', 'theme_snap');
$default = '0';
$enabledloginchoices = [
    '0' => new lang_string('beginswith', 'theme_snap'),
    '1' => new lang_string('contains', 'theme_snap'),
    '2' => new lang_string('endswith', 'theme_snap'),
    '3' => new lang_string('regex', 'theme_snap')
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices, 40);
$snapsettings->add($setting);

// Extra tab 2 prefix.
$name = 'theme_snap/extratab2searchterm';
$title = new lang_string('extratab2searchterm', 'theme_snap');
$description = new lang_string('extratab2searchterm_help', 'theme_snap');
$default = 'Blueprint ';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, 16);
$snapsettings->add($setting);

// Extra tab 3 heading.
$name = 'theme_snap/extratab3head';
$title = new lang_string('extratab3head', 'theme_snap');
$setting = new admin_setting_heading($name, $title, '');
$snapsettings->add($setting);

// Extra tab 3 toggle.
$name = 'theme_snap/extratab3toggle';
$title = new lang_string('extratab3toggle', 'theme_snap');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Extra tab 3 require enrollment.
$name = 'theme_snap/extratab3enrolled';
$title = new lang_string('extratab3enrolled', 'theme_snap');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Extra tab 3 require course enddate and within timeframe.
$name = 'theme_snap/extratab3datelimits';
$title = new lang_string('extratab3datelimits', 'theme_snap');
$description = new lang_string('extratab3datelimits_help', 'theme_snap');
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Extra tab 3 name.
$name = 'theme_snap/extratab3name';
$title = new lang_string('extratab3name', 'theme_snap');
$description = new lang_string('extratab3name_help', 'theme_snap');
$default = 'Blueprint Courses';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, 16);
$snapsettings->add($setting);

// Extra tab 3 course field options.
$name = 'theme_snap/extratab3coursefield';
$title = new lang_string('extratab3coursefield', 'theme_snap');
$description = new lang_string('extratab3coursefield_help', 'theme_snap');
$default = '0';
$enabledloginchoices = [
    'fullname' => new lang_string('fullname', 'moodle'),
    'shortname' => new lang_string('shortname', 'moodle'),
    'idnumber' => new lang_string('idnumber', 'moodle')
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices);
$snapsettings->add($setting);

// Extra tab 3 course search options.
$name = 'theme_snap/extratab3searchopts';
$title = new lang_string('extratab3searchopts', 'theme_snap');
$description = new lang_string('extratab3searchopts_help', 'theme_snap');
$default = '0';
$enabledloginchoices = [
    '0' => new lang_string('beginswith', 'theme_snap'),
    '1' => new lang_string('contains', 'theme_snap'),
    '2' => new lang_string('endswith', 'theme_snap'),
    '3' => new lang_string('regex', 'theme_snap')
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices, 40);
$snapsettings->add($setting);

// Extra tab 3 prefix.
$name = 'theme_snap/extratab3searchterm';
$title = new lang_string('extratab3searchterm', 'theme_snap');
$description = new lang_string('extratab3searchterm_help', 'theme_snap');
$default = 'Blueprint ';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, 16);
$snapsettings->add($setting);

// Extra tab 3 repopulate courses from tab 3 if main course tab is empty.
$name = 'theme_snap/extratab3repop';
$title = new lang_string('extratab3repop', 'theme_snap');
$description = new lang_string('extratab3repop_help', 'theme_snap');
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$snapsettings->add($setting);

// Add all the settings.
$settings->add($snapsettings);
