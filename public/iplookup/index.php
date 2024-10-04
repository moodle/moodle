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

$ip = optional_param('ip', getremoteaddr(), PARAM_RAW);
$user = optional_param('user', 0, PARAM_INT);
$width = optional_param('width', 0, PARAM_INT);
$height = optional_param('height', 0, PARAM_INT);
$ispopup = optional_param('popup', 0, PARAM_INT);

if (isset($CFG->iplookup)) {
    // Clean up of old settings.
    set_config('iplookup', NULL);
}

$urlparams = [
    'id' => $ip,
    'user' => $user,
];

// Params width and height are set, we assume to have a popup.
if ($width > 0 && $height > 0) {
    $urlparams['width'] = $width;
    $urlparams['height'] = $height;
    $ispopup = 1;
} else if ($ispopup === 1) {  // Param popup was set, then we know that we want a popup.
    $urlparams['ispopup'] = 1;
}
// Set the page layout accordingly.
if ($ispopup) {
    $PAGE->set_pagelayout('popup');
} else {
    $PAGE->set_pagelayout('standard');
}

$PAGE->set_url('/iplookup/index.php', $urlparams);
$PAGE->set_context(context_system::instance());

$info = array($ip);
$note = array();

if (cleanremoteaddr($ip) === false) {
    throw new \moodle_exception('invalidipformat', 'error');
}

if (!ip_is_public($ip)) {
    throw new \moodle_exception('iplookupprivate', 'error');
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

$title = $ip;
foreach ($info['title'] as $component) {
    if (!empty(trim($component))) {
        $title .= ' - ' . $component;
    }
}
$PAGE->set_title(get_string('iplookup', 'admin').': '.$title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

// The map dimension is here as big as the popup/page is, so max with and at least 360px height.
if ($ispopup) {
    echo '<h1 class="iplookup h2">' . htmlspecialchars($title, ENT_QUOTES | ENT_HTML401 | ENT_SUBSTITUTE) . '</h1>';
    $mapdim = 'width: '
        . (($width > 0) ? $width . 'px' : '100%')
        . '; height: '
        . (($height > 0) ? $height . 'px;' : '100%; min-height:360px;');
} else {
    $mapdim = 'width:100%; height:100%;min-height:360px';
}

if (empty($CFG->googlemapkey3)) { // No Google API key is set, we use OSM.

    // Have a fixed zoom factor to calculate corners of the map.
    $fkt = 4;
    $bboxleft = $info['longitude'] - $fkt;
    $bboxbottom = $info['latitude'] - $fkt;
    $bboxright = $info['longitude'] + $fkt;
    $bboxtop = $info['latitude'] + $fkt;

    echo '<div id="map" style="' . $mapdim . '">'
        . '<object data="https://www.openstreetmap.org/export/embed.html?bbox='
        . $bboxleft . '%2C' . $bboxbottom . '%2C' . $bboxright . '%2C' . $bboxtop
        . '&layer=mapnik&marker=' . $info['latitude']  . '%2C' . $info['longitude'] . '" style="' . $mapdim . '"></object>'
        . '</div>'
        . '<div id="note">' . $info['note'] . '</div>';


} else { // Google API key is set, then use Google Maps.
    $PAGE->requires->js(new moodle_url(
        'https://maps.googleapis.com/maps/api/js',
        [
            'key' => $CFG->googlemapkey3,
            'sensor' => 'false'
        ]
    ));
    $module = array('name'=>'core_iplookup', 'fullpath'=>'/iplookup/module.js');
    $PAGE->requires->js_init_call('M.core_iplookup.init3', [$info['latitude'], $info['longitude'], $ip], true, $module);

    echo '<div id="map" style="' . $mapdim . '"></div>';
    echo '<div id="note">'.$info['note'].'</div>';
}

echo $OUTPUT->footer();
