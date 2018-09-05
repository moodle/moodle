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
 * Auth email webservice definitions.
 *
 * @package    auth_email
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(

    'auth_email_get_signup_settings' => array(
        'classname'   => 'auth_email_external',
        'methodname'  => 'get_signup_settings',
        'description' => 'Get the signup required settings and profile fields.',
        'type'        => 'read',
        'ajax'          => true,
        'loginrequired' => false,
    ),
    'auth_email_signup_user' => array(
        'classname'   => 'auth_email_external',
        'methodname'  => 'signup_user',
        'description' => 'Adds a new user (pendingto be confirmed) in the site.',
        'type'        => 'write',
        'ajax'          => true,
        'loginrequired' => false,
    ),
);

