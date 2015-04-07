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
 * Resource module admin settings and defaults
 *
 * @package    mod_resource
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(RESOURCELIB_DISPLAY_AUTO,
                                                           RESOURCELIB_DISPLAY_EMBED,
                                                           RESOURCELIB_DISPLAY_FRAME,
                                                           RESOURCELIB_DISPLAY_DOWNLOAD,
                                                           RESOURCELIB_DISPLAY_OPEN,
                                                           RESOURCELIB_DISPLAY_NEW,
                                                           RESOURCELIB_DISPLAY_POPUP,
                                                          ));
    $defaultdisplayoptions = array(RESOURCELIB_DISPLAY_AUTO,
                                   RESOURCELIB_DISPLAY_EMBED,
                                   RESOURCELIB_DISPLAY_DOWNLOAD,
                                   RESOURCELIB_DISPLAY_OPEN,
                                   RESOURCELIB_DISPLAY_POPUP,
                                  );

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configtext('resource/framesize',
        get_string('framesize', 'resource'), get_string('configframesize', 'resource'), 130, PARAM_INT));
    $settings->add(new admin_setting_configmultiselect('resource/displayoptions',
        get_string('displayoptions', 'resource'), get_string('configdisplayoptions', 'resource'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('resourcemodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox('resource/printintro',
        get_string('printintro', 'resource'), get_string('printintroexplain', 'resource'), 1));
    $settings->add(new admin_setting_configselect('resource/display',
        get_string('displayselect', 'resource'), get_string('displayselectexplain', 'resource'), RESOURCELIB_DISPLAY_AUTO,
        $displayoptions));
    $settings->add(new admin_setting_configcheckbox('resource/showsize',
        get_string('showsize', 'resource'), get_string('showsize_desc', 'resource'), 0));
    $settings->add(new admin_setting_configcheckbox('resource/showtype',
        get_string('showtype', 'resource'), get_string('showtype_desc', 'resource'), 0));
    $settings->add(new admin_setting_configtext('resource/popupwidth',
        get_string('popupwidth', 'resource'), get_string('popupwidthexplain', 'resource'), 620, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('resource/popupheight',
        get_string('popupheight', 'resource'), get_string('popupheightexplain', 'resource'), 450, PARAM_INT, 7));
    $options = array('0' => get_string('none'), '1' => get_string('allfiles'), '2' => get_string('htmlfilesonly'));
    $settings->add(new admin_setting_configselect('resource/filterfiles',
        get_string('filterfiles', 'resource'), get_string('filterfilesexplain', 'resource'), 0, $options));
}
