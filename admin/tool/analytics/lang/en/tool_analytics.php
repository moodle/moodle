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
$string['actions'] = 'Actions';
$string['actionsexecutedbyusers'] = 'Actions executed by users';
$string['actionsexecutedbyusersfor'] = 'Actions executed by users for "{$a}" model';
$string['actionexecutedgroupedusefulness'] = 'Grouped actions';
$string['allpredictions'] = 'All predictions';
$string['alltimesplittingmethods'] = 'All analysis intervals';
$string['analysingsitedata'] = 'Analysing the site';
$string['analysis'] = 'Analysis';
$string['analyticmodels'] = 'Analytics models';
$string['bettercli'] = 'Evaluating models and generating predictions may involve heavy processing. It is recommended to run these actions from the command line.';
$string['cantguessstartdate'] = 'Can\'t guess the start date';
$string['cantguessenddate'] = 'Can\'t guess the end date';
$string['classdoesnotexist'] = 'Class {$a} does not exist';
$string['clearpredictions'] = 'Clear predictions';
$string['clearmodelpredictions'] = 'Are you sure you want to clear all "{$a}" predictions?';
$string['clienablemodel'] = 'You can enable the model by selecting an analysis interval by its ID. Note that you can also enable it later using the web interface (\'none\' to exit).';
$string['clievaluationandpredictions'] = 'A scheduled task iterates through enabled models and gets predictions. Models evaluation via the web interface is disabled. You can allow these processes to be executed manually via the web interface by disabling the <a href="{$a}">\'onlycli\'</a> analytics setting.';
$string['clievaluationandpredictionsnoadmin'] = 'A scheduled task iterates through enabled models and gets predictions. Models evaluation via the web interface is disabled. It may be enabled by a site administrator.';
$string['component'] = 'Component';
$string['componentcore'] = 'Core';
$string['componentselect'] = 'Select all models provided by the component \'{$a}\'';
$string['componentselectnone'] = 'Unselect all';
$string['contexts'] = 'Contexts';
$string['contexts_help'] = 'The model will be limited to this set of contexts. No context restrictions will be applied if no contexts are selected.';
$string['createmodel'] = 'Create model';
$string['currenttimesplitting'] = 'Current analysis interval';
$string['delete'] = 'Delete';
$string['deletemodelconfirmation'] = 'Are you sure you want to delete "{$a}"? These changes cannot be reverted.';
$string['disabled'] = 'Disabled';
$string['editmodel'] = 'Edit "{$a}" model';
$string['edittrainedwarning'] = 'This model has already been trained. Note that changing its indicators or its analysis interval will delete its previous predictions and start generating new predictions.';
$string['enabled'] = 'Enabled';
$string['errorcantenablenotimesplitting'] = 'You need to select an analysis interval before enabling the model';
$string['errornoenabledandtrainedmodels'] = 'There are no enabled and trained models to predict.';
$string['errornoenabledmodels'] = 'There are no enabled models to train.';
$string['errornoexport'] = 'Only trained models can be exported';
$string['errornostaticevaluated'] = 'Models based on assumptions cannot be evaluated. They are always 100% correct according to how they were defined.';
$string['errornostaticlog'] = 'Models based on assumptions cannot be evaluated because there is no performance log.';
$string['erroronlycli'] = 'Execution only allowed via command line';
$string['errortrainingdataexport'] = 'The model training data could not be exported';
$string['evaluate'] = 'Evaluate';
$string['evaluatemodel'] = 'Evaluate model';
$string['evaluationmode'] = 'Evaluation mode';
$string['evaluationmode_help'] = 'There are two evaluation modes:

* Trained model -  Site data is used as testing data to evaluate the accuracy of the trained model.
* Configuration - Site data is split into training and testing data, to both train and test the accuracy of the model configuration.

