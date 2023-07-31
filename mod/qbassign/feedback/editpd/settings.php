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
 * Settings for qbassignfeedback PDF plugin
 *
 * @package   qbassignfeedback_editpd
 * @copyright 2013 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Enabled by default.
$settings->add(new admin_setting_configcheckbox('qbassignfeedback_editpd/default',
                   new lang_string('default', 'qbassignfeedback_editpd'),
                   new lang_string('default_help', 'qbassignfeedback_editpd'), 1));

// Stamp files setting.
$name = 'qbassignfeedback_editpd/stamps';
$title = get_string('stamps','qbassignfeedback_editpd');
$description = get_string('stampsdesc', 'qbassignfeedback_editpd');

$setting = new admin_setting_configstoredfile($name, $title, $description, 'stamps', 0,
    array('maxfiles' => 8, 'accepted_types' => array('image')));
$settings->add($setting);

// Ghostscript setting.
$systempathslink = new moodle_url('/admin/settings.php', array('section' => 'systempaths'));
$systempathlink = html_writer::link($systempathslink, get_string('systempaths', 'admin'));
$settings->add(new admin_setting_heading('pathtogs', get_string('pathtogs', 'admin'),
        get_string('pathtogspathdesc', 'qbassignfeedback_editpd', $systempathlink)));

$url = new moodle_url('/mod/qbassign/feedback/editpd/testgs.php');
$link = html_writer::link($url, get_string('testgs', 'qbassignfeedback_editpd'));
$settings->add(new admin_setting_heading('testgs', '', $link));
