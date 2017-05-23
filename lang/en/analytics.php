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
$string['analyticssettings'] = 'Analytics settings';
$string['enabledtimesplittings'] = 'Time splitting methods';
$string['enabledtimesplittings_help'] = 'The time splitting method divides the course duration in parts, the predictions engine will run at the end of these parts. It is recommended that you only enable the time splitting methods you could be interested on using; the evaluation process will iterate through all of them so the more time splitting methods to go through the slower the evaluation process will be.';
$string['erroralreadypredict'] = '{$a} file has already been used to predict';
$string['errorinvalidindicator'] = 'Invalid {$a} indicator';
$string['errorinvalidtimesplitting'] = 'Invalid time splitting, please ensure you added the class fully qualified class name';
$string['errornoindicators'] = 'This model does not have any indicator';
$string['errornopredictresults'] = 'No results returned from the predictions processor, check the output directory contents for more info';
$string['errornotimesplittings'] = 'This model does not have any time splitting method';
$string['errornoroles'] = 'Student or teacher roles have not been defined. Define them in analytics settings page.';
$string['errornotarget'] = 'This model does not have any target';
$string['errorpredictionformat'] = 'Wrong prediction calculations format';
$string['errorpredictionsprocessor'] = 'Predictions processor error: {$a}';
$string['errorpredictwrongformat'] = 'The predictions processor return can not be decoded: "{$a}"';
$string['errorprocessornotready'] = 'The selected predictions processor is not ready: {$a}';
$string['errorsamplenotavailable'] = 'The predicted sample is not available anymore';
$string['errorunexistingtimesplitting'] = 'The selected time splitting method is not available';
$string['errorunknownaction'] = 'Unknown action';
$string['eventactionclicked'] = 'Prediction action clicked';
$string['indicator:accessesafterend'] = 'Accesses after the end date';
$string['indicator:accessesbeforestart'] = 'Accesses before the start date';
$string['indicator:anywrite'] = 'Any write action';
$string['indicator:cognitivedepthassign'] = 'Assignment cognitive';
$string['indicator:cognitivedepthbook'] = 'Book resources\' cognitive';
$string['indicator:cognitivedepthchat'] = 'Chat cognitive';
$string['indicator:cognitivedepthchoice'] = 'Choice cognitive';
$string['indicator:cognitivedepthdata'] = 'Database cognitive';
$string['indicator:cognitivedepthfeedback'] = 'Feedback cognitive';
$string['indicator:cognitivedepthfolder'] = 'Folder resources cognitive';
$string['indicator:cognitivedepthforum'] = 'Forum cognitive';
$string['indicator:cognitivedepthglossary'] = 'Glossary cognitive';
$string['indicator:cognitivedepthimscp'] = 'IMS content packages\' cognitive';
$string['indicator:cognitivedepthlabel'] = 'Label resources cognitive';
$string['indicator:cognitivedepthlesson'] = 'Lesson cognitive';
$string['indicator:cognitivedepthlti'] = 'LTI cognitive';
$string['indicator:cognitivedepthpage'] = 'Page resources\' cognitive';
$string['indicator:cognitivedepthquiz'] = 'Quiz cognitive';
$string['indicator:cognitivedepthresource'] = 'File resources\' cognitive';
$string['indicator:cognitivedepthscorm'] = 'SCORM cognitive';
$string['indicator:cognitivedepthsurvey'] = 'Survey cognitive';
$string['indicator:cognitivedepthurl'] = 'URL resources\' cognitive';
$string['indicator:cognitivedepthwiki'] = 'Wiki cognitive';
$string['indicator:cognitivedepthworkshop'] = 'Workshop cognitive';
$string['indicator:socialbreadthassign'] = 'Assignment social';
$string['indicator:socialbreadthbook'] = 'Book resources\' social';
$string['indicator:socialbreadthchat'] = 'Chat social';
$string['indicator:socialbreadthchoice'] = 'Choice social';
$string['indicator:socialbreadthdata'] = 'Database social';
$string['indicator:socialbreadthfeedback'] = 'Feedback social';
$string['indicator:socialbreadthfolder'] = 'Folder resources social';
$string['indicator:socialbreadthforum'] = 'Forum social';
$string['indicator:socialbreadthglossary'] = 'Glossary social';
$string['indicator:socialbreadthimscp'] = 'IMS content packages\' social';
$string['indicator:socialbreadthlabel'] = 'Label resources social';
$string['indicator:socialbreadthlesson'] = 'Lesson social';
$string['indicator:socialbreadthlti'] = 'LTI social';
$string['indicator:socialbreadthpage'] = 'Page resources\' social';
$string['indicator:socialbreadthquiz'] = 'Quiz social';
$string['indicator:socialbreadthresource'] = 'File resources\' social';
$string['indicator:socialbreadthscorm'] = 'SCORM social';
$string['indicator:socialbreadthsurvey'] = 'Survey social';
$string['indicator:socialbreadthurl'] = 'URL resources\' social';
$string['indicator:socialbreadthwiki'] = 'Wiki social';
$string['indicator:socialbreadthworkshop'] = 'Workshop social';
$string['indicator:readactions'] = 'Read actions amount';
$string['indicator:completeduserprofile'] = 'User profile is completed';
$string['indicator:userforumstracking'] = 'User is tracking forums';


