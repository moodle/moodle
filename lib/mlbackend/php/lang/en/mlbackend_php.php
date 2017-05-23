<?php

$string['pluginname'] = 'PHP predictor';
$string['errorcantloadmodel'] = 'Model file {$a} does not exist, ensure the model has been trained before using it to predict.';
$string['errornotenoughdata'] = 'The evaluation results varied too much, you could try to gather more data to ensure the model is valid. Evaluation results standard deviation = {$a->deviation}, maximum recommended standard deviation = {$a->accepteddeviation}';
$string['errorlowscore'] = 'The evaluated model prediction accuracy is not very high, some predictions may not be accurate. Model score = {$a->score}, minimum score = {$a->minscore}';
$string['datasetsizelimited'] = 'Only a part of the evaluation dataset has been evaluated due to its size. Set $CFG->mlbackend_php_no_memory_limit if you are confident that your system can cope a {$a} dataset';
