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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsForecastingEvaluationMetrics extends \Google\Collection
{
  protected $collection_key = 'quantileMetrics';
  /**
   * Mean Absolute Error (MAE).
   *
   * @var float
   */
  public $meanAbsoluteError;
  /**
   * Mean absolute percentage error. Infinity when there are zeros in the ground
   * truth.
   *
   * @var float
   */
  public $meanAbsolutePercentageError;
  protected $quantileMetricsType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsForecastingEvaluationMetricsQuantileMetricsEntry::class;
  protected $quantileMetricsDataType = 'array';
  /**
   * Coefficient of determination as Pearson correlation coefficient. Undefined
   * when ground truth or predictions are constant or near constant.
   *
   * @var float
   */
  public $rSquared;
  /**
   * Root Mean Squared Error (RMSE).
   *
   * @var float
   */
  public $rootMeanSquaredError;
  /**
   * Root mean squared log error. Undefined when there are negative ground truth
   * values or predictions.
   *
   * @var float
   */
  public $rootMeanSquaredLogError;
  /**
   * Root Mean Square Percentage Error. Square root of MSPE. Undefined/imaginary
   * when MSPE is negative.
   *
   * @var float
   */
  public $rootMeanSquaredPercentageError;
  /**
   * Weighted Absolute Percentage Error. Does not use weights, this is just what
   * the metric is called. Undefined if actual values sum to zero. Will be very
   * large if actual values sum to a very small number.
   *
   * @var float
   */
  public $weightedAbsolutePercentageError;

  /**
   * Mean Absolute Error (MAE).
   *
   * @param float $meanAbsoluteError
   */
  public function setMeanAbsoluteError($meanAbsoluteError)
  {
    $this->meanAbsoluteError = $meanAbsoluteError;
  }
  /**
   * @return float
   */
  public function getMeanAbsoluteError()
  {
    return $this->meanAbsoluteError;
  }
  /**
   * Mean absolute percentage error. Infinity when there are zeros in the ground
   * truth.
   *
   * @param float $meanAbsolutePercentageError
   */
  public function setMeanAbsolutePercentageError($meanAbsolutePercentageError)
  {
    $this->meanAbsolutePercentageError = $meanAbsolutePercentageError;
  }
  /**
   * @return float
   */
  public function getMeanAbsolutePercentageError()
  {
    return $this->meanAbsolutePercentageError;
  }
  /**
   * The quantile metrics entries for each quantile.
   *
   * @param GoogleCloudAiplatformV1SchemaModelevaluationMetricsForecastingEvaluationMetricsQuantileMetricsEntry[] $quantileMetrics
   */
  public function setQuantileMetrics($quantileMetrics)
  {
    $this->quantileMetrics = $quantileMetrics;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaModelevaluationMetricsForecastingEvaluationMetricsQuantileMetricsEntry[]
   */
  public function getQuantileMetrics()
  {
    return $this->quantileMetrics;
  }
  /**
   * Coefficient of determination as Pearson correlation coefficient. Undefined
   * when ground truth or predictions are constant or near constant.
   *
   * @param float $rSquared
   */
  public function setRSquared($rSquared)
  {
    $this->rSquared = $rSquared;
  }
  /**
   * @return float
   */
  public function getRSquared()
  {
    return $this->rSquared;
  }
  /**
   * Root Mean Squared Error (RMSE).
   *
   * @param float $rootMeanSquaredError
   */
  public function setRootMeanSquaredError($rootMeanSquaredError)
  {
    $this->rootMeanSquaredError = $rootMeanSquaredError;
  }
  /**
   * @return float
   */
  public function getRootMeanSquaredError()
  {
    return $this->rootMeanSquaredError;
  }
  /**
   * Root mean squared log error. Undefined when there are negative ground truth
   * values or predictions.
   *
   * @param float $rootMeanSquaredLogError
   */
  public function setRootMeanSquaredLogError($rootMeanSquaredLogError)
  {
    $this->rootMeanSquaredLogError = $rootMeanSquaredLogError;
  }
  /**
   * @return float
   */
  public function getRootMeanSquaredLogError()
  {
    return $this->rootMeanSquaredLogError;
  }
  /**
   * Root Mean Square Percentage Error. Square root of MSPE. Undefined/imaginary
   * when MSPE is negative.
   *
   * @param float $rootMeanSquaredPercentageError
   */
  public function setRootMeanSquaredPercentageError($rootMeanSquaredPercentageError)
  {
    $this->rootMeanSquaredPercentageError = $rootMeanSquaredPercentageError;
  }
  /**
   * @return float
   */
  public function getRootMeanSquaredPercentageError()
  {
    return $this->rootMeanSquaredPercentageError;
  }
  /**
   * Weighted Absolute Percentage Error. Does not use weights, this is just what
   * the metric is called. Undefined if actual values sum to zero. Will be very
   * large if actual values sum to a very small number.
   *
   * @param float $weightedAbsolutePercentageError
   */
  public function setWeightedAbsolutePercentageError($weightedAbsolutePercentageError)
  {
    $this->weightedAbsolutePercentageError = $weightedAbsolutePercentageError;
  }
  /**
   * @return float
   */
  public function getWeightedAbsolutePercentageError()
  {
    return $this->weightedAbsolutePercentageError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsForecastingEvaluationMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsForecastingEvaluationMetrics');
