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
 * Special admin setting for auth_ldap that validates ntlm usernames.
 *
 * @package    auth_ldap
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Special admin setting for auth_ldap that validates ntlm usernames.
 *
 * @package    auth_ldap
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_ldap_admin_setting_special_ntlm_configtext extends admin_setting_configtext {

    /**
     * We need to validate the username format when using NTLM.
     *
     * @param string $data Form data.
     * @return string Empty when no errors.
     */
    public function validate($data) {

        if (get_config('auth_ldap', 'ntlmsso_type') === 'ntlm') {
            $format = trim($data);
            if (!empty($format) && !preg_match('/%username%/i', $format)) {
                return get_string('auth_ntlmsso_missing_username', 'auth_ldap');
            }
        }

        return parent::validate($data);
    }
}
