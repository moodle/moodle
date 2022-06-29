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
 * Special setting for auth_shibboleth WAYF.
 *
 * @package    auth_shibboleth
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Special setting for auth_shibboleth WAYF.
 *
 * @package    auth_shibboleth
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_shibboleth_admin_setting_special_idp_configtextarea extends admin_setting_configtextarea {

    /**
     * Calls parent::__construct with specific arguments.
     */
    public function __construct() {
        $default = $orgdefault = "urn:mace:organization1:providerID, Example Organization 1
        https://another.idp-id.com/shibboleth, Other Example Organization, /Shibboleth.sso/DS/SWITCHaai
        urn:mace:organization2:providerID, Example Organization 2, /Shibboleth.sso/WAYF/SWITCHaai";

        parent::__construct('auth_shibboleth/organization_selection',
                get_string('auth_shib_idp_list', 'auth_shibboleth'),
                get_string('auth_shib_idp_list_description', 'auth_shibboleth'), $default, PARAM_RAW, '60', '8');
    }

    /**
     * We need to overwrite the global "alternate login url" setting if wayf is enabled.
     *
     * @param string $data Form data.
     * @return string Empty when no errors.
     */
    public function write_setting($data) {
        global $CFG;

        $login = get_config('auth_shibboleth', 'alt_login');
        if (isset($data) && !empty($data) && isset($login) && $login == 'on') {

            // Need to use the get_idp_list() function here.
            require_once($CFG->dirroot.'/auth/shibboleth/auth.php');

            $idplist = get_idp_list($data);
            if (count($idplist) < 1) {
                return false;
            }
            $data = '';
            foreach ($idplist as $idp => $value) {
                $data .= $idp.', '.$value[0];
                if (isset($value[1])) {
                    // Value[1] is optional.
                    $data .= ', '.$value[1] . "\n";
                } else {
                    $data .= "\n";
                }
            }
        }
        return parent::write_setting($data);
    }
}
