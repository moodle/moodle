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

class GoogleCloudMlV1HyperparameterSpec extends \Google\Collection
{
  /**
   * The default algorithm used by the hyperparameter tuning service. This is a
   * Bayesian optimization algorithm.
   */
  public const ALGORITHM_ALGORITHM_UNSPECIFIED = 'ALGORITHM_UNSPECIFIED';
  /**
   * Simple grid search within the feasible space. To use grid search, all
   * parameters must be `INTEGER`, `CATEGORICAL`, or `DISCRETE`.
   */
  public const ALGORITHM_GRID_SEARCH = 'GRID_SEARCH';
  /**
   * Simple random search within the feasible space.
   */
  public const ALGORITHM_RANDOM_SEARCH = 'RANDOM_SEARCH';
  /**
   * Goal Type will default to maximize.
   */
  public const GOAL_GOAL_TYPE_UNSPECIFIED = 'GOAL_TYPE_UNSPECIFIED';
  /**
   * Maximize the goal metric.
   */
  public const GOAL_MAXIMIZE = 'MAXIMIZE';
  /**
   * Minimize the goal metric.
   */
  public const GOAL_MINIMIZE = 'MINIMIZE';
  protected $collection_key = 'params';
  /**
   * Optional. The search algorithm specified for the hyperparameter tuning job.
   * Uses the default AI Platform hyperparameter tuning algorithm if
   * unspecified.
   *
   * @var string
   */
  public $algorithm;
  /**
   * Optional. Indicates if the hyperparameter tuning job enables auto trial
   * early stopping.
   *
   * @var bool
   */
  public $enableTrialEarlyStopping;
  /**
   * Required. The type of goal to use for tuning. Available types are
   * `MAXIMIZE` and `MINIMIZE`. Defaults to `MAXIMIZE`.
   *
   * @var string
   */
  public $goal;
  /**
   * Optional. The TensorFlow summary tag name to use for optimizing trials. For
   * current versions of TensorFlow, this tag name should exactly match what is
   * shown in TensorBoard, including all scopes. For versions of TensorFlow
   * prior to 0.12, this should be only the tag passed to tf.Summary. By
   * default, "training/hptuning/metric" will be used.
   *
   * @var string
   */
  public $hyperparameterMetricTag;
  /**
   * Optional. The number of failed trials that need to be seen before failing
   * the hyperparameter tuning job. You can specify this field to override the
   * default failing criteria for AI Platform hyperparameter tuning jobs.
   * Defaults to zero, which means the service decides when a hyperparameter job
   * should fail.
   *
   * @var int
   */
  public $maxFailedTrials;
  /**
   * Optional. The number of training trials to run concurrently. You can reduce
   * the time it takes to perform hyperparameter tuning by adding trials in
   * parallel. However, each trail only benefits from the information gained in
   * completed trials. That means that a trial does not get access to the
   * results of trials running at the same time, which could reduce the quality
   * of the overall optimization. Each trial will use the same scale tier and
   * machine types. Defaults to one.
   *
   * @var int
   */
  public $maxParallelTrials;
  /**
   * Optional. How many training trials should be attempted to optimize the
   * specified hyperparameters. Defaults to one.
   *
   * @var int
   */
  public $maxTrials;
  protected $paramsType = GoogleCloudMlV1ParameterSpec::class;
  protected $paramsDataType = 'array';
  /**
   * Optional. The prior hyperparameter tuning job id that users hope to
   * continue with. The job id will be used to find the corresponding vizier
   * study guid and resume the study.
   *
   * @var string
   */
  public $resumePreviousJobId;

