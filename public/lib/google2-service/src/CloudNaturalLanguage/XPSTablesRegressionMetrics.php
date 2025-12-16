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

class XPSTablesRegressionMetrics extends \Google\Collection
{
  protected $collection_key = 'regressionMetricsEntries';
  /**
   * Mean absolute error.
   *
   * @var 
   */
  public $meanAbsoluteError;
  /**
   * Mean absolute percentage error, only set if all of the target column's
   * values are positive.
   *
   * @var 
   */
  public $meanAbsolutePercentageError;
  /**
   * R squared.
   *
   * @var 
   */
  public $rSquared;
  protected $regressionMetricsEntriesType = XPSRegressionMetricsEntry::class;
  protected $regressionMetricsEntriesDataType = 'array';
  /**
   * Root mean squared error.
   *
   * @var 
   */
  public $rootMeanSquaredError;
  /**
   * Root mean squared log error.
   *
   * @var 
   */
  public $rootMeanSquaredLogError;

  public function setMeanAbsoluteError($meanAbsoluteError)
  {
    $this->meanAbsoluteError = $meanAbsoluteError;
  }
  public function getMeanAbsoluteError()
  {
    return $this->meanAbsoluteError;
  }
  public function setMeanAbsolutePercentageError($meanAbsolutePercentageError)
  {
    $this->meanAbsolutePercentageError = $meanAbsolutePercentageError;
  }
  public function getMeanAbsolutePercentageError()
  {
    return $this->meanAbsolutePercentageError;
  }
  public function setRSquared($rSquared)
  {
    $this->rSquared = $rSquared;
  }
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
  public function setRootMeanSquaredError($rootMeanSquaredError)
  {
    $this->rootMeanSquaredError = $rootMeanSquaredError;
  }
  public function getRootMeanSquaredError()
  {
    return $this->rootMeanSquaredError;
  }
  public function setRootMeanSquaredLogError($rootMeanSquaredLogError)
  {
    $this->rootMeanSquaredLogError = $rootMeanSquaredLogError;
  }
  public function getRootMeanSquaredLogError()
  {
    return $this->rootMeanSquaredLogError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTablesRegressionMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSTablesRegressionMetrics');
