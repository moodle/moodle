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

/**
 * The hotpot module configuration variables
 *
 * The values defined here are often used as defaults for all module instances.
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// we need hotpot/lib.php for the callback validation functions
require_once($CFG->dirroot.'/mod/hotpot/lib.php');
require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

// admin_setting_xxx classes are defined in "lib/adminlib.php"
// new admin_setting_configcheckbox($name, $visiblename, $description, $defaultsetting);

// show Quizports on MyMoodle page (default=1)
$settings->add(
    new admin_setting_configcheckbox('hotpot_enablemymoodle', get_string('enablemymoodle', 'mod_hotpot'), get_string('configenablemymoodle', 'mod_hotpot'), 1)
);

// enable caching of browser content for each quiz (default=1)
$str = get_string('clearcache', 'mod_hotpot');
$url = new moodle_url('/mod/hotpot/tools/clear_cache.php', array('sesskey' => sesskey()));
$link = html_writer::link($url, $str, array('class' => 'small', 'style'=> 'white-space: nowrap', 'onclick' => "this.target='_blank'"))."\n";
$settings->add(
    new admin_setting_configcheckbox('hotpot_enablecache', get_string('enablecache', 'mod_hotpot'), get_string('configenablecache', 'mod_hotpot').' '.$link, 1)
);

// restrict cron job to certain hours of the day (default=never)
if (class_exists('core_date') && method_exists('core_date', 'get_user_timezone')) {
    // Moodle >= 2.9
    $timezone = core_date::get_user_timezone(99);
    $datetime = new DateTime('now', new DateTimeZone($timezone));
    $timezone = ($datetime->getOffset() - dst_offset_on(time(), $timezone)) / (3600.0);
} else {
    // Moodle <= 2.8
    $timezone = get_user_timezone_offset();
}
if (abs($timezone) > 13) {
    $timezone = 0;
} else if ($timezone>0) {
    $timezone = $timezone - 24;
}
$options = array();
for ($i=0; $i<=23; $i++) {
    $options[($i - $timezone) % 24] = gmdate('H:i', $i * HOURSECS);
}
$settings->add(
    new admin_setting_configmultiselect('hotpot_enablecron', get_string('enablecron', 'mod_hotpot'), get_string('configenablecron', 'mod_hotpot'), array(), $options)
);

// enable embedding of swf media objects inhotpot quizzes (default=1)
$settings->add(
    new admin_setting_configcheckbox('hotpot_enableswf', get_string('enableswf', 'mod_hotpot'), get_string('configenableswf', 'mod_hotpot'), 1)
);

// enable obfuscation of javascript in html files (default=1)
$settings->add(
    new admin_setting_configcheckbox('hotpot_enableobfuscate', get_string('enableobfuscate', 'mod_hotpot'), get_string('configenableobfuscate', 'mod_hotpot'), 1)
);

$options = array(
    hotpot::BODYSTYLES_BACKGROUND => get_string('bodystylesbackground', 'mod_hotpot'),
    hotpot::BODYSTYLES_COLOR      => get_string('bodystylescolor', 'mod_hotpot'),
    hotpot::BODYSTYLES_FONT       => get_string('bodystylesfont', 'mod_hotpot'),
    hotpot::BODYSTYLES_MARGIN     => get_string('bodystylesmargin', 'mod_hotpot')
);
$settings->add(
    new admin_setting_configmultiselect('hotpot_bodystyles', get_string('bodystyles', 'mod_hotpot'), get_string('configbodystyles', 'mod_hotpot'), array(), $options)
);

// hotpot navigation frame height (default=85)
$settings->add(
    new admin_setting_configtext('hotpot_frameheight', get_string('frameheight', 'mod_hotpot'), get_string('configframeheight', 'mod_hotpot'), 85, PARAM_INT, 4)
);

// lock hotpot navigation frame so it is not scrollable (default=0)
$settings->add(
    new admin_setting_configcheckbox('hotpot_lockframe', get_string('lockframe', 'mod_hotpot'), get_string('configlockframe', 'mod_hotpot'), 0)
);

// store raw xml details of HotPot quiz attempts (default=1)
$str = get_string('cleardetails', 'mod_hotpot');
$url = new moodle_url('/mod/hotpot/tools/clear_details.php', array('sesskey' => sesskey()));
$link = html_writer::link($url, $str, array('class' => 'small', 'style'=> 'white-space: nowrap', 'onclick' => "this.target='_blank'"))."\n";
$settings->add(
    new admin_setting_configcheckbox('hotpot_storedetails', get_string('storedetails', 'mod_hotpot'), get_string('configstoredetails', 'mod_hotpot').' '.$link, 0)
);

// maximum duration of a single calendar event (default=5 mins)
$setting = new admin_setting_configtext('hotpot_maxeventlength', get_string('maxeventlength', 'mod_hotpot'), get_string('configmaxeventlength', 'mod_hotpot'), 5, PARAM_INT, 4);
$setting->set_updatedcallback('hotpot_refresh_events');
$settings->add($setting);

unset($i, $link, $options, $setting, $str, $timezone, $datetime, $url);
