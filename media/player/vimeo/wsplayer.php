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
 * A script to embed vimeo videos via the site (so vimeo privacy restrictions by domain will work in the mobile app).
 *
 * The site is doing a double frame embedding:
 *  - First, the media player replaces the vimeo link with an iframe pointing to vimeo.
 *  - Second, the app replaces the previous iframe link with a link to this file that includes again the iframe to vimeo.
 *  Thanks to these changes, the video is embedded in a page in the site server so the privacy restrictions will work.
 *
 *  Note 1: Vimeo privacy restrictions seems to be based on the Referer HTTP header.
 *  Note 2: This script works even if the plugin is disabled (some users could be using the vimeo embedding code).
 *
 * @package    media_vimeo
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/webservice/lib.php');

global $OUTPUT;

$token = required_param('token', PARAM_ALPHANUM);
$video = required_param('video', PARAM_ALPHANUM);   // Video ids are numeric, but it's more solid to expect things like 00001.
$width = optional_param('width', 0, PARAM_INT);
$height = optional_param('height', 0, PARAM_INT);
$h = optional_param('h', '', PARAM_ALPHANUM); // Security hash for restricted videos.

// Authenticate the user.
$webservicelib = new webservice();
$webservicelib->authenticate_user($token);

$params = ['lang' => current_language()];
if (!empty($h)) {
    $params['h'] = $h;
}

// Add do not track parameter.
if (get_config('media_vimeo', 'donottrack')) {
    $params['dnt'] = 1;
}

$embedurl = new moodle_url("https://player.vimeo.com/video/$video", $params);
$context = ['embedurl' => $embedurl->out(false)]; // Template context.

if (empty($width) && empty($height)) {
    // Use the full page. The video will keep the ratio.
    $context['display'] = "position:absolute; top:0; left:0; width:100%; height:100%;";
} else {
    $context['width'] = $width;
    $context['height'] = $height;
}

// Output the rendered template.
echo $OUTPUT->render_from_template('media_vimeo/appembed', $context);
