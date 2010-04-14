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
 * Displays IP address on map
 *
 * @package    moodlecore
 * @subpackage iplookup
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/geoip/geoipcity.inc');

require_login();

$ip   = optional_param('ip', getremoteaddr(), PARAM_HOST);
$user = optional_param('user', $USER->id, PARAM_INT);

if (isset($CFG->iplookup)) {
    //clean up of old settings
    set_config('iplookup', NULL);
}

$PAGE->set_url('/iplookup/index.php', array('id'=>$ip, 'user'=>$user));
$PAGE->set_pagelayout('popup');

$info = array($ip);
$note = array();

if (!preg_match('/(^\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $ip, $match)) {
    print_error('invalidipformat', 'error');
}

if ($match[1] > 255 or $match[2] > 255 or $match[3] > 255 or $match[4] > 255) {
    print_error('invalidipformat', 'error');
}

if ($match[1] == '127' or $match[1] == '10' or ($match[1] == '172' and $match[2] >= '16' and $match[2] <= '31') or ($match[1] == '192' and $match[2] == '168')) {
    print_error('iplookupprivate', 'error');
}

if ($user) {
    if ($user = $DB->get_record('user', array('id'=>$user, 'deleted'=>0))) {
        $info[] = fullname($user);
    }
}

if (!empty($CFG->geoipfile) and file_exists($CFG->geoipfile)) {
    $gi = geoip_open($CFG->geoipfile, GEOIP_STANDARD);
    $location = geoip_record_by_addr($gi, $ip);
    geoip_close($gi);

    if (empty($location)) {
        print_error('iplookupfailed', 'error', '', $ip);
    }
    if (!empty($location->city)) {
        $info[] = $location->city;
    }

    if (!empty($location->country_code)) {
        $countries = get_string_manager()->get_list_of_countries(true);
        if (isset($countries[$location->country_code])) {
            // prefer our localized country names
            $info[] = $countries[$location->country_code];
        } else {
            $info[] = $location->country_name;
        }
    }
    $longitude = $location->longitude;
    $latitude  = $location->latitude;
    $note[] = get_string('iplookupmaxmindnote', 'admin');

} else {
    $ipdata = download_file_content('http://netgeo.caida.org/perl/netgeo.cgi?target='.$ip);
    if ($ipdata === false) {
        print_error('cannotnetgeo');
    }
    $matches = null;
    if (!preg_match('/LAT:\s*(-?\d+\.\d+)/s', $ipdata, $matches)) {
        print_error('iplookupfailed', 'error', '', $ip);
    }
    $latitude  = (float)$matches[1];
    if (!preg_match('/LONG:\s*(-?\d+\.\d+)/s', $ipdata, $matches)) {
        print_error('iplookupfailed', 'error', '', $ip);
    }
    $longitude = (float)$matches[1];

    if (preg_match('/CITY:\s*([^<]*)/', $ipdata, $matches)) {
        if (!empty($matches[1])) {
            $info[] = s($matches[1]);
        }
    }

    if (preg_match('/COUNTRY:\s*([^<]*)/', $ipdata, $matches)) {
        if (!empty($matches[1])) {
            $countrycode = $matches[1];
            $countries = get_string_manager()->get_list_of_countries(true);
            if (isset($countries[$countrycode])) {
                // prefer our localized country names
                $info[] = $countries[$countrycode];
            } else {
                $info[] = $countrycode;
            }
        }
    }
    $note[] = get_string('iplookupnetgeonote', 'admin');
}



if (empty($CFG->googlemapkey)) {
    $info = implode(' - ', $info);
    $note = implode('<br />', $note);

    $imgwidth  = 620;
    $imgheight = 310;
    $dotwidth  = 18;
    $dotheight = 30;

    $dx = round((($longitude + 180) * ($imgwidth / 360)) - $imgwidth - $dotwidth/2);
    $dy = round((($latitude + 90) * ($imgheight / 180)));

    $PAGE->set_title(get_string('iplookup', 'admin').': '.$info);
    $PAGE->set_heading($info);
    echo $OUTPUT->header();

    echo '<div id="map" style="width:'.($imgwidth+$dotwidth).'px; height:'.$imgheight.'px;">';
    echo '<img src="earth.jpeg" style="width:'.$imgwidth.'px; height:'.$imgheight.'px" alt="" />';
    echo '<img src="marker.gif" style="width:'.$dotwidth.'px; height:'.$dotheight.'px; margin-left:'.$dx.'px; margin-bottom:'.$dy.'px;" alt="" />';
    echo '</div>';
    echo '<div id="note">'.$note.'</div>';

    echo $OUTPUT->footer();

} else {
    $info = implode(' - ', $info);
    $note = implode('<br />', $note);

    $PAGE->requires->js(new moodle_url("http://maps.google.com/maps?file=api&v=2&key=$CFG->googlemapkey"));
    $module = array('name'=>'core_iplookup', 'fullpath'=>'/iplookup/module.js');
    $PAGE->requires->js_init_call('M.core_iplookup.init', array($latitude, $longitude), true, $module);

    $PAGE->set_title(get_string('iplookup', 'admin').': '.$info);
    $PAGE->set_heading($info);
    echo $OUTPUT->header();

    echo '<div id="map" style="width: 650px; height: 360px"></div>';
    echo '<div id="note">'.$note.'</div>';

    echo $OUTPUT->footer();
}

