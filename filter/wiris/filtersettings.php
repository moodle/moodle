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
 * MathType filter settings.
 *
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    global $CFG;
    global $wirisconfigurationclass;

    require_once("$CFG->dirroot/filter/wiris/lib.php");
    // Automatic class loading not avaliable for Moodle 2.4 and 2.5.
    wrs_loadclasses();
    $wirisplugin = new filter_wiris_pluginwrapper();

    $editorplugininstalled = filter_wiris_pluginwrapper::get_wiris_plugin();
    if (!empty($editorplugininstalled)) {
        // Editor checkbox.
        $output = '';
        $wirisplugin->begin();
        $waseditorenabled = $wirisplugin->was_editor_enabled();
        $waschemeditorenabled = $wirisplugin->was_chem_editor_enabled();
        $conf = $wirisplugin->get_instance()->getConfiguration();
        $wirisplugin->end();

        // Backwards compatibility: some old installations could have the configuration
        // file into the editor plugin inestad of filter. Show a notification to advise
        // users to copy the file from the older location to the new one.
        if ($oldconfile = filter_wiris_pluginwrapper::get_old_configuration()) {
            $warningoutput = get_string('oldconfiguration', 'filter_wiris', $oldconfile);
            if ($CFG->version > 2016052300) {
                \core\notification::warning($warningoutput);
            } else {
                $settings->add(new admin_setting_heading('filter_wiris_old_configuration', '', $warningoutput));
            }
        }

        $settings->add(new admin_setting_heading('filter_wiris/editorsettings',
                                                            get_string('editorsettings', 'filter_wiris'),
                                                            get_string('editorsettings_text', 'filter_wiris')));

        if ($waseditorenabled) {
            $settings->add(new admin_setting_configcheckbox('filter_wiris/editor_enable',
                                                            get_string('wirismatheditor', 'filter_wiris'), '', '1'));
        }

        if ($waschemeditorenabled) {
            $settings->add(new admin_setting_configcheckbox('filter_wiris/chem_editor_enable',
                                                            get_string('wirischemeditor', 'filter_wiris'), '', '1'));
        }

        // Allow MathType be enabled despite of the filter is disabled on a course.
        $settings->add(new admin_setting_configcheckbox('filter_wiris/allow_editorplugin_active_course',
                                                            get_string('alloweditorpluginactive', 'filter_wiris'),
                                                            get_string('alloweditorpluginactive_help', 'filter_wiris'), '0'));

        // Configuration.ini wrapper.


        // Connection properties.
        $settings->add(new admin_setting_heading('filter_wiris/connectionsettings',
                                                            get_string('connectionsettings', 'filter_wiris'),
                                                            get_string('connectionsettings_text', 'filter_wiris')));

        $settings->add(new admin_setting_configtext('filter_wiris/imageservicehost',
                                                            get_string('imageservicehost', 'filter_wiris'),
                                                            get_string('imageservicehost_help', 'filter_wiris'),
                                                            'www.wiris.net',
                                                            PARAM_URL));

        $settings->add(new admin_setting_configtext('filter_wiris/imageservicepath',
                                                            get_string('imageservicepath', 'filter_wiris'),
                                                            get_string('imageservicepath_help', 'filter_wiris'),
                                                            '/demo/editor/render',
                                                            PARAM_LOCALURL));

        $settings->add(new admin_setting_configselect('filter_wiris/imageserviceprotocol',
                                                            get_string('imageserviceprotocol', 'filter_wiris'),
                                                            get_string('imageserviceprotocol_help', 'filter_wiris'),
                                                            'https',
                                                            array('http' => 'http', 'https' => 'https')));

        // Image properties.

        $settings->add(new admin_setting_heading('filter_wiris/imagesettings',
                                                            get_string('imagesettings', 'filter_wiris'),
                                                            get_string('imagesettings_text', 'filter_wiris')));

        $settings->add(new admin_setting_configselect('filter_wiris/imageformat',
                                                            get_string('imageformat', 'filter_wiris'),
                                                            get_string('imageformat_help', 'filter_wiris'),
                                                            'svg',
                                                            array('svg' => 'svg', 'png' => 'png')));

        $settings->add(new admin_setting_configcheckbox('filter_wiris/pluginperformance',
                                                            get_string('pluginperformance', 'filter_wiris'),
                                                            get_string('pluginperformance_help', 'filter_wiris'), '1'));

        // Window properties.

        $settings->add(new admin_setting_heading('filter_wiris/windowsettings',
                                                            get_string('windowsettings', 'filter_wiris'),
                                                            get_string('windowsettings_text', 'filter_wiris')));


        $settings->add(new admin_setting_configcheckbox('filter_wiris/editormodalwindowfullscreen',
                                                            get_string('editormodalwindowfullscreen', 'filter_wiris'),
                                                            get_string('editormodalwindowfullscreen_help', 'filter_wiris'), '0'));

        // Access Provider: If enabled MathType services can not be accessed from non logged users.

        $settings->add(new admin_setting_heading('securitysettings',
                                                            get_string('securitysettings', 'filter_wiris'),
                                                            get_string('securitysettings_text', 'filter_wiris')));

        $settings->add(new admin_setting_configcheckbox('filter_wiris/access_provider_enabled',
                                                            get_string('accessproviderenabled', 'filter_wiris'),
                                                            get_string('accessproviderenabled_help', 'filter_wiris'), '0'));

    } else {
        if (!get_config('filter_wiris', 'filter_standalone')) {

            // Moodle notification API since Moodel 3.1.
            if ($CFG->version >= 2016052300) {
                // Due to Moodle doesn't support circular dependencies between plugins, if any editor plugin is installed
                // a warning message is shown as a notification.
                $message = '';
                $tinyurl = 'https://moodle.org/plugins/tinymce_tiny_mce_wiris';
                $attourl = 'https://moodle.org/plugins/atto_wiris';
                $linkattributes = array('target' => '_blank');
                $attributes = array();
                $message .= html_writer::link($attourl, get_string('wirispluginforatto', 'filter_wiris'), $attributes);
                $message .= get_string('or', 'filter_wiris');
                $message .= html_writer::link($tinyurl, get_string('wirispluginfortinymce', 'filter_wiris'), $attributes);
                $message .= get_string('arenotinstalled', 'filter_wiris');
                $message .= get_string('furtherinformation', 'filter_wiris');

                $imageurl = "https://www.wiris.com/system/files/attachments/1689/WIRIS_manual_icon_17_17.png";
                $image = html_writer::empty_tag('img', array('src' => $imageurl, 'style' => 'vertical-align:-3px;'));
                $troubleshootingurl = 'http://www.wiris.com/plugins/docs/moodle/troubleshooting';
                $imagelink = html_writer::link($troubleshootingurl, $image, $linkattributes);

                $message .= $imagelink;

                // Moodle notification API: https://docs.moodle.org/dev/Notifications.
                \core\notification::warning($message);
            }

        }
        $settings->add(new admin_setting_configcheckbox('filter_wiris/filter_standalone',
                                                        get_string('filter_standalone',
                                                        'filter_wiris'),
                                                        get_string('filter_standalonedesc',
                                                        'filter_wiris'), false, true, false));
    }

    $wirisquizzes = dirname(__FILE__) . '/../../question/type/wq/';
    $quizzesinstalled = file_exists($wirisquizzes);

    if ($quizzesinstalled) {
        $url = $CFG->wwwroot . '/admin/settings.php?section=qtypesettingwq';
        $url = '<a href="' . $url . '">Wiris Quizzes settings</a>';
        $settings->add(new admin_setting_heading('filter_wirisquizzesheading', $url, ''));
    }

}
