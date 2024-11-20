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
 * Special settings for auth_shibboleth WAYF.
 *
 * @package    auth_shibboleth
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Special settings for auth_shibboleth WAYF.
 *
 * @package    auth_shibboleth
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_shibboleth_admin_setting_special_wayf_select extends admin_setting_configselect {

    /**
     * Calls parent::__construct with specific arguments.
     */
    public function __construct() {
        $yesno = array();
        $yesno['off'] = new lang_string('no');
        $yesno['on'] = new lang_string('yes');
        parent::__construct('auth_shibboleth/alt_login',
                new lang_string('auth_shib_integrated_wayf', 'auth_shibboleth'),
                new lang_string('auth_shib_integrated_wayf_description', 'auth_shibboleth'),
                'off',
                $yesno);
    }

    /**
     * We need to overwrite the global "alternate login url" setting if wayf is enabled.
     *
     * @param string $data Form data.
     * @return string Empty when no errors.
     */
    public function write_setting($data) {
        global $CFG;

        // Overwrite alternative login URL if integrated WAYF is used.
        if (isset($data) && $data == 'on') {
            set_config('alt_login', $data, 'auth_shibboleth');
            set_config('alternateloginurl', $CFG->wwwroot.'/auth/shibboleth/login.php');
        } else {
            // Check if integrated WAYF was enabled and is now turned off.
            // If it was and only then, reset the Moodle alternate URL.
            $oldsetting = get_config('auth_shibboleth', 'alt_login');
            if (isset($oldsetting) and $oldsetting == 'on') {
                set_config('alt_login', 'off', 'auth_shibboleth');
                set_config('alternateloginurl', '');
            }
            $data = 'off';
        }
        return parent::write_setting($data);
    }
}
