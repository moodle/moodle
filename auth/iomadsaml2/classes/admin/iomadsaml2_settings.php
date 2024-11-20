<?php
// This file is part of IOMAD SAML2 Authentication Plugin for Moodle
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
namespace auth_iomadsaml2\admin;

/**
 * Class Saml2 Settings
 *
 * @package     auth_iomadsaml2
 * @author      Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright   2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class iomadsaml2_settings {
    /** @var int Option dual login no */
    const OPTION_DUAL_LOGIN_NO = 0;
    /** @var int Option dual login yes */
    const OPTION_DUAL_LOGIN_YES = 1;
    /** @var int Option dual passive */
    const OPTION_DUAL_LOGIN_PASSIVE = 2;
    /** @var int Option dual passive */
    const OPTION_DUAL_LOGIN_TEST = 3;
    /** @var int Option multi IDP Display dropdown */
    const OPTION_MULTI_IDP_DISPLAY_DROPDOWN = 0;
    /** @var int Option multi IDP Display buttons */
    const OPTION_MULTI_IDP_DISPLAY_BUTTONS = 1;
    /** @var int Option flagged login message */
    const OPTION_FLAGGED_LOGIN_MESSAGE = 1;
    /** @var int Option flagged login redirect */
    const OPTION_FLAGGED_LOGIN_REDIRECT = 2;
    /** @var int Option flagged login message */
    const OPTION_AUTO_LOGIN_NO = 0;
    /** @var int Option auto login session */
    const OPTION_AUTO_LOGIN_SESSION = 1;
    /** @var int Option flagged login cookie */
    const OPTION_AUTO_LOGIN_COOKIE = 2;
    /** @var int Option tolower exact */
    const OPTION_TOLOWER_EXACT = 0;
    /** @var int Option tolower lower case */
    const OPTION_TOLOWER_LOWER_CASE = 1;
    /** @var int Option tolower case insensitive */
    const OPTION_TOLOWER_CASE_INSENSITIVE = 2;
    /** @var int Option tolower case and accent insensitive */
    const OPTION_TOLOWER_CASE_AND_ACCENT_INSENSITIVE = 3;
}
