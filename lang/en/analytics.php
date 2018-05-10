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
$string['analytics'] = 'Analytics';
$string['analyticslogstore'] = 'Log store used for analytics';
$string['analyticslogstore_help'] = 'The log store that will be used by the analytics API to read users\' activity';
$string['analyticssettings'] = 'Analytics settings';
$string['coursetoolong'] = 'The course is too long';
$string['enabledtimesplittings'] = 'Time splitting methods';
$string['disabledmodel'] = 'Disabled model';
$string['erroralreadypredict'] = 'File {$a} has already been used to generate predictions.';
$string['errorcannotreaddataset'] = 'Dataset file {$a} can not be read';
$string['errorcannotwritedataset'] = 'Dataset file {$a} cannot be written';
$string['errorendbeforestart'] = 'The end date ({$a}) is before the course start date.';
$string['errorinvalidindicator'] = 'Invalid {$a} indicator';
$string['errorinvalidtimesplitting'] = 'Invalid time splitting; please ensure you add the class fully qualified class name.';
$string['errornoindicators'] = 'This model does not have any indicators.';
$string['errornopredictresults'] = 'No results returned from the predictions processor. Check the output directory contents for more information.';
$string['errornotimesplittings'] = 'This model does not have any time-splitting method.';
$string['errornoroles'] = 'Student or teacher roles have not been defined. Define them in the analytics settings page.';
$string['errornotarget'] = 'This model does not have any target.';
$string['errorpredictioncontextnotavailable'] = 'This prediction context is not available anymore.';
$string['errorpredictionformat'] = 'Wrong prediction calculations format';
$string['errorpredictionnotfound'] = 'Prediction not found';
$string['errorpredictionsprocessor'] = 'Predictions processor error: {$a}';
$string['errorpredictwrongformat'] = 'The predictions processor return cannot be decoded: "{$a}"';
$string['errorprocessornotready'] = 'The selected predictions processor is not ready: {$a}';
$string['errorsamplenotavailable'] = 'The predicted sample is not available anymore';
$string['errorunexistingtimesplitting'] = 'The selected time-splitting method is not available.';
$string['errorunexistingmodel'] = 'Non-existing model {$a}';
$string['errorunknownaction'] = 'Unknown action';
$string['eventpredictionactionstarted'] = 'Prediction process started';
$string['eventinsightsviewed'] = 'Insights viewed';
$string['fixedack'] = 'Acknowledged';
$string['insightmessagesubject'] = 'New insight for "{$a->contextname}": {$a->insightname}';
$string['insightinfomessage'] = 'The system generated some insights for you: {$a}';
$string['insightinfomessagehtml'] = 'The system generated some insights for you: <a href="{$a}">{$a}</a>.';
$string['invalidtimesplitting'] = 'Model with ID {$a} needs a time-splitting method before it can be used to train.';
$string['invalidanalysablefortimesplitting'] = 'It cannot be analysed using {$a} time-splitting method.';
$string['nocourses'] = 'No courses to analyse';
$string['modeloutputdir'] = 'Models output directory';
$string['modeloutputdirinfo'] = 'Directory where prediction processors store all evaluation info. Useful for debugging and research.';
$string['modeltimelimit'] = 'Analysis time limit per model';
$string['modeltimelimitinfo'] = 'This setting limits the time each model spends analysing the site contents.';
$string['noevaluationbasedassumptions'] = 'Models based on assumptions cannot be evaluated.';
$string['nodata'] = 'No data to analyse';
$string['noinsightsmodel'] = 'This model does not generate insights';
$string['noinsights'] = 'No insights reported';
$string['nonewdata'] = 'No new data available';
$string['nonewranges'] = 'No new predictions yet';
$string['nonewtimeranges'] = 'No new time ranges; nothing to predict.';
$string['nopredictionsyet'] = 'No predictions available yet';
$string['noranges'] = 'No predictions yet';
$string['notrainingbasedassumptions'] = 'Models based on assumptions do not need training';
$string['notuseful'] = 'Not useful';
$string['novaliddata'] = 'No valid data available';
$string['novalidsamples'] = 'No valid samples available';
$string['onlycli'] = 'Analytics processes execution via command line only';
$string['onlycliinfo'] = 'Analytics processes like evaluating models, training machine learning algorithms or getting predictions can take some time, they will run as cron tasks and they can be forced via command line. Disable this setting if you want your site managers to be able to run these processes manually via web interface';
$string['predictionsprocessor'] = 'Predictions processor';
$string['predictionsprocessor_help'] = 'A predictions processor is the machine-learning backend that processes the datasets generated by calculating models\' indicators and targets. All trained algorithms and predictions will be deleted if you change to another predictions processor.';
$string['privacy:metadata:analytics:indicatorcalc'] = 'Indicator calculations';
$string['privacy:metadata:analytics:indicatorcalc:starttime'] = 'Calculation start time';
$string['privacy:metadata:analytics:indicatorcalc:endtime'] = 'Calculation end time';
$string['privacy:metadata:analytics:indicatorcalc:contextid'] = 'The context';
$string['privacy:metadata:analytics:indicatorcalc:sampleorigin'] = 'The origin table of the sample';
$string['privacy:metadata:analytics:indicatorcalc:sampleid'] = 'The sample id';
$string['privacy:metadata:analytics:indicatorcalc:indicator'] = 'The indicator calculator class';
$string['privacy:metadata:analytics:indicatorcalc:value'] = 'The calculated value';
$string['privacy:metadata:analytics:indicatorcalc:timecreated'] = 'When the prediction was made';
$string['privacy:metadata:analytics:predictions'] = 'Predictions';
$string['privacy:metadata:analytics:predictions:modelid'] = 'The model id';
$string['privacy:metadata:analytics:predictions:contextid'] = 'The context';
$string['privacy:metadata:analytics:predictions:sampleid'] = 'The sample id';
$string['privacy:metadata:analytics:predictions:rangeindex'] = 'The index of the time splitting method';
$string['privacy:metadata:analytics:predictions:prediction'] = 'The prediction';
$string['privacy:metadata:analytics:predictions:predictionscore'] = 'The prediction score';
$string['privacy:metadata:analytics:predictions:calculations'] = 'Indicator calculations';
$string['privacy:metadata:analytics:predictions:timecreated'] = 'When the prediction was made';
$string['privacy:metadata:analytics:predictions:timestart'] = 'Calculations time start';
$string['privacy:metadata:analytics:predictions:timeend'] = 'Calculations time end';
$string['privacy:metadata:analytics:predictionactions'] = 'Prediction actions';
$string['privacy:metadata:analytics:predictionactions:predictionid'] = 'The prediction id';
$string['privacy:metadata:analytics:predictionactions:userid'] = 'The user that made the action';
$string['privacy:metadata:analytics:predictionactions:actionname'] = 'The action name';
$string['privacy:metadata:analytics:predictionactions:timecreated'] = 'When the prediction action was performed';
$string['processingsitecontents'] = 'Processing site contents';
$string['successfullyanalysed'] = 'Successfully analysed';
$string['timesplittingmethod'] = 'Time-splitting method';
$string['timesplittingmethod_help'] = 'The time-splitting method divides the course duration into parts; the predictions engine will run at the end of these parts. It is recommended that you only enable the time-splitting methods you may be interested in using. The evaluation process will iterate through all enabled time-splitting methods, so the more time-splitting methods enabled, the slower the evaluation process will be.';
$string['viewprediction'] = 'View prediction details';
