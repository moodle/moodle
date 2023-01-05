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
$string['analyticsdisabled'] = 'Analytics is disabled. You can enable it in "Site administration > Advanced features".';
$string['analyticslogstore'] = 'Log store used for analytics';
$string['analyticslogstore_help'] = 'The log store that will be used by the analytics API to read users\' activity.';
$string['analyticssettings'] = 'Analytics settings';
$string['analyticssiteinfo'] = 'Site information';
$string['calclifetime'] = 'Keep analytics calculations for';
$string['configlcalclifetime'] = 'This specifies the length of time you want to keep calculation data - this will not delete predictions, but deletes the data used to generate the predictions. Using the default option here is best as it keeps your disk usage under control, however if you are using calculation tables for other purposes you may want to increase this value.';
$string['defaulttimesplittingmethods'] = 'Default analysis intervals for model\'s evaluation';
$string['defaulttimesplittingmethods_help'] = 'The analysis interval defines when the system will calculate predictions and the portion of activity logs that will be considered for those predictions. The model evaluation process will iterate through these analysis intervals unless a specific analysis interval is specified.';
$string['defaultpredictionsprocessor'] = 'Default predictions processor';
$string['defaultpredictoroption'] = 'Default processor ({$a})';
$string['disabledmodel'] = 'Disabled model';
$string['erroralreadypredict'] = 'File {$a} has already been used to generate predictions.';
$string['errorcannotreaddataset'] = 'Dataset file {$a} cannot be read.';
$string['errorcannotusetimesplitting'] = 'The provided analysis interval can\'t be used on this model.';
$string['errorcannotwritedataset'] = 'Dataset file {$a} cannot be written.';
$string['errorexportmodelresult'] = 'The machine learning model cannot be exported.';
$string['errorimport'] = 'Error importing the provided JSON file.';
$string['errorimportmissingcomponents'] = 'The provided model requires the following plugins to be installed: {$a}. Note that the versions do not necessarily need to match with the versions installed on your site. Installing the same or a newer version of the plugin should be fine in most cases.';
$string['errorimportversionmismatches'] = 'The version of the following components differs from the version installed on this site: {$a}. You can use the option \'Ignore version mismatches\' to ignore these differences.';
$string['errorimportmissingclasses'] = 'The following analytics components are not available on this site: {$a->missingclasses}.';
$string['errorinvalidindicator'] = 'Invalid {$a} indicator';
$string['errorinvalidcontexts'] = 'Some of the selected contexts can not be used in this target.';
$string['errorinvalidtarget'] = 'Invalid {$a} target';
$string['errorinvalidtimesplitting'] = 'Invalid analysis interval; please ensure you add the fully qualified class name.';
$string['errornocontextrestrictions'] = 'The selected target does not support context restrictions';
$string['errornoexportconfig'] = 'There was a problem exporting the model configuration.';
$string['errornoexportconfigrequirements'] = 'Only non-static models with an analysis interval can be exported.';
$string['errornoindicators'] = 'This model does not have any indicators.';
$string['errornopredictresults'] = 'No results returned from the predictions processor. Check the output directory contents for more information.';
$string['errornotimesplittings'] = 'This model does not have an analysis interval.';
$string['errornoroles'] = 'Student or teacher roles have not been defined. Define them in the analytics settings page.';
$string['errornotarget'] = 'This model does not have any target.';
$string['errorpredictioncontextnotavailable'] = 'This prediction context is no longer available.';
$string['errorpredictionformat'] = 'Wrong prediction calculations format';
$string['errorpredictionnotfound'] = 'Prediction not found';
$string['errorpredictionsprocessor'] = 'Predictions processor error: {$a}';
$string['errorpredictwrongformat'] = 'The predictions processor return cannot be decoded: "{$a}"';
$string['errorprocessornotready'] = 'The selected predictions processor is not ready: {$a}';
$string['errorsamplenotavailable'] = 'The predicted sample is no longer available.';
$string['errorunexistingtimesplitting'] = 'The selected analysis interval is not available.';
$string['errorunexistingmodel'] = 'Non-existing model {$a}';
$string['errorunknownaction'] = 'Unknown action';
$string['eventpredictionactionstarted'] = 'Prediction process started';
$string['eventinsightsviewed'] = 'Insights viewed';
$string['fixedack'] = 'Accept';
$string['incorrectlyflagged'] = 'Incorrectly flagged';
$string['insightmessagesubject'] = 'New insight for "{$a}"';
$string['insightinfomessagehtml'] = 'The system generated an insight for you.';
$string['insightinfomessageplain'] = 'The system generated an insight for you: {$a}';
$string['insightinfomessageaction'] = '{$a->text}: {$a->url}';
$string['invalidtimesplitting'] = 'Model with ID {$a} needs an analysis interval before it can be used for training.';
$string['invalidanalysablefortimesplitting'] = 'It cannot be analysed using {$a} analysis interval.';
$string['levelinstitution'] = 'Level of education';
$string['levelinstitutionisced0'] = 'Early childhood education (‘less than primary’ for educational attainment)';
$string['levelinstitutionisced1'] = 'Primary education';
$string['levelinstitutionisced2'] = 'Lower secondary education';
$string['levelinstitutionisced3'] = 'Upper secondary education';
$string['levelinstitutionisced4'] = 'Post-secondary non-tertiary education (may include corporate or community/NGO training)';
$string['levelinstitutionisced5'] = 'Short-cycle tertiary education (may include corporate or community/NGO training)';
$string['levelinstitutionisced6'] = 'Bachelor or equivalent level';
$string['levelinstitutionisced7'] = 'Master or equivalent level';
$string['levelinstitutionisced8'] = 'Doctoral or equivalent level';
$string['nocourses'] = 'No courses to analyse';
$string['modeinstruction'] = 'Modes of instruction';
$string['modeinstructionfacetoface'] = 'Face to face';
$string['modeinstructionblendedhybrid'] = 'Blended or hybrid';
$string['modeinstructionfullyonline'] = 'Fully online';
$string['modeloutputdir'] = 'Models output directory';
$string['modeloutputdirwithdefaultinfo'] = 'Directory where prediction processors store all evaluation info. Useful for debugging and research. If empty, then {$a} will be used as default.';
$string['modeltimelimit'] = 'Analysis time limit per model';
$string['modeltimelimitinfo'] = 'This setting limits the time each model spends analysing the site contents.';
$string['neutral'] = 'Neutral';
$string['neverdelete'] = 'Never delete calculations';
$string['noevaluationbasedassumptions'] = 'Models based on assumptions cannot be evaluated.';
$string['nodata'] = 'No data to analyse';
$string['noinsightsmodel'] = 'This model does not generate insights';
$string['noinsights'] = 'No insights reported';
$string['nonewdata'] = 'No new data available. The model will be analysed after the next analysis interval.';
$string['nonewranges'] = 'No new predictions yet. The model will be analysed after the next analysis interval.';
$string['nopredictionsyet'] = 'No predictions available yet';
$string['noranges'] = 'No predictions yet';
$string['notapplicable'] = 'Not applicable';
$string['notrainingbasedassumptions'] = 'Models based on assumptions do not need training';
$string['notuseful'] = 'Not useful';
$string['novaliddata'] = 'No valid data available';
$string['novalidsamples'] = 'No valid samples available';
$string['onlycli'] = 'Analytics processes execution via command line only';
$string['onlycliinfo'] = 'Analytics processes such as evaluating models, training machine learning algorithms or getting predictions may take some time. They run as cron tasks or may be forced via command line. If disabled, analytics processes can be run manually via the web interface.';
$string['percentonline'] = 'Percent online';
$string['percentonline_help'] = 'If your organisation offers blended or hybrid courses, what percentage of student work is conducted online in Moodle? Enter a number between 0 and 100.';
$string['predictionsprocessor'] = 'Predictions processor';
$string['predictionsprocessor_help'] = 'A predictions processor is the machine-learning backend that processes the datasets generated by calculating models\' indicators and targets. Each model can use a different processor. The one specified here will be the default.';
$string['privacy:metadata:analytics:indicatorcalc'] = 'Indicator calculations';
$string['privacy:metadata:analytics:indicatorcalc:starttime'] = 'Calculation start time';
$string['privacy:metadata:analytics:indicatorcalc:endtime'] = 'Calculation end time';
$string['privacy:metadata:analytics:indicatorcalc:contextid'] = 'The context';
$string['privacy:metadata:analytics:indicatorcalc:sampleorigin'] = 'The origin table of the sample';
$string['privacy:metadata:analytics:indicatorcalc:sampleid'] = 'The sample ID';
$string['privacy:metadata:analytics:indicatorcalc:indicator'] = 'The indicator calculator class';
$string['privacy:metadata:analytics:indicatorcalc:value'] = 'The calculated value';
$string['privacy:metadata:analytics:indicatorcalc:timecreated'] = 'When the prediction was made';
$string['privacy:metadata:analytics:predictions'] = 'Predictions';
$string['privacy:metadata:analytics:predictions:modelid'] = 'The model ID';
$string['privacy:metadata:analytics:predictions:contextid'] = 'The context';
$string['privacy:metadata:analytics:predictions:sampleid'] = 'The sample ID';
$string['privacy:metadata:analytics:predictions:rangeindex'] = 'The index of the analysis interval';
$string['privacy:metadata:analytics:predictions:prediction'] = 'The prediction';
$string['privacy:metadata:analytics:predictions:predictionscore'] = 'The prediction score';
$string['privacy:metadata:analytics:predictions:calculations'] = 'Indicator calculations';
$string['privacy:metadata:analytics:predictions:timecreated'] = 'When the prediction was made';
$string['privacy:metadata:analytics:predictions:timestart'] = 'Calculations time start';
$string['privacy:metadata:analytics:predictions:timeend'] = 'Calculations time end';
$string['privacy:metadata:analytics:predictionactions'] = 'Prediction actions';
$string['privacy:metadata:analytics:predictionactions:predictionid'] = 'The prediction ID';
$string['privacy:metadata:analytics:predictionactions:userid'] = 'The user that made the action';
$string['privacy:metadata:analytics:predictionactions:actionname'] = 'The action name';
$string['privacy:metadata:analytics:predictionactions:timecreated'] = 'When the prediction action was performed';
$string['privacy:metadata:analytics:analyticsmodels'] = 'Analytics models';
$string['privacy:metadata:analytics:analyticsmodels:usermodified'] = 'The user that modified the model';
$string['privacy:metadata:analytics:analyticsmodelslog'] = 'The log used for analytics models';
$string['privacy:metadata:analytics:analyticsmodelslog:usermodified'] = 'The user that modified the log';
$string['processingsitecontents'] = 'Processing site contents';
$string['successfullyanalysed'] = 'Successfully analysed';
$string['timesplittingmethod'] = 'Analysis interval';
$string['timesplittingmethod_help'] = 'The analysis interval defines when the system will calculate predictions and the portion of activity logs that will be considered for those predictions. For example, the course duration may be divided into parts, with a prediction generated at the end of each part.';
$string['timesplittingmethod_link'] = 'Time_splitting_methods';
$string['typeinstitution'] = 'Type of institution';
$string['typeinstitutionacademic'] = 'Academic';
$string['typeinstitutiontraining'] = 'Corporate training';
$string['typeinstitutionngo'] = 'Non-governmental organization (NGO)';
$string['useful'] = 'Useful';
$string['viewdetails'] = 'View details';
$string['viewinsight'] = 'View insight';
$string['viewinsightdetails'] = 'View insight details';
$string['viewprediction'] = 'View prediction details';
$string['washelpful'] = 'Was this helpful?';
