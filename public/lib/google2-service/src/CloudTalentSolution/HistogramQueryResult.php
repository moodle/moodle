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

namespace Google\Service\CloudTalentSolution;

class HistogramQueryResult extends \Google\Model
{
  /**
   * A map from the values of the facet associated with distinct values to the
   * number of matching entries with corresponding value. The key format is: *
   * (for string histogram) string values stored in the field. * (for named
   * numeric bucket) name specified in `bucket()` function, like for `bucket(0,
   * MAX, "non-negative")`, the key will be `non-negative`. * (for anonymous
   * numeric bucket) range formatted as `-`, for example, `0-1000`, `MIN-0`, and
   * `0-MAX`.
   *
   * @var string[]
   */
  public $histogram;
  /**
   * Requested histogram expression.
   *
   * @var string
   */
  public $histogramQuery;

  /**
   * A map from the values of the facet associated with distinct values to the
   * number of matching entries with corresponding value. The key format is: *
   * (for string histogram) string values stored in the field. * (for named
   * numeric bucket) name specified in `bucket()` function, like for `bucket(0,
   * MAX, "non-negative")`, the key will be `non-negative`. * (for anonymous
   * numeric bucket) range formatted as `-`, for example, `0-1000`, `MIN-0`, and
   * `0-MAX`.
   *
   * @param string[] $histogram
   */
  public function setHistogram($histogram)
  {
    $this->histogram = $histogram;
  }
  /**
   * @return string[]
   */
  public function getHistogram()
  {
    return $this->histogram;
  }
  /**
   * Requested histogram expression.
   *
   * @param string $histogramQuery
   */
  public function setHistogramQuery($histogramQuery)
  {
    $this->histogramQuery = $histogramQuery;
  }
  /**
   * @return string
   */
  public function getHistogramQuery()
  {
    return $this->histogramQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HistogramQueryResult::class, 'Google_Service_CloudTalentSolution_HistogramQueryResult');
