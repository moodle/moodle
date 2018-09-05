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
 * Strings for component 'qbehaviour_adaptive', language 'en'.
 *
 * @package    qbehaviour
 * @subpackage adaptive
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['disregardedwithoutpenalty'] = 'The submission was invalid, and has been disregarded without penalty.';
$string['gradingdetails'] = 'Marks for this submission: {$a->raw}/{$a->max}.';
$string['gradingdetailswithadjustment'] = 'Marks for this submission: {$a->raw}/{$a->max}. Accounting for previous tries, this gives <strong>{$a->cur}/{$a->max}</strong>.';
$string['gradingdetailswithadjustmentpenalty'] = 'Marks for this submission: {$a->raw}/{$a->max}. Accounting for previous tries, this gives <strong>{$a->cur}/{$a->max}</strong>. This submission attracted a penalty of {$a->penalty}.';
$string['gradingdetailswithadjustmenttotalpenalty'] = 'Marks for this submission: {$a->raw}/{$a->max}. Accounting for previous tries, this gives <strong>{$a->cur}/{$a->max}</strong>. This submission attracted a penalty of {$a->penalty}. Total penalties so far: {$a->totalpenalty}.';
$string['gradingdetailswithpenalty'] = 'Marks for this submission: {$a->raw}/{$a->max}. This submission attracted a penalty of {$a->penalty}.';
$string['gradingdetailswithtotalpenalty'] = 'Marks for this submission: {$a->raw}/{$a->max}. This submission attracted a penalty of {$a->penalty}. Total penalties so far: {$a->totalpenalty}.';
$string['notcomplete'] = 'Not complete';
$string['pluginname'] = 'Adaptive mode';
$string['privacy:metadata'] = 'The Adaptive mode question behaviour plugin does not store any personal data.';

// Old strings these are currently only used in the unit tests, to verify that the new
// strings give the same results as the old strings.
$string['gradingdetailsadjustment'] = 'Accounting for previous tries, this gives <strong>{$a->cur}/{$a->max}</strong>.';
$string['gradingdetailspenalty'] = 'This submission attracted a penalty of {$a}.';
$string['gradingdetailspenaltytotal'] = 'Total penalties so far: {$a}.';
