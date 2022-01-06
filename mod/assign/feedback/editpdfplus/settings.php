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
 * Settings for assignfeedback PDF plugin
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/settings.php by Davo Smith.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// Enabled by default.
$settings->add(new admin_setting_configcheckbox('assignfeedback_editpdfplus/default',
                new lang_string('default', 'assignfeedback_editpdfplus'),
                new lang_string('default_help', 'assignfeedback_editpdfplus'), 0));

// Base palette (contextid = 1) link.
$basepaletteurl = $CFG->wwwroot . '/mod/assign/feedback/editpdfplus/view_admin.php?id=' . \context_system::instance()->id;
$settings->add(new admin_setting_heading('basepalette', get_string('basepalette', 'assignfeedback_editpdfplus'), get_string('basepalette_desc', 'assignfeedback_editpdfplus', $basepaletteurl)));

// Ghostscript setting.
$systempathslink = new moodle_url('/admin/settings.php', array('section' => 'systempaths'));
$systempathlink = html_writer::link($systempathslink, get_string('systempaths', 'admin'));
$settings->add(new admin_setting_heading('pathtogs', get_string('pathtogs', 'admin'),
                get_string('pathtogspathdesc', 'assignfeedback_editpdfplus', $systempathlink)));

$url = new moodle_url('/mod/assign/feedback/editpdfplus/testgs.php');
$link = html_writer::link($url, get_string('testgs', 'assignfeedback_editpdfplus'));
$settings->add(new admin_setting_heading('testgs', '', $link));

$settings->add(new admin_setting_heading('erase_student_annotation', get_string('erase_student_annotation', 'assignfeedback_editpdfplus'), get_string('erase_student_annotation_desc', 'assignfeedback_editpdfplus')));
$settings->add(new admin_setting_configcheckbox('preserve_student_on_update', get_string('erase_student_on_update', 'assignfeedback_editpdfplus'), get_string('erase_student_on_update_desc', 'assignfeedback_editpdfplus'), 0));

