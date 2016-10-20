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

$string['audiocssclass'] = 'CSS class for audios';
$string['audioextensions'] = 'Audio files extensions';
$string['configaudiocssclass'] = 'CSS class that will be added to &lt;audio&gt; element';
$string['configaudioextensions'] = 'Comma-separated list of supported video file extensions, VideoJS will try to use the browser native video player when available, ' .
    'and fall back to flash player for other formats if flash is supported by the browser and flash playback is enabled here.';
$string['configlimitsize'] = 'If width and height are not specified for the video, display with default width/height. If unchecked the videos without specified dimensions will stretch to maximum possible width';
$string['configvideocssclass'] = 'CSS class that will be added to &lt;video&gt; element. For example class "vjs-big-play-centered" will place the play button in the middle. You can also set the custom skin, refer to <a href="http://docs.videojs.com/">VideoJS documentation</a>';
$string['configvideoextensions'] = 'Comma-separated list of supported video file extensions, VideoJS will try to use the browser native video player when available, ' .
    'and fall back to flash player for other formats if flash is supported by the browser and flash playback is enabled here.';
$string['configyoutube'] = 'Use Video.JS to play YouTube videos. Youtube playlists are not currently supported by Video.JS';
$string['configuseflash'] = 'Use Flash player if video format is not natively supported by the browser. If enabled, VideoJS will be engaged for any '.
    'file extension from the above list without browser check. Please note that Flash is not available in mobile browsers and discouraged in many desktop ones.';
$string['limitsize'] = 'Limit size';
$string['pluginname'] = 'VideoJS player';
$string['pluginname_help'] = 'Javascript wrapper for video files played by browser native video player with fallback to Flash player. (Format support depends on browser.)';
$string['videoextensions'] = 'Video files extensions';
$string['useflash'] = 'Use Flash fallback';
$string['videocssclass'] = 'CSS class for videos';
$string['youtube'] = 'YouTube videos';
