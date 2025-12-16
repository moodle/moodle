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

class GoogleCloudMlV1TrainingOutput extends \Google\Collection
{
  protected $collection_key = 'trials';
  protected $builtInAlgorithmOutputType = GoogleCloudMlV1BuiltInAlgorithmOutput::class;
  protected $builtInAlgorithmOutputDataType = '';
  /**
   * The number of hyperparameter tuning trials that completed successfully.
   * Only set for hyperparameter tuning jobs.
   *
   * @var string
   */
  public $completedTrialCount;
  /**
   * The amount of ML units consumed by the job.
   *
   * @var 
   */
  public $consumedMLUnits;
  /**
   * The TensorFlow summary tag name used for optimizing hyperparameter tuning
   * trials. See [`HyperparameterSpec.hyperparameterMetricTag`](#HyperparameterS
   * pec.FIELDS.hyperparameter_metric_tag) for more information. Only set for
   * hyperparameter tuning jobs.
   *
   * @var string
   */
  public $hyperparameterMetricTag;
  /**
   * Whether this job is a built-in Algorithm job.
   *
   * @var bool
   */
  public $isBuiltInAlgorithmJob;
  /**
   * Whether this job is a hyperparameter tuning job.
   *
   * @var bool
   */
  public $isHyperparameterTuningJob;
  protected $trialsType = GoogleCloudMlV1HyperparameterOutput::class;
  protected $trialsDataType = 'array';
  /**
   * Output only. URIs for accessing [interactive
   * shells](https://cloud.google.com/ai-platform/training/docs/monitor-debug-
   * interactive-shell) (one URI for each training node). Only available if
   * training_input.enable_web_access is `true`. The keys are names of each node
   * in the training job; for example, `master-replica-0` for the master node,
   * `worker-replica-0` for the first worker, and `ps-replica-0` for the first
   * parameter server. The values are the URIs for each node's interactive
   * shell.
   *
   * @var string[]
   */
  public $webAccessUris;

  /**
   * Details related to built-in algorithms jobs. Only set for built-in
   * algorithms jobs.
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
   * The number of hyperparameter tuning trials that completed successfully.
   * Only set for hyperparameter tuning jobs.
   *
   * @param string $completedTrialCount
   */
  public function setCompletedTrialCount($completedTrialCount)
  {
    $this->completedTrialCount = $completedTrialCount;
  }
  /**
   * @return string
   */
  public function getCompletedTrialCount()
  {
    return $this->completedTrialCount;
  }
  public function setConsumedMLUnits($consumedMLUnits)
  {
    $this->consumedMLUnits = $consumedMLUnits;
  }
  public function getConsumedMLUnits()
  {
    return $this->consumedMLUnits;
  }
  /**
   * The TensorFlow summary tag name used for optimizing hyperparameter tuning
   * trials. See [`HyperparameterSpec.hyperparameterMetricTag`](#HyperparameterS
   * pec.FIELDS.hyperparameter_metric_tag) for more information. Only set for
   * hyperparameter tuning jobs.
   *
   * @param string $hyperparameterMetricTag
   */
  public function setHyperparameterMetricTag($hyperparameterMetricTag)
  {
    $this->hyperparameterMetricTag = $hyperparameterMetricTag;
  }
  /**
   * @return string
   */
  public function getHyperparameterMetricTag()
  {
    return $this->hyperparameterMetricTag;
  }
  /**
   * Whether this job is a built-in Algorithm job.
   *
   * @param bool $isBuiltInAlgorithmJob
   */
  public function setIsBuiltInAlgorithmJob($isBuiltInAlgorithmJob)
  {
    $this->isBuiltInAlgorithmJob = $isBuiltInAlgorithmJob;
  }
  /**
   * @return bool
   */
  public function getIsBuiltInAlgorithmJob()
  {
    return $this->isBuiltInAlgorithmJob;
  }
  /**
   * Whether this job is a hyperparameter tuning job.
   *
   * @param bool $isHyperparameterTuningJob
   */
  public function setIsHyperparameterTuningJob($isHyperparameterTuningJob)
  {
    $this->isHyperparameterTuningJob = $isHyperparameterTuningJob;
  }
  /**
   * @return bool
   */
  public function getIsHyperparameterTuningJob()
  {
    return $this->isHyperparameterTuningJob;
  }
  /**
   * Results for individual Hyperparameter trials. Only set for hyperparameter
   * tuning jobs.
   *
   * @param GoogleCloudMlV1HyperparameterOutput[] $trials
   */
  public function setTrials($trials)
  {
    $this->trials = $trials;
  }
  /**
   * @return GoogleCloudMlV1HyperparameterOutput[]
   */
  public function getTrials()
  {
    return $this->trials;
  }
  /**
   * Output only. URIs for accessing [interactive
   * shells](https://cloud.google.com/ai-platform/training/docs/monitor-debug-
   * interactive-shell) (one URI for each training node). Only available if
   * training_input.enable_web_access is `true`. The keys are names of each node
   * in the training job; for example, `master-replica-0` for the master node,
   * `worker-replica-0` for the first worker, and `ps-replica-0` for the first
   * parameter server. The values are the URIs for each node's interactive
   * shell.
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
class_alias(GoogleCloudMlV1TrainingOutput::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1TrainingOutput');
