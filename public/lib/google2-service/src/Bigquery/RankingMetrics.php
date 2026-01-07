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

namespace Google\Service\Bigquery;

class RankingMetrics extends \Google\Model
{
  /**
   * Determines the goodness of a ranking by computing the percentile rank from
   * the predicted confidence and dividing it by the original rank.
   *
   * @var 
   */
  public $averageRank;
  /**
   * Calculates a precision per user for all the items by ranking them and then
   * averages all the precisions across all the users.
   *
   * @var 
   */
  public $meanAveragePrecision;
  /**
   * Similar to the mean squared error computed in regression and explicit
   * recommendation models except instead of computing the rating directly, the
   * output from evaluate is computed against a preference which is 1 or 0
   * depending on if the rating exists or not.
   *
   * @var 
   */
  public $meanSquaredError;
  /**
   * A metric to determine the goodness of a ranking calculated from the
   * predicted confidence by comparing it to an ideal rank measured by the
   * original ratings.
   *
   * @var 
   */
  public $normalizedDiscountedCumulativeGain;

  public function setAverageRank($averageRank)
  {
    $this->averageRank = $averageRank;
  }
  public function getAverageRank()
  {
    return $this->averageRank;
  }
  public function setMeanAveragePrecision($meanAveragePrecision)
  {
    $this->meanAveragePrecision = $meanAveragePrecision;
  }
  public function getMeanAveragePrecision()
  {
    return $this->meanAveragePrecision;
  }
  public function setMeanSquaredError($meanSquaredError)
  {
    $this->meanSquaredError = $meanSquaredError;
  }
  public function getMeanSquaredError()
  {
    return $this->meanSquaredError;
  }
  public function setNormalizedDiscountedCumulativeGain($normalizedDiscountedCumulativeGain)
  {
    $this->normalizedDiscountedCumulativeGain = $normalizedDiscountedCumulativeGain;
  }
  public function getNormalizedDiscountedCumulativeGain()
  {
    return $this->normalizedDiscountedCumulativeGain;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RankingMetrics::class, 'Google_Service_Bigquery_RankingMetrics');
