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
 *  JW Player media plugin settings.
 *
 * @package    media_jwplayer
 * @copyright  2017 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    require_once(__DIR__ . '/lib.php');
    require_once(__DIR__ . '/adminlib.php');
    require_once(__DIR__ . '/classes/plugin.php');

    // Library hosting methods.
    $settings->add(new admin_setting_heading('hostingconfig',
        get_string('libraryhosting', 'media_jwplayer'), ''));

    // Hosting method.
    $hostingmethodchoice = array(
        'cloud' => get_string('hostingmethodcloud', 'media_jwplayer'),
        'self' => get_string('hostingmethodself', 'media_jwplayer'),
    );
    $settings->add(new media_jwplayer_hostingmethod_setting('media_jwplayer/hostingmethod',
        get_string('hostingmethod', 'media_jwplayer'),
        get_string('hostingmethoddesc', 'media_jwplayer'),
        'cloud', $hostingmethodchoice));

    // Cloud-hosted library URL.
    $settings->add(new admin_setting_configtext('media_jwplayer/libraryurl',
        get_string('libraryurl', 'media_jwplayer'),
        get_string('libraryurldesc', 'media_jwplayer'),
        '', PARAM_URL));

    // License key.
    $settings->add(new media_jwplayer_license_setting('media_jwplayer/licensekey',
        get_string('licensekey', 'media_jwplayer'),
        get_string('licensekeydesc', 'media_jwplayer'),
        '', PARAM_RAW_TRIMMED));

    // General.
    $settings->add(new admin_setting_heading('generalconfig',
        get_string('general', 'media_jwplayer'), ''));

    // Enabled extensions.
    $supportedextensions = media_jwplayer_plugin::list_supported_extensions();
    $enabledextensionsmenu = array_combine($supportedextensions, $supportedextensions);
    $settings->add(new admin_setting_configmultiselect('media_jwplayer/enabledextensions',
        get_string('enabledextensions', 'media_jwplayer'),
        get_string('enabledextensionsdesc', 'media_jwplayer'),
        $supportedextensions, $enabledextensionsmenu));

    // Enabled events to log.
    $supportedevents = media_jwplayer_plugin::list_supported_events();
    $supportedeventsmenu = array_combine($supportedevents, $supportedevents);
    $settings->add(new admin_setting_configmultiselect('media_jwplayer/enabledevents',
        get_string('enabledevents', 'media_jwplayer'),
        get_string('enabledeventsdesc', 'media_jwplayer'),
        ['started', 'completed'], $supportedeventsmenu));

    // Error logging.
    $settings->add(new admin_setting_configcheckbox('media_jwplayer/logerrors',
        get_string('logerrors', 'media_jwplayer'),
        get_string('logerrorsdesc', 'media_jwplayer'),
        1));

    // Appearance related settings.
    $settings->add(new admin_setting_heading('appearanceconfig',
        get_string('appearanceconfig', 'media_jwplayer'), ''));

    // Display mode (fixed or responsive).
    $displaymodechoice = [
        'fixedwidth' => get_string('displayfixedwidth', 'media_jwplayer'),
        'fixed' => get_string('displayfixed', 'media_jwplayer'),
        'responsive' => get_string('displayresponsive', 'media_jwplayer'),
    ];
    $commonsettings = new moodle_url('/admin/settings.php', ['section' => 'managemediaplayers']);
    $settings->add(new admin_setting_configselect('media_jwplayer/displaymode',
            get_string('displaymode', 'media_jwplayer'),
            get_string('displaymodedesc', 'media_jwplayer', $commonsettings->out()),
            'fixedwidth', $displaymodechoice));

    // Aspect ratio.
    $aspectratiochoice = ['16:9', '16:10', '9:16', '4:3', '3:2', '1:1', '2.4:1'];
    $aspectratiochoice = array_combine($aspectratiochoice, $aspectratiochoice);
    $settings->add(new admin_setting_configselect('media_jwplayer/aspectratio',
            get_string('aspectratio', 'media_jwplayer'),
            get_string('aspectratiodesc', 'media_jwplayer'),
            media_jwplayer_plugin::VIDEO_ASPECTRATIO, $aspectratiochoice));

    // Allow empty title.
    $settings->add(new admin_setting_configcheckbox('media_jwplayer/emptytitle',
        get_string('emptytitle', 'media_jwplayer'),
        get_string('emptytitledesc', 'media_jwplayer'),
        0));

    // Download button.
    $settings->add(new admin_setting_configcheckbox('media_jwplayer/downloadbutton',
        get_string('downloadbutton', 'media_jwplayer'),
        get_string('downloadbuttondesc', 'media_jwplayer'),
        0));

    // Playback rate controls.
    $supportedrates = ['0.25', '0.5', '0.75', '1', '1.25', '1.5', '1.75', '2'];
    $supportedratesvalues = array_map(function($param) {
        return $param . 'x';
    }, $supportedrates);

    $settings->add(new admin_setting_configmultiselect('media_jwplayer/playbackrates',
        get_string('playbackrates', 'media_jwplayer'),
        get_string('playbackratesdesc', 'media_jwplayer'),
        ['1'], array_combine($supportedrates, $supportedratesvalues)));

    // Default Poster Image.
    $settings->add(new admin_setting_configstoredfile('media_jwplayer/defaultposter',
        get_string('defaultposter', 'media_jwplayer'),
        get_string('defaultposterdesc', 'media_jwplayer'),
        'defaultposter', 0, ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]));

    // Custom skin name.
    $settings->add(new admin_setting_configtext('media_jwplayer/customskinname',
        get_string('customskinname', 'media_jwplayer'),
        get_string('customskinnamedesc', 'media_jwplayer'),
        '', PARAM_ALPHANUMEXT));

    // Google Analytics settings.
    $settings->add(new admin_setting_heading('googleanalyticsconfig',
            get_string('googleanalyticsconfig', 'media_jwplayer'),
            get_string('googleanalyticsconfigdesc', 'media_jwplayer')));

    $addhtml = new moodle_url('/admin/settings.php', ['section' => 'additionalhtml']);
    $settings->add(new admin_setting_configcheckbox('media_jwplayer/googleanalytics',
            get_string('googleanalytics', 'media_jwplayer'),
            get_string('googleanalyticsdesc', 'media_jwplayer', $addhtml->out()),
            0));

    $settings->add(new admin_setting_configtext('media_jwplayer/galabel',
            get_string('galabel', 'media_jwplayer'),
            get_string('galabeldesc', 'media_jwplayer'),
            ''));
}
