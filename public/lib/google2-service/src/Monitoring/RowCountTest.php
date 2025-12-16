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

namespace Google\Service\Monitoring;

class RowCountTest extends \Google\Model
{
  /**
   * No ordering relationship is specified.
   */
  public const COMPARISON_COMPARISON_UNSPECIFIED = 'COMPARISON_UNSPECIFIED';
  /**
   * True if the left argument is greater than the right argument.
   */
  public const COMPARISON_COMPARISON_GT = 'COMPARISON_GT';
  /**
   * True if the left argument is greater than or equal to the right argument.
   */
  public const COMPARISON_COMPARISON_GE = 'COMPARISON_GE';
  /**
   * True if the left argument is less than the right argument.
   */
  public const COMPARISON_COMPARISON_LT = 'COMPARISON_LT';
  /**
   * True if the left argument is less than or equal to the right argument.
   */
  public const COMPARISON_COMPARISON_LE = 'COMPARISON_LE';
  /**
   * True if the left argument is equal to the right argument.
   */
  public const COMPARISON_COMPARISON_EQ = 'COMPARISON_EQ';
  /**
   * True if the left argument is not equal to the right argument.
   */
  public const COMPARISON_COMPARISON_NE = 'COMPARISON_NE';
  /**
   * Required. The comparison to apply between the number of rows returned by
   * the query and the threshold.
   *
   * @var string
   */
  public $comparison;
  /**
   * Required. The value against which to compare the row count.
   *
   * @var string
   */
  public $threshold;

  /**
   * Required. The comparison to apply between the number of rows returned by
   * the query and the threshold.
   *
   * Accepted values: COMPARISON_UNSPECIFIED, COMPARISON_GT, COMPARISON_GE,
   * COMPARISON_LT, COMPARISON_LE, COMPARISON_EQ, COMPARISON_NE
   *
   * @param self::COMPARISON_* $comparison
   */
  public function setComparison($comparison)
  {
    $this->comparison = $comparison;
  }
  /**
   * @return self::COMPARISON_*
   */
  public function getComparison()
  {
    return $this->comparison;
  }
  /**
   * Required. The value against which to compare the row count.
   *
   * @param string $threshold
   */
  public function setThreshold($threshold)
  {
    $this->threshold = $threshold;
  }
  /**
   * @return string
   */
  public function getThreshold()
  {
    return $this->threshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RowCountTest::class, 'Google_Service_Monitoring_RowCountTest');
