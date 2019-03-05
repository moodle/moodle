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
 * Strings for tool_analytics.
 *
 * @package tool_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accuracy'] = 'Accuracy';
$string['allpredictions'] = 'All predictions';
$string['analysingsitedata'] = 'Analysing the site';
$string['analyticmodels'] = 'Analytics models';
$string['bettercli'] = 'Evaluating models and generating predictions may involve heavy processing. It is recommended to run these actions from the command line.';
$string['cantguessstartdate'] = 'Can\'t guess the start date';
$string['cantguessenddate'] = 'Can\'t guess the end date';
$string['classdoesnotexist'] = 'Class {$a} does not exist';
$string['clearpredictions'] = 'Clear predictions';
$string['clearmodelpredictions'] = 'Are you sure you want to clear all "{$a}" predictions?';
$string['clienablemodel'] = 'You can enable the model by selecting a time-splitting method by its ID. Note that you can also enable it later using the web interface (\'none\' to exit).';
$string['clievaluationandpredictions'] = 'A scheduled task iterates through enabled models and gets predictions. Models evaluation via the web interface is disabled. You can allow these processes to be executed manually via the web interface by disabling the <a href="{$a}">\'onlycli\'</a> analytics setting.';
$string['clievaluationandpredictionsnoadmin'] = 'A scheduled task iterates through enabled models and gets predictions. Models evaluation via the web interface is disabled. It may be enabled by a site administrator.';
$string['createmodel'] = 'Create model';
$string['delete'] = 'Delete';
$string['deletemodelconfirmation'] = 'Are you sure you want to delete "{$a}"? These changes can not be reverted.';
$string['disabled'] = 'Disabled';
$string['editmodel'] = 'Edit "{$a}" model';
$string['edittrainedwarning'] = 'This model has already been trained. Note that changing its indicators or its time-splitting method will delete its previous predictions and start generating new predictions.';
$string['enabled'] = 'Enabled';
$string['errorcantenablenotimesplitting'] = 'You need to select a time-splitting method before enabling the model';
$string['errornoenabledandtrainedmodels'] = 'There are no enabled and trained models to predict.';
$string['errornoenabledmodels'] = 'There are no enabled models to train.';
$string['errornoexport'] = 'Only trained models can be exported';
$string['errornostaticedit'] = 'Models based on assumptions cannot be edited.';
$string['errornostaticevaluated'] = 'Models based on assumptions cannot be evaluated. They are always 100% correct according to how they were defined.';
$string['errornostaticlog'] = 'Models based on assumptions cannot be evaluated because there is no performance log.';
$string['erroronlycli'] = 'Execution only allowed via command line';
$string['errortrainingdataexport'] = 'The model training data could not be exported';
$string['evaluate'] = 'Evaluate';
$string['evaluatemodel'] = 'Evaluate model';
$string['evaluationinbatches'] = 'The site contents are calculated and stored in batches. The evaluation process may be stopped at any time. The next time it is run, it will continue from the point when it was stopped.';
$string['exportmodel'] = 'Export configuration';
$string['exporttrainingdata'] = 'Export training data';
$string['getpredictionsresultscli'] = 'Results using {$a->name} (id: {$a->id}) course duration splitting';
$string['getpredictionsresults'] = 'Results using {$a->name} course duration splitting';
$string['extrainfo'] = 'Info';
$string['generalerror'] = 'Evaluation error. Status code {$a}';
$string['getpredictions'] = 'Get predictions';
$string['goodmodel'] = 'This is a good model for using to obtain predictions. Enable it to start obtaining predictions.';
$string['importmodel'] = 'Import model';
$string['indicators'] = 'Indicators';
$string['info'] = 'Info';
$string['ignoreversionmismatches'] = 'Ignore version mismatches';
$string['ignoreversionmismatchescheckbox'] = 'Ignore the differences between this site version and the original site version.';
$string['importedsuccessfully'] = 'The model has been successfully imported.';
$string['insights'] = 'Insights';
$string['invalidanalysables'] = 'Invalid site elements';
$string['invalidanalysablesinfo'] = 'This page lists analysable elements that can\'t be used by this prediction model. The listed elements can\'t be used either to train the prediction model nor can the prediction model obtain predictions for them.';
$string['invalidanalysablestable'] = 'Invalid site analysable elements table';
$string['invalidindicatorsremoved'] = 'A new model has been added. Indicators that do not work with the selected target have been automatically removed.';
$string['invalidprediction'] = 'Invalid to get predictions';
$string['invalidtraining'] = 'Invalid to train the model';
$string['loginfo'] = 'Log extra info';
$string['missingmoodleversion'] = 'Imported file does not define a moodle version number';
$string['modelid'] = 'Model ID';
$string['modelinvalidanalysables'] = 'Invalid analysable elements for "{$a}" model';
$string['modelresults'] = '{$a} results';
$string['modeltimesplitting'] = 'Time splitting';
$string['nextpage'] = 'Next page';
$string['nodatatoevaluate'] = 'There is no data to evaluate the model';
$string['nodatatopredict'] = 'No new elements to get predictions for';
$string['nodatatotrain'] = 'There is no new data that can be used for training';
$string['noinvalidanalysables'] = 'This site does not contain any invalid analysable element.';
$string['notdefined'] = 'Not yet defined';
$string['pluginname'] = 'Analytic models';
$string['predictionresults'] = 'Prediction results';
$string['predictmodels'] = 'Predict models';
$string['predictorresultsin'] = 'Predictor logged information in {$a} directory';
$string['predictionprocessfinished'] = 'Prediction process finished';
$string['previouspage'] = 'Previous page';
$string['samestartdate'] = 'Current start date is good';
$string['sameenddate'] = 'Current end date is good';
$string['target'] = 'Target';
$string['target_help'] = 'The target is what the model will predict.';
$string['timesplittingnotdefined'] = 'Time splitting is not defined.';
$string['timesplittingnotdefined_help'] = 'You need to select a time-splitting method before enabling the model.';
$string['trainandpredictmodel'] = 'Training model and calculating predictions';
$string['trainingprocessfinished'] = 'Training process finished';
$string['trainingresults'] = 'Training results';
$string['trainmodels'] = 'Train models';
$string['versionnotsame'] = 'Imported file was from a different moodle version ({$a->importedversion}) than the current one ({$a->version})';
$string['viewlog'] = 'Log';
$string['weeksenddateautomaticallyset'] = 'End date automatically set based on start date and the number of sections';
$string['weeksenddatedefault'] = 'End date automatically calculated from the course start date.';
$string['privacy:metadata'] = 'The Analytic models plugin does not store any personal data.';