Trained model is only available if a trained model has been imported into the site, and has not yet been re-trained using site data.';
$string['evaluationmodeinfo'] = 'This model has been imported into the site. You can either evaluate the performance of the model, or you can evaluate the performance of the model configuration using site data.';
$string['evaluationmodetrainedmodel'] = 'Evaluate the trained model';
$string['evaluationmodecoltrainedmodel'] = 'Trained model';
$string['evaluationmodecolconfiguration'] = 'Configuration';
$string['evaluationmodeconfiguration'] = 'Evaluate the model configuration';
$string['evaluationinbatches'] = 'The site contents are calculated and stored in batches. The evaluation process may be stopped at any time. The next time it is run, it will continue from the point when it was stopped.';
$string['executescheduledanalysis'] = 'Execute scheduled analysis';
$string['export'] = 'Export';
$string['exportincludeweights'] = 'Include the weights of the trained model';
$string['exportmodel'] = 'Export configuration';
$string['exporttrainingdata'] = 'Export training data';
$string['extrainfo'] = 'Info';
$string['generalerror'] = 'Evaluation error. Status code {$a}';
$string['goodmodel'] = 'This is a good model for using to obtain predictions. Enable it to start obtaining predictions.';
$string['importmodel'] = 'Import model';
$string['indicators'] = 'Indicators';
$string['indicators_help'] = 'The indicators are what you think will lead to an accurate prediction of the target.';
$string['indicators_link'] = 'Indicators';
$string['indicatorsnum'] = 'Number of indicators: {$a}';
$string['info'] = 'Info';
$string['insightsreport'] = 'Insights report';
$string['ignoreversionmismatches'] = 'Ignore version mismatches';
$string['ignoreversionmismatchescheckbox'] = 'Ignore the differences between this site version and the original site version.';
$string['importedsuccessfully'] = 'The model has been successfully imported.';
$string['insights'] = 'Insights';
$string['invalidanalysables'] = 'Invalid site elements';
$string['invalidanalysablesinfo'] = 'This page lists analysable elements that can\'t be used by this prediction model. The listed elements can\'t be used either to train the prediction model nor can the prediction model obtain predictions for them.';
$string['invalidanalysablestable'] = 'Invalid site analysable elements table';
$string['invalidcurrenttimesplitting'] = 'The current analysis interval is invalid for the target of this model. Please select a different interval.';
$string['invalidindicatorsremoved'] = 'A new model has been added. Indicators that don\'t work with the selected target have been automatically removed.';
$string['invalidtimesplitting'] = 'The selected analysis interval is invalid for the selected target.';
$string['invalidtimesplittinginmodels'] = 'The analysis interval used by some of the models is invalid. Please select a different interval for the following models: {$a}';
$string['invalidprediction'] = 'Invalid to get predictions';
$string['invalidtraining'] = 'Invalid to train the model';
$string['loginfo'] = 'Log extra info';
$string['missingmoodleversion'] = 'Imported file doesn\'t define a version number';
$string['modelid'] = 'Model ID';
$string['modelinvalidanalysables'] = 'Invalid analysable elements for "{$a}" model';
$string['modelname'] = 'Model name';
$string['modelresults'] = '{$a} results';
$string['modeltimesplitting'] = 'Analysis interval';
$string['newmodel'] = 'New model';
$string['nextpage'] = 'Next page';
$string['noactionsfound'] = 'Users have not executed any actions on the generated insights.';
$string['nodatatoevaluate'] = 'There is no data to evaluate the model';
$string['nodatatopredict'] = 'No new elements to get predictions for.';
$string['nodatatotrain'] = 'There is no new data that can be used for training.';
$string['noinvalidanalysables'] = 'This site does not contain any invalid analysable element.';
$string['notdefined'] = 'Not yet defined';
$string['pluginname'] = 'Analytic models';
$string['predictionresults'] = 'Prediction results';
$string['predictmodels'] = 'Predict models';
$string['predictorresultsin'] = 'Predictor logged information in {$a} directory';
$string['predictionprocessfinished'] = 'Prediction process finished';
$string['previouspage'] = 'Previous page';
$string['restoredefault'] = 'Restore default models';
$string['restoredefaultempty'] = 'Please select models to be restored.';
$string['restoredefaultinfo'] = 'These default models are missing or have changed since being installed. You can restore selected default models.';
$string['restoredefaultnone'] = 'All default models provided by core and installed plugins have been created. No new models were found; there is nothing to restore.';
$string['restoredefaultsome'] = 'Succesfully re-created {$a->count} new model(s).';
$string['restoredefaultsubmit'] = 'Restore selected';
$string['samestartdate'] = 'Current start date is good';
$string['sameenddate'] = 'Current end date is good';
$string['scheduledanalysisresults'] = 'Results using {$a->name} analysis interval';
$string['scheduledanalysisresultscli'] = 'Results using {$a->name} (id: {$a->id}) analysis interval';
$string['selecttimesplittingforevaluation'] = 'Select the analysis interval you want to use to evaluate the model configuration.';
$string['target'] = 'Target';
$string['target_help'] = 'The target is what the model will predict.';
$string['target_link'] = 'Targets';
$string['timesplittingnotdefined'] = 'No analysis interval is defined.';
$string['timesplittingnotdefined_help'] = 'You need to select an analysis interval before enabling the model.';
$string['trainandpredictmodel'] = 'Training model and calculating predictions';
$string['trainingprocessfinished'] = 'Training process finished';
$string['trainingresults'] = 'Training results';
$string['trainmodels'] = 'Train models';
$string['versionnotsame'] = 'Imported file was from a different version ({$a->importedversion}) than the current one ({$a->version})';
$string['viewlog'] = 'Evaluation log';
$string['weeksenddateautomaticallyset'] = 'End date automatically set based on start date and the number of sections';
$string['weeksenddatedefault'] = 'End date automatically calculated from the course start date.';
$string['privacy:metadata'] = 'The Analytic models plugin does not store any personal data.';

// Deprecated since Moodle 3.8.
$string['getpredictions'] = 'Get predictions';