$string['insightmessagesubject'] = 'New insight for "{$a->contextname}": {$a->insightname}';
$string['insightinfo'] = '{$a->insightname} - {$a->contextname}';
$string['insightinfomessage'] = 'There are some insights you may find useful. Check out {$a}';
$string['insights'] = 'Insights';
$string['invalidtimesplitting'] = 'Model with id {$a} needs a time splitting method before it can be used to train';
$string['invalidanalysablefortimesplitting'] = 'It can not be analysed using {$a} time splitting method';
$string['messageprovider:insights'] = 'Insights generated by prediction models';
$string['modeloutputdir'] = 'Models output directory';
$string['modeloutputdirinfo'] = 'Directory where prediction processors store all evaluation info. Useful for debugging and research.';
$string['nocourses'] = 'No courses to analyse';
$string['nodata'] = 'No data available';
$string['nodatatotrain'] = 'There is no data to use as training data';
$string['nonewdata'] = 'No new data available';
$string['nonewtimeranges'] = 'No new time ranges, nothing to predict';
$string['nopredictionsyet'] = 'No predictions available yet';
$string['novaliddata'] = 'No valid data available';
$string['predictionsprocessor'] = 'Predictions processor';
$string['predictionsprocessor_help'] = 'Prediction processors are the machine learning backends that process the datasets generated by calculating models\' indicators and targets.';
$string['processingsitecontents'] = 'Processing site contents';
$string['processingsitecontents'] = 'Processing site contents';
$string['studentroles'] = 'Student roles';
$string['successfullyanalysed'] = 'Successfully analysed';
$string['teacherroles'] = 'Teacher roles';
$string['timesplitting:deciles'] = 'Deciles';
$string['timesplitting:decilesaccum'] = 'Deciles accumulative';
$string['timesplitting:nosplitting'] = 'No time splitting';
$string['timesplitting:quarters'] = 'Quarters';
$string['timesplitting:quartersaccum'] = 'Quarters accumulative';
$string['timesplitting:singlerange'] = 'Single range';
$string['timesplitting:weekly'] = 'Weekly';
$string['timesplitting:weeklyaccum'] = 'Weekly accumulative';
$string['timesplittingmethod'] = 'Time splitting method';
$string['timesplittingmethod_help'] = 'The time splitting method divides the course duration in parts, the predictions engine will run at the end of these parts. It is recommended that you only enable the time splitting methods you could be interested on using; the evaluation process will iterate through all of them so the more time splitting methods to go through the slower the evaluation process will be.';
$string['viewprediction'] = 'View prediction details';
