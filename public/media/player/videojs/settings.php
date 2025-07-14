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
 * Settings file for plugin 'media_videojs'
 *
 * @package   media_videojs
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_filetypes('media_videojs/videoextensions',
        new lang_string('videoextensions', 'media_videojs'),
        new lang_string('configvideoextensions', 'media_videojs'),
        'html_video,media_source,.f4v,.flv',
        array('onlytypes' => array('video', 'web_video', 'html_video', 'media_source'))));

    $settings->add(new admin_setting_filetypes('media_videojs/audioextensions',
        new lang_string('audioextensions', 'media_videojs'),
        new lang_string('configaudioextensions', 'media_videojs'),
        'html_audio',
        array('onlytypes' => array('audio', 'web_audio', 'html_audio'))));

    $settings->add(new admin_setting_configcheckbox('media_videojs/youtube',
        new lang_string('youtube', 'media_videojs'),
        new lang_string('configyoutube', 'media_videojs'), 1));

    $settings->add(new admin_setting_configtext('media_videojs/videocssclass',
        new lang_string('videocssclass', 'media_videojs'),
        new lang_string('configvideocssclass', 'media_videojs'), 'video-js'));

    $settings->add(new admin_setting_configtext('media_videojs/audiocssclass',
        new lang_string('audiocssclass', 'media_videojs'),
        new lang_string('configaudiocssclass', 'media_videojs'), 'video-js'));

    $settings->add(new admin_setting_configcheckbox('media_videojs/limitsize',
        new lang_string('limitsize', 'media_videojs'),
        new lang_string('configlimitsize', 'media_videojs'), 1));
}
