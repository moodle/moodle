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
 * Strings for component 'filter_mediaplugin', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   filter_mediaplugin
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['fallbackaudio'] = 'Audio link';
$string['fallbackvideo'] = 'Video link';
$string['filtername'] = 'Multimedia plugins';
$string['flashanimation'] = 'Flash animation';
$string['flashanimation_help'] = 'Files with extension *.swf. For security reasons this filter is used only in trusted texts.';
$string['flashvideo'] = 'Flash video';
$string['flashvideo_help'] = 'Files with extension *.flv and *.f4v. Plays video clips using Flowplayer, requires Flash plugin and javascript. Uses HTML 5 video fallback if multiple sources specified.';
$string['html5audio'] = 'HTML 5 audio';
$string['html5audio_help'] = 'Audio files with extension *.ogg, *.aac and others. It is compatible with latest web browsers only, unfortunately there is no format that is supported by all browsers.
Workaround is to specify fallbacks separated with # (ex: http://example.org/audio.aac#http://example.org/audio.aac#http://example.org/audio.mp3#), QuickTime player is used as a fallback for old browsers, fallback can be any audio type.';
$string['html5video'] = 'HTML 5 video';
$string['html5video_help'] = 'Video files with extension *.webm, *.m4v, *.ogv, *.mp4 and others. It is compatible with latest web browsers only, unfortunately there is no format that is supported by all browsers.
Workaround is to specify fallbacks sources separated with # (ex: http://example.org/video.m4v#http://example.org/video.aac#http://example.org/video.ogv#d=640x480), QuickTime player is used as a fallback for old browsers.';
$string['mp3audio'] = 'MP3 audio';
$string['mp3audio_help'] = 'Files with extension *.mp3. Plays audio using Flowplayer, requires Flash plugin.';
$string['legacyquicktime'] = 'QuickTime player';
$string['legacyquicktime_help'] = 'Files with extension *.mov, *.mp4, *.m4a, *.mp4 and *.mpg. Requires QuickTime player or codecs.';
$string['legacyreal'] = 'Real media player';
$string['legacyreal_help'] = 'Files with extension *.rm, *.ra, *.ram, *.rp, *.rv. Requires RealPlayer.';
$string['legacywmp'] = 'Windows media player';
$string['legacywmp_help'] = 'Files with extension *.avi and *.wmv. Fully compatible with Internet Explorer in Windows, may be problematic in other browsers or operating systems.';
$string['legacyheading'] = 'Legacy media players';
$string['legacyheading_help'] = 'Following formats are not recommended for general usage, they are usually used in intranet installation with centrally managed clients.';
$string['sitevimeo'] = 'Vimeo';
$string['sitevimeo_help'] = 'Vimeo video sharing site.';
$string['siteyoutube'] = 'YouTube';
$string['siteyoutube_help'] = 'YouTube video sharing site, video and playlist links supported.';

