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
 * ClamAV admin settings.
 *
 * @package    core
 * @subpackage antivirus_clamav
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox('antivirus_clamav/runclamonupload',
            new lang_string('runclamavonupload', 'admin'), new lang_string('configrunclamavonupload', 'admin'), 0));
    $settings->add(new admin_setting_configexecutable('antivirus_clamav/pathtoclam',
            new lang_string('pathtoclam', 'admin'), new lang_string('configpathtoclam', 'admin'), ''));
    $settings->add(new admin_setting_configdirectory('antivirus_clamav/quarantinedir',
            new lang_string('quarantinedir', 'admin'), new lang_string('configquarantinedir', 'admin'), ''));
    $options = array('donothing' => new lang_string('configclamdonothing', 'admin'),'actlikevirus' => new lang_string('configclamactlikevirus', 'admin'));
    $settings->add(new admin_setting_configselect('antivirus_clamav/clamfailureonupload',
            new lang_string('clamfailureonupload', 'admin'), new lang_string('configclamfailureonupload', 'admin'), 'donothing', $options));
}