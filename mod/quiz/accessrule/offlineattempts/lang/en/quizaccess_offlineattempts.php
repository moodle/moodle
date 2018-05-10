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
 * Strings for the quizaccess_offlineattempts plugin.
 *
 * @package    quizaccess_offlineattempts
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allowofflineattempts'] = 'Allow quiz to be attempted offline using the mobile app';
$string['allowofflineattempts_help'] = 'If enabled, a mobile app user can download the quiz and attempt it offline.

Note: It is not possible for a quiz to be attempted offline if it has a time limit, or requires a network address, or uses any question behaviour other than deferred feedback (with or without CBM).';
$string['confirmdatasaved'] = 'I confirm that I do not have any unsaved work on a mobile device.';
$string['mobileapp'] = 'Mobile app';
$string['offlineattemptserror'] = 'It is not possible for a quiz to be attempted offline if it has a time limit, or requires a network address, or uses any question behaviour other than deferred feedback (with or without CBM).';
$string['offlinedatamessage'] = 'You have worked on this attempt using a mobile device. Data was last saved to this site {$a} ago.';
$string['pleaseconfirm'] = 'Please check and confirm that you do not have any unsaved work.';
$string['pluginname'] = 'Offline attempts access rule';
$string['privacy:metadata'] = 'The Offline attempts quiz access rule plugin does not store any personal data.';
