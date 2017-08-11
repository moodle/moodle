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
 * Strings for core_analytics.
 *
 * @package core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['analysablenotused'] = 'Analysable {$a->analysableid} not used: {$a->errors}';
$string['analysablenotvalidfortarget'] = 'Analysable {$a->analysableid} is not valid for this target: {$a->result}';
$string['analysisinprogress'] = 'Still being analysed by a previous execution';
$string['analyticslogstore'] = 'Log store used for analytics';
$string['analyticslogstore_help'] = 'The log store that will be used by the analytics API to read users\' activity';
$string['analyticssettings'] = 'Analytics settings';
$string['coursetoolong'] = 'The course is too long';
$string['enabledtimesplittings'] = 'Time splitting methods';
$string['erroralreadypredict'] = '{$a} file has already been used to predict';
$string['errorcannotreaddataset'] = 'Dataset file {$a} can not be read';
$string['errorcannotwritedataset'] = 'Dataset file {$a} can not be written';
$string['errorendbeforestart'] = 'The guessed end date ({$a}) is before the course start date.';
$string['errorinvalidindicator'] = 'Invalid {$a} indicator';
$string['errorinvalidtimesplitting'] = 'Invalid time splitting, please ensure you added the class fully qualified class name';
$string['errornoindicators'] = 'This model does not have any indicator';
$string['errornopredictresults'] = 'No results returned from the predictions processor, check the output directory contents for more info';
$string['errornotimesplittings'] = 'This model does not have any time splitting method';
$string['errornoroles'] = 'Student or teacher roles have not been defined. Define them in analytics settings page.';
$string['errornotarget'] = 'This model does not have any target';
$string['errorpredictioncontextnotavailable'] = 'This prediction context is not available anymore';
$string['errorpredictionformat'] = 'Wrong prediction calculations format';
$string['errorpredictionnotfound'] = 'Prediction not found';
$string['errorpredictionsprocessor'] = 'Predictions processor error: {$a}';
$string['errorpredictwrongformat'] = 'The predictions processor return can not be decoded: "{$a}"';
$string['errorprocessornotready'] = 'The selected predictions processor is not ready: {$a}';
$string['errorsamplenotavailable'] = 'The predicted sample is not available anymore';
$string['errorunexistingtimesplitting'] = 'The selected time splitting method is not available';
$string['errorunexistingmodel'] = 'Unexisting model {$a}';
$string['errorunknownaction'] = 'Unknown action';
$string['eventpredictionactionstarted'] = 'Prediction action started';
$string['insightmessagesubject'] = 'New insight for "{$a->contextname}": {$a->insightname}';
$string['insightinfo'] = '{$a->insightname} - {$a->contextname}';
$string['insightinfomessage'] = 'The system generated some insights for you: {$a}';
$string['insightinfomessagehtml'] = 'The system generated some insights for you: <a href="{$a}">{$a}</a>.';
$string['invalidtimesplitting'] = 'Model with id {$a} needs a time splitting method before it can be used to train';
$string['invalidanalysablefortimesplitting'] = 'It can not be analysed using {$a} time splitting method';
$string['nocourses'] = 'No courses to analyse';
$string['modeloutputdir'] = 'Models output directory';
$string['modeloutputdirinfo'] = 'Directory where prediction processors store all evaluation info. Useful for debugging and research.';
$string['noevaluationbasedassumptions'] = 'Models based on assumptions can not be evaluated';
$string['nodata'] = 'No data to analyse';
$string['noinsightsmodel'] = 'This model does not generate insights';
$string['noinsights'] = 'No insights reported';
$string['nonewdata'] = 'No new data available';
$string['nonewranges'] = 'No new predictions yet';
$string['nonewtimeranges'] = 'No new time ranges, nothing to predict';
$string['nopredictionsyet'] = 'No predictions available yet';
$string['noranges'] = 'No predictions yet';
$string['notrainingbasedassumptions'] = 'Models based on assumptions do not need training';
$string['novaliddata'] = 'No valid data available';
$string['novalidsamples'] = 'No valid samples available';
$string['predictionsprocessor'] = 'Predictions processor';
$string['predictionsprocessor_help'] = 'Prediction processors are the machine learning backends that process the datasets generated by calculating models\' indicators and targets.';
$string['processingsitecontents'] = 'Processing site contents';
$string['successfullyanalysed'] = 'Successfully analysed';
$string['timesplittingmethod'] = 'Time splitting method';
$string['timesplittingmethod_help'] = 'The time splitting method divides the course duration in parts, the predictions engine will run at the end of these parts. It is recommended that you only enable the time splitting methods you could be interested on using; the evaluation process will iterate through all of them so the more time splitting methods to go through the slower the evaluation process will be.';
$string['viewprediction'] = 'View prediction details';