//$settings->add(new admin_setting_configtext("unsetting", "le nom ici", "description", 50, PARAM_INT, 20));
$settings->add(new admin_setting_heading('highlightplus', get_string('typetool_highlightplus', 'assignfeedback_editpdfplus'), get_string('typetool_highlightplus_desc', 'assignfeedback_editpdfplus')));
//$settings->add(new admin_setting_configcheckbox('highlightplus_configurable',get_string('is_not_configurable','assignfeedback_editpdfplus'),get_string('is_not_configurable_desc','assignfeedback_editpdfplus'),0));
$settings->add(new admin_setting_configcolourpicker('highlightplus_color', get_string('adminplugin_color', 'assignfeedback_editpdfplus'), get_string('adminplugin_color_desc', 'assignfeedback_editpdfplus'), '#FFFF40'));
$settings->add(new admin_setting_configcolourpicker('highlightplus_cartridge_color', get_string('adminplugin_cartridge_color', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_color_desc', 'assignfeedback_editpdfplus'), '#FF6F40'));
$settings->add(new admin_setting_configtext('highlightplus_cartridge_x', get_string('adminplugin_cartridge_x', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_x_desc', 'assignfeedback_editpdfplus'), 0));
$settings->add(new admin_setting_configtext('highlightplus_cartridge_y', get_string('adminplugin_cartridge_y', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_y_desc', 'assignfeedback_editpdfplus'), -24));

$settings->add(new admin_setting_heading('stampplus', get_string('typetool_stampplus', 'assignfeedback_editpdfplus'), get_string('typetool_stampplus_desc', 'assignfeedback_editpdfplus')));
//$settings->add(new admin_setting_configcheckbox('stampplus_configurable',get_string('is_not_configurable','assignfeedback_editpdfplus'),get_string('is_not_configurable_desc','assignfeedback_editpdfplus'),0));
$settings->add(new admin_setting_configcolourpicker('stampplus_color', get_string('adminplugin_color', 'assignfeedback_editpdfplus'), get_string('adminplugin_color_desc', 'assignfeedback_editpdfplus'), '#FF0000'));

$settings->add(new admin_setting_heading('frame', get_string('typetool_frame', 'assignfeedback_editpdfplus'), get_string('typetool_frame_desc', 'assignfeedback_editpdfplus')));
//$settings->add(new admin_setting_configcheckbox('frame_configurable',get_string('is_not_configurable','assignfeedback_editpdfplus'),get_string('is_not_configurable_desc','assignfeedback_editpdfplus'),0));
$settings->add(new admin_setting_configtext('frame_cartridge_x', get_string('adminplugin_cartridge_x', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_x_desc', 'assignfeedback_editpdfplus'), 5));
$settings->add(new admin_setting_configtext('frame_cartridge_y', get_string('adminplugin_cartridge_y', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_y_desc', 'assignfeedback_editpdfplus'), -8));

$settings->add(new admin_setting_heading('verticalline', get_string('typetool_verticalline', 'assignfeedback_editpdfplus'), get_string('typetool_verticalline_desc', 'assignfeedback_editpdfplus')));
//$settings->add(new admin_setting_configcheckbox('verticalline_configurable',get_string('is_not_configurable','assignfeedback_editpdfplus'),get_string('is_not_configurable_desc','assignfeedback_editpdfplus'),0));
$settings->add(new admin_setting_configcolourpicker('verticalline_color', get_string('adminplugin_color', 'assignfeedback_editpdfplus'), get_string('adminplugin_color_desc', 'assignfeedback_editpdfplus'), '#0000FF'));
$settings->add(new admin_setting_configcolourpicker('verticalline_cartridge_color', get_string('adminplugin_cartridge_color', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_color_desc', 'assignfeedback_editpdfplus'), '#0000FF'));
$settings->add(new admin_setting_configtext('verticalline_cartridge_x', get_string('adminplugin_cartridge_x', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_x_desc', 'assignfeedback_editpdfplus'), 5));
$settings->add(new admin_setting_configtext('verticalline_cartridge_y', get_string('adminplugin_cartridge_y', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_y_desc', 'assignfeedback_editpdfplus'), 0));

$settings->add(new admin_setting_heading('stampcomment', get_string('typetool_stampcomment', 'assignfeedback_editpdfplus'), get_string('typetool_stampcomment_desc', 'assignfeedback_editpdfplus')));
//$settings->add(new admin_setting_configcheckbox('stampcomment_configurable',get_string('is_not_configurable','assignfeedback_editpdfplus'),get_string('is_not_configurable_desc','assignfeedback_editpdfplus'),0));
//$settings->add(new admin_setting_configcolourpicker('stampcomment_color',get_string('adminplugin_color','assignfeedback_editpdfplus'),get_string('adminplugin_color_desc','assignfeedback_editpdfplus'),'#000099'));
$settings->add(new admin_setting_configcolourpicker('stampcomment_cartridge_color', get_string('adminplugin_cartridge_color', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_color_desc', 'assignfeedback_editpdfplus'), '#000099'));
$settings->add(new admin_setting_configtext('stampcomment_cartridge_x', get_string('adminplugin_cartridge_x', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_x_desc', 'assignfeedback_editpdfplus'), 35));
$settings->add(new admin_setting_configtext('stampcomment_cartridge_y', get_string('adminplugin_cartridge_y', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_y_desc', 'assignfeedback_editpdfplus'), 6));

$settings->add(new admin_setting_heading('commentplus', get_string('typetool_commentplus', 'assignfeedback_editpdfplus'), get_string('typetool_commentplus_desc', 'assignfeedback_editpdfplus')));
//$settings->add(new admin_setting_configcheckbox('commentplus_configurable',get_string('is_not_configurable','assignfeedback_editpdfplus'),get_string('is_not_configurable_desc','assignfeedback_editpdfplus'),0));
$settings->add(new admin_setting_configcolourpicker('commentplus_cartridge_color', get_string('adminplugin_cartridge_color', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_color_desc', 'assignfeedback_editpdfplus'), '#000000'));
$settings->add(new admin_setting_configtext('commentplus_cartridge_x', get_string('adminplugin_cartridge_x', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_x_desc', 'assignfeedback_editpdfplus'), 35));
$settings->add(new admin_setting_configtext('commentplus_cartridge_y', get_string('adminplugin_cartridge_y', 'assignfeedback_editpdfplus'), get_string('adminplugin_cartridge_y_desc', 'assignfeedback_editpdfplus'), 6));

//get_config("ass:::", "unsetting")
