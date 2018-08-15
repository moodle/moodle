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
 * Strings for component 'ltiservice_memberships', language 'en'
 *
 * @package    ltiservice_memberships
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allow'] = 'Use this service to retrieve members\' information as per privacy settings';
$string['ltiservice_memberships'] = 'IMS LTI Names and Role Provisioning';
$string['ltiservice_memberships_help'] = 'Allow the tool to retrieve member\'s info from the course ' .
  'using the IMS LTI Names and Role Provisioning Service. The privacy settings will apply.  For course-level ' .
  'requests these will be based on the tool confguration settings.  If you wish to always send such details ' .
  'do not delegate the choice to teachers.  Link-level requests will always use the privacy settings which ' .
  'apply to the link.';
$string['notallow'] = 'Do not use this service';
$string['pluginname'] = 'Names and Role Provisioning LTI Service';
$string['privacy:metadata:email'] = 'The email of the user using the LTI consumer.';
$string['privacy:metadata:externalpurpose'] = 'This information is sent to an external LTI provider.';
$string['privacy:metadata:firstname'] = 'The firstname of the user using the LTI consumer.';
$string['privacy:metadata:fullname'] = 'The fullname of the user using the LTI consumer.';
$string['privacy:metadata:lastname'] = 'The lastname of the user using the LTI consumer.';
$string['privacy:metadata:userid'] = 'The ID of the user using the LTI consumer.';
$string['privacy:metadata:useridnumber'] = 'The ID number of the user using the LTI consumer';