  /**
   * Optional. The search algorithm specified for the hyperparameter tuning job.
   * Uses the default AI Platform hyperparameter tuning algorithm if
   * unspecified.
   *
   * Accepted values: ALGORITHM_UNSPECIFIED, GRID_SEARCH, RANDOM_SEARCH
   *
   * @param self::ALGORITHM_* $algorithm
   */
  public function setAlgorithm($algorithm)
  {
    $this->algorithm = $algorithm;
  }
  /**
   * @return self::ALGORITHM_*
   */
  public function getAlgorithm()
  {
    return $this->algorithm;
  }
  /**
   * Optional. Indicates if the hyperparameter tuning job enables auto trial
   * early stopping.
   *
   * @param bool $enableTrialEarlyStopping
   */
  public function setEnableTrialEarlyStopping($enableTrialEarlyStopping)
  {
    $this->enableTrialEarlyStopping = $enableTrialEarlyStopping;
  }
  /**
   * @return bool
   */
  public function getEnableTrialEarlyStopping()
  {
    return $this->enableTrialEarlyStopping;
  }
  /**
   * Required. The type of goal to use for tuning. Available types are
   * `MAXIMIZE` and `MINIMIZE`. Defaults to `MAXIMIZE`.
   *
   * Accepted values: GOAL_TYPE_UNSPECIFIED, MAXIMIZE, MINIMIZE
   *
   * @param self::GOAL_* $goal
   */
  public function setGoal($goal)
  {
    $this->goal = $goal;
  }
  /**
   * @return self::GOAL_*
   */
  public function getGoal()
  {
    return $this->goal;
  }
  /**
   * Optional. The TensorFlow summary tag name to use for optimizing trials. For
   * current versions of TensorFlow, this tag name should exactly match what is
   * shown in TensorBoard, including all scopes. For versions of TensorFlow
   * prior to 0.12, this should be only the tag passed to tf.Summary. By
   * default, "training/hptuning/metric" will be used.
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
   * Optional. The number of failed trials that need to be seen before failing
   * the hyperparameter tuning job. You can specify this field to override the
   * default failing criteria for AI Platform hyperparameter tuning jobs.
   * Defaults to zero, which means the service decides when a hyperparameter job
   * should fail.
   *
   * @param int $maxFailedTrials
   */
  public function setMaxFailedTrials($maxFailedTrials)
  {
    $this->maxFailedTrials = $maxFailedTrials;
  }
  /**
   * @return int
   */
  public function getMaxFailedTrials()
  {
    return $this->maxFailedTrials;
  }
  /**
   * Optional. The number of training trials to run concurrently. You can reduce
   * the time it takes to perform hyperparameter tuning by adding trials in
   * parallel. However, each trail only benefits from the information gained in
   * completed trials. That means that a trial does not get access to the
   * results of trials running at the same time, which could reduce the quality
   * of the overall optimization. Each trial will use the same scale tier and
   * machine types. Defaults to one.
   *
   * @param int $maxParallelTrials
   */
  public function setMaxParallelTrials($maxParallelTrials)
  {
    $this->maxParallelTrials = $maxParallelTrials;
  }
  /**
   * @return int
   */
  public function getMaxParallelTrials()
  {
    return $this->maxParallelTrials;
  }
  /**
   * Optional. How many training trials should be attempted to optimize the
   * specified hyperparameters. Defaults to one.
   *
   * @param int $maxTrials
   */
  public function setMaxTrials($maxTrials)
  {
    $this->maxTrials = $maxTrials;
  }
  /**
   * @return int
   */
  public function getMaxTrials()
  {
    return $this->maxTrials;
  }
  /**
   * Required. The set of parameters to tune.
   *
   * @param GoogleCloudMlV1ParameterSpec[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return GoogleCloudMlV1ParameterSpec[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Optional. The prior hyperparameter tuning job id that users hope to
   * continue with. The job id will be used to find the corresponding vizier
   * study guid and resume the study.
   *
   * @param string $resumePreviousJobId
   */
  public function setResumePreviousJobId($resumePreviousJobId)
  {
    $this->resumePreviousJobId = $resumePreviousJobId;
  }
  /**
   * @return string
   */
  public function getResumePreviousJobId()
  {
    return $this->resumePreviousJobId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1HyperparameterSpec::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1HyperparameterSpec');
