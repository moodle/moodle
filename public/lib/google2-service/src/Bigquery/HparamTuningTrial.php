<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Bigquery;

class HparamTuningTrial extends \Google\Model
{
  /**
   * Default value.
   */
  public const STATUS_TRIAL_STATUS_UNSPECIFIED = 'TRIAL_STATUS_UNSPECIFIED';
  /**
   * Scheduled but not started.
   */
  public const STATUS_NOT_STARTED = 'NOT_STARTED';
  /**
   * Running state.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * The trial succeeded.
   */
  public const STATUS_SUCCEEDED = 'SUCCEEDED';
  /**
   * The trial failed.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * The trial is infeasible due to the invalid params.
   */
  public const STATUS_INFEASIBLE = 'INFEASIBLE';
  /**
   * Trial stopped early because it's not promising.
   */
  public const STATUS_STOPPED_EARLY = 'STOPPED_EARLY';
  /**
   * Ending time of the trial.
   *
   * @var string
   */
  public $endTimeMs;
  /**
   * Error message for FAILED and INFEASIBLE trial.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Loss computed on the eval data at the end of trial.
   *
   * @var 
   */
  public $evalLoss;
  protected $evaluationMetricsType = EvaluationMetrics::class;
  protected $evaluationMetricsDataType = '';
  protected $hparamTuningEvaluationMetricsType = EvaluationMetrics::class;
  protected $hparamTuningEvaluationMetricsDataType = '';
  protected $hparamsType = TrainingOptions::class;
  protected $hparamsDataType = '';
  /**
   * Starting time of the trial.
   *
   * @var string
   */
  public $startTimeMs;
  /**
   * The status of the trial.
   *
   * @var string
   */
  public $status;
  /**
   * Loss computed on the training data at the end of trial.
   *
   * @var 
   */
  public $trainingLoss;
  /**
   * 1-based index of the trial.
   *
   * @var string
   */
  public $trialId;

  /**
   * Ending time of the trial.
   *
   * @param string $endTimeMs
   */
  public function setEndTimeMs($endTimeMs)
  {
    $this->endTimeMs = $endTimeMs;
  }
  /**
   * @return string
   */
  public function getEndTimeMs()
  {
    return $this->endTimeMs;
  }
  /**
   * Error message for FAILED and INFEASIBLE trial.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  public function setEvalLoss($evalLoss)
  {
    $this->evalLoss = $evalLoss;
  }
  public function getEvalLoss()
  {
    return $this->evalLoss;
  }
  /**
   * Evaluation metrics of this trial calculated on the test data. Empty in Job
   * API.
   *
   * @param EvaluationMetrics $evaluationMetrics
   */
  public function setEvaluationMetrics(EvaluationMetrics $evaluationMetrics)
  {
    $this->evaluationMetrics = $evaluationMetrics;
  }
  /**
   * @return EvaluationMetrics
   */
  public function getEvaluationMetrics()
  {
    return $this->evaluationMetrics;
  }
  /**
   * Hyperparameter tuning evaluation metrics of this trial calculated on the
   * eval data. Unlike evaluation_metrics, only the fields corresponding to the
   * hparam_tuning_objectives are set.
   *
   * @param EvaluationMetrics $hparamTuningEvaluationMetrics
   */
  public function setHparamTuningEvaluationMetrics(EvaluationMetrics $hparamTuningEvaluationMetrics)
  {
    $this->hparamTuningEvaluationMetrics = $hparamTuningEvaluationMetrics;
  }
  /**
   * @return EvaluationMetrics
   */
  public function getHparamTuningEvaluationMetrics()
  {
    return $this->hparamTuningEvaluationMetrics;
  }
  /**
   * The hyperprameters selected for this trial.
   *
   * @param TrainingOptions $hparams
   */
  public function setHparams(TrainingOptions $hparams)
  {
    $this->hparams = $hparams;
  }
  /**
   * @return TrainingOptions
   */
  public function getHparams()
  {
    return $this->hparams;
  }
  /**
   * Starting time of the trial.
   *
   * @param string $startTimeMs
   */
  public function setStartTimeMs($startTimeMs)
  {
    $this->startTimeMs = $startTimeMs;
  }
  /**
   * @return string
   */
  public function getStartTimeMs()
  {
    return $this->startTimeMs;
  }
  /**
   * The status of the trial.
   *
   * Accepted values: TRIAL_STATUS_UNSPECIFIED, NOT_STARTED, RUNNING, SUCCEEDED,
   * FAILED, INFEASIBLE, STOPPED_EARLY
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  public function setTrainingLoss($trainingLoss)
  {
    $this->trainingLoss = $trainingLoss;
  }
  public function getTrainingLoss()
  {
    return $this->trainingLoss;
  }
  /**
   * 1-based index of the trial.
   *
   * @param string $trialId
   */
  public function setTrialId($trialId)
  {
    $this->trialId = $trialId;
  }
  /**
   * @return string
   */
  public function getTrialId()
  {
    return $this->trialId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HparamTuningTrial::class, 'Google_Service_Bigquery_HparamTuningTrial');
