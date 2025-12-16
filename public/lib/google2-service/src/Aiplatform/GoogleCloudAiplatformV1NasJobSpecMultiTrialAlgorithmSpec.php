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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpec extends \Google\Model
{
  /**
   * Defaults to `REINFORCEMENT_LEARNING`.
   */
  public const MULTI_TRIAL_ALGORITHM_MULTI_TRIAL_ALGORITHM_UNSPECIFIED = 'MULTI_TRIAL_ALGORITHM_UNSPECIFIED';
  /**
   * The Reinforcement Learning Algorithm for Multi-trial Neural Architecture
   * Search (NAS).
   */
  public const MULTI_TRIAL_ALGORITHM_REINFORCEMENT_LEARNING = 'REINFORCEMENT_LEARNING';
  /**
   * The Grid Search Algorithm for Multi-trial Neural Architecture Search (NAS).
   */
  public const MULTI_TRIAL_ALGORITHM_GRID_SEARCH = 'GRID_SEARCH';
  protected $metricType = GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecMetricSpec::class;
  protected $metricDataType = '';
  /**
   * The multi-trial Neural Architecture Search (NAS) algorithm type. Defaults
   * to `REINFORCEMENT_LEARNING`.
   *
   * @var string
   */
  public $multiTrialAlgorithm;
  protected $searchTrialSpecType = GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecSearchTrialSpec::class;
  protected $searchTrialSpecDataType = '';
  protected $trainTrialSpecType = GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecTrainTrialSpec::class;
  protected $trainTrialSpecDataType = '';

  /**
   * Metric specs for the NAS job. Validation for this field is done at
   * `multi_trial_algorithm_spec` field.
   *
   * @param GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecMetricSpec $metric
   */
  public function setMetric(GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecMetricSpec $metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecMetricSpec
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * The multi-trial Neural Architecture Search (NAS) algorithm type. Defaults
   * to `REINFORCEMENT_LEARNING`.
   *
   * Accepted values: MULTI_TRIAL_ALGORITHM_UNSPECIFIED, REINFORCEMENT_LEARNING,
   * GRID_SEARCH
   *
   * @param self::MULTI_TRIAL_ALGORITHM_* $multiTrialAlgorithm
   */
  public function setMultiTrialAlgorithm($multiTrialAlgorithm)
  {
    $this->multiTrialAlgorithm = $multiTrialAlgorithm;
  }
  /**
   * @return self::MULTI_TRIAL_ALGORITHM_*
   */
  public function getMultiTrialAlgorithm()
  {
    return $this->multiTrialAlgorithm;
  }
  /**
   * Required. Spec for search trials.
   *
   * @param GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecSearchTrialSpec $searchTrialSpec
   */
  public function setSearchTrialSpec(GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecSearchTrialSpec $searchTrialSpec)
  {
    $this->searchTrialSpec = $searchTrialSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecSearchTrialSpec
   */
  public function getSearchTrialSpec()
  {
    return $this->searchTrialSpec;
  }
  /**
   * Spec for train trials. Top N [TrainTrialSpec.max_parallel_trial_count]
   * search trials will be trained for every M [TrainTrialSpec.frequency] trials
   * searched.
   *
   * @param GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecTrainTrialSpec $trainTrialSpec
   */
  public function setTrainTrialSpec(GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecTrainTrialSpec $trainTrialSpec)
  {
    $this->trainTrialSpec = $trainTrialSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpecTrainTrialSpec
   */
  public function getTrainTrialSpec()
  {
    return $this->trainTrialSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NasJobSpecMultiTrialAlgorithmSpec');
