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
 * @package   local_iomad_signup
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['authenticationtypes'] = 'Select authentication types';
$string['authenticationtypes_desc'] = 'These are the authentication types which can be used for automatically assigning a user to a company';
$string['autoenrol'] = 'Auto enrol user';
$string['autoenrol_help'] = 'Selecting this will automaticaly enrol new users onto non licensed or self enroll courses assigned to the company.';
$string['autoenrol_unassigned'] = 'Auto enrol unassigned courses';
$string['autoenrol_unassigned_help'] = 'Selecting this will automaticaly enrol new users onto non licensed or self enroll courses not assigned to any company.';
$string['choosepassword'] = 'Create new user';
$string['company'] = 'Default company users are assigned to';
$string['configcompany'] = 'This is the company that the user will be assigned to once they have completed the sign up process if no other company is defined either through the sign up for or through the email domain.';
$string['configrole'] = 'This is the role the user will be given when they have completed the sign up process';
$string['emailasusernamehelp'] = 'Enter your email address.  This will be your username';
$string['emaildomaindoesntmatch'] = 'Your email domain is not in the list of accepted domains for this company';
$string['enable'] = 'Enable';
$string['enable_help'] = 'New users will be assigned to a company on creation when this is enabled';
$string['logininfo'] = 'Fill out the form below to create a new user.  An email will be sent to the email address you specify to verify the account and allow access.';
$string['pluginname'] = 'IOMAD signup';
$string['privacy:metadata'] = 'The Local IOMAD signup plugin only shows data stored in other locations.';
$string['role'] = 'Role to be assigned';
$string['showinstructions'] = 'Show the self signup instructions on the login page';
$string['showinstructions_help'] = 'By default Moodle will show the self signup intructions on the login page when self enrol is enabled.  This allows them to be removed';
$string['useemail'] = 'Force email to be username';
$string['useemail_help'] = 'Selecting this will remove the option for a user to select their own username.  Their email address will be used instead.';

