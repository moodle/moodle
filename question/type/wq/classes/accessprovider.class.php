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
 * This class implements com_wiris_plugin_api_Accesprovider interface
 * to use Moodle access methods to control access to Wiris Quizzes services.
 *
 * @package    qtype
 * @subpackage wq
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/lib/moodlelib.php');

// Avoid redeclareding or AccessProvider interface.
if (!interface_exists('com_wiris_util_sys_AccessProvider')) {
    require_once($CFG->dirroot . '/question/type/wq/quizzes/lib/com/wiris/util/sys/AccessProvider.interface.php');
}

class accessprovider implements com_wiris_util_sys_AccessProvider {

    /**
     * This method is called before all service. We use it as a wrapper to call
     * Moodle require_login() method. Any Wiris service can't be called without a
     * login.
     */
    // @codingStandardsIgnoreStart
    function requireAccess() {
    // @codingStandardsIgnoreEnd
        // Moodle require_login() method.
        require_login();
        // Not logged in: require_login throws and exception or exit so if we reach this point
        // user is logged.
        return true;
    }

    /**
     * Access Provider is enabled if 'access_provider_enabled' setting is enabled (wq/settings.php)
     */
    // @codingStandardsIgnoreStart
    function isEnabled() {
    // @codingStandardsIgnoreEnd
        return get_config('qtype_wq', 'access_provider_enabled');
    }
}
