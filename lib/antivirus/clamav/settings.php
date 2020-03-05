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
    require_once(__DIR__ . '/adminlib.php');
    require_once(__DIR__ . '/classes/scanner.php');

    // Running method.
    $runningmethodchoice = array(
        'commandline' => get_string('runningmethodcommandline', 'antivirus_clamav'),
        'unixsocket' => get_string('runningmethodunixsocket', 'antivirus_clamav'),
        'tcpsocket' => get_string('runningmethodtcpsocket', 'antivirus_clamav'),
    );
    $settings->add(new antivirus_clamav_runningmethod_setting('antivirus_clamav/runningmethod',
            get_string('runningmethod', 'antivirus_clamav'),
            get_string('runningmethoddesc', 'antivirus_clamav'),
            'commandline', $runningmethodchoice));

    // Path to ClamAV scanning utility (used in command line running method).
    $settings->add(new admin_setting_configexecutable('antivirus_clamav/pathtoclam',
            new lang_string('pathtoclam', 'antivirus_clamav'), new lang_string('pathtoclamdesc', 'antivirus_clamav'), ''));

    // Path to ClamAV unix socket (used in unix socket running method).
    $settings->add(new antivirus_clamav_pathtounixsocket_setting('antivirus_clamav/pathtounixsocket',
            new lang_string('pathtounixsocket', 'antivirus_clamav'),
            new lang_string('pathtounixsocketdesc', 'antivirus_clamav'), '', PARAM_PATH));

    // Hostname to reach ClamAV tcp socket (used in tcp socket running method).
    $settings->add(new antivirus_clamav_tcpsockethost_setting('antivirus_clamav/tcpsockethost',
            new lang_string('tcpsockethost', 'antivirus_clamav'),
            new lang_string('tcpsockethostdesc', 'antivirus_clamav'), '', PARAM_HOST));

    // Port to reach ClamAV tcp socket (used in tcp socket running method).
    $settings->add(new admin_setting_configtext('antivirus_clamav/tcpsocketport',
            new lang_string('tcpsocketport', 'antivirus_clamav'),
            new lang_string('tcpsocketportdesc', 'antivirus_clamav'), 3310, PARAM_INT));

    // How to act on ClamAV failure.
    $options = array(
        'donothing' => new lang_string('configclamdonothing', 'antivirus_clamav'),
        'actlikevirus' => new lang_string('configclamactlikevirus', 'antivirus_clamav'),
        'tryagain' => new lang_string('configclamtryagain', 'antivirus_clamav')
    );
    $settings->add(new admin_setting_configselect('antivirus_clamav/clamfailureonupload',
            new lang_string('clamfailureonupload', 'antivirus_clamav'),
            new lang_string('configclamfailureonupload', 'antivirus_clamav'), 'donothing', $options));

    // Number of attempts clamav will try when there is error during a scanning process.
    $options = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5);
    $settings->add(new admin_setting_configselect('antivirus_clamav/tries',
        new lang_string('tries', 'antivirus_clamav'),
        new lang_string('tries_desc', 'antivirus_clamav'), 1, $options));
}
