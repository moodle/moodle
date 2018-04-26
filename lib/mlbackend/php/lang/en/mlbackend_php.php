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
 * Strings for component 'mlbackend_php'
 *
 * @package   mlbackend_php
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['datasetsizelimited'] = 'Only part of the dataset has been evaluated due to its size. Set $CFG->mlbackend_php_no_memory_limit if you are confident that your system can cope with a {$a} dataset.';
$string['errorcantloadmodel'] = 'Model file {$a} does not exist. The model should been trained before using it to predict.';
$string['errorlowscore'] = 'The evaluated model prediction accuracy is not very high, so some predictions may not be accurate. Model score = {$a->score}, minimum score = {$a->minscore}';
$string['errornotenoughdata'] = 'There is not enough data to evaluate this model using the time-splitting method.';
$string['errornotenoughdatadev'] = 'The evaluation results varied too much. It is recommended that more data is gathered to ensure the model is valid. Evaluation results standard deviation = {$a->deviation}, maximum recommended standard deviation = {$a->accepteddeviation}';
$string['errorphp7required'] = 'The PHP machine learning backend requires PHP 7';
$string['pluginname'] = 'PHP machine learning backend';
$string['privacy:metadata'] = 'The PHP machine learning backend plugin does not store any personal data.';
