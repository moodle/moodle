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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsForecastingEvaluationMetricsQuantileMetricsEntry extends \Google\Model
{
  /**
   * This is a custom metric that calculates the percentage of true values that
   * were less than the predicted value for that quantile. Only populated when
   * optimization_objective is minimize-quantile-loss and each entry corresponds
   * to an entry in quantiles The percent value can be used to compare with the
   * quantile value, which is the target value.
   *
   * @var 
   */
  public $observedQuantile;
  /**
   * The quantile for this entry.
   *
   * @var 
   */
  public $quantile;
  /**
   * The scaled pinball loss of this quantile.
   *
   * @var float
   */
  public $scaledPinballLoss;

  public function setObservedQuantile($observedQuantile)
  {
    $this->observedQuantile = $observedQuantile;
  }
  public function getObservedQuantile()
  {
    return $this->observedQuantile;
  }
  public function setQuantile($quantile)
  {
    $this->quantile = $quantile;
  }
  public function getQuantile()
  {
    return $this->quantile;
  }
  /**
   * The scaled pinball loss of this quantile.
   *
   * @param float $scaledPinballLoss
   */
  public function setScaledPinballLoss($scaledPinballLoss)
  {
    $this->scaledPinballLoss = $scaledPinballLoss;
  }
  /**
   * @return float
   */
  public function getScaledPinballLoss()
  {
    return $this->scaledPinballLoss;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsForecastingEvaluationMetricsQuantileMetricsEntry::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsForecastingEvaluationMetricsQuantileMetricsEntry');
