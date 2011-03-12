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
 *  Mediaplugin filter settings
 *
 * @package    filter
 * @subpackage mediaplugin
 * @copyright  2017 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // External services
    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_youtube', get_string('siteyoutube','filter_mediaplugin'), get_string('siteyoutube_help','filter_mediaplugin'), 1));
    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_vimeo', get_string('sitevimeo','filter_mediaplugin'), get_string('sitevimeo_help','filter_mediaplugin'), 0));

    // these require flash
    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_mp3', get_string('mp3audio','filter_mediaplugin'), get_string('mp3audio_help','filter_mediaplugin'), 1));
    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_flv', get_string('flashvideo','filter_mediaplugin'), get_string('flashvideo_help','filter_mediaplugin'), 1));
    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_swf', get_string('flashanimation','filter_mediaplugin'), get_string('flashanimation_help','filter_mediaplugin'), 1));

    // HTML 5 media
    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_html5audio', get_string('html5audio','filter_mediaplugin'), get_string('html5audio_help','filter_mediaplugin'), 0)); // disabled because mp3 is much better choice
    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_html5video', get_string('html5video','filter_mediaplugin'), get_string('html5video_help','filter_mediaplugin'), 0)); // disabled because flv with html5 fallback works better

    // legacy players
    $settings->add(new admin_setting_heading('legacymediaformats', get_string('legacyheading', 'filter_mediaplugin'), get_string('legacyheading_help', 'filter_mediaplugin')));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_qt', get_string('legacyquicktime','filter_mediaplugin'), get_string('legacyquicktime_help','filter_mediaplugin'), 1));
    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_wmp', get_string('legacywmp','filter_mediaplugin'), get_string('legacywmp_help','filter_mediaplugin'), 1));
    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_rm', get_string('legacyreal','filter_mediaplugin'), get_string('legacyreal_help','filter_mediaplugin'), 1));

}
