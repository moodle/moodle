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
 * @package    antivirus_clamav
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configexecutable('antivirus_clamav/pathtoclam',
            new lang_string('pathtoclam', 'antivirus_clamav'), new lang_string('configpathtoclam', 'antivirus_clamav'), ''));
    $settings->add(new admin_setting_configdirectory('antivirus_clamav/quarantinedir',
            new lang_string('quarantinedir', 'antivirus_clamav'), new lang_string('configquarantinedir', 'antivirus_clamav'), ''));
    $options = array(
        'donothing' => new lang_string('configclamdonothing', 'antivirus_clamav'),
        'actlikevirus' => new lang_string('configclamactlikevirus', 'antivirus_clamav'),
    );
    $settings->add(new admin_setting_configselect('antivirus_clamav/clamfailureonupload',
            new lang_string('clamfailureonupload', 'antivirus_clamav'),
            new lang_string('configclamfailureonupload', 'antivirus_clamav'), 'donothing', $options));
}