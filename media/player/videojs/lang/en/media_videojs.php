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
 * Strings for plugin 'media_videojs'
 *
 * @package   media_videojs
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['audiocssclass'] = 'CSS class for audio';
$string['audioextensions'] = 'Audio file extensions';
$string['configaudiocssclass'] = 'A CSS class that will be added to the &lt;audio&gt; element.';
$string['configaudioextensions'] = 'A comma-separated list of supported audio file extensions. VideoJS will try to use the browser\'s native video player when available, and fall back to a Flash player for other formats if Flash is supported by the browser and Flash fallback is enabled below.';
$string['configlimitsize'] = 'If enabled, and width and height are not specified, the video will display with default width and height. Otherwise it will stretch to the maximum possible width.';
$string['configrtmp'] = 'If enabled, links that start with rtmp:// will be handled by the plugin, irrespective of whether its extension is enabled in the supported extensions setting. Flash fallback must be enabled for RTMP to work.';
$string['configvideocssclass'] = 'A CSS class that will be added to the &lt;video&gt; element. For example, the CSS class "vjs-big-play-centered" will place the play button in the middle. For details, including how to set a custom skin, see docs.videojs.com.';
$string['configvideoextensions'] = 'A comma-separated list of supported video file extensions. VideoJS will try to use the browser\'s native video player when available, and fall back to a Flash player for other formats if Flash is supported by the browser and Flash fallback is enabled below.';
$string['configyoutube'] = 'Use VideoJS to play YouTube videos. Note that YouTube playlists are not yet supported by VideoJS.';
$string['configuseflash'] = 'Use Flash player if video format is not natively supported by the browser. If enabled, VideoJS will be engaged for any file extension from the above list without browser check. Please note that Flash is not available in mobile browsers and discouraged in many desktop ones.';
$string['limitsize'] = 'Limit size';
$string['pluginname'] = 'VideoJS player';
$string['pluginname_help'] = 'A JavaScript wrapper for video files played by the browser\'s native video player with a Flash player fallback. (Format support depends on the browser.)';
$string['privacy:metadata'] = 'The VideoJS player media plugin does not store any personal data.';
$string['rtmp'] = 'RTMP streams';
$string['videoextensions'] = 'Video file extensions';
$string['useflash'] = 'Use Flash fallback';
$string['videocssclass'] = 'CSS class for video';
$string['youtube'] = 'YouTube videos';
