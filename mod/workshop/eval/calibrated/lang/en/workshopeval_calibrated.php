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
 * Strings for component 'workshopeval_best', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    workshopeval
 * @subpackage best
 * @copyright  2009 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['comparison'] = 'Comparison against example assessments';
$string['comparison_help'] = <<<MDOWN
This setting specifies how strict the comparison should be. The stricter the comparison, the closer the marker had to be to the provided example assessments.
	
The following graph represents the grading curves for <span style="color:green">relaxed (9)</span> to <span style="color:#FFCC00">normal (5)</span> to <span style="color:red">strict (1)</span>. The x-axis is the correlation, where 1 is an assessment completely identical to the reference assessment, and 0 is the opposite i.e. completely wrong, and the y-axis is the resultant scaled grade for assessment. This curve is applied after accuracy is assessed.

<img src="{$CFG->wwwroot}/mod/workshop/eval/calibrated/pix/curves.png" />
MDOWN;
$string['consistency'] = 'Consistency of assessment accuracy';
$string['consistency_help'] = <<<MDOWN
This setting specifies how consistent the assesssor must be. A stricter consistency means markers must be more accurate with all of their example assessments. Accuracy in this case means how close the assessor got to the provided reference assessments.

This is calculated as the mean absolute deviation of comparisons from **each other**. In other words, if an assessor has a consistent 80% accuracy, they are deemed to be more consistent than one who was 100% accurate on one assessment but only 50% accurate on another.

An assessor's accuracy score can be reduced by their inconsistency according to this setting. At its most relaxed setting, assessors are not penalized for inconsistency. At its strictest setting, anyone with a mean deviation of more than 33% will get zero and be considered an incompetent marker.

In this graph, the x axis is the mean absolute deviation of the assessor's marks, and the y axis is the value their score will be multiplied by.

<img src="{$CFG->wwwroot}/mod/workshop/eval/calibrated/pix/lines.png" />
MDOWN;
$string['comparisonlevel1'] = '1 (strictest)';
$string['comparisonlevel2'] = '2';
$string['comparisonlevel3'] = '3';
$string['comparisonlevel4'] = '4';
$string['comparisonlevel5'] = '5 (normal)';
$string['comparisonlevel6'] = '6';
$string['comparisonlevel7'] = '7';
$string['comparisonlevel8'] = '8';
$string['comparisonlevel9'] = '9 (most relaxed)';
$string['configcomparison'] = 'Default value of the factor that influence the grading evaluation.';
$string['configconsistency'] = 'Default value of the factor that influence the grading evaluation consistency.';
$string['pluginname'] = 'Calibrated against example assessments';
$string['settings'] = 'Grading evaluation settings';

$string['nocompetentreviewers'] = 'According to your settings, the following submissions have no competent reviewers and have not been given a mark:';