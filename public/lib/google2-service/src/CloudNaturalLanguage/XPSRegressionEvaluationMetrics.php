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

namespace Google\Service\CloudNaturalLanguage;

class XPSRegressionEvaluationMetrics extends \Google\Collection
{
  protected $collection_key = 'regressionMetricsEntries';
  /**
   * Mean Absolute Error (MAE).
   *
   * @var float
   */
  public $meanAbsoluteError;
  /**
   * Mean absolute percentage error. Only set if all ground truth values are
   * positive.
   *
   * @var float
   */
  public $meanAbsolutePercentageError;
  /**
   * R squared.
   *
   * @var float
   */
  public $rSquared;
  protected $regressionMetricsEntriesType = XPSRegressionMetricsEntry::class;
  protected $regressionMetricsEntriesDataType = 'array';
  /**
   * Root Mean Squared Error (RMSE).
   *
   * @var float
   */
  public $rootMeanSquaredError;
  /**
   * Root mean squared log error.
   *
   * @var float
   */
  public $rootMeanSquaredLogError;

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
   * Mean absolute percentage error. Only set if all ground truth values are
   * positive.
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
   * R squared.
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
   * A list of actual versus predicted points for the model being evaluated.
   *
   * @param XPSRegressionMetricsEntry[] $regressionMetricsEntries
   */
  public function setRegressionMetricsEntries($regressionMetricsEntries)
  {
    $this->regressionMetricsEntries = $regressionMetricsEntries;
  }
  /**
   * @return XPSRegressionMetricsEntry[]
   */
  public function getRegressionMetricsEntries()
  {
    return $this->regressionMetricsEntries;
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
   * Root mean squared log error.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSRegressionEvaluationMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSRegressionEvaluationMetrics');
