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

$string['allowofflineattempts'] = 'Allow quiz to be attempted offline in the mobile app';
$string['allowofflineattempts_help'] = 'If checked, the user will be able to download the quiz to attempt it offline using the Mobile app. If the user download a quiz for offline, a new attempt will be created. This attempt will be empty until the user synchronize the results of the attempt in the Mobile app.';
$string['confirmdatasaved'] = 'I confirm that I don’t have unsaved work on my mobile devices before continuing this attempt.';
$string['mobileapp'] = 'Mobile app';
$string['offlineattemptserror'] = 'Offline quizzes are not compatible with quizzes using timers, access restriction by password or subnet and quizzes using behaviours different than deferred feedback without or with CBM';
$string['offlinedatamessage'] = 'You have worked on this attempt in a mobile device, and that data was last saved to this site {$a} ago.';
$string['pleaseconfirm'] = 'Please, confirm that you don\'t have unsaved work on your devices';
$string['pluginname'] = 'Offline attempts access rule';

