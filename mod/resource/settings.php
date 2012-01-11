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
 * @package    mod
 * @subpackage resource
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
    $settings->add(new admin_setting_configcheckbox('resource/requiremodintro',
        get_string('requiremodintro', 'admin'), get_string('configrequiremodintro', 'admin'), 1));
    $settings->add(new admin_setting_configmultiselect('resource/displayoptions',
        get_string('displayoptions', 'resource'), get_string('configdisplayoptions', 'resource'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('resourcemodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox_with_advanced('resource/printheading',
        get_string('printheading', 'resource'), get_string('printheadingexplain', 'resource'),
        array('value'=>0, 'adv'=>false)));
    $settings->add(new admin_setting_configcheckbox_with_advanced('resource/printintro',
        get_string('printintro', 'resource'), get_string('printintroexplain', 'resource'),
        array('value'=>1, 'adv'=>false)));
    $settings->add(new admin_setting_configselect_with_advanced('resource/display',
        get_string('displayselect', 'resource'), get_string('displayselectexplain', 'resource'),
        array('value'=>RESOURCELIB_DISPLAY_AUTO, 'adv'=>false), $displayoptions));
    $settings->add(new admin_setting_configcheckbox_with_advanced('resource/showsize',
            get_string('showsize', 'resource'), get_string('showsize_desc', 'resource'),
            array('value'=>0, 'adv'=>false)));
    $settings->add(new admin_setting_configcheckbox_with_advanced('resource/showtype',
            get_string('showtype', 'resource'), get_string('showtype_desc', 'resource'),
            array('value'=>0, 'adv'=>false)));
    $settings->add(new admin_setting_configtext_with_advanced('resource/popupwidth',
        get_string('popupwidth', 'resource'), get_string('popupwidthexplain', 'resource'),
        array('value'=>620, 'adv'=>true), PARAM_INT, 7));
    $settings->add(new admin_setting_configtext_with_advanced('resource/popupheight',
        get_string('popupheight', 'resource'), get_string('popupheightexplain', 'resource'),
        array('value'=>450, 'adv'=>true), PARAM_INT, 7));
    $options = array('0' => get_string('none'), '1' => get_string('allfiles'), '2' => get_string('htmlfilesonly'));
    $settings->add(new admin_setting_configselect_with_advanced('resource/filterfiles',
        get_string('filterfiles', 'resource'), get_string('filterfilesexplain', 'resource'),
        array('value'=>0, 'adv'=>true), $options));
}
