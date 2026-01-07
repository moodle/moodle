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

class GoogleCloudMlV1StudyConfig extends \Google\Collection
{
  /**
   * The default algorithm used by the Cloud AI Platform Vizier service.
   */
  public const ALGORITHM_ALGORITHM_UNSPECIFIED = 'ALGORITHM_UNSPECIFIED';
  /**
   * Gaussian Process Bandit.
   */
  public const ALGORITHM_GAUSSIAN_PROCESS_BANDIT = 'GAUSSIAN_PROCESS_BANDIT';
  /**
   * Simple grid search within the feasible space. To use grid search, all
   * parameters must be `INTEGER`, `CATEGORICAL`, or `DISCRETE`.
   */
  public const ALGORITHM_GRID_SEARCH = 'GRID_SEARCH';
  /**
   * Simple random search within the feasible space.
   */
  public const ALGORITHM_RANDOM_SEARCH = 'RANDOM_SEARCH';
  protected $collection_key = 'parameters';
  /**
   * The search algorithm specified for the study.
   *
   * @var string
   */
  public $algorithm;
  protected $automatedStoppingConfigType = GoogleCloudMlV1AutomatedStoppingConfig::class;
  protected $automatedStoppingConfigDataType = '';
  protected $metricsType = GoogleCloudMlV1StudyConfigMetricSpec::class;
  protected $metricsDataType = 'array';
  protected $parametersType = GoogleCloudMlV1StudyConfigParameterSpec::class;
  protected $parametersDataType = 'array';

  /**
   * The search algorithm specified for the study.
   *
   * Accepted values: ALGORITHM_UNSPECIFIED, GAUSSIAN_PROCESS_BANDIT,
   * GRID_SEARCH, RANDOM_SEARCH
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
   * Configuration for automated stopping of unpromising Trials.
   *
   * @param GoogleCloudMlV1AutomatedStoppingConfig $automatedStoppingConfig
   */
  public function setAutomatedStoppingConfig(GoogleCloudMlV1AutomatedStoppingConfig $automatedStoppingConfig)
  {
    $this->automatedStoppingConfig = $automatedStoppingConfig;
  }
  /**
   * @return GoogleCloudMlV1AutomatedStoppingConfig
   */
  public function getAutomatedStoppingConfig()
  {
    return $this->automatedStoppingConfig;
  }
  /**
   * Metric specs for the study.
   *
   * @param GoogleCloudMlV1StudyConfigMetricSpec[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigMetricSpec[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Required. The set of parameters to tune.
   *
   * @param GoogleCloudMlV1StudyConfigParameterSpec[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudMlV1StudyConfigParameterSpec[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1StudyConfig::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1StudyConfig');
