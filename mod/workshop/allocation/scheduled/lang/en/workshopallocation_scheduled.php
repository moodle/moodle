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
 * Strings for the Workshop's scheduled allocator
 *
 * @package     workshopallocation_scheduled
 * @subpackage  mod_workshop
 * @copyright   2012 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['enablescheduled'] = 'Enable scheduled allocation';
$string['enablescheduledinfo'] = 'Automatically allocate submissions after the deadline';
$string['scheduledsettings'] = 'Scheduled allocation settings';
$string['scheduledsettings_help'] = 'If enabled, the scheduled allocation method will automatically allocate submissions for the assessment at the end of the submission phase. The end of the phase can be defined in the workshop setting \'Submissions deadline\'.

Internally, the random allocation method is executed with the parameters pre-defined in this form. It means that the scheduled allocation works as if the teacher executed the random allocation themselves at the end of the submission phase using the allocation settings below.

Note that the scheduled allocation is *not* executed if you manually switch the workshop into the assessment phase before the submissions deadline. You have to allocate submissions yourself in that case. The scheduled allocation method is particularly useful when used together with the automatic phase switching feature.';
$string['nosubmissionend'] = 'You have to define the submissions deadline in the workshop settings in order to use scheduled allocation. Press the \'Continue\' button to edit the workshop settings.';
$string['pluginname'] = 'Scheduled allocation';
$string['resultdisabled'] = 'Scheduled allocation was disabled';
$string['resultenabled'] = 'Scheduled allocation was enabled';
