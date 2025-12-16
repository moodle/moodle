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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1HyperparameterOutput extends \Google\Collection
{
  /**
   * The job state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The job has been just created and processing has not yet begun.
   */
  public const STATE_QUEUED = 'QUEUED';
  /**
   * The service is preparing to run the job.
   */
  public const STATE_PREPARING = 'PREPARING';
  /**
   * The job is in progress.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The job completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The job failed. `error_message` should contain the details of the failure.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The job is being cancelled. `error_message` should describe the reason for
   * the cancellation.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The job has been cancelled. `error_message` should describe the reason for
   * the cancellation.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  protected $collection_key = 'allMetrics';
  protected $allMetricsType = GoogleCloudMlV1HyperparameterOutputHyperparameterMetric::class;
  protected $allMetricsDataType = 'array';
  protected $builtInAlgorithmOutputType = GoogleCloudMlV1BuiltInAlgorithmOutput::class;
  protected $builtInAlgorithmOutputDataType = '';
  /**
   * Output only. End time for the trial.
   *
   * @var string
   */
  public $endTime;
  protected $finalMetricType = GoogleCloudMlV1HyperparameterOutputHyperparameterMetric::class;
  protected $finalMetricDataType = '';
  /**
   * The hyperparameters given to this trial.
   *
   * @var string[]
   */
  public $hyperparameters;
  /**
   * True if the trial is stopped early.
   *
   * @var bool
   */
  public $isTrialStoppedEarly;
  /**
   * Output only. Start time for the trial.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The detailed state of the trial.
   *
   * @var string
   */
  public $state;
  /**
   * The trial id for these results.
   *
   * @var string
   */
  public $trialId;
  /**
   * URIs for accessing [interactive shells](https://cloud.google.com/ai-
   * platform/training/docs/monitor-debug-interactive-shell) (one URI for each
   * training node). Only available if this trial is part of a hyperparameter
   * tuning job and the job's training_input.enable_web_access is `true`. The
   * keys are names of each node in the training job; for example, `master-
   * replica-0` for the master node, `worker-replica-0` for the first worker,
   * and `ps-replica-0` for the first parameter server. The values are the URIs
   * for each node's interactive shell.
   *
   * @var string[]
   */
  public $webAccessUris;

  /**
   * All recorded object metrics for this trial. This field is not currently
   * populated.
   *
   * @param GoogleCloudMlV1HyperparameterOutputHyperparameterMetric[] $allMetrics
   */
  public function setAllMetrics($allMetrics)
  {
    $this->allMetrics = $allMetrics;
  }
  /**
   * @return GoogleCloudMlV1HyperparameterOutputHyperparameterMetric[]
   */
  public function getAllMetrics()
  {
    return $this->allMetrics;
  }
  /**
   * Details related to built-in algorithms jobs. Only set for trials of built-
   * in algorithms jobs that have succeeded.
   *
   * @param GoogleCloudMlV1BuiltInAlgorithmOutput $builtInAlgorithmOutput
   */
  public function setBuiltInAlgorithmOutput(GoogleCloudMlV1BuiltInAlgorithmOutput $builtInAlgorithmOutput)
  {
    $this->builtInAlgorithmOutput = $builtInAlgorithmOutput;
  }
  /**
   * @return GoogleCloudMlV1BuiltInAlgorithmOutput
   */
  public function getBuiltInAlgorithmOutput()
  {
    return $this->builtInAlgorithmOutput;
  }
  /**
   * Output only. End time for the trial.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The final objective metric seen for this trial.
   *
   * @param GoogleCloudMlV1HyperparameterOutputHyperparameterMetric $finalMetric
   */
  public function setFinalMetric(GoogleCloudMlV1HyperparameterOutputHyperparameterMetric $finalMetric)
  {
    $this->finalMetric = $finalMetric;
  }
  /**
   * @return GoogleCloudMlV1HyperparameterOutputHyperparameterMetric
   */
  public function getFinalMetric()
  {
    return $this->finalMetric;
  }
  /**
   * The hyperparameters given to this trial.
   *
   * @param string[] $hyperparameters
   */
  public function setHyperparameters($hyperparameters)
  {
    $this->hyperparameters = $hyperparameters;
  }
  /**
   * @return string[]
   */
  public function getHyperparameters()
  {
    return $this->hyperparameters;
  }
  /**
   * True if the trial is stopped early.
   *
   * @param bool $isTrialStoppedEarly
   */
  public function setIsTrialStoppedEarly($isTrialStoppedEarly)
  {
    $this->isTrialStoppedEarly = $isTrialStoppedEarly;
  }
  /**
   * @return bool
   */
  public function getIsTrialStoppedEarly()
  {
    return $this->isTrialStoppedEarly;
  }
  /**
   * Output only. Start time for the trial.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The detailed state of the trial.
   *
   * Accepted values: STATE_UNSPECIFIED, QUEUED, PREPARING, RUNNING, SUCCEEDED,
   * FAILED, CANCELLING, CANCELLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The trial id for these results.
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
  /**
   * URIs for accessing [interactive shells](https://cloud.google.com/ai-
   * platform/training/docs/monitor-debug-interactive-shell) (one URI for each
   * training node). Only available if this trial is part of a hyperparameter
   * tuning job and the job's training_input.enable_web_access is `true`. The
   * keys are names of each node in the training job; for example, `master-
   * replica-0` for the master node, `worker-replica-0` for the first worker,
   * and `ps-replica-0` for the first parameter server. The values are the URIs
   * for each node's interactive shell.
   *
   * @param string[] $webAccessUris
   */
  public function setWebAccessUris($webAccessUris)
  {
    $this->webAccessUris = $webAccessUris;
  }
  /**
   * @return string[]
   */
  public function getWebAccessUris()
  {
    return $this->webAccessUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1HyperparameterOutput::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1HyperparameterOutput');
