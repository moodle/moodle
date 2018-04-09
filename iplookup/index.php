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
 * Displays IP address on map.
 *
 * This script is not compatible with IPv6.
 *
 * @package    core_iplookup
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once('lib.php');

require_login(0, false);
if (isguestuser()) {
    // Guest users cannot perform lookups.
    throw new require_login_exception('Guests are not allowed here.');
}

$ip   = optional_param('ip', getremoteaddr(), PARAM_RAW);
$user = optional_param('user', 0, PARAM_INT);

if (isset($CFG->iplookup)) {
    // Clean up of old settings.
    set_config('iplookup', NULL);
}

$PAGE->set_url('/iplookup/index.php', array('id'=>$ip, 'user'=>$user));
$PAGE->set_pagelayout('popup');
$PAGE->set_context(context_system::instance());

$info = array($ip);
$note = array();

if (cleanremoteaddr($ip) === false) {
    print_error('invalidipformat', 'error');
}

if (!ip_is_public($ip)) {
    print_error('iplookupprivate', 'error');
}

$info = iplookup_find_location($ip);

if ($info['error']) {
    // Can not display.
    notice($info['error']);
}

if ($user) {
    if ($user = $DB->get_record('user', array('id'=>$user, 'deleted'=>0))) {
        // note: better not show full names to everybody
        if (has_capability('moodle/user:viewdetails', context_user::instance($user->id))) {
            array_unshift($info['title'], fullname($user));
        }
    }
}
array_unshift($info['title'], $ip);

$title = implode(' - ', $info['title']);
$PAGE->set_title(get_string('iplookup', 'admin').': '.$title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

if (empty($CFG->googlemapkey3)) {
    $imgwidth  = 620;
    $imgheight = 310;
    $dotwidth  = 18;
    $dotheight = 30;

    $dx = round((($info['longitude'] + 180) * ($imgwidth / 360)) - $imgwidth - $dotwidth/2);
    $dy = round((($info['latitude'] + 90) * ($imgheight / 180)));

    echo '<div id="map" style="width:'.($imgwidth+$dotwidth).'px; height:'.$imgheight.'px;">';
    echo '<img src="earth.jpeg" style="width:'.$imgwidth.'px; height:'.$imgheight.'px" alt="" />';
    echo '<img src="marker.gif" style="width:'.$dotwidth.'px; height:'.$dotheight.'px; margin-left:'.$dx.'px; margin-bottom:'.$dy.'px;" alt="" />';
    echo '</div>';
    echo '<div id="note">'.$info['note'].'</div>';

} else {
    if (is_https()) {
        $PAGE->requires->js(new moodle_url('https://maps.googleapis.com/maps/api/js', array('key'=>$CFG->googlemapkey3, 'sensor'=>'false')));
    } else {
        $PAGE->requires->js(new moodle_url('http://maps.googleapis.com/maps/api/js', array('key'=>$CFG->googlemapkey3, 'sensor'=>'false')));
    }
    $module = array('name'=>'core_iplookup', 'fullpath'=>'/iplookup/module.js');
    $PAGE->requires->js_init_call('M.core_iplookup.init3', array($info['latitude'], $info['longitude'], $ip), true, $module);

    echo '<div id="map" style="width: 650px; height: 360px"></div>';
    echo '<div id="note">'.$info['note'].'</div>';
}

echo $OUTPUT->footer();
