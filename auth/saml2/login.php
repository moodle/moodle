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
 * Force login page for SAML
 *
 * @package    auth_saml2
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
This page is only used when dual auth is turned on. In this case
the user is redirected to here which forces SAML auth and then
returns to the wantsurl.
*/

// @codingStandardsIgnoreStart
require_once(__DIR__ . '/../../config.php');
// @codingStandardsIgnoreEnd
require('setup.php');

$wantsurl = optional_param('wantsurl', '', PARAM_LOCALURL); // Overrides $SESSION->wantsurl if given.
if ($wantsurl !== '') {
    // This is later used in core_login_get_return_url().
    $SESSION->wantsurl = (new moodle_url($wantsurl))->out(false);
}

// Crap for hash anchor handling.
$saml2auth->saml_login();

