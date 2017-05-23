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
 * Strings for tool_models.
 *
 * @package tool_models
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accuracy'] = 'Accuracy';
$string['allindicators'] = 'All indicators';
$string['analysingsitedata'] = 'Analysing the site';
$string['analyticmodels'] = 'Analytic models';
$string['bettercli'] = 'Models\' evaluation and execution are heavy processes, it is better to run them through command line interface';
$string['cantguessstartdate'] = 'Can\'t guess the start date';
$string['cantguessenddate'] = 'Can\'t guess the end date';
$string['clienablemodel'] = 'You can enable the model by selecting a time splitting method by its id. Note that you can also enable it later using the web interface (\'none\' to exit)';
$string['coursenotyetstarted'] = 'The course is not yet started';
$string['coursenotyetfinished'] = 'The course is not yet finished';
$string['editmodel'] = 'Edit model {$a}';
$string['edittrainedwarning'] = 'This model has already been trained, note that changing its indicators or its time splitting method will delete its previous predictions and start generating the new ones';
$string['enabled'] = 'Enabled';
$string['errorcantenablenotimesplitting'] = 'You need to select a time splitting method before enabling the model';
$string['errornoenabledandtrainedmodels'] = 'There are not enabled and trained models to predict';
$string['errornoenabledmodels'] = 'There are not enabled models to train';
$string['evaluate'] = 'Evaluate';
$string['evaluatemodel'] = 'Evaluate model';
$string['evaluationinbatches'] = 'The site contents are calculated and stored in batches, during evaluation you can stop the process at any moment, the next time you run it it will continue from the point you stopped it.';
$string['executemodel'] = 'Execute';
$string['executingmodel'] = 'Training model and calculating predictions';
$string['executionresultscli'] = 'Results using {$a->name} (id: {$a->id}) course duration splitting';
$string['executionresults'] = 'Results using {$a->name} course duration splitting';
$string['extrainfo'] = 'Info';
$string['generalerror'] = 'Evaluation error. Status code {$a}';

$string['goodmodel'] = 'This is a good model and it can be used to predict, enable it and execute it to start getting predictions.';
$string['indicators'] = 'Indicators';
$string['info'] = 'Info';
$string['labelstudentdropoutyes'] = 'Student at risk of dropping out';
$string['labelstudentdropoutno'] = 'Not at risk';
$string['loginfo'] = 'Log extra info';
$string['lowaccuracy'] = 'The model accuracy is low';
$string['modelslist'] = 'Models list';
$string['modeltimesplitting'] = 'Time splitting';
$string['nocompletiondetection'] = 'No method available to detect course completion (no completion nor competencies nor course grade pass)';
$string['nocourseactivity'] = 'Not enough course activity between the start and the end of the course';
$string['nocourseendtime'] = 'The course does not have an end time';
$string['nocoursesections'] = 'No course sections';
$string['nocoursestudents'] = 'No students';
$string['nodatatoevaluate'] = 'There is no data to evaluate the model';
$string['nodatatopredict'] = 'There is no data to use for predictions';
$string['notdefined'] = 'Not yet defined';
$string['prediction'] = 'Prediction';
$string['predictionresults'] = 'Prediction results';
$string['predictions'] = 'Predictions';
$string['predictmodels'] = 'Predict models';
$string['predictorresultsin'] = 'Predictor logged information in {$a} directory';
$string['predictiondetails'] = 'Prediction details';
$string['predictionprocessfinished'] = 'Prediction process finished';

$string['pluginname'] = 'Analytic models';
$string['modelresults'] = '{$a} results';
$string['samestartdate'] = 'Current start date is good';
$string['sameenddate'] = 'Current end date is good';
$string['target'] = 'Target';
$string['target:coursedropout'] = 'Students at risk of dropping out';
$string['target:coursedropoutinfo'] = 'Here you can find a list of students at risk of dropping out.';
$string['timemodified'] = 'Last modification';
$string['trainingprocessfinished'] = 'Training process finished';
$string['trainingresults'] = 'Training results';
$string['trainmodels'] = 'Train models';
$string['viewlog'] = 'Log';
$string['viewpredictions'] = 'View model predictions';
$string['weeksenddateautomaticallyset'] = 'End date automatically set based on start date and the number of sections';
$string['weeksenddatedefault'] = 'End date would be automatically calculated from the course start date';
