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

class GoogleCloudAiplatformV1Measurement extends \Google\Collection
{
  protected $collection_key = 'metrics';
  /**
   * Output only. Time that the Trial has been running at the point of this
   * Measurement.
   *
   * @var string
   */
  public $elapsedDuration;
  protected $metricsType = GoogleCloudAiplatformV1MeasurementMetric::class;
  protected $metricsDataType = 'array';
  /**
   * Output only. The number of steps the machine learning model has been
   * trained for. Must be non-negative.
   *
   * @var string
   */
  public $stepCount;

  /**
   * Output only. Time that the Trial has been running at the point of this
   * Measurement.
   *
   * @param string $elapsedDuration
   */
  public function setElapsedDuration($elapsedDuration)
  {
    $this->elapsedDuration = $elapsedDuration;
  }
  /**
   * @return string
   */
  public function getElapsedDuration()
  {
    return $this->elapsedDuration;
  }
  /**
   * Output only. A list of metrics got by evaluating the objective functions
   * using suggested Parameter values.
   *
   * @param GoogleCloudAiplatformV1MeasurementMetric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudAiplatformV1MeasurementMetric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Output only. The number of steps the machine learning model has been
   * trained for. Must be non-negative.
   *
   * @param string $stepCount
   */
  public function setStepCount($stepCount)
  {
    $this->stepCount = $stepCount;
  }
  /**
   * @return string
   */
  public function getStepCount()
  {
    return $this->stepCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Measurement::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Measurement');
